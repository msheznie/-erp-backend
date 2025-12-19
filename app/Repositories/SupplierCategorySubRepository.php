<?php

namespace App\Repositories;

use App\Models\SupplierCategorySub;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SupplierCategorySubRepository
 * @package App\Repositories
 * @version February 27, 2018, 10:49 am UTC
 *
 * @method SupplierCategorySub findWithoutFail($id, $columns = ['*'])
 * @method SupplierCategorySub find($id, $columns = ['*'])
 * @method SupplierCategorySub first($columns = ['*'])
*/
class SupplierCategorySubRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'supMasterCategoryID',
        'subCategoryCode',
        'categoryDescription',
        'timeStamp',
        'createdUserGroup',
        'createdPcID',
        'createdUserID',
        'modifiedPc',
        'modifiedUser',
        'createdDateTime'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SupplierCategorySub::class;
    }
}
