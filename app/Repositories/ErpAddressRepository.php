<?php

namespace App\Repositories;

use App\Models\ErpAddress;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ErpAddressRepository
 * @package App\Repositories
 * @version April 9, 2018, 2:58 pm UTC
 *
 * @method ErpAddress findWithoutFail($id, $columns = ['*'])
 * @method ErpAddress find($id, $columns = ['*'])
 * @method ErpAddress first($columns = ['*'])
*/
class ErpAddressRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
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
        return ErpAddress::class;
    }
}
