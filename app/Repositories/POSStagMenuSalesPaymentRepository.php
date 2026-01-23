<?php

namespace App\Repositories;

use App\Models\POSStagMenuSalesPayment;
use App\Repositories\BaseRepository;

/**
 * Class POSStagMenuSalesPaymentRepository
 * @package App\Repositories
 * @version July 27, 2022, 8:26 am +04
 *
 * @method POSStagMenuSalesPayment findWithoutFail($id, $columns = ['*'])
 * @method POSStagMenuSalesPayment find($id, $columns = ['*'])
 * @method POSStagMenuSalesPayment first($columns = ['*'])
*/
class POSStagMenuSalesPaymentRepository extends BaseRepository
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
        return POSStagMenuSalesPayment::class;
    }
}
