<?php

namespace App\Repositories;

use App\Models\BankReconciliationTemplateMapping;
use App\Models\BankAccount;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class BankReconciliationTemplateMappingRepository
 * @package App\Repositories
 * @version January 28, 2025, 2:32 pm +04
 *
 * @method BankReconciliationTemplateMapping findWithoutFail($id, $columns = ['*'])
 * @method BankReconciliationTemplateMapping find($id, $columns = ['*'])
 * @method BankReconciliationTemplateMapping first($columns = ['*'])
*/
class BankReconciliationTemplateMappingRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'bankAccountAutoID',
        'bankmasterAutoID',
        'companySystemID',
        'companyID',
        'bankName',
        'bankAccountNumber',
        'statementStartDate',
        'statementEndDate',
        'bankReconciliationMonth',
        'bankStatementDate',
        'openingBalance',
        'endingBalance',
        'firstLine',
        'headerLine',
        'transactionNumber',
        'transactionDate',
        'debit',
        'credit',
        'description',
        'category',
        'createdDateTime',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return BankReconciliationTemplateMapping::class;
    }

    public function updateAllAccounts($input) {
        /** Update details for existing mappings */
        if(isset($input['templateId'])){
            unset($input['templateId']);
        }
        if($input['bankAccountAutoID']){
            unset($input['bankAccountAutoID']);
        }
        BankReconciliationTemplateMapping::where('bankmasterAutoID', $input['bankmasterAutoID'])
            ->where('companySystemID', $input['companySystemID'])
            ->update($input);

        /** Add details for not existing mappings */
        $bankAccountAutoIDList = BankAccount::where('bankmasterAutoID', $input['bankmasterAutoID'])
            ->where('companySystemID', $input['companySystemID'])
            ->whereNotIn('bankAccountAutoID', BankReconciliationTemplateMapping::where('bankmasterAutoID', $input['bankmasterAutoID'])
                                ->where('companySystemID', $input['companySystemID'])
                                ->pluck('bankAccountAutoID')
            )
            ->pluck('bankAccountAutoID');

        foreach ($bankAccountAutoIDList as $accountId) {
            $input['bankAccountAutoID'] = $accountId;
            $bankReconciliationTemplateMapping = new BankReconciliationTemplateMapping();
            $bankReconciliationTemplateMapping->fill($input);
            $bankReconciliationTemplateMapping->save();
        }
        return 'Bank reconciliation template mapping updated to all accounts successfully';
    }
}
