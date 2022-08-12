<?php

namespace App\Repositories;

use App\Models\PosSourceMenuCategory;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class PosSourceMenuCategoryRepository
 * @package App\Repositories
 * @version July 27, 2022, 12:24 pm +04
 *
 * @method PosSourceMenuCategory findWithoutFail($id, $columns = ['*'])
 * @method PosSourceMenuCategory find($id, $columns = ['*'])
 * @method PosSourceMenuCategory first($columns = ['*'])
*/
class PosSourceMenuCategoryRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'menuCategoryDescription',
        'image',
        'revenueGLAutoID',
        'topSalesRptYN',
        'companyID',
        'sortOrder',
        'isPack',
        'masterLevelID',
        'levelNo',
        'bgColor',
        'isActive',
        'showImageYN',
        'isDeleted',
        'deletedBy',
        'deletedDatetime',
        'createdPCID',
        'createdUserID',
        'createdDateTime',
        'createdUserName',
        'createdUserGroup',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedDateTime',
        'modifiedUserName',
        'timeStamp',
        'transaction_log_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PosSourceMenuCategory::class;
    }
}
