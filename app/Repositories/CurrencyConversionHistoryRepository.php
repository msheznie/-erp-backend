<?php

namespace App\Repositories;

use App\Models\CurrencyConversionHistory;
use App\Repositories\BaseRepository;

/**
 * Class CurrencyConversionHistoryRepository
 * @package App\Repositories
 * @version August 22, 2019, 10:35 am +04
 *
 * @method CurrencyConversionHistory findWithoutFail($id, $columns = ['*'])
 * @method CurrencyConversionHistory find($id, $columns = ['*'])
 * @method CurrencyConversionHistory first($columns = ['*'])
*/
class CurrencyConversionHistoryRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'serialNo',
        'masterCurrencyID',
        'subCurrencyID',
        'conversion',
        'createdBy',
        'createdUserID',
        'createdpc',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CurrencyConversionHistory::class;
    }
}
