<?php

namespace App\Repositories;

use App\Models\LogisticStatusTranslations;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class LogisticStatusTranslationsRepository
 * @package App\Repositories
 * @version September 19, 2025, 12:15 pm +04
 *
 * @method LogisticStatusTranslations findWithoutFail($id, $columns = ['*'])
 * @method LogisticStatusTranslations find($id, $columns = ['*'])
 * @method LogisticStatusTranslations first($columns = ['*'])
*/
class LogisticStatusTranslationsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'StatusID',
        'languageCode',
        'description'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return LogisticStatusTranslations::class;
    }
}
