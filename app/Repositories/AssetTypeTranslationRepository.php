<?php

namespace App\Repositories;

use App\Models\AssetTypeTranslation;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class AssetTypeTranslationRepository
 * @package App\Repositories
 * @version September 14, 2025, 9:36 pm +04
 *
 * @method AssetTypeTranslation findWithoutFail($id, $columns = ['*'])
 * @method AssetTypeTranslation find($id, $columns = ['*'])
 * @method AssetTypeTranslation first($columns = ['*'])
*/
class AssetTypeTranslationRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'typeID',
        'languageCode',
        'data'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return AssetTypeTranslation::class;
    }
}
