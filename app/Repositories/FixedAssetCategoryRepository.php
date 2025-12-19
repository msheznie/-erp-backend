<?php

namespace App\Repositories;

use App\Models\FixedAssetCategory;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class FixedAssetCategoryRepository
 * @package App\Repositories
 * @version September 26, 2018, 5:17 am UTC
 *
 * @method FixedAssetCategory findWithoutFail($id, $columns = ['*'])
 * @method FixedAssetCategory find($id, $columns = ['*'])
 * @method FixedAssetCategory first($columns = ['*'])
*/
class FixedAssetCategoryRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companySystemID',
        'companyID',
        'catDescription',
        'isActive',
        'createdPcID',
        'createdUserGroup',
        'createdUserSystemID',
        'createdUserID',
        'createdDateTime',
        'modifiedPc',
        'modifiedUserSystemID',
        'modifiedUser',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return FixedAssetCategory::class;
    }
}
