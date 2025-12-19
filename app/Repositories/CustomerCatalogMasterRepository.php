<?php

namespace App\Repositories;

use App\Models\CustomerCatalogMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class CustomerCatalogMasterRepository
 * @package App\Repositories
 * @version April 8, 2020, 12:37 pm +04
 *
 * @method CustomerCatalogMaster findWithoutFail($id, $columns = ['*'])
 * @method CustomerCatalogMaster find($id, $columns = ['*'])
 * @method CustomerCatalogMaster first($columns = ['*'])
*/
class CustomerCatalogMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'catalogID',
        'catalogName',
        'fromDate',
        'toDate',
        'customerID',
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
        return CustomerCatalogMaster::class;
    }
}
