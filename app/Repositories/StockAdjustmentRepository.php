<?php

namespace App\Repositories;

use App\Models\StockAdjustment;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class StockAdjustmentRepository
 * @package App\Repositories
 * @version August 20, 2018, 11:55 am UTC
 *
 * @method StockAdjustment findWithoutFail($id, $columns = ['*'])
 * @method StockAdjustment find($id, $columns = ['*'])
 * @method StockAdjustment first($columns = ['*'])
*/
class StockAdjustmentRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companySystemID',
        'companyID',
        'serviceLineSystemID',
        'serviceLineCode',
        'documentSystemID',
        'documentID',
        'companyFinanceYearID',
        'companyFinancePeriodID',
        'FYBiggin',
        'FYEnd',
        'serialNo',
        'stockAdjustmentCode',
        'refNo',
        'stockAdjustmentDate',
        'location',
        'comment',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'approved',
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
        return StockAdjustment::class;
    }
}
