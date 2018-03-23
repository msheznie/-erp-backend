<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class SupplierType
 * @package App\Models
 * @version February 28, 2018, 4:18 am UTC
 *
 * @property string typeDescription
 */
class SupplierType extends Model
{
    //use SoftDeletes;

    public $table = 'suppliertype';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'typeDescription'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'supplierTypeID' => 'integer',
        'typeDescription' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
