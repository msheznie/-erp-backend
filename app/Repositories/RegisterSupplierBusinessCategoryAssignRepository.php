<?php

namespace App\Repositories;

use App\Models\RegisterSupplierBusinessCategoryAssign;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class RegisterSupplierBusinessCategoryAssignRepository
 * @package App\Repositories
 * @version December 19, 2023, 4:22 pm +04
 *
 * @method RegisterSupplierBusinessCategoryAssign findWithoutFail($id, $columns = ['*'])
 * @method RegisterSupplierBusinessCategoryAssign find($id, $columns = ['*'])
 * @method RegisterSupplierBusinessCategoryAssign first($columns = ['*'])
*/
class RegisterSupplierBusinessCategoryAssignRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'supCategoryMasterID',
        'supplierID'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return RegisterSupplierBusinessCategoryAssign::class;
    }
}
