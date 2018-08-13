<?php

namespace App\Repositories;

use App\Models\InventoryReclassification;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class InventoryReclassificationRepository
 * @package App\Repositories
 * @version August 10, 2018, 5:05 am UTC
 *
 * @method InventoryReclassification findWithoutFail($id, $columns = ['*'])
 * @method InventoryReclassification find($id, $columns = ['*'])
 * @method InventoryReclassification first($columns = ['*'])
*/
class InventoryReclassificationRepository extends BaseRepository
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
        'inventoryReclassificationDate',
        'narration',
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
        'rejectedYN',
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
        return InventoryReclassification::class;
    }
}
