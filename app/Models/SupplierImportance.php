<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class SupplierImportance
 * @package App\Models
 * @version February 28, 2018, 4:16 am UTC
 *
 * @property string importanceDescription
 */
class SupplierImportance extends Model
{
    //use SoftDeletes;

    public $table = 'supplierimportance';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'importanceDescription'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'supplierImportanceID' => 'integer',
        'importanceDescription' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
