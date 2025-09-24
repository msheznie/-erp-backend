<?php

namespace App\Repositories;

use App\Models\ExpensesClaimTypeLanguage;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ExpensesClaimTypeLanguageRepository
 * @package App\Repositories
 * @version September 24, 2025, 8:47 am +04
 *
 * @method ExpensesClaimTypeLanguage findWithoutFail($id, $columns = ['*'])
 * @method ExpensesClaimTypeLanguage find($id, $columns = ['*'])
 * @method ExpensesClaimTypeLanguage first($columns = ['*'])
*/
class ExpensesClaimTypeLanguageRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'typeId',
        'languageCode',
        'description'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ExpensesClaimTypeLanguage::class;
    }
}
