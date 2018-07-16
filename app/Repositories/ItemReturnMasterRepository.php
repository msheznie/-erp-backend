<?php

namespace App\Repositories;

use App\Models\ItemReturnMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ItemReturnMasterRepository
 * @package App\Repositories
 * @version July 16, 2018, 4:53 am UTC
 *
 * @method ItemReturnMaster findWithoutFail($id, $columns = ['*'])
 * @method ItemReturnMaster find($id, $columns = ['*'])
 * @method ItemReturnMaster first($columns = ['*'])
*/
class ItemReturnMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
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
        'postedDate',
        'RollLevForApp_curr',
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
        return ItemReturnMaster::class;
    }
}
