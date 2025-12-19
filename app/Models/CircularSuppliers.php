<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CircularSuppliers extends Model
{
    public $table = 'srm_circular_suppliers';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $timestamps = false;

    public $fillable = [
        'circular_id',
        'supplier_id',
        'status',
        'created_at',
        'created_by',
        'updated_by',
        'updated_at'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'circular_id' => 'integer',
        'supplier_id' => 'integer',
        'status' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];


    public function supplier_registration_link()
    {
        return $this->belongsTo('App\Models\SupplierRegistrationLink', 'supplier_id', 'id');
    }

    public function srm_circular_amendments()
    {
        return $this->belongsTo('App\Models\CircularAmendments', 'circular_id', 'circular_id');
    }

    public static function getCircularSuppliers($circularID)
    {
        return self::where('circular_id', $circularID)->get();
    }
}
