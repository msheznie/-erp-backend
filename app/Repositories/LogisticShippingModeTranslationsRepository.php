<?php

namespace App\Repositories;

use App\Models\LogisticShippingModeTranslations;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class LogisticShippingModeTranslationsRepository
 * @package App\Repositories
 * @version September 19, 2025, 12:11 pm +04
 *
 * @method LogisticShippingModeTranslations findWithoutFail($id, $columns = ['*'])
 * @method LogisticShippingModeTranslations find($id, $columns = ['*'])
 * @method LogisticShippingModeTranslations first($columns = ['*'])
*/
class LogisticShippingModeTranslationsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'logisticShippingModeID',
        'languageCode',
        'description'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return LogisticShippingModeTranslations::class;
    }
}
