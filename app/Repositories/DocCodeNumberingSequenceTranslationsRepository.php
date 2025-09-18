<?php

namespace App\Repositories;

use App\Models\DocCodeNumberingSequenceTranslations;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class DocCodeNumberingSequenceTranslationsRepository
 * @package App\Repositories
 * @version September 17, 2025, 3:02 pm +04
 *
 * @method DocCodeNumberingSequenceTranslations findWithoutFail($id, $columns = ['*'])
 * @method DocCodeNumberingSequenceTranslations find($id, $columns = ['*'])
 * @method DocCodeNumberingSequenceTranslations first($columns = ['*'])
*/
class DocCodeNumberingSequenceTranslationsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'sequenceId',
        'languageCode',
        'description'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return DocCodeNumberingSequenceTranslations::class;
    }
}
