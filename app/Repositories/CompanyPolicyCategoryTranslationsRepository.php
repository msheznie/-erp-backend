<?php

namespace App\Repositories;

use App\Models\CompanyPolicyCategoryTranslations;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class CompanyPolicyCategoryTranslationsRepository
 * @package App\Repositories
 * @version September 17, 2025, 4:47 pm +04
 *
 * @method CompanyPolicyCategoryTranslations findWithoutFail($id, $columns = ['*'])
 * @method CompanyPolicyCategoryTranslations find($id, $columns = ['*'])
 * @method CompanyPolicyCategoryTranslations first($columns = ['*'])
*/
class CompanyPolicyCategoryTranslationsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companyPolicyCategoryID',
        'languageCode',
        'description',
        'comment'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CompanyPolicyCategoryTranslations::class;
    }
}
