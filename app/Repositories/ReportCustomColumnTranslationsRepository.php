<?php

namespace App\Repositories;

use App\Models\ReportCustomColumnTranslations;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ReportCustomColumnTranslationsRepository
 * @package App\Repositories
 * @version September 15, 2025, 6:19 pm +04
 *
 * @method ReportCustomColumnTranslations findWithoutFail($id, $columns = ['*'])
 * @method ReportCustomColumnTranslations find($id, $columns = ['*'])
 * @method ReportCustomColumnTranslations first($columns = ['*'])
*/
class ReportCustomColumnTranslationsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'documentSystemID',
        'languageCode',
        'description'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ReportCustomColumnTranslations::class;
    }
}
