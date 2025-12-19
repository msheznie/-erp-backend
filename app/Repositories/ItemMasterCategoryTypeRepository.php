<?php

namespace App\Repositories;

use App\Models\ItemMasterCategoryType;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ItemMasterCategoryTypeRepository
 * @package App\Repositories
 * @version August 15, 2024, 9:48 am +04
 *
 * @method ItemMasterCategoryType findWithoutFail($id, $columns = ['*'])
 * @method ItemMasterCategoryType find($id, $columns = ['*'])
 * @method ItemMasterCategoryType first($columns = ['*'])
*/
class ItemMasterCategoryTypeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'itemCodeSystem',
        'categoryTypeID'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ItemMasterCategoryType::class;
    }
}
