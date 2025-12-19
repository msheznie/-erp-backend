<?php

namespace App\Repositories;

use App\Models\UserGroupAssign;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class UserGroupAssignRepository
 * @package App\Repositories
 * @version March 20, 2018, 4:57 am UTC
 *
 * @method UserGroupAssign findWithoutFail($id, $columns = ['*'])
 * @method UserGroupAssign find($id, $columns = ['*'])
 * @method UserGroupAssign first($columns = ['*'])
*/
class UserGroupAssignRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'userGroupID',
        'companyID',
        'navigationMenuID',
        'description',
        'masterID',
        'url',
        'pageID',
        'pageTitle',
        'pageIcon',
        'levelNo',
        'sortOrder',
        'isSubExist',
        'readonly',
        'create',
        'update',
        'delete',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return UserGroupAssign::class;
    }
}
