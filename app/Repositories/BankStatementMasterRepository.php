<?php

namespace App\Repositories;

use App\Models\BankLedger;
use App\Models\BankReconciliation;
use App\Models\BankStatementDetail;
use App\Models\BankStatementMaster;
use InfyOm\Generator\Common\BaseRepository;

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
        $bankAccountId = $data['details']['bankAccountAutoID'];

        $openingBalance = BankLedger::selectRaw('companySystemID,bankAccountID,trsClearedYN,bankClearedYN,ABS(SUM(if(bankClearedAmount < 0,bankClearedAmount,0))) - SUM(if(bankClearedAmount > 0,bankClearedAmount,0)) as opening')
            ->where('companySystemID', $companySystemID)
            ->where("bankAccountID", $bankAccountId)
            ->where("trsClearedYN", -1)
            ->where("bankClearedYN", -1)
            ->groupBy('companySystemID', 'bankAccountID')
            ->first();

        if (!empty($openingBalance)) {
            $data['systemOpeningBalance'] = $openingBalance->opening;
        } else {
            $data['systemOpeningBalance'] = 0;
        }
        $closingBalance = BankLedger::selectRaw('companySystemID,bankAccountID,trsClearedYN,bankClearedYN,ABS(SUM(if(bankClearedAmount < 0,bankClearedAmount,0))) - SUM(if(bankClearedAmount > 0,bankClearedAmount,0)) as closing')
            ->where('companySystemID', $companySystemID)
            ->where("bankAccountID", $bankAccountId)
            ->where("trsClearedYN", -1)
            ->where("bankClearedYN", -1)
            ->groupBy('companySystemID', 'bankAccountID')
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

        return $data;
    }

    function getBankWorkbookDetails($statementId, $companySystemID)
    {
        $statementDetails = BankStatementMaster::where('statementId', $statementId)->first();

        $data['bankLedger'] = BankLedger::where("bankAccountID", $statementDetails->bankAccountAutoID)
            ->where("trsClearedYN", -1)
            ->whereDate("postedDate", '<=', $statementDetails->statementEndDate)
            ->where("bankClearedYN", 0)
            ->where("companySystemID", $companySystemID)
            ->get()->toArray();

        $data['bankStatement'] = BankStatementDetail::where('statementId', $statementId)->get()->toArray();

        return $data;
    }
}
