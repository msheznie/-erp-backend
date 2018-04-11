<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class PoPaymentTermTypes
 * @package App\Models
 * @version April 10, 2018, 1:07 pm UTC
 *
 * @property string categoryDescription
 */
class PoPaymentTermTypes extends Model
{
    //use SoftDeletes;

    public $table = 'erp_popaymenttermstype';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $primaryKey = 'paymentTermsCategoryID';

    protected $dates = ['deleted_at'];

    public $fillable = [
        'categoryDescription'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'paymentTermsCategoryID' => 'integer',
        'categoryDescription' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
