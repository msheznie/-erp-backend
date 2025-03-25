<?php

namespace App\Repositories;

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
            $bankstatementMaster = $bankstatementMaster->where(function ($query) use ($searchValue) {
                $query->where('bankReconciliationMonth', 'LIKE', "%{$searchValue}%")
                    ->orWhere('bankStatementDate', 'LIKE', "%{$searchValue}%");
            });
        }

        return $bankstatementMaster;
    }
}
