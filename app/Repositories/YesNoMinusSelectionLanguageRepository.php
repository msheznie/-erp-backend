<?php

namespace App\Repositories;

use App\Models\YesNoMinusSelectionLanguage;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class YesNoMinusSelectionLanguageRepository
 * @package App\Repositories
 * @version September 5, 2025, 9:06 pm +04
 *
 * @method YesNoMinusSelectionLanguage findWithoutFail($id, $columns = ['*'])
 * @method YesNoMinusSelectionLanguage find($id, $columns = ['*'])
 * @method YesNoMinusSelectionLanguage first($columns = ['*'])
*/
class YesNoMinusSelectionLanguageRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'yesNoSelectionID',
        'languageCode',
        'YesNo'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return YesNoMinusSelectionLanguage::class;
    }
}
