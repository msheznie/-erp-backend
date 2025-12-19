<?php

namespace App\Repositories;

use App\Models\SupplierCategoryMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SupplierCategoryMasterRepository
 * @package App\Repositories
 * @version February 27, 2018, 1:02 pm UTC
 *
 * @method SupplierCategoryMaster findWithoutFail($id, $columns = ['*'])
 * @method SupplierCategoryMaster find($id, $columns = ['*'])
 * @method SupplierCategoryMaster first($columns = ['*'])
*/
class SupplierCategoryMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'categoryCode',
        'categoryDescription',
        'createdUserGroup',
        'createdPcID',
        'createdUserID',
        'modifiedPc',
        'modifiedUser',
        'createdDateTime',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SupplierCategoryMaster::class;
    }
}
