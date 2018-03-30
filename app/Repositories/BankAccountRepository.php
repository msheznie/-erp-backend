<?php

namespace App\Repositories;

use App\Models\BankAccount;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class BankAccountRepository
 * @package App\Repositories
 * @version March 30, 2018, 9:40 am UTC
 *
 * @method BankAccount findWithoutFail($id, $columns = ['*'])
 * @method BankAccount find($id, $columns = ['*'])
 * @method BankAccount first($columns = ['*'])
*/
class BankAccountRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'bankAssignedAutoID',
        'bankmasterAutoID',
        'companyID',
        'bankShortCode',
        'bankName',
        'bankBranch',
        'BranchCode',
        'BranchAddress',
        'BranchContactPerson',
        'BranchTel',
        'BranchFax',
        'BranchEmail',
        'AccountNo',
        'accountCurrencyID',
        'accountSwiftCode',
        'accountIBAN#',
        'chqueManualStartingNo',
        'isManualActive',
        'chquePrintedStartingNo',
        'isPrintedActive',
        'glCodeLinked',
        'extraNote',
        'isAccountActive',
        'isDefault',
        'approvedYN',
        'approvedByEmpID',
        'approvedEmpName',
        'approvedDate',
        'approvedComments',
        'createdDateTime',
        'createdEmpID',
        'createdPCID',
        'modifedDateTime',
        'modifiedByEmpID',
        'modifiedPCID',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return BankAccount::class;
    }
}
