<?php

namespace App\Repositories;

use App\Models\ChartOfAccountsRefferedBack;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ChartOfAccountsRefferedBackRepository
 * @package App\Repositories
 * @version December 18, 2018, 6:06 am UTC
 *
 * @method ChartOfAccountsRefferedBack findWithoutFail($id, $columns = ['*'])
 * @method ChartOfAccountsRefferedBack find($id, $columns = ['*'])
 * @method ChartOfAccountsRefferedBack first($columns = ['*'])
*/
class ChartOfAccountsRefferedBackRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'chartOfAccountSystemID',
        'primaryCompanySystemID',
        'primaryCompanyID',
        'documentSystemID',
        'documentID',
        'AccountCode',
        'AccountDescription',
        'masterAccount',
        'catogaryBLorPLID',
        'catogaryBLorPL',
        'controllAccountYN',
        'controlAccountsSystemID',
        'controlAccounts',
        'isApproved',
        'approvedBySystemID',
        'approvedBy',
        'approvedDate',
        'approvedComment',
        'isActive',
        'isBank',
        'AllocationID',
        'relatedPartyYN',
        'interCompanySystemID',
        'interCompanyID',
        'confirmedYN',
        'confirmedEmpSystemID',
        'confirmedEmpID',
        'confirmedEmpName',
        'confirmedEmpDate',
        'isMasterAccount',
        'RollLevForApp_curr',
        'refferedBackYN',
        'timesReferred',
        'createdPcID',
        'createdUserGroup',
        'createdUserID',
        'createdDateTime',
        'modifiedPc',
        'modifiedUser',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ChartOfAccountsRefferedBack::class;
    }
}
