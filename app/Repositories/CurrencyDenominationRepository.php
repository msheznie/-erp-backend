<?php

namespace App\Repositories;

use App\Models\CurrencyDenomination;
use App\Repositories\BaseRepository;

/**
 * Class CurrencyDenominationRepository
 * @package App\Repositories
 * @version January 14, 2019, 11:03 am +04
 *
 * @method CurrencyDenomination findWithoutFail($id, $columns = ['*'])
 * @method CurrencyDenomination find($id, $columns = ['*'])
 * @method CurrencyDenomination first($columns = ['*'])
*/
class CurrencyDenominationRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'currencyID',
        'currencyCode',
        'amount',
        'value',
        'isNote',
        'caption'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CurrencyDenomination::class;
    }
}
