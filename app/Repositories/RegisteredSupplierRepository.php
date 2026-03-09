<?php

namespace App\Repositories;

use App\Models\RegisteredSupplier;
use App\Repositories\BaseRepository;

/**
 * Class RegisteredSupplierRepository
 * @package App\Repositories
 * @version November 9, 2020, 2:00 pm +04
 *
 * @method RegisteredSupplier findWithoutFail($id, $columns = ['*'])
 * @method RegisteredSupplier find($id, $columns = ['*'])
 * @method RegisteredSupplier first($columns = ['*'])
*/
class RegisteredSupplierRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'supplierName',
        'telephone',
        'supEmail',
        'supplierCountryID',
        'registrationExprity',
        'currency',
        'nameOnPaymentCheque',
        'address',
        'fax',
        'webAddress',
        'registrationNumber',
        'createdDate'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return RegisteredSupplier::class;
    }
}
