<?php

namespace App\Repositories;

use App\Models\AppointmentDetails;
use App\Repositories\BaseRepository;

/**
 * Class AppointmentDetailsRepository
 * @package App\Repositories
 * @version November 12, 2021, 3:37 pm +04
 *
 * @method AppointmentDetails findWithoutFail($id, $columns = ['*'])
 * @method AppointmentDetails find($id, $columns = ['*'])
 * @method AppointmentDetails first($columns = ['*'])
*/
class AppointmentDetailsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'appointment_id',
        'created_by',
        'item_id',
        'po_master_id',
        'qty'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return AppointmentDetails::class;
    }
}
