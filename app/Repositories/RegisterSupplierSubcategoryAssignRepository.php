<?php

namespace App\Repositories;

use App\Models\RegisterSupplierSubcategoryAssign;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class RegisterSupplierSubcategoryAssignRepository
 * @package App\Repositories
 * @version December 19, 2023, 4:24 pm +04
 *
 * @method RegisterSupplierSubcategoryAssign findWithoutFail($id, $columns = ['*'])
 * @method RegisterSupplierSubcategoryAssign find($id, $columns = ['*'])
 * @method RegisterSupplierSubcategoryAssign first($columns = ['*'])
*/
class RegisterSupplierSubcategoryAssignRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'supplierID',
        'supSubCategoryID'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return RegisterSupplierSubcategoryAssign::class;
    }
}
