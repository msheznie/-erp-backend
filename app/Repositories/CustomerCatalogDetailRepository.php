<?php

namespace App\Repositories;

use App\Models\CustomerCatalogDetail;
use App\Repositories\BaseRepository;

/**
 * Class CustomerCatalogDetailRepository
 * @package App\Repositories
 * @version April 8, 2020, 1:05 pm +04
 *
 * @method CustomerCatalogDetail findWithoutFail($id, $columns = ['*'])
 * @method CustomerCatalogDetail find($id, $columns = ['*'])
 * @method CustomerCatalogDetail first($columns = ['*'])
*/
class CustomerCatalogDetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'customerCatalogMasterID',
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
        'isDelete',
        'timstamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CustomerCatalogDetail::class;
    }
}
