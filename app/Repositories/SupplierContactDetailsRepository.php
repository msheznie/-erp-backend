<?php

namespace App\Repositories;

use App\Models\SupplierContactDetails;
use App\Repositories\BaseRepository;

/**
 * Class SupplierContactDetailsRepository
 * @package App\Repositories
 * @version March 6, 2018, 10:52 am UTC
 *
 * @method SupplierContactDetails findWithoutFail($id, $columns = ['*'])
 * @method SupplierContactDetails find($id, $columns = ['*'])
 * @method SupplierContactDetails first($columns = ['*'])
*/
class SupplierContactDetailsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'supplierID',
        'contactTypeID',
        'contactPersonName',
        'contactPersonTelephone',
        'contactPersonFax',
        'contactPersonEmail',
        'isDefault',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SupplierContactDetails::class;
    }
}
