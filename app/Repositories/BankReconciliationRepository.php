<?php

namespace App\Repositories;

use App\Models\BankReconciliation;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class BankReconciliationRepository
 * @package App\Repositories
 * @version September 18, 2018, 4:11 am UTC
 *
 * @method BankReconciliation findWithoutFail($id, $columns = ['*'])
 * @method BankReconciliation find($id, $columns = ['*'])
 * @method BankReconciliation first($columns = ['*'])
*/
class BankReconciliationRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'documentSystemID',
        'documentID',
        'companySystemID',
        'companyID',
        'bankGLAutoID',
        'month',
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
        return BankReconciliation::class;
    }
}
