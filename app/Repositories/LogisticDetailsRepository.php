<?php

namespace App\Repositories;

use App\Models\LogisticDetails;
use App\Repositories\BaseRepository;

/**
 * Class LogisticDetailsRepository
 * @package App\Repositories
 * @version September 12, 2018, 5:06 am UTC
 *
 * @method LogisticDetails findWithoutFail($id, $columns = ['*'])
 * @method LogisticDetails find($id, $columns = ['*'])
 * @method LogisticDetails first($columns = ['*'])
*/
class LogisticDetailsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'logisticMasterID',
        'companySystemID',
        'companyID',
        'supplierID',
        'POid',
        'POdetailID',
        'itemcodeSystem',
        'itemPrimaryCode',
        'itemDescription',
        'partNo',
        'itemUOM',
        'itemPOQtry',
        'itemShippingQty',
        'POdeliveryWarehousLocation',
        'GRVStatus',
        'GRVsystemCode',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return LogisticDetails::class;
    }
}
