<?php

namespace App\Repositories;

use App\Models\SupplierSubCategoryAssign;
use App\Repositories\BaseRepository;

/**
 * Class SupplierSubCategoryAssignRepository
 * @package App\Repositories
 * @version February 28, 2018, 8:49 am UTC
 *
 * @method SupplierSubCategoryAssign findWithoutFail($id, $columns = ['*'])
 * @method SupplierSubCategoryAssign find($id, $columns = ['*'])
 * @method SupplierSubCategoryAssign first($columns = ['*'])
*/
class SupplierSubCategoryAssignRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'supplierID',
        'supSubCategoryID',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SupplierSubCategoryAssign::class;
    }
}
