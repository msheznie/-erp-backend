<?php

namespace App\Repositories;

use App\Models\ItemReturnMasterRefferedBack;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ItemReturnMasterRefferedBackRepository
 * @package App\Repositories
 * @version December 6, 2018, 5:21 am UTC
 *
 * @method ItemReturnMasterRefferedBack findWithoutFail($id, $columns = ['*'])
 * @method ItemReturnMasterRefferedBack find($id, $columns = ['*'])
 * @method ItemReturnMasterRefferedBack first($columns = ['*'])
*/
class ItemReturnMasterRefferedBackRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'itemReturnAutoID',
        'companySystemID',
        'companyID',
        'serviceLineSystemID',
        'serviceLineCode',
        'companyFinanceYearID',
        'companyFinancePeriodID',
        'FYBiggin',
        'FYEnd',
        'documentSystemID',
        'documentID',
        'serialNo',
        'itemReturnCode',
        'ReturnType',
        'ReturnDate',
        'ReturnedBy',
        'jobNo',
        'customerID',
        'wareHouseLocation',
        'ReturnRefNo',
        'comment',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'approved',
        'approvedDate',
        'approvedByUserID',
        'approvedByUserSystemID',
        'postedDate',
        'RollLevForApp_curr',
        'refferedBackYN',
        'timesReferred',
        'createdDateTime',
        'createdUserGroup',
        'createdPCid',
        'createdUserSystemID',
        'createdUserID',
        'modifiedUserSystemID',
        'modifiedUser',
        'modifiedPc',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ItemReturnMasterRefferedBack::class;
    }
}
