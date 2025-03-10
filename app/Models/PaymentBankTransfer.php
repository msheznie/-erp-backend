<?php
/**
 * =============================================
 * -- File Name : PaymentBankTransfer.php
 * -- Project Name : ERP
 * -- Module Name :  Payment Bank Transfer
 * -- Author : Mohamed Fayas
 * -- Create date : 03 - October 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="PaymentBankTransfer",
 *      required={""},
 *      @SWG\Property(
 *          property="paymentBankTransferID",
 *          description="paymentBankTransferID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentSystemID",
 *          description="documentSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentID",
 *          description="documentID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="companySystemID",
 *          description="companySystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="bankTransferDocumentCode",
 *          description="bankTransferDocumentCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="serialNumber",
 *          description="serialNumber",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="bankMasterID",
 *          description="bankMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="bankAccountAutoID",
 *          description="bankAccountAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="confirmedYN",
 *          description="confirmedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="confirmedByEmpSystemID",
 *          description="confirmedByEmpSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="confirmedByEmpID",
 *          description="confirmedByEmpID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="confirmedByName",
 *          description="confirmedByName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="approvedYN",
 *          description="approvedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="approvedByUserID",
 *          description="approvedByUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="approvedByUserSystemID",
 *          description="approvedByUserSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="RollLevForApp_curr",
 *          description="RollLevForApp_curr",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdPcID",
 *          description="createdPcID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserSystemID",
 *          description="createdUserSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdUserID",
 *          description="createdUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedPc",
 *          description="modifiedPc",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserSystemID",
 *          description="modifiedUserSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUser",
 *          description="modifiedUser",
 *          type="string"
 *      )
 * )
 */
class PaymentBankTransfer extends Model
{

    public $table = 'erp_paymentbanktransfer';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timeStamp';
    protected $primaryKey  = 'paymentBankTransferID';
    protected  $appends = ['fileTypeName'];


    public $fillable = [
        'documentSystemID',
        'documentID',
        'companySystemID',
        'bankTransferDocumentCode',
        'serialNumber',
        'documentDate',
        'bankMasterID',
        'bankAccountAutoID',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'approvedYN',
        'approvedDate',
        'approvedByUserID',
        'approvedByUserSystemID',
        'RollLevForApp_curr',
        'createdPcID',
        'createdUserSystemID',
        'narration',
        'createdUserID',
        'modifiedPc',
        'modifiedUserSystemID',
        'modifiedUser',
        'createdDateTime',
        'timeStamp',
        'exportedYN',
        'exportedUserSystemID',
        'exportedDate',
        'refferedBackYN',
        'timesReferred',
        'fileType',
        'submittedDate',
        'submittedStatus',
        'portalStatus',
        'batchReference',
        'batchReferencePV'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'paymentBankTransferID' => 'integer',
        'documentSystemID' => 'integer',
        'documentID' => 'string',
        'companySystemID' => 'integer',
        'bankTransferDocumentCode' => 'string',
        'serialNumber' => 'integer',
        'bankMasterID' => 'integer',
        'bankAccountAutoID' => 'integer',
        'confirmedYN' => 'integer',
        'confirmedByEmpSystemID' => 'integer',
        'confirmedByEmpID' => 'string',
        'confirmedByName' => 'string',
        'approvedYN' => 'integer',
        'approvedByUserID' => 'string',
        'approvedByUserSystemID' => 'integer',
        'RollLevForApp_curr' => 'integer',
        'createdPcID' => 'string',
        'createdUserSystemID' => 'integer',
        'createdUserID' => 'string',
        'modifiedPc' => 'string',
        'modifiedUserSystemID' => 'integer',
        'modifiedUser' => 'string',
        'narration' => 'string',
        'exportedYN'  => 'integer',
        'exportedUserSystemID'  => 'integer',
        'exportedDate' => 'string',
        'refferedBackYN' => 'integer',
        'timesReferred' => 'integer',
        'fileType'  => 'integer',
        'submittedDate' => 'date',
        'submittedStatus' => 'integer',
        'portalStatus' => 'integer',
        'batchReference' => 'string',
        'batchReferencePV' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function created_by()
    {
        return $this->belongsTo('App\Models\Employee', 'createdUserSystemID', 'employeeSystemID');
    }
    public function bank_account()
    {
        return $this->belongsTo('App\Models\BankAccount', 'bankAccountAutoID', 'bankAccountAutoID');
    }

    public function confirmed_by()
    {
        return $this->belongsTo('App\Models\Employee', 'confirmedByEmpSystemID', 'employeeSystemID');
    }

    public function company(){
        return $this->belongsTo('App\Models\Company','companySystemID','companySystemID');
    }

    public function approved_by(){
        return $this->hasMany('App\Models\DocumentApproved','documentSystemCode','paymentBankTransferID');
    }

     public function ledger_data()
    {
        return $this->hasMany('App\Models\BankLedger', 'paymentBankTransferID', 'paymentBankTransferID');
    }

    public function audit_trial()
    {
        return $this->hasMany('App\Models\AuditTrail', 'documentSystemCode', 'paymentBankTransferID')->where('documentSystemID',64);
    }

    public function getFileTypeNameAttribute()
    {
            switch ($this->fileType) {
                case 0 :
                    return "Vendor File";
                    break;
                case 1 :
                    return "Employee File";
                    break;
                default :
                    return null;
            }
    }
}
