<?php

namespace App\Repositories;

use App\Models\BankLedger;
use App\Models\BankReconciliation;
use App\Models\BankStatementDetail;
use App\Models\BankStatementMaster;
use Carbon\Carbon;
use InfyOm\Generator\Common\BaseRepository;
use App\Models\BankAccount;

/**
 * Class BankStatementMasterRepository
 * @package App\Repositories
 * @version February 4, 2025, 6:00 am +04
 *
 * @method BankStatementMaster findWithoutFail($id, $columns = ['*'])
 * @method BankStatementMaster find($id, $columns = ['*'])
 * @method BankStatementMaster first($columns = ['*'])
*/
class BankStatementMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'bankAccountAutoID',
        'bankmasterAutoID',
        'companySystemID',
        'companyID',
        'transactionCount',
        'statementStartDate',
        'statementEndDate',
        'bankReconciliationMonth',
        'bankStatementDate',
        'openingBalance',
        'endingBalance',
        'filePath',
        'documentStatus',
        'matchingInprogress',
        'importStatus',
        'importError',
        'createdDateTime',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return BankStatementMaster::class;
    }

    public function bankStatementImportHistory($searchValue, $companyId)
    {
        $isGroup = \Helper::checkIsCompanyGroup($companyId);
        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($companyId);
        } else {
            $subCompanies = [$companyId];
        }

        $bankstatementMaster = BankStatementMaster::with('bankAccount')->whereIn('companySystemID', $subCompanies);
        if ($searchValue) {
            $searchValue = str_replace("\\", "\\\\", $searchValue);
            $bankstatementMaster = $bankstatementMaster->whereHas('bankAccount', function ($query) use ($searchValue) {
                $query->where('bankName', 'LIKE', "%{$searchValue}%");
                $query->orWhere('AccountNo', 'LIKE', "%{$searchValue}%");
            });
        }

        return $bankstatementMaster;
    }

    public function bankStatementWorkBook($searchValue, $companyId)
    {
        $isGroup = \Helper::checkIsCompanyGroup($companyId);
        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($companyId);
        } else {
            $subCompanies = [$companyId];
        }

        $bankstatementMaster = BankStatementMaster::with('bankAccount.currency')->whereIn('companySystemID', $subCompanies)->where('importStatus', 1);
        if ($searchValue) {
            $searchValue = str_replace("\\", "\\\\", $searchValue);
            $bankstatementMaster = $bankstatementMaster->whereHas('bankAccount', function ($query) use ($searchValue) {
                $query->where('bankName', 'LIKE', "%{$searchValue}%");
                $query->orWhere('AccountNo', 'LIKE', "%{$searchValue}%");
            });
        }

        return $bankstatementMaster;
    }

    public function getBankWorkbookHeaderDetails($statementId, $companySystemID)
    {
        $data = [];
        $data['details'] = BankStatementMaster::with('bankAccount.currency')->where('statementId', $statementId)->first()->toArray();
        if(!empty($data['details']))
        {
            $bankAccountId = $data['details']['bankAccountAutoID'];

            $fromDate = new Carbon($data['details']['statementStartDate']);
            $openingBalance = BankLedger::selectRaw('companySystemID, documentDate, bankAccountID,trsClearedYN,bankClearedYN, SUM(payAmountBank) * -1 as opening')
                ->where('companySystemID', $companySystemID)
                ->where("bankAccountID", $bankAccountId)
                ->whereDate("documentDate", "<", $fromDate)
                ->first();

            if (!empty($openingBalance)) {
                $data['systemOpeningBalance'] = $openingBalance->opening;
            } else {
                $data['systemOpeningBalance'] = 0;
            }
            $toDate = new Carbon($data['details']['statementEndDate']);
            $closingBalance = BankLedger::selectRaw('companySystemID, documentDate, bankAccountID,trsClearedYN,bankClearedYN, SUM(payAmountBank) * -1 as closing')
                ->where('companySystemID', $companySystemID)
                ->where("bankAccountID", $bankAccountId)
                ->where("documentDate", "<", $toDate)
                ->first();

            if (!empty($closingBalance)) {
                $data['systemClosingBalance'] = $closingBalance->closing;
            } else {
                $data['systemClosingBalance'] = 0;
            }

            $lastRec = BankReconciliation::where('bankAccountAutoID', $bankAccountId)
                ->orderBy('bankRecAsOf', 'desc')
                ->first();

            $data['lastBankRecAmount'] = $lastRec ? $lastRec->closingBalance : 0;
        }

        return $data;
    }

    function getBankWorkbookDetails($statementId, $companySystemID)
    {
        $statementDetails = BankStatementMaster::where('statementId', $statementId)->first();
        $decimalPlaces = BankAccount::with('currency')->where('bankAccountAutoID', $statementDetails->bankAccountAutoID)->first()->currency->DecimalPlaces;
        
        /*** Bank Ledger Details ***/
        $bankLedgers = BankLedger::with('bankStatementDetail')
                                ->where('bankAccountID', $statementDetails->bankAccountAutoID)
                                ->where('trsClearedYN', -1)
                                ->whereDate('postedDate', '<=', $statementDetails->statementEndDate)
                                ->where('bankClearedYN', 0)
                                ->where('companySystemID', $companySystemID)
                                ->get();
                                
        $data['fullyMatchedBankLedger'] = $bankLedgers->filter(function ($ledger) {
            return $ledger->bankStatementDetail && $ledger->bankStatementDetail->matchType == 1;
        })->values()->toArray();

        $data['partiallyMatchedBankLedger'] = $bankLedgers->filter(function ($ledger) {
            return $ledger->bankStatementDetail && $ledger->bankStatementDetail->matchType == 2;
        })->values()->toArray();

        $data['unmatchedBankLedger'] = $bankLedgers->filter(function ($ledger) {
            return is_null($ledger->bankStatementDetail);
        })->values()->toArray();

        $data['totalValues'] = [
            'fullyMatchedSystemTotal' => number_format((array_sum(array_map(function($val) {
                return $val < 0 ? $val * -1 : $val;
            }, array_column($data['fullyMatchedBankLedger'], 'payAmountBank')))), $decimalPlaces),
            'partiallyMatchedSystemTotal' => number_format((array_sum(array_map(function($val) {
                return $val < 0 ? $val * -1 : $val;
            }, array_column($data['partiallyMatchedBankLedger'], 'payAmountBank')))), $decimalPlaces)
        ];


        /*** Bank Statement Details ***/
        $bankStatements = BankStatementDetail::where('statementId', $statementId)->get();

        $data['fullyMatchedBankStatement'] = $bankStatements->filter(function ($item) {
            return $item->matchType == 1;
        })->values()->toArray();

        $data['partiallyMatchedBankStatement'] = $bankStatements->filter(function ($item) {
            return $item->matchType == 2;
        })->values()->toArray();

        $data['unMatchedBankStatement'] = $bankStatements->filter(function ($item) {
            return !in_array($item->matchType, [1, 2]);
        })->values()->toArray();

        $creditFullyTotal = array_sum(array_map(function($val) {
            return $val < 0 ? $val * -1 : $val;
        }, array_column($data['fullyMatchedBankStatement'], 'credit')));

        $debitFullyTotal = array_sum(array_map(function($val) {
            return $val < 0 ? $val * -1 : $val;
        }, array_column($data['fullyMatchedBankStatement'], 'debit')));

        $data['totalValues']['fullyMatchedStatementTotal'] = number_format($creditFullyTotal + $debitFullyTotal, $decimalPlaces);

        $creditPartialTotal = array_sum(array_map(function($val) {
            return $val < 0 ? $val * -1 : $val;
        }, array_column($data['partiallyMatchedBankStatement'], 'credit')));

        $debitPartialTotal = array_sum(array_map(function($val) {
            return $val < 0 ? $val * -1 : $val;
        }, array_column($data['partiallyMatchedBankStatement'], 'debit')));

        $data['totalValues']['partiallyMatchedStatementTotal'] = number_format($creditPartialTotal + $debitPartialTotal, $decimalPlaces);

        return $data;
    }
}
