<?php

namespace App\Repositories;

use App\Models\ReportTemplateColumnsTranslations;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ReportTemplateColumnsTranslationsRepository
 * @package App\Repositories
 * @version September 17, 2025, 3:17 pm +04
 *
 * @method ReportTemplateColumnsTranslations findWithoutFail($id, $columns = ['*'])
 * @method ReportTemplateColumnsTranslations find($id, $columns = ['*'])
 * @method ReportTemplateColumnsTranslations first($columns = ['*'])
*/
class ReportTemplateColumnsTranslationsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'columnID',
        'languageCode',
        'description'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ReportTemplateColumnsTranslations::class;
    }
}
