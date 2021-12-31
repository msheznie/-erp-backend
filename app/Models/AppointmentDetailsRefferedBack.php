<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentDetailsRefferedBack extends Model
{
    public $table = 'appointmentDetailsRefferedBackHistory';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $fillable = [
        'appointment_details_id',
        'appointment_id',
        'created_by',
        'item_id',
        'po_master_id',
        'qty',
        'po_detail_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'appointment_details_id' => 'integer',
        'appointment_id' => 'integer',
        'created_by' => 'integer',
        'id' => 'integer',
        'item_id' => 'integer',
        'po_master_id' => 'integer',
        'qty' => 'integer',
        'po_detail_id' => 'integer'

    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];
}
