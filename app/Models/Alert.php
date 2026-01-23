<?php
/**
 * =============================================
 * -- File Name : Alert.php
 * -- Project Name : ERP
 * -- Module Name :  Alert
 * -- Author : Mohamed Fayas
 * -- Create date : 04- May 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Alert
 * @package App\Models
 * @version April 26, 2018, 4:02 am UTC
 *
 * @property string companyID
 * @property string empID
 * @property string docID
 * @property integer docApprovedYN
 * @property integer docSystemCode
 * @property string docCode
 * @property string alertMessage
 * @property string|\Carbon\Carbon alertDateTime
 * @property integer alertViewedYN
 * @property string|\Carbon\Carbon alertViewedDateTime
 * @property string empName
 * @property string empEmail
 * @property string ccEmailID
 * @property string emailAlertMessage
 * @property integer isEmailSend
 * @property string attachmentFileName
 * @property string|\Carbon\Carbon timeStamp
 */
class Alert extends Model
{
    //use SoftDeletes;

    public $table = 'erp_alert';
    
    const CREATED_AT = 'alertDateTime';
    const UPDATED_AT = 'timeStamp';
    protected $primaryKey = 'alertID';





    public $fillable = [
        'companySystemID',
        'companyID',
        'empSystemID',
        'empID',
        'docID',
        'docApprovedYN',
        'docSystemCode',
        'docSystemID',
        'docCode',
        'alertMessage',
        'alertDateTime',
        'alertViewedYN',
        'alertViewedDateTime',
        'empName',
        'empEmail',
        'ccEmailID',
        'emailAlertMessage',
        'isEmailSend',
        'attachmentFileName',
        'timeStamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'alertID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'empSystemID' => 'integer',
        'empID' => 'string',
        'docSystemID' => 'integer',
        'docID' => 'string',
        'docApprovedYN' => 'integer',
        'docSystemCode' => 'integer',
        'docCode' => 'string',
        'alertMessage' => 'string',
        'alertViewedYN' => 'integer',
        'empName' => 'string',
        'empEmail' => 'string',
        'ccEmailID' => 'string',
        'emailAlertMessage' => 'string',
        'isEmailSend' => 'integer',
        'attachmentFileName' => 'string',
        'deleted_at' => 'datetime',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
