<?php

namespace App\Repositories;

use App\Models\CustomerCurrency;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class CustomerCurrencyRepository
 * @package App\Repositories
 * @version March 21, 2018, 4:46 am UTC
 *
 * @method CustomerCurrency findWithoutFail($id, $columns = ['*'])
 * @method CustomerCurrency find($id, $columns = ['*'])
 * @method CustomerCurrency first($columns = ['*'])
*/
class CustomerCurrencyRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'customerCodeSystem',
        'customerCode',
        'currencyID',
        'isDefault',
        'isAssigned',
        'createdBy',
        'createdDateTime',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CustomerCurrency::class;
    }
}
