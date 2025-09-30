<?php

namespace App\Repositories;

use App\Models\ExchangeSetupDocumentTypeTranslations;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ExchangeSetupDocumentTypeTranslationsRepository
 * @package App\Repositories
 * @version September 17, 2025, 2:21 pm +04
 *
 * @method ExchangeSetupDocumentTypeTranslations findWithoutFail($id, $columns = ['*'])
 * @method ExchangeSetupDocumentTypeTranslations find($id, $columns = ['*'])
 * @method ExchangeSetupDocumentTypeTranslations first($columns = ['*'])
*/
class ExchangeSetupDocumentTypeTranslationsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'slug',
        'languageCode',
        'description'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ExchangeSetupDocumentTypeTranslations::class;
    }
}
