<?php

namespace App\Repositories;

use App\Models\ItemAssigned;
use App\Repositories\BaseRepository;

/**
 * Class ItemAssignedRepository
 * @package App\Repositories
 * @version March 9, 2018, 11:24 am UTC
 *
 * @method ItemAssigned findWithoutFail($id, $columns = ['*'])
 * @method ItemAssigned find($id, $columns = ['*'])
 * @method ItemAssigned first($columns = ['*'])
*/
class ItemAssignedRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'itemCodeSystem',
        'itemPrimaryCode',
        'secondaryItemCode',
        'barcode',
        'itemDescription',
        'itemUnitOfMeasure',
        'itemUrl',
        'companySystemID',
        'companyID',
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
        'categorySub1',
        'categorySub2',
        'categorySub3',
        'categorySub4',
        'categorySub5',
        'isActive',
        'isAssigned',
        'selectedForWarehouse',
        'itemMovementCategory',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ItemAssigned::class;
    }
}
