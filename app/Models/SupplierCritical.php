<?php
/**
 * =============================================
 * -- File Name : SupplierCritical.php
 * -- Project Name : ERP
 * -- Module Name :  Supplier Critical
 * -- Author : Mohamed Fayas
 * -- Create date : 04- May 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class SupplierCritical
 * @package App\Models
 * @version March 2, 2018, 11:15 am UTC
 *
 * @property string description
 * @property string|\Carbon\Carbon timestamp
 */
class SupplierCritical extends Model
{
    //use SoftDeletes;

    public $table = 'suppliercritical';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'description',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'suppliercriticalID' => 'integer',
        'description' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
