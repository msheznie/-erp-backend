<?php

namespace App\Repositories;

use App\Models\SupplierBusinessCategoryAssign;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SupplierBusinessCategoryAssignRepository
 * @package App\Repositories
 * @version October 20, 2023, 11:55 am +04
 *
 * @method SupplierBusinessCategoryAssign findWithoutFail($id, $columns = ['*'])
 * @method SupplierBusinessCategoryAssign find($id, $columns = ['*'])
 * @method SupplierBusinessCategoryAssign first($columns = ['*'])
*/
class SupplierBusinessCategoryAssignRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'supplierID',
        'supCategoryMasterID',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SupplierBusinessCategoryAssign::class;
    }
}
