<?php

namespace App\Repositories;

use App\Models\POSSTAGMenuSalesTaxes;
use App\Repositories\BaseRepository;

/**
 * Class POSSTAGMenuSalesTaxesRepository
 * @package App\Repositories
 * @version August 16, 2022, 8:50 am +04
 *
 * @method POSSTAGMenuSalesTaxes findWithoutFail($id, $columns = ['*'])
 * @method POSSTAGMenuSalesTaxes find($id, $columns = ['*'])
 * @method POSSTAGMenuSalesTaxes first($columns = ['*'])
*/
class POSSTAGMenuSalesTaxesRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'wareHouseAutoID',
        'menuSalesID',
        'menuSalesItemID',
        'menuID',
        'menutaxID',
        'taxmasterID',
        'vatType',
        'GLCode',
        'taxPercentage',
        'taxAmount',
        'beforeDiscountTotalTaxAmount',
        'menusalesDiscount',
        'menusalesPromotionalDiscount',
        'unitMenuTaxAmount',
        'menusalesItemQty',
        'companyID',
        'companyCode',
        'createdUserGroup',
        'createdPCID',
        'createdUserID',
        'createdDateTime',
        'createdUserName',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedDateTime',
        'modifiedUserName',
        'timestamp',
        'is_sync',
        'id_store',
        'transaction_log_id',
        'isSync'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return POSSTAGMenuSalesTaxes::class;
    }
}
