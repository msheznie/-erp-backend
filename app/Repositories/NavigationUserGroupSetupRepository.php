<?php

namespace App\Repositories;

use App\Models\NavigationUserGroupSetup;
use App\Repositories\BaseRepository;

/**
 * Class NavigationUserGroupSetupRepository
 * @package App\Repositories
 * @version February 13, 2018, 9:01 am UTC
 *
 * @method NavigationUserGroupSetup findWithoutFail($id, $columns = ['*'])
 * @method NavigationUserGroupSetup find($id, $columns = ['*'])
 * @method NavigationUserGroupSetup first($columns = ['*'])
*/
class NavigationUserGroupSetupRepository extends BaseRepository
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
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return NavigationUserGroupSetup::class;
    }
}
