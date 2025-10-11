<?php

namespace App\Repositories;

use App\Models\DocumentCodeModuleTranslations;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class DocumentCodeModuleTranslationsRepository
 * @package App\Repositories
 * @version September 17, 2025, 2:38 pm +04
 *
 * @method DocumentCodeModuleTranslations findWithoutFail($id, $columns = ['*'])
 * @method DocumentCodeModuleTranslations find($id, $columns = ['*'])
 * @method DocumentCodeModuleTranslations first($columns = ['*'])
*/
class DocumentCodeModuleTranslationsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'documentId',
        'languageCode',
        'description'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return DocumentCodeModuleTranslations::class;
    }
}
