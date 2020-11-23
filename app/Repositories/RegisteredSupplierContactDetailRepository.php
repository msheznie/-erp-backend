<?php

namespace App\Repositories;

use App\Models\RegisteredSupplierContactDetail;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class RegisteredSupplierContactDetailRepository
 * @package App\Repositories
 * @version November 9, 2020, 2:47 pm +04
 *
 * @method RegisteredSupplierContactDetail findWithoutFail($id, $columns = ['*'])
 * @method RegisteredSupplierContactDetail find($id, $columns = ['*'])
 * @method RegisteredSupplierContactDetail first($columns = ['*'])
*/
class RegisteredSupplierContactDetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'registeredSupplierID',
        'contactTypeID',
        'contactPersonName',
        'contactPersonTelephone',
        'contactPersonFax',
        'contactPersonEmail',
        'isDefault'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return RegisteredSupplierContactDetail::class;
    }
}
