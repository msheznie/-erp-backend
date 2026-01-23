<?php

namespace App\Repositories;

use App\Models\CustomerContactDetails;
use App\Repositories\BaseRepository;

/**
 * Class CustomerContactDetailsRepository
 * @package App\Repositories
 * @version April 25, 2019, 1:00 pm +04
 *
 * @method CustomerContactDetails findWithoutFail($id, $columns = ['*'])
 * @method CustomerContactDetails find($id, $columns = ['*'])
 * @method CustomerContactDetails first($columns = ['*'])
*/
class CustomerContactDetailsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'customerID',
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
        return CustomerContactDetails::class;
    }
}
