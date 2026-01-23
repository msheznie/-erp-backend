<?php

namespace App\Repositories;

use App\Models\BankReconciliationRefferedBack;
use App\Repositories\BaseRepository;

/**
 * Class BankReconciliationRefferedBackRepository
 * @package App\Repositories
 * @version December 11, 2018, 10:55 am UTC
 *
 * @method BankReconciliationRefferedBack findWithoutFail($id, $columns = ['*'])
 * @method BankReconciliationRefferedBack find($id, $columns = ['*'])
 * @method BankReconciliationRefferedBack first($columns = ['*'])
*/
class BankReconciliationRefferedBackRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'bankRecAutoID',
        'documentSystemID',
        'documentID',
        'companySystemID',
        'companyID',
        'bankMasterID',
        'bankAccountAutoID',
        'bankGLAutoID',
        'month',
        'serialNo',
        'bankRecPrimaryCode',
        'year',
        'bankRecAsOf',
        'openingBalance',
        'closingBalance',
        'description',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'approvedYN',
        'approvedDate',
        'approvedByUserID',
        'approvedByUserSystemID',
        'RollLevForApp_curr',
        'timesReferred',
        'refferedBackYN',
        'createdPcID',
        'createdUserSystemID',
        'createdUserID',
        'modifiedPc',
        'modifiedUserSystemID',
        'modifiedUser',
        'createdDateTime',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return BankReconciliationRefferedBack::class;
    }
}
