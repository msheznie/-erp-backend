<?php

namespace App\Repositories;

use App\Models\POSSOURCEMenuSalesOutletTaxes;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class POSSOURCEMenuSalesOutletTaxesRepository
 * @package App\Repositories
 * @version August 16, 2022, 8:49 am +04
 *
 * @method POSSOURCEMenuSalesOutletTaxes findWithoutFail($id, $columns = ['*'])
 * @method POSSOURCEMenuSalesOutletTaxes find($id, $columns = ['*'])
 * @method POSSOURCEMenuSalesOutletTaxes first($columns = ['*'])
*/
class POSSOURCEMenuSalesOutletTaxesRepository extends BaseRepository
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
        return POSSOURCEMenuSalesOutletTaxes::class;
    }
}
