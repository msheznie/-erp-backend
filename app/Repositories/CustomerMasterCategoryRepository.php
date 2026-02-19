<?php

namespace App\Repositories;

use App\Models\CustomerMasterCategory;
use App\Repositories\BaseRepository;

/**
 * Class CustomerMasterCategoryRepository
 * @package App\Repositories
 * @version January 20, 2019, 10:14 am +04
 *
 * @method CustomerMasterCategory findWithoutFail($id, $columns = ['*'])
 * @method CustomerMasterCategory find($id, $columns = ['*'])
 * @method CustomerMasterCategory first($columns = ['*'])
*/
class CustomerMasterCategoryRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'categoryDescription',
        'companySystemID',
        'companyID',
        'createdUserGroup',
        'createdPCID',
        'createdUserID',
        'createdDateTime',
        'createdUserName',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedDateTime',
        'modifiedUserName',
        'TIMESTAMP'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CustomerMasterCategory::class;
    }
}
