<?php

namespace App\Repositories;

use App\Models\ItemCategoryTypeMasterTranslation;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ItemCategoryTypeMasterTranslationRepository
 * @package App\Repositories
 * @version September 10, 2025, 12:23 pm +04
 *
 * @method ItemCategoryTypeMasterTranslation findWithoutFail($id, $columns = ['*'])
 * @method ItemCategoryTypeMasterTranslation find($id, $columns = ['*'])
 * @method ItemCategoryTypeMasterTranslation first($columns = ['*'])
*/
class ItemCategoryTypeMasterTranslationRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'typeId',
        'languageCode',
        'name'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ItemCategoryTypeMasterTranslation::class;
    }
}
