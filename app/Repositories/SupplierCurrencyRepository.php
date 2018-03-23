<?php

namespace App\Repositories;

use App\Models\SupplierCurrency;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SupplierCurrencyRepository
 * @package App\Repositories
 * @version March 2, 2018, 6:24 am UTC
 *
 * @method SupplierCurrency findWithoutFail($id, $columns = ['*'])
 * @method SupplierCurrency find($id, $columns = ['*'])
 * @method SupplierCurrency first($columns = ['*'])
*/
class SupplierCurrencyRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'supplierCodeSystem',
        'currencyID',
        'bankMemo',
        'timestamp',
        'isAssigned',
        'isDefault'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SupplierCurrency::class;
    }
}
