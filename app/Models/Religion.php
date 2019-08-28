<?php
/**
 * =============================================
 * -- File Name : Religion.php
 * -- Project Name : ERP
 * -- Module Name : Employee Details
 * -- Author : Mohamed Rilwan
 * -- Create date : 27- August 2019
 * -- Description :
 * -- REVISION HISTORY
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Religion extends Model
{
    public $table = 'hrms_religion';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $primaryKey = 'religionID';


    public $fillable = [
        'religionName',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'religionID' => 'integer',
        'religionName' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

}
