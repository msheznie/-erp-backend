<?php

namespace App\Repositories;

use App\Models\BankMemoSupplier;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class BankMemoSupplierRepository
 * @package App\Repositories
 * @version March 8, 2018, 4:56 am UTC
 *
 * @method BankMemoSupplier findWithoutFail($id, $columns = ['*'])
 * @method BankMemoSupplier find($id, $columns = ['*'])
 * @method BankMemoSupplier first($columns = ['*'])
*/
class BankMemoSupplierRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'memoHeader',
        'memoDetail',
        'supplierCodeSystem',
        'supplierCurrencyID',
        'updatedByUserID',
        'updatedByUserName',
        'updatedDate',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return BankMemoSupplier::class;
    }
}
