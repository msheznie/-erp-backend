<?php

namespace App\Repositories;

use App\Models\WarehouseItems;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class WarehouseItemsRepository
 * @package App\Repositories
 * @version September 7, 2018, 9:08 am UTC
 *
 * @method WarehouseItems findWithoutFail($id, $columns = ['*'])
 * @method WarehouseItems find($id, $columns = ['*'])
 * @method WarehouseItems first($columns = ['*'])
*/
class WarehouseItemsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companySystemID',
        'companyID',
        'warehouseSystemCode',
        'itemSystemCode',
        'itemPrimaryCode',
        'itemDescription',
        'unitOfMeasure',
        'stockQty',
        'maximunQty',
        'minimumQty',
        'rolQuantity',
        'wacValueLocalCurrencyID',
        'wacValueLocal',
        'wacValueReportingCurrencyID',
        'wacValueReporting',
        'totalQty',
        'totalValueLocal',
        'totalValueRpt',
        'financeCategoryMaster',
        'financeCategorySub',
        'binNumber',
        'toDelete',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return WarehouseItems::class;
    }
}
