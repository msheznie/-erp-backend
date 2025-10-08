<?php

namespace App\Repositories;

use App\Models\GrvTypeLanguage;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class GrvTypeLanguageRepository
 * @package App\Repositories
 * @version September 5, 2025, 9:28 pm +04
 *
 * @method GrvTypeLanguage findWithoutFail($id, $columns = ['*'])
 * @method GrvTypeLanguage find($id, $columns = ['*'])
 * @method GrvTypeLanguage first($columns = ['*'])
*/
class GrvTypeLanguageRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'grvTypeID',
        'languageCode',
        'des'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return GrvTypeLanguage::class;
    }
}
