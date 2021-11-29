<?php

namespace App\Repositories;

use App\Models\Appointment;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class AppointmentRepository
 * @package App\Repositories
 * @version November 12, 2021, 3:35 pm +04
 *
 * @method Appointment findWithoutFail($id, $columns = ['*'])
 * @method Appointment find($id, $columns = ['*'])
 * @method Appointment first($columns = ['*'])
*/
class AppointmentRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'company_id',
        'created_by',
        'slot_detail_id',
        'status',
        'supplier_id',
        'tenat_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Appointment::class;
    }
}
