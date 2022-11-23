<?php

namespace App\Repositories;

use App\Models\POSSourceMenuSalesPayment;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class POSSourceMenuSalesPaymentRepository
 * @package App\Repositories
 * @version July 27, 2022, 8:29 am +04
 *
 * @method POSSourceMenuSalesPayment findWithoutFail($id, $columns = ['*'])
 * @method POSSourceMenuSalesPayment find($id, $columns = ['*'])
 * @method POSSourceMenuSalesPayment first($columns = ['*'])
*/
class POSSourceMenuSalesPaymentRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'wareHouseAutoID',
        'menuSalesID',
        'paymentConfigMasterID',
        'paymentConfigDetailID',
        'glAccountType',
        'GLCode',
        'amount',
        'reference',
        'customerAutoID',
        'isAdvancePayment',
        'createdUserGroup',
        'createdPCID',
        'createdUserID',
        'createdUserName',
        'createdDateTime',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedUserName',
        'modifiedDateTime',
        'timestamp',
        'is_sync',
        'id_store',
        'isVerifiedByCashier',
        'transaction_log_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return POSSourceMenuSalesPayment::class;
    }
}
