<?php

namespace App\Repositories;

use App\Models\CurrencyConversionDetail;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class CurrencyConversionDetailRepository
 * @package App\Repositories
 * @version June 8, 2021, 2:44 pm +04
 *
 * @method CurrencyConversionDetail findWithoutFail($id, $columns = ['*'])
 * @method CurrencyConversionDetail find($id, $columns = ['*'])
 * @method CurrencyConversionDetail first($columns = ['*'])
*/
class CurrencyConversionDetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'currencyConversioMasterID',
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
        return CurrencyConversionDetail::class;
    }
}
