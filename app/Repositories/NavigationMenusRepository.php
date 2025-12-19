<?php

namespace App\Repositories;

use App\Models\NavigationMenus;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class NavigationMenusRepository
 * @package App\Repositories
 * @version February 13, 2018, 9:00 am UTC
 *
 * @method NavigationMenus findWithoutFail($id, $columns = ['*'])
 * @method NavigationMenus find($id, $columns = ['*'])
 * @method NavigationMenus first($columns = ['*'])
*/
class NavigationMenusRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'description',
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
        return NavigationMenus::class;
    }
}
