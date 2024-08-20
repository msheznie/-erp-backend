<?php

namespace App\Repositories;

use App\Models\ItemCategoryTypeMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ItemCategoryTypeMasterRepository
 * @package App\Repositories
 * @version August 15, 2024, 9:12 am +04
 *
 * @method ItemCategoryTypeMaster findWithoutFail($id, $columns = ['*'])
 * @method ItemCategoryTypeMaster find($id, $columns = ['*'])
 * @method ItemCategoryTypeMaster first($columns = ['*'])
*/
class ItemCategoryTypeMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ItemCategoryTypeMaster::class;
    }
}
