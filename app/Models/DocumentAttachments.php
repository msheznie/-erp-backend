<?php
/**
 * =============================================
 * -- File Name : DocumentAttachments.php
 * -- Project Name : ERP
 * -- Module Name : Document Attachments
 * -- Author : Mohamed Fayas
 * -- Create date : 04- May 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class DocumentAttachments
 * @package App\Models
 * @version April 3, 2018, 12:18 pm UTC
 *
 * @property integer companySystemID
 * @property string companyID
 * @property integer documentSystemID
 * @property string documentID
 * @property integer documentSystemCode
 * @property string attachmentDescription
 * @property string originalFileName
 * @property string myFileName
 * @property string|\Carbon\Carbon docExpirtyDate
 * @property integer attachmentType
 * @property float sizeInKbs
 * @property string|\Carbon\Carbon timeStamp
 */
class DocumentAttachments extends Model
{
    //use SoftDeletes;

    public $table = 'erp_documentattachments';
    
    const CREATED_AT = 'timeStamp';
    const UPDATED_AT = 'timeStamp';
    protected $primaryKey  = 'attachmentID';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'companySystemID',
        'companyID',
        'documentSystemID',
        'documentID',
        'documentSystemCode',
        'attachmentDescription',
        'originalFileName',
        'myFileName',
        'docExpirtyDate',
        'attachmentType',
        'sizeInKbs',
        'timeStamp',
        'isUploaded',
        'path',
        'pullFromAnotherDocument'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'attachmentID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'documentSystemID' => 'integer',
        'documentID' => 'string',
        'documentSystemCode' => 'integer',
        'attachmentDescription' => 'string',
        'originalFileName' => 'string',
        'myFileName' => 'string',
        'attachmentType' => 'integer',
        'sizeInKbs' => 'float',
        'isUploaded' => 'integer',
        'path' => 'string',
        'pullFromAnotherDocument' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
