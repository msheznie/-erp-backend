<?php
/**
 * =============================================
 * -- File Name : BankAccountRefferedBack.php
 * -- Project Name : ERP
 * -- Module Name :  Bank Account RefferedBack
 * -- Author : Fayas
 * -- Create date : 20 - December 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="BankAccountRefferedBack",
 *      required={""},
 *      @SWG\Property(
 *          property="bankAccountAutoIDRefferedBack",
 *          description="bankAccountAutoIDRefferedBack",
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
 *          property="bankAssignedAutoID",
 *          description="bankAssignedAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="bankmasterAutoID",
 *          description="bankmasterAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companySystemID",
 *          description="companySystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyID",
 *          description="companyID",
 *          type="string"
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
 *          property="bankShortCode",
 *          description="bankShortCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="bankName",
 *          description="bankName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="bankBranch",
 *          description="bankBranch",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="BranchCode",
 *          description="BranchCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="BranchAddress",
 *          description="BranchAddress",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="BranchContactPerson",
 *          description="BranchContactPerson",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="BranchTel",
 *          description="BranchTel",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="BranchFax",
 *          description="BranchFax",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="BranchEmail",
 *          description="BranchEmail",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="AccountNo",
 *          description="AccountNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="accountCurrencyID",
 *          description="accountCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="accountSwiftCode",
 *          description="accountSwiftCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="accountIBAN#",
 *          description="accountIBAN#",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="chqueManualStartingNo",
 *          description="chqueManualStartingNo",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isManualActive",
 *          description="isManualActive",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="chquePrintedStartingNo",
 *          description="chquePrintedStartingNo",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isPrintedActive",
 *          description="isPrintedActive",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="chartOfAccountSystemID",
 *          description="chartOfAccountSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="glCodeLinked",
 *          description="glCodeLinked",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="extraNote",
 *          description="extraNote",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isAccountActive",
 *          description="isAccountActive",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isDefault",
 *          description="isDefault",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="approvedYN",
 *          description="approvedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="approvedByEmpID",
 *          description="approvedByEmpID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="approvedByUserSystemID",
 *          description="approvedByUserSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="approvedEmpName",
 *          description="approvedEmpName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="approvedComments",
 *          description="approvedComments",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserSystemID",
 *          description="createdUserSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdEmpID",
 *          description="createdEmpID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdPCID",
 *          description="createdPCID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifedDateTime",
 *          description="modifedDateTime",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserSystemID",
 *          description="modifiedUserSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="modifiedByEmpID",
 *          description="modifiedByEmpID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedPCID",
 *          description="modifiedPCID",
 *          type="string"
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
 *          property="RollLevForApp_curr",
 *          description="RollLevForApp_curr",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="refferedBackYN",
 *          description="refferedBackYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="timesReferred",
 *          description="timesReferred",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class BankAccountRefferedBack extends Model
{

    public $table = 'erp_bankaccount_refferedback';

    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timeStamp';

    protected $primaryKey = 'bankAccountAutoIDRefferedBack';

    public $fillable = [
        'bankAccountAutoID',
        'bankAssignedAutoID',
        'bankmasterAutoID',
        'companySystemID',
        'companyID',
        'documentSystemID',
        'documentID',
        'bankShortCode',
        'bankName',
        'bankBranch',
        'BranchCode',
        'BranchAddress',
        'BranchContactPerson',
        'BranchTel',
        'BranchFax',
        'BranchEmail',
        'AccountNo',
        'accountCurrencyID',
        'accountSwiftCode',
        'accountIBAN#',
        'chqueManualStartingNo',
        'isManualActive',
        'chquePrintedStartingNo',
        'isPrintedActive',
        'chartOfAccountSystemID',
        'glCodeLinked',
        'extraNote',
        'isAccountActive',
        'isDefault',
        'approvedYN',
        'approvedByEmpID',
        'approvedByUserSystemID',
        'approvedEmpName',
        'approvedDate',
        'approvedComments',
        'createdDateTime',
        'createdUserSystemID',
        'createdEmpID',
        'createdPCID',
        'modifedDateTime',
        'modifiedUserSystemID',
        'modifiedByEmpID',
        'modifiedPCID',
        'timeStamp',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'RollLevForApp_curr',
        'refferedBackYN',
        'timesReferred'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'bankAccountAutoIDRefferedBack' => 'integer',
        'bankAccountAutoID' => 'integer',
        'bankAssignedAutoID' => 'integer',
        'bankmasterAutoID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'documentSystemID' => 'integer',
        'documentID' => 'string',
        'bankShortCode' => 'string',
        'bankName' => 'string',
        'bankBranch' => 'string',
        'BranchCode' => 'string',
        'BranchAddress' => 'string',
        'BranchContactPerson' => 'string',
        'BranchTel' => 'string',
        'BranchFax' => 'string',
        'BranchEmail' => 'string',
        'AccountNo' => 'string',
        'accountCurrencyID' => 'integer',
        'accountSwiftCode' => 'string',
        'accountIBAN#' => 'string',
        'chqueManualStartingNo' => 'integer',
        'isManualActive' => 'integer',
        'chquePrintedStartingNo' => 'integer',
        'isPrintedActive' => 'integer',
        'chartOfAccountSystemID' => 'integer',
        'glCodeLinked' => 'string',
        'extraNote' => 'string',
        'isAccountActive' => 'integer',
        'isDefault' => 'integer',
        'approvedYN' => 'integer',
        'approvedByEmpID' => 'string',
        'approvedByUserSystemID' => 'integer',
        'approvedEmpName' => 'string',
        'approvedComments' => 'string',
        'createdUserSystemID' => 'integer',
        'createdEmpID' => 'string',
        'createdPCID' => 'string',
        'modifedDateTime' => 'string',
        'modifiedUserSystemID' => 'integer',
        'modifiedByEmpID' => 'string',
        'modifiedPCID' => 'string',
        'confirmedYN' => 'integer',
        'confirmedByEmpSystemID' => 'integer',
        'confirmedByEmpID' => 'string',
        'confirmedByName' => 'string',
        'RollLevForApp_curr' => 'integer',
        'refferedBackYN' => 'integer',
        'timesReferred' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function currency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'accountCurrencyID','currencyID');
    }

    public function confirmed_by()
    {
        return $this->belongsTo('App\Models\Employee', 'confirmedByEmpSystemID', 'employeeSystemID');
    }

    public function chart_of_account()
    {
        return $this->belongsTo('App\Models\ChartOfAccount', 'chartOfAccountSystemID','chartOfAccountSystemID');
    }

    public function created_by()
    {
        return $this->belongsTo('App\Models\Employee', 'createdUserSystemID', 'employeeSystemID');
    }

    public function modified_by()
    {
        return $this->belongsTo('App\Models\Employee', 'modifiedUserSystemID', 'employeeSystemID');
    }

    public function approved_by(){
        return $this->hasMany('App\Models\DocumentApproved','documentSystemCode','bankAccountAutoID');
    }
    public function company(){
        return $this->belongsTo('App\Models\Company','companySystemID','companySystemID');
    }
}
