<?php

namespace App\Repositories;

use App\Models\SupplierCategoryICVMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SupplierCategoryICVMasterRepository
 * @package App\Repositories
 * @version December 3, 2018, 7:28 am UTC
 *
 * @method SupplierCategoryICVMaster findWithoutFail($id, $columns = ['*'])
 * @method SupplierCategoryICVMaster find($id, $columns = ['*'])
 * @method SupplierCategoryICVMaster first($columns = ['*'])
*/
class SupplierCategoryICVMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'categoryCode',
        'categoryDescription',
        'createdDateTime',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SupplierCategoryICVMaster::class;
    }
}
