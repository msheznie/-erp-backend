<?php

namespace App\Repositories;

use App\Models\POSSourceMenuSalesServiceCharge;
use App\Repositories\BaseRepository;

/**
 * Class POSSourceMenuSalesServiceChargeRepository
 * @package App\Repositories
 * @version July 27, 2022, 8:57 am +04
 *
 * @method POSSourceMenuSalesServiceCharge findWithoutFail($id, $columns = ['*'])
 * @method POSSourceMenuSalesServiceCharge find($id, $columns = ['*'])
 * @method POSSourceMenuSalesServiceCharge first($columns = ['*'])
*/
class POSSourceMenuSalesServiceChargeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'wareHouseAutoID',
        'menuSalesID',
        'menuSalesItemID',
        'menuServiceChargeID',
        'menuMasterID',
        'serviceChargePercentage',
        'serviceChargeAmount',
        'GLAutoID',
        'beforeDiscountTotalServiceCharge',
        'menusalesDiscount',
        'menusalesPromotionalDiscount',
        'unitMenuServiceCharge',
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
        'transaction_log_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return POSSourceMenuSalesServiceCharge::class;
    }
}
