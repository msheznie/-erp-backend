<?php

namespace App\Repositories;

use App\Models\DocumentEmailNotificationMasterTranslations;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class DocumentEmailNotificationMasterTranslationsRepository
 * @package App\Repositories
 * @version September 17, 2025, 5:18 pm +04
 *
 * @method DocumentEmailNotificationMasterTranslations findWithoutFail($id, $columns = ['*'])
 * @method DocumentEmailNotificationMasterTranslations find($id, $columns = ['*'])
 * @method DocumentEmailNotificationMasterTranslations first($columns = ['*'])
*/
class DocumentEmailNotificationMasterTranslationsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'emailNotificationID',
        'languageCode',
        'description'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return DocumentEmailNotificationMasterTranslations::class;
    }
}
