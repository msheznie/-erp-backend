<?php

namespace App\Repositories;

use App\Models\POSSourceMenueSalesItemDetail;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class POSSourceMenueSalesItemDetailRepository
 * @package App\Repositories
 * @version July 27, 2022, 8:25 am +04
 *
 * @method POSSourceMenueSalesItemDetail findWithoutFail($id, $columns = ['*'])
 * @method POSSourceMenueSalesItemDetail find($id, $columns = ['*'])
 * @method POSSourceMenueSalesItemDetail first($columns = ['*'])
*/
class POSSourceMenueSalesItemDetailRepository extends BaseRepository
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
        return POSSourceMenueSalesItemDetail::class;
    }
}
