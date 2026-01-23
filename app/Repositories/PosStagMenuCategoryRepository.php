<?php

namespace App\Repositories;

use App\Models\PosStagMenuCategory;
use App\Repositories\BaseRepository;

/**
 * Class PosStagMenuCategoryRepository
 * @package App\Repositories
 * @version July 27, 2022, 12:23 pm +04
 *
 * @method PosStagMenuCategory findWithoutFail($id, $columns = ['*'])
 * @method PosStagMenuCategory find($id, $columns = ['*'])
 * @method PosStagMenuCategory first($columns = ['*'])
*/
class PosStagMenuCategoryRepository extends BaseRepository
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
        return PosStagMenuCategory::class;
    }
}
