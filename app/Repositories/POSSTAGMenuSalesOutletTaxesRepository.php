<?php

namespace App\Repositories;

use App\Models\POSSTAGMenuSalesOutletTaxes;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class POSSTAGMenuSalesOutletTaxesRepository
 * @package App\Repositories
 * @version August 16, 2022, 8:49 am +04
 *
 * @method POSSTAGMenuSalesOutletTaxes findWithoutFail($id, $columns = ['*'])
 * @method POSSTAGMenuSalesOutletTaxes find($id, $columns = ['*'])
 * @method POSSTAGMenuSalesOutletTaxes first($columns = ['*'])
*/
class POSSTAGMenuSalesOutletTaxesRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'wareHouseAutoID',
        'menuSalesID',
        'outletTaxID',
        'taxmasterID',
        'GLCode',
        'taxPercentage',
        'taxAmount',
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
        return POSSTAGMenuSalesOutletTaxes::class;
    }
}
