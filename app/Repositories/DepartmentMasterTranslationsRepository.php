<?php

namespace App\Repositories;

use App\Models\DepartmentMasterTranslations;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class DepartmentMasterTranslationsRepository
 * @package App\Repositories
 * @version September 17, 2025, 1:26 pm +04
 *
 * @method DepartmentMasterTranslations findWithoutFail($id, $columns = ['*'])
 * @method DepartmentMasterTranslations find($id, $columns = ['*'])
 * @method DepartmentMasterTranslations first($columns = ['*'])
*/
class DepartmentMasterTranslationsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'DepartmentID',
        'languageCode',
        'description'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return DepartmentMasterTranslations::class;
    }
}
