<?php

namespace App\Repositories;

use App\Models\VatReturnFillingCategoryLanguage;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class VatReturnFillingCategoryLanguageRepository
 * @package App\Repositories
 * @version September 12, 2025, 6:53 am +04
 *
 * @method VatReturnFillingCategoryLanguage findWithoutFail($id, $columns = ['*'])
 * @method VatReturnFillingCategoryLanguage find($id, $columns = ['*'])
 * @method VatReturnFillingCategoryLanguage first($columns = ['*'])
*/
class VatReturnFillingCategoryLanguageRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'returnFillingCategoryID',
        'languageCode',
        'description'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return VatReturnFillingCategoryLanguage::class;
    }
}
