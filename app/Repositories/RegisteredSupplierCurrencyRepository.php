<?php

namespace App\Repositories;

use App\Models\RegisteredSupplierCurrency;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class RegisteredSupplierCurrencyRepository
 * @package App\Repositories
 * @version November 9, 2020, 2:26 pm +04
 *
 * @method RegisteredSupplierCurrency findWithoutFail($id, $columns = ['*'])
 * @method RegisteredSupplierCurrency find($id, $columns = ['*'])
 * @method RegisteredSupplierCurrency first($columns = ['*'])
*/
class RegisteredSupplierCurrencyRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'registeredSupplierID',
        'currencyID',
        'isAssigned',
        'isDefault'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return RegisteredSupplierCurrency::class;
    }
}
