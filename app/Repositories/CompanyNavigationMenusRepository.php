<?php

namespace App\Repositories;

use App\Models\CompanyNavigationMenus;
use App\Repositories\BaseRepository;

/**
 * Class CompanyNavigationMenusRepository
 * @package App\Repositories
 * @version March 15, 2018, 7:59 am UTC
 *
 * @method CompanyNavigationMenus findWithoutFail($id, $columns = ['*'])
 * @method CompanyNavigationMenus find($id, $columns = ['*'])
 * @method CompanyNavigationMenus first($columns = ['*'])
*/
class CompanyNavigationMenusRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'description',
        'companyID',
        'masterID',
        'languageID',
        'url',
        'pageID',
        'pageTitle',
        'pageIcon',
        'levelNo',
        'sortOrder',
        'isSubExist',
        'timestamp',
        'isAddon',
        'addonDescription',
        'addonDetails',
        'isCoreModule',
        'isGroup'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CompanyNavigationMenus::class;
    }
}
