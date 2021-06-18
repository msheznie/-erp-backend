<?php

namespace App\Repositories;

use App\Models\CustomerMasterCategoryAssigned;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class CustomerMasterCategoryAssignedRepository
 * @package App\Repositories
 * @version May 7, 2021, 9:46 am +04
 *
 * @method CustomerMasterCategoryAssigned findWithoutFail($id, $columns = ['*'])
 * @method CustomerMasterCategoryAssigned find($id, $columns = ['*'])
 * @method CustomerMasterCategoryAssigned first($columns = ['*'])
*/
class CustomerMasterCategoryAssignedRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'customerMasterCategoryID',
        'companySystemID',
        'categoryDescription',
        'createdUserID',
        'createdDateTime',
        'isAssigned',
        'isActive'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CustomerMasterCategoryAssigned::class;
    }
}
