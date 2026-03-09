<?php

namespace App\Repositories;

use App\Models\InventoryReclassificationDetail;
use App\Repositories\BaseRepository;

/**
 * Class InventoryReclassificationDetailRepository
 * @package App\Repositories
 * @version August 10, 2018, 5:05 am UTC
 *
 * @method InventoryReclassificationDetail findWithoutFail($id, $columns = ['*'])
 * @method InventoryReclassificationDetail find($id, $columns = ['*'])
 * @method InventoryReclassificationDetail first($columns = ['*'])
*/
class InventoryReclassificationDetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'inventoryreclassificationID',
        'itemSystemCode',
        'itemPrimaryCode',
        'itemDescription',
        'unitOfMeasure',
        'itemFinanceCategoryID',
        'itemFinanceCategorySubID',
        'financeGLcodebBSSystemID',
        'financeGLcodebBS',
        'financeGLcodePLSystemID',
        'financeGLcodePL',
        'includePLForGRVYN',
        'currentStockQty',
        'unitCostLocal',
        'unitCostRpt',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return InventoryReclassificationDetail::class;
    }
}
