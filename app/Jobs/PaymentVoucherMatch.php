<?php

namespace App\Jobs;

use App\helper\CommonJobService;
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

class PaymentVoucherMatch implements ShouldQueue
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

            /*** Payment Voucher Exact Matching ***/
            $pvMatchingRule = BankReconciliationRules::where('bankAccountAutoID', $bankAccountID)
                                        ->where('isDefault', 1)
                                        ->where('companySystemID', $companySystemID)
                                        ->where('matchType', 1)
                                        ->where('transactionType', 1)
                                        ->first();

            $pvBankLedgerData = BankLedger::where("bankAccountID", $bankAccountID)
                                        ->where("trsClearedYN", -1)
                                        ->whereDate("postedDate", '<=', $bankStatementMaster->statementEndDate)
                                        ->where("bankClearedYN", 0)
                                        ->where("companySystemID", $companySystemID)
                                        ->where('documentSystemID', 4)
                                        ->get()->toArray();
            if (!empty($pvMatchingRule)) 
            {
                collect($pvBankLedgerData)->chunk(100)->each(function ($chunk) use ($pvMatchingRule, $statementId, $exactMatchId) {
                    foreach ($chunk as $bankLedgerDetail) {
                        $pvWhereCondition = 'debit != 0 AND (bankLedgerAutoID IS NULL OR bankLedgerAutoID = 0)';
                        if($pvMatchingRule->isMatchAmount == 1) {
                            $payAmount = (float) $bankLedgerDetail['payAmountBank'];
                            $amountDiff = (float) $pvMatchingRule->amountDifference;

                            $minCredit = $payAmount - $amountDiff;
                            $maxCredit = $payAmount + $amountDiff;

                            $pvWhereCondition .= " AND (debit >= {$minCredit} AND debit <= {$maxCredit})";
                        }
                        if($pvMatchingRule->isMatchDate == 1) {
                            $pvWhereCondition .= " AND (transactionDate >= '" . date('Y-m-d', strtotime($bankLedgerDetail['postedDate'] . ' - ' . $pvMatchingRule->dateDifference . ' days')) . "' 
                                            OR transactionDate <= '" . date('Y-m-d', strtotime($bankLedgerDetail['postedDate'] . ' + ' . $pvMatchingRule->dateDifference . ' days')) . "')";
                        }
                        if($pvMatchingRule->isMatchDocument == 1) {
                            $bankledgerDocument = $pvMatchingRule->systemDocumentColumn == 1? $bankLedgerDetail['documentCode'] : $bankLedgerDetail['documentNarration'];
                            $statementDocument = $pvMatchingRule->statementDocumentColumn == 1? 'transactionNumber' : 'description';
                            
                            if($pvMatchingRule->isUseReference == 1) {
                                $bankledgerDocument = substr($bankledgerDocument, ($pvMatchingRule->statementReferenceFrom - 1), ($pvMatchingRule->statementReferenceTo - $pvMatchingRule->statementReferenceFrom + 1));
                            }

                            $pvWhereCondition .= " AND (".$statementDocument." LIKE '%" . $bankledgerDocument . "%')";
                        }

                        if($pvMatchingRule->isMatchChequeNo) {
                            $chequeStatementDoc = $pvMatchingRule->statementChqueColumn == 1? 'transactionNumber' : 'description';

                            $pvWhereCondition .= " AND (".$chequeStatementDoc." LIKE '%" . $bankLedgerDetail['documentCode'] . "%')";
                        }

                        $pvMatchedBankStatement = BankStatementDetail::where('statementId', $statementId)
                                        ->whereRaw($pvWhereCondition)
                                        ->first();

                        if(!empty($pvMatchedBankStatement)) {
                            $matchedDetail = [
                                'matchType' => $pvMatchingRule->matchType,
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
            /*** End of Payment Voucher Exact Matching ***/
            
            /*** Payment Voucher Partial Matching ***/
            $pvPartialMatchingRule = BankReconciliationRules::where('bankAccountAutoID', $bankAccountID)
                            ->where('isDefault', 1)
                            ->where('companySystemID', $companySystemID)
                            ->where('matchType', 2)
                            ->where('transactionType', 1)
                            ->first();
                            
            if (!empty($pvPartialMatchingRule)) 
            {
                $exactMatchId = BankStatementDetail::where('statementId', $statementId)->where('matchType', 2)->count() + 1;
                $updatedLedgerId = BankStatementDetail::where('statementId', $statementId)
                    ->where('bankLedgerAutoID', '!=', null)
                    ->pluck('bankLedgerAutoID')->toArray();

                $pvPendingLedgerDetails = array_filter($pvBankLedgerData, function($ledger) use ($updatedLedgerId) {
                    return !in_array($ledger['bankLedgerAutoID'], $updatedLedgerId);
                });
                
                collect($pvPendingLedgerDetails)->chunk(100)->each(function ($chunk) use ($pvPartialMatchingRule, $statementId, $exactMatchId) {
                    foreach ($chunk as $bankLedgerDetail) {
                        $partialWhereCondition = 'debit != 0 AND (bankLedgerAutoID IS NULL OR bankLedgerAutoID = 0)';
                        if($pvPartialMatchingRule->isMatchAmount == 1) {
                            $payAmount = (float) $bankLedgerDetail['payAmountBank'];
                            $amountDiff = (float) $pvPartialMatchingRule->amountDifference;

                            $minCredit = $payAmount - $amountDiff;
                            $maxCredit = $payAmount + $amountDiff;

                            $partialWhereCondition .= " AND (debit >= {$minCredit} AND debit <= {$maxCredit})";
                        }
                        if($pvPartialMatchingRule->isMatchDate == 1) {
                            $partialWhereCondition .= " AND (transactionDate >= '" . date('Y-m-d', strtotime($bankLedgerDetail['postedDate'] . ' - ' . $pvPartialMatchingRule->dateDifference . ' days')) . "' 
                                            OR transactionDate <= '" . date('Y-m-d', strtotime($bankLedgerDetail['postedDate'] . ' + ' . $pvPartialMatchingRule->dateDifference . ' days')) . "')";
                        }
                        if($pvPartialMatchingRule->isMatchDocument == 1) {
                            $bankledgerDocument = $pvPartialMatchingRule->systemDocumentColumn == 1? $bankLedgerDetail['documentCode'] : $bankLedgerDetail['documentNarration'];
                            $statementDocument = $pvPartialMatchingRule->statementDocumentColumn == 1? 'transactionNumber' : 'description';
                            
                            if($pvPartialMatchingRule->isUseReference == 1) {
                                $referenceFrom = $pvMatchpvPartialMatchingRuleingRule->statementReferenceFrom - 1;
                                $referenceTo = $pvPartialMatchingRule->statementReferenceTo - $pvPartialMatchingRule->statementReferenceFrom + 1;
                                $bankledgerDocument = substr($bankledgerDocument, $referenceFrom, $referenceTo);
                            }

                            $partialWhereCondition .= " AND (".$statementDocument." LIKE '%" . $bankledgerDocument . "%')";
                        }

                        if($pvPartialMatchingRule->isMatchChequeNo) {
                            $chequeStatementDoc = $pvPartialMatchingRule->statementChqueColumn == 1? 'transactionNumber' : 'description';

                            $partialWhereCondition .= " AND (".$chequeStatementDoc." LIKE '%" . $bankLedgerDetail['documentCode'] . "%')";
                        }

                        $pvMatchedBankStatement = BankStatementDetail::where('statementId', $statementId)
                                        ->whereRaw($partialWhereCondition)
                                        ->first();

                        if(!empty($pvMatchedBankStatement)) {
                            $matchedDetail = [
                                'matchType' => $pvPartialMatchingRule->matchType,
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
            /*** End of Payment Voucher Partial Matching ***/

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
