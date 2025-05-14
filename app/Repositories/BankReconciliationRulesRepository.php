<?php

namespace App\Repositories;

use App\Models\BankReconciliationRules;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class BankReconciliationRulesRepository
 * @package App\Repositories
 * @version May 8, 2025, 8:45 am +04
 *
 * @method BankReconciliationRules findWithoutFail($id, $columns = ['*'])
 * @method BankReconciliationRules find($id, $columns = ['*'])
 * @method BankReconciliationRules first($columns = ['*'])
*/
class BankReconciliationRulesRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'bankAccountAutoID',
        'ruleDescription',
        'transactionType',
        'matchType',
        'isMatchAmount',
        'amountDifference',
        'isMatchDate',
        'dateDifference',
        'isMatchDocument',
        'systemDocumentColumn',
        'statementDocumentColumn',
        'isUseReference',
        'statementReferenceFrom',
        'statementReferenceTo',
        'isMatchChequeNo',
        'statementChqueColumn',
        'isDefault',
        'companySystemID',
        'companyID',
        'createdDateTime',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return BankReconciliationRules::class;
    }

    public function getBankStatementUploadRules($bankAccountId, $searchValue, $companyId)
    {
        $isGroup = \Helper::checkIsCompanyGroup($companyId);
        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($companyId);
        } else {
            $subCompanies = [$companyId];
        }

        $bankReconciliationRules = BankReconciliationRules::where('bankAccountAutoID', $bankAccountId)->whereIn('companySystemID', $subCompanies);
        if ($searchValue) {
            $searchValue = str_replace("\\", "\\\\", $searchValue);
            $bankReconciliationRules = $bankReconciliationRules->whereHas('bankAccount', function ($query) use ($searchValue) {
                $query->where('ruleDescription', 'LIKE', "%{$searchValue}%");
            });
        }

        return $bankReconciliationRules;
    }
}
