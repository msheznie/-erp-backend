<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\BankStatementMaster;
use App\Models\BankReconciliationRules;
use App\Models\BankLedger;
use App\Models\BankStatementDetail;
use App\helper\CommonJobService;

class ReceiptVoucherMatch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $db;
    public $statementId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($db, $statementId)
    {
        if(env('QUEUE_DRIVER_CHANGE','database') == 'database'){
            if(env('IS_MULTI_TENANCY',false)){
                self::onConnection('database_main');
            }else{
                self::onConnection('database');
            }
        }else{
            self::onConnection(env('QUEUE_DRIVER_CHANGE','database'));
        }
        $this->db = $db;
        $this->statementId = $statementId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $db = $this->db;
        $statementId = $this->statementId;
        CommonJobService::db_switch($db);
        DB::beginTransaction();
        try {
            $exactMatchId = BankStatementDetail::where('statementId', $statementId)->where('matchType', 1)->count() + 1;
            $bankStatementMaster = BankStatementMaster::find($statementId);
            $bankAccountID = $bankStatementMaster->bankAccountAutoID;
            $companySystemID = $bankStatementMaster->companySystemID;

            /*** Receipt Voucher Exact Matching ***/
            $rvMatchingRule = BankReconciliationRules::where('bankAccountAutoID', $bankAccountID)
                                ->where('isDefault', 1)
                                ->where('companySystemID', $companySystemID)
                                ->where('matchType', 1)
                                ->where('transactionType', 2)
                                ->first();
            
            $matchedLedgerIds = BankStatementDetail::where('statementId', $statementId)->pluck('bankLedgerAutoID')->toArray();
            $rvBankLedgerData = BankLedger::with('receiptVoucher')
                                ->where("bankAccountID", $bankAccountID)
                                ->where("trsClearedYN", -1)
                                ->whereDate("postedDate", '<=', $bankStatementMaster->statementEndDate)
                                ->where("bankClearedYN", 0)
                                ->where("companySystemID", $companySystemID)
                                ->where('documentSystemID', 21)
                                ->whereNotIn('bankLedgerAutoID', $matchedLedgerIds)
                                ->get()->toArray();

            if (!empty($rvMatchingRule)) 
            {
                collect($rvBankLedgerData)->chunk(100)->each(function ($chunk) use ($rvMatchingRule, $statementId, $exactMatchId) {
                    foreach ($chunk as $bankLedgerDetail) 
                    {
                        $receiptWhereCondition = 'credit != 0 AND (bankLedgerAutoID IS NULL OR bankLedgerAutoID = 0)';
                        if($rvMatchingRule->isMatchAmount == 1) {
                            $payAmount = (float) $bankLedgerDetail['payAmountBank'] < 0? $bankLedgerDetail['payAmountBank'] * -1 : $bankLedgerDetail['payAmountBank'];
                            $amountDiff = (float) $rvMatchingRule->amountDifference;

                            $minCredit = $payAmount - $amountDiff;
                            $maxCredit = $payAmount + $amountDiff;

                            $receiptWhereCondition .= " AND (ABS(credit) >= {$minCredit} AND ABS(credit) <= {$maxCredit})";
                        }
                        if($rvMatchingRule->isMatchDate == 1) {
                            $receiptWhereCondition .= " AND transactionDate BETWEEN '" . 
                                                            date('Y-m-d', strtotime(date('Y-m-d', strtotime($bankLedgerDetail['postedDate'])) . ' - ' . $rvMatchingRule->dateDifference . ' days')) . 
                                                            "' AND '" . 
                                                            date('Y-m-d', strtotime(date('Y-m-d', strtotime($bankLedgerDetail['postedDate'])) . ' + ' . $rvMatchingRule->dateDifference . ' days')) . 
                                                        "'";
                        }
                        if($rvMatchingRule->isMatchDocument == 1) {
                            $bankledgerDocument = $rvMatchingRule->systemDocumentColumn == 1? $bankLedgerDetail['documentCode'] : $bankLedgerDetail['documentNarration'];
                            $statementDocument = $rvMatchingRule->statementDocumentColumn == 1? 'transactionNumber' : 'description';
                            
                            if($rvMatchingRule->isUseReference == 1) {
                                $referenceFrom = $rvMatchingRule->statementReferenceFrom - 1;
                                $referenceTo = $rvMatchingRule->statementReferenceTo - $rvMatchingRule->statementReferenceFrom + 1;
                                $bankledgerDocument = substr($bankledgerDocument, $referenceFrom, $referenceTo);
                            }
                            $safeBankledgerDocument = str_replace('\\', '\\\\\\\\', $bankledgerDocument);
                            $receiptWhereCondition .= " AND (`$statementDocument` LIKE '%{$safeBankledgerDocument}%')";
                        }

                        if($rvMatchingRule->isMatchChequeNo == 1) {
                            $chequeStatementDoc = $rvMatchingRule->statementChqueColumn == 1? 'transactionNumber' : 'description';

                            if($bankLedgerDetail['receipt_voucher']['custChequeNo'] != null && $bankLedgerDetail['receipt_voucher']['custChequeNo'] != 0) { 
                                $receiptWhereCondition .= " AND (".$chequeStatementDoc." LIKE '%" . $bankLedgerDetail['receipt_voucher']['custChequeNo'] . "%')";
                            } else {
                                $receiptWhereCondition .= " AND (1=0)";
                            }
                        }

                        $pvMatchedBankStatement = BankStatementDetail::where('statementId', $statementId)
                                        ->whereRaw($receiptWhereCondition)
                                        ->first();
                        if(!empty($pvMatchedBankStatement)) {
                            $matchedDetail = [
                                'matchType' => $rvMatchingRule->matchType,
                                'bankLedgerAutoID' => $bankLedgerDetail['bankLedgerAutoID'],
                                'matchedId' => $exactMatchId
                            ];
                            BankStatementDetail::where('statementDetailId', $pvMatchedBankStatement->statementDetailId)
                                ->update($matchedDetail);

                            $exactMatchId++;
                        }
                    }
                });
            }
            /*** End of Receipt Voucher Exact Matching ***/

            /*** Receipt Voucher Partial Matching ***/
            $rvPatialMatchingRule = BankReconciliationRules::where('bankAccountAutoID', $bankAccountID)
                                ->where('isDefault', 1)
                                ->where('companySystemID', $companySystemID)
                                ->where('matchType', 2)
                                ->where('transactionType', 2)
                                ->first();

            if (!empty($rvPatialMatchingRule)) 
            {
                $exactMatchId = BankStatementDetail::where('statementId', $statementId)->where('matchType', 2)->count() + 1;
                
                $updatedLedgerId = BankStatementDetail::where('statementId', $statementId)
                    ->where('bankLedgerAutoID', '!=', null)
                    ->pluck('bankLedgerAutoID')->toArray();

                $pvPendingLedgerDetails = array_filter($rvBankLedgerData, function($ledger) use ($updatedLedgerId) {
                    return !in_array($ledger['bankLedgerAutoID'], $updatedLedgerId);
                });
                
                collect($pvPendingLedgerDetails)->chunk(100)->each(function ($chunk) use ($rvPatialMatchingRule, $statementId, $exactMatchId) {
                    foreach ($chunk as $bankLedgerDetail) {
                        $receiptWhereCondition = 'credit != 0 AND (bankLedgerAutoID IS NULL OR bankLedgerAutoID = 0)';
                        if($rvPatialMatchingRule->isMatchAmount == 1) {
                            $payAmount = (float) $bankLedgerDetail['payAmountBank'] < 0 ? $bankLedgerDetail['payAmountBank'] * -1 : $bankLedgerDetail['payAmountBank'];
                            $amountDiff = (float) $rvPatialMatchingRule->amountDifference;
    
                            $minCredit = $payAmount - $amountDiff;
                            $maxCredit = $payAmount + $amountDiff;
    
                            $receiptWhereCondition .= " AND (ABS(credit) >= {$minCredit} AND ABS(credit) <= {$maxCredit})";
                        }
                        if($rvPatialMatchingRule->isMatchDate == 1) {
                            $receiptWhereCondition .= " AND transactionDate BETWEEN '" . 
                                                            date('Y-m-d', strtotime(date('Y-m-d', strtotime($bankLedgerDetail['postedDate'])) . ' - ' . $rvPatialMatchingRule->dateDifference . ' days')) . 
                                                            "' AND '" . 
                                                            date('Y-m-d', strtotime(date('Y-m-d', strtotime($bankLedgerDetail['postedDate'])) . ' + ' . $rvPatialMatchingRule->dateDifference . ' days')) . 
                                                        "'";
                        }
                        if($rvPatialMatchingRule->isMatchDocument == 1) {
                            $bankledgerDocument = $rvPatialMatchingRule->systemDocumentColumn == 1? $bankLedgerDetail['documentCode'] : $bankLedgerDetail['documentNarration'];
                            $statementDocument = $rvPatialMatchingRule->statementDocumentColumn == 1? 'transactionNumber' : 'description';
                            
                            if($rvPatialMatchingRule->isUseReference == 1) {
                                $referenceFrom = $rvPatialMatchingRule->statementReferenceFrom - 1;
                                $referenceTo = $rvPatialMatchingRule->statementReferenceTo - $rvPatialMatchingRule->statementReferenceFrom + 1;
                                $bankledgerDocument = substr($bankledgerDocument, $referenceFrom, $referenceTo);
                            }
                            $safeBankledgerDocument = str_replace('\\', '\\\\\\\\', $bankledgerDocument);
                            $receiptWhereCondition .= " AND (`$statementDocument` LIKE '%{$safeBankledgerDocument}%')";
                        }
    
                        if($rvPatialMatchingRule->isMatchChequeNo == 1) {
                            $chequeStatementDoc = $rvPatialMatchingRule->statementChqueColumn == 1? 'transactionNumber' : 'description';
    
                            if($bankLedgerDetail['receipt_voucher']['custChequeNo'] != null && $bankLedgerDetail['receipt_voucher']['custChequeNo'] != 0) { 
                                $receiptWhereCondition .= " AND (".$chequeStatementDoc." LIKE '%" . $bankLedgerDetail['receipt_voucher']['custChequeNo'] . "%')";
                            } else {
                                $receiptWhereCondition .= " AND (1=0)";
                            }
                        }
    
                        $pvMatchedBankStatement = BankStatementDetail::where('statementId', $statementId)
                                        ->whereRaw($receiptWhereCondition)
                                        ->first();
                        if(!empty($pvMatchedBankStatement)) {
                            $matchedDetail = [
                                'matchType' => $rvPatialMatchingRule->matchType,
                                'bankLedgerAutoID' => $bankLedgerDetail['bankLedgerAutoID'],
                                'matchedId' => $exactMatchId
                            ];
                            BankStatementDetail::where('statementDetailId', $pvMatchedBankStatement->statementDetailId)
                                ->update($matchedDetail);
    
                            $exactMatchId++;
                        }
                    }
                });
            }
            /*** End of Receipt Voucher Partial Matching ***/

            BankStatementMaster::where('statementId', $statementId)->increment('matchingInprogress', 1);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            BankStatementMaster::where('statementId', $statementId)
                ->update([
                    'matchingInprogress' => 4 
                ]);
        }
    }
}
