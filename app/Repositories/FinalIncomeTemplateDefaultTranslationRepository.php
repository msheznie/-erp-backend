<?php

namespace App\Repositories;

use App\Models\FinalIncomeTemplateDefaultTranslation;
use App\Repositories\BaseRepository;

/**
 * Class FinalIncomeTemplateDefaultTranslationRepository
 * @package App\Repositories
 * @version September 24, 2025, 8:45 pm +04
 *
 * @method FinalIncomeTemplateDefaultTranslation findWithoutFail($id, $columns = ['*'])
 * @method FinalIncomeTemplateDefaultTranslation find($id, $columns = ['*'])
 * @method FinalIncomeTemplateDefaultTranslation first($columns = ['*'])
*/
class FinalIncomeTemplateDefaultTranslationRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'defaultId',
        'languageCode',
        'description'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return FinalIncomeTemplateDefaultTranslation::class;
    }
}
