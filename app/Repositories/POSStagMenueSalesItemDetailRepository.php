<?php

namespace App\Repositories;

use App\Models\POSStagMenueSalesItemDetail;
use App\Repositories\BaseRepository;

/**
 * Class POSStagMenueSalesItemDetailRepository
 * @package App\Repositories
 * @version July 27, 2022, 8:24 am +04
 *
 * @method POSStagMenueSalesItemDetail findWithoutFail($id, $columns = ['*'])
 * @method POSStagMenueSalesItemDetail find($id, $columns = ['*'])
 * @method POSStagMenueSalesItemDetail first($columns = ['*'])
*/
class POSStagMenueSalesItemDetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'warehouseAutoID',
        'menuSalesItemID',
        'menuSalesID',
        'itemAutoID',
        'qty',
        'UOM',
        'UOMID',
        'cost',
        'actualInventoryCost',
        'menuID',
        'menuSalesQty',
        'costGLAutoID',
        'assetGLAutoID',
        'isWastage',
        'companyID',
        'companyCode',
        'segmentID',
        'segmentCode',
        'createdPCID',
        'createdUserID',
        'createdDateTime',
        'createdUserName',
        'createdUserGroup',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedDateTime',
        'modifiedUserName',
        'timeStamp',
        'is_sync',
        'id_store',
        'transaction_log_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return POSStagMenueSalesItemDetail::class;
    }
}
