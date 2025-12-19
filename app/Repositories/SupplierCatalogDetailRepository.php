<?php

namespace App\Repositories;

use App\Models\SupplierCatalogDetail;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SupplierCatalogDetailRepository
 * @package App\Repositories
 * @version April 1, 2020, 3:59 pm +04
 *
 * @method SupplierCatalogDetail findWithoutFail($id, $columns = ['*'])
 * @method SupplierCatalogDetail find($id, $columns = ['*'])
 * @method SupplierCatalogDetail first($columns = ['*'])
*/
class SupplierCatalogDetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'supplierCatalogMasterID',
        'itemCodeSystem',
        'itemPrimaryCode',
        'itemDescription',
        'itemUnitOfMeasure',
        'partNo',
        'localCurrencyID',
        'localPrice',
        'reportingCurrencyID',
        'reportingPrice',
        'leadTime',
        'timstamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SupplierCatalogDetail::class;
    }
}
