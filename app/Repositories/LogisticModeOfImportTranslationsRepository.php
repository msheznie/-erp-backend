<?php

namespace App\Repositories;

use App\Models\LogisticModeOfImportTranslations;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class LogisticModeOfImportTranslationsRepository
 * @package App\Repositories
 * @version September 19, 2025, 12:09 pm +04
 *
 * @method LogisticModeOfImportTranslations findWithoutFail($id, $columns = ['*'])
 * @method LogisticModeOfImportTranslations find($id, $columns = ['*'])
 * @method LogisticModeOfImportTranslations first($columns = ['*'])
*/
class LogisticModeOfImportTranslationsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'modeOfImportID',
        'languageCode',
        'description'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return LogisticModeOfImportTranslations::class;
    }
}
