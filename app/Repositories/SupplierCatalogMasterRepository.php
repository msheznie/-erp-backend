<?php

namespace App\Repositories;

use App\Models\SupplierCatalogMaster;
use App\Repositories\BaseRepository;

/**
 * Class SupplierCatalogMasterRepository
 * @package App\Repositories
 * @version April 1, 2020, 3:44 pm +04
 *
 * @method SupplierCatalogMaster findWithoutFail($id, $columns = ['*'])
 * @method SupplierCatalogMaster find($id, $columns = ['*'])
 * @method SupplierCatalogMaster first($columns = ['*'])
*/
class SupplierCatalogMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'catalogID',
        'catalogName',
        'fromDate',
        'toDate',
        'supplierID',
        'companySystemID',
        'documentSystemID',
        'createdBy',
        'createdDate',
        'modifiedBy',
        'modifiedDate',
        'isDelete',
        'isActive'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SupplierCatalogMaster::class;
    }
}
