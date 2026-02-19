<?php

namespace App\Repositories;

use App\Models\PurchaseOrderStatus;
use App\Repositories\BaseRepository;

/**
 * Class PurchaseOrderStatusRepository
 * @package App\Repositories
 * @version May 30, 2018, 8:57 am UTC
 *
 * @method PurchaseOrderStatus findWithoutFail($id, $columns = ['*'])
 * @method PurchaseOrderStatus find($id, $columns = ['*'])
 * @method PurchaseOrderStatus first($columns = ['*'])
*/
class PurchaseOrderStatusRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'purchaseOrderID',
        'purchaseOrderCode',
        'POCategoryID',
        'comments',
        'updatedByEmpSystemID',
        'updatedByEmpID',
        'updatedByEmpName',
        'updatedDate',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PurchaseOrderStatus::class;
    }
}
