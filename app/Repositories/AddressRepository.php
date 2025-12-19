<?php

namespace App\Repositories;

use App\Models\Address;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class AddressRepository
 * @package App\Repositories
 * @version May 4, 2018, 10:47 am UTC
 *
 * @method Address findWithoutFail($id, $columns = ['*'])
 * @method Address find($id, $columns = ['*'])
 * @method Address first($columns = ['*'])
*/
class AddressRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companySystemID',
        'companyID',
        'locationID',
        'departmentID',
        'addressTypeID',
        'addressDescrption',
        'contactPersonID',
        'contactPersonTelephone',
        'contactPersonFaxNo',
        'contactPersonEmail',
        'isDefault',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Address::class;
    }
}
