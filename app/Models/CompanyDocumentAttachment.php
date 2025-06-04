<?php
/**
 * =============================================
 * -- File Name : CompanyDocumentAttachment.php
 * -- Project Name : ERP
 * -- Module Name : Company Document Attachment
 * -- Author : Mohamed Fayas
 * -- Create date : 04- May 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class CompanyDocumentAttachment
 * @package App\Models
 * @version March 29, 2018, 5:13 am UTC
 *
 * @property integer companySystemID
 * @property string companyID
 * @property integer documentSystemID
 * @property string documentID
 * @property string docRefNumber
 * @property integer isAttachmentYN
 * @property integer sendEmailYN
 * @property string codeGeneratorFormat
 * @property integer isAmountApproval
 * @property integer isServiceLineApproval
 * @property integer blockYN
 * @property string|\Carbon\Carbon timeStamp
 */
class CompanyDocumentAttachment extends Model
{
    //use SoftDeletes;

    public $table = 'companydocumentattachment';
    
    const CREATED_AT = 'timeStamp';
    const UPDATED_AT = 'timeStamp';
    protected $primaryKey  = 'companyDocumentAttachmentID';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'companySystemID',
        'companyID',
        'documentSystemID',
        'documentID',
        'docRefNumber',
        'isAttachmentYN',
        'sendEmailYN',
        'codeGeneratorFormat',
        'isAmountApproval',
        'isServiceLineApproval',
        'isServiceLineAccess',
        'blockYN',
        'timeStamp',
        'isCategoryApproval',
        'enableAttachmentAfterApproval'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'companyDocumentAttachmentID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'documentSystemID' => 'integer',
        'documentID' => 'string',
        'docRefNumber' => 'string',
        'isAttachmentYN' => 'integer',
        'sendEmailYN' => 'integer',
        'codeGeneratorFormat' => 'string',
        'isAmountApproval' => 'integer',
        'isServiceLineAccess' => 'integer',
        'isServiceLineApproval' => 'integer',
        'blockYN' => 'integer',
        'isCategoryApproval' => 'integer',
        'enableAttachmentAfterApproval' => 'integer',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company','companySystemID','companySystemID');
    }

    public function document()
    {
        return $this->belongsTo('App\Models\DocumentMaster','documentSystemID','documentSystemID');
    }

     public static function companyDocumentAttachemnt($companySystemID, $documentSystemID)
    {
        $attachemnt = new CompanyDocumentAttachment();
        return $attachemnt->where('companySystemID', $companySystemID)
                        ->where('documentSystemID', $documentSystemID)
                        ->first();
    }

       public function access()
    {
        return $this->hasOne('App\Models\CompanyDocumentAttachmentAccess','documentSystemID','documentSystemID');
    }
}
