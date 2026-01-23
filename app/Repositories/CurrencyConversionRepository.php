<?php

namespace App\Repositories;

use App\Models\CurrencyConversion;
use App\Repositories\BaseRepository;

/**
 * Class CurrencyConversionRepository
 * @package App\Repositories
 * @version March 30, 2018, 9:09 am UTC
 *
 * @method CurrencyConversion findWithoutFail($id, $columns = ['*'])
 * @method CurrencyConversion find($id, $columns = ['*'])
 * @method CurrencyConversion first($columns = ['*'])
*/
class CurrencyConversionRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'masterCurrencyID',
        'subCurrencyID',
        'conversion',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CurrencyConversion::class;
    }
}
