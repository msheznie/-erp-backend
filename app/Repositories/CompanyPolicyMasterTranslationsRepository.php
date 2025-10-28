<?php

namespace App\Repositories;

use App\Models\CompanyPolicyMasterTranslations;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class CompanyPolicyMasterTranslationsRepository
 * @package App\Repositories
 * @version September 17, 2025, 3:34 pm +04
 *
 * @method CompanyPolicyMasterTranslations findWithoutFail($id, $columns = ['*'])
 * @method CompanyPolicyMasterTranslations find($id, $columns = ['*'])
 * @method CompanyPolicyMasterTranslations first($columns = ['*'])
*/
class CompanyPolicyMasterTranslationsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companypolicymasterID',
        'languageCode',
        'description'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CompanyPolicyMasterTranslations::class;
    }
}
