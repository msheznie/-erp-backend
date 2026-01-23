<?php

namespace App\Repositories;

use App\Models\RegisteredBankMemoSupplier;
use App\Repositories\BaseRepository;

/**
 * Class RegisteredBankMemoSupplierRepository
 * @package App\Repositories
 * @version November 9, 2020, 2:27 pm +04
 *
 * @method RegisteredBankMemoSupplier findWithoutFail($id, $columns = ['*'])
 * @method RegisteredBankMemoSupplier find($id, $columns = ['*'])
 * @method RegisteredBankMemoSupplier first($columns = ['*'])
*/
class RegisteredBankMemoSupplierRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'memoHeader',
        'memoDetail',
        'registeredSupplierID',
        'supplierCurrencyID',
        'bankMemoTypeID'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return RegisteredBankMemoSupplier::class;
    }
}
