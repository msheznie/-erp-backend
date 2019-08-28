<?php
/**
 * =============================================
 * -- File Name : MaritialStatus.php
 * -- Project Name : ERP
 * -- Module Name : Employee Details
 * -- Author : Mohamed Rilwan
 * -- Create date : 27- August 2019
 * -- Description :
 * -- REVISION HISTORY
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaritialStatus extends Model
{
    public $table = 'hrms_maritialstatus';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $primaryKey = 'maritialstatusID';


    public $fillable = [
        'code',
        'description',
        'description_O',
        'noOfkids',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'maritialstatusID' => 'integer',
        'code' => 'string',
        'description_O' => 'string',
        'noOfkids' => 'string',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];


}
