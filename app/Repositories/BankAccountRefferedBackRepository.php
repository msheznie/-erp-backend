<?php

namespace App\Repositories;

use App\Models\BankAccountRefferedBack;
use App\Repositories\BaseRepository;

/**
 * Class BankAccountRefferedBackRepository
 * @package App\Repositories
 * @version December 21, 2018, 12:04 pm UTC
 *
 * @method BankAccountRefferedBack findWithoutFail($id, $columns = ['*'])
 * @method BankAccountRefferedBack find($id, $columns = ['*'])
 * @method BankAccountRefferedBack first($columns = ['*'])
*/
class BankAccountRefferedBackRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'bankAccountAutoID',
        'bankAssignedAutoID',
        'bankmasterAutoID',
        'companySystemID',
        'companyID',
        'documentSystemID',
        'documentID',
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
        'chartOfAccountSystemID',
        'glCodeLinked',
        'extraNote',
        'isAccountActive',
        'isDefault',
        'approvedYN',
        'approvedByEmpID',
        'approvedByUserSystemID',
        'approvedEmpName',
        'approvedDate',
        'approvedComments',
        'createdDateTime',
        'createdUserSystemID',
        'createdEmpID',
        'createdPCID',
        'modifedDateTime',
        'modifiedUserSystemID',
        'modifiedByEmpID',
        'modifiedPCID',
        'timeStamp',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'RollLevForApp_curr',
        'refferedBackYN',
        'timesReferred'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return BankAccountRefferedBack::class;
    }
}
