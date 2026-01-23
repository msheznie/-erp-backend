<?php

namespace App\Repositories;

use App\Models\FixedAssetCategorySub;
use App\Repositories\BaseRepository;

/**
 * Class FixedAssetCategorySubRepository
 * @package App\Repositories
 * @version October 7, 2018, 8:57 am UTC
 *
 * @method FixedAssetCategorySub findWithoutFail($id, $columns = ['*'])
 * @method FixedAssetCategorySub find($id, $columns = ['*'])
 * @method FixedAssetCategorySub first($columns = ['*'])
*/
class FixedAssetCategorySubRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companySystemID',
        'companyID',
        'catDescription',
        'faCatID',
        'mainCatDescription',
        'suCatLevel',
        'isActive',
        'createdPcID',
        'createdUserGroup',
        'createdUserID',
        'createdDateTime',
        'modifiedPc',
        'modifiedUser',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return FixedAssetCategorySub::class;
    }
}
