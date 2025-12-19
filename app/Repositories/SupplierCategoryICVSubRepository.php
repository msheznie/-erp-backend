<?php

namespace App\Repositories;

use App\Models\SupplierCategoryICVSub;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SupplierCategoryICVSubRepository
 * @package App\Repositories
 * @version December 3, 2018, 7:26 am UTC
 *
 * @method SupplierCategoryICVSub findWithoutFail($id, $columns = ['*'])
 * @method SupplierCategoryICVSub find($id, $columns = ['*'])
 * @method SupplierCategoryICVSub first($columns = ['*'])
*/
class SupplierCategoryICVSubRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'supCategoryICVMasterID',
        'subCategoryCode',
        'categoryDescription',
        'timeStamp',
        'createdDateTime'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SupplierCategoryICVSub::class;
    }
}
