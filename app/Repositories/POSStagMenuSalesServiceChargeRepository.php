<?php

namespace App\Repositories;

use App\Models\POSStagMenuSalesServiceCharge;
use App\Repositories\BaseRepository;

/**
 * Class POSStagMenuSalesServiceChargeRepository
 * @package App\Repositories
 * @version July 27, 2022, 8:55 am +04
 *
 * @method POSStagMenuSalesServiceCharge findWithoutFail($id, $columns = ['*'])
 * @method POSStagMenuSalesServiceCharge find($id, $columns = ['*'])
 * @method POSStagMenuSalesServiceCharge first($columns = ['*'])
*/
class POSStagMenuSalesServiceChargeRepository extends BaseRepository
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
        return POSStagMenuSalesServiceCharge::class;
    }
}
