<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CircularAmendments extends Model
{
    public $table = 'srm_circular_amendments';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $timestamps = false;

    public $fillable = [
        'circular_id',
        'amendment_id',
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
        'amendment_id' => 'integer',
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
}
