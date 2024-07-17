<?php
/**
 * =============================================
 * -- File Name : ItemMasterRefferedBack.php
 * -- Project Name : ERP
 * -- Module Name :  Item Master Reffered Back
 * -- Author : Mohamed Fayas
 * -- Create date : 14- December 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="ItemMasterRefferedBack",
 *      required={""},
 *      @SWG\Property(
 *          property="itemCodeSystemRefferedback",
 *          description="itemCodeSystemRefferedback",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="itemCodeSystem",
 *          description="itemCodeSystem",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="primaryItemCode",
 *          description="primaryItemCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="runningSerialOrder",
 *          description="runningSerialOrder",
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
 *          property="primaryCompanySystemID",
 *          description="primaryCompanySystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="primaryCompanyID",
 *          description="primaryCompanyID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="primaryCode",
 *          description="primaryCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="secondaryItemCode",
 *          description="secondaryItemCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="barcode",
 *          description="barcode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="itemDescription",
 *          description="itemDescription",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="itemShortDescription",
 *          description="itemShortDescription",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="itemUrl",
 *          description="itemUrl",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="unit",
 *          description="unit",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="financeCategoryMaster",
 *          description="financeCategoryMaster",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="financeCategorySub",
 *          description="financeCategorySub",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="itemPicture",
 *          description="itemPicture",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="selectedForAssign",
 *          description="selectedForAssign",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isActive",
 *          description="isActive",
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
 *          property="sentConfirmationEmail",
 *          description="sentConfirmationEmail",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="confirmationEmailSentByEmpID",
 *          description="confirmationEmailSentByEmpID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="confirmationEmailSentByEmpName",
 *          description="confirmationEmailSentByEmpName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="itemConfirmedYN",
 *          description="itemConfirmedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="itemConfirmedByEMPSystemID",
 *          description="itemConfirmedByEMPSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="itemConfirmedByEMPID",
 *          description="itemConfirmedByEMPID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="itemConfirmedByEMPName",
 *          description="itemConfirmedByEMPName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="itemApprovedBySystemID",
 *          description="itemApprovedBySystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="itemApprovedBy",
 *          description="itemApprovedBy",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="itemApprovedYN",
 *          description="itemApprovedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="itemApprovedComment",
 *          description="itemApprovedComment",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="timesReferred",
 *          description="timesReferred",
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
 *          property="createdUserGroup",
 *          description="createdUserGroup",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdPcID",
 *          description="createdPcID",
 *          type="string"
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
 *          property="modifiedUser",
 *          description="modifiedUser",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserSystemID",
 *          description="createdUserSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserSystemID",
 *          description="modifiedUserSystemID",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class ItemMasterRefferedBack extends Model
{

    public $table = 'itemmaster_refferedback';

    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timeStamp';
    protected $primaryKey  = 'itemCodeSystemRefferedback';



    public $fillable = [
        'itemCodeSystem',
        'primaryItemCode',
        'runningSerialOrder',
        'documentSystemID',
        'documentID',
        'primaryCompanySystemID',
        'primaryCompanyID',
        'categoryType',
        'primaryCode',
        'secondaryItemCode',
        'barcode',
        'itemDescription',
        'itemShortDescription',
        'itemUrl',
        'unit',
        'financeCategoryMaster',
        'financeCategorySub',
        'itemPicture',
        'selectedForAssign',
        'isActive',
        'RollLevForApp_curr',
        'sentConfirmationEmail',
        'confirmationEmailSentByEmpID',
        'confirmationEmailSentByEmpName',
        'itemConfirmedYN',
        'itemConfirmedByEMPSystemID',
        'itemConfirmedByEMPID',
        'itemConfirmedByEMPName',
        'itemConfirmedDate',
        'itemApprovedBySystemID',
        'itemApprovedBy',
        'itemApprovedYN',
        'itemApprovedDate',
        'itemApprovedComment',
        'timesReferred',
        'refferedBackYN',
        'createdUserGroup',
        'createdPcID',
        'createdUserID',
        'modifiedPc',
        'modifiedUser',
        'createdDateTime',
        'timestamp',
        'createdUserSystemID',
        'modifiedUserSystemID',
        'isPOSItem',
        'vatSubCategory',
        'pos_type'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'itemCodeSystemRefferedback' => 'integer',
        'itemCodeSystem' => 'integer',
        'primaryItemCode' => 'string',
        'runningSerialOrder' => 'integer',
        'documentSystemID' => 'integer',
        'documentID' => 'string',
        'primaryCompanySystemID' => 'integer',
        'primaryCompanyID' => 'string',
        'primaryCode' => 'string',
        'secondaryItemCode' => 'string',
        'barcode' => 'string',
        'itemDescription' => 'string',
        'itemShortDescription' => 'string',
        'itemUrl' => 'string',
        'unit' => 'integer',
        'financeCategoryMaster' => 'integer',
        'financeCategorySub' => 'integer',
        'itemPicture' => 'string',
        'selectedForAssign' => 'integer',
        'isActive' => 'integer',
        'RollLevForApp_curr' => 'integer',
        'sentConfirmationEmail' => 'integer',
        'confirmationEmailSentByEmpID' => 'string',
        'confirmationEmailSentByEmpName' => 'string',
        'itemConfirmedYN' => 'integer',
        'itemConfirmedByEMPSystemID' => 'integer',
        'itemConfirmedByEMPID' => 'string',
        'itemConfirmedByEMPName' => 'string',
        'itemApprovedBySystemID' => 'integer',
        'itemApprovedBy' => 'string',
        'itemApprovedYN' => 'integer',
        'itemApprovedComment' => 'string',
        'timesReferred' => 'integer',
        'refferedBackYN' => 'integer',
        'createdUserGroup' => 'string',
        'createdPcID' => 'string',
        'createdUserID' => 'string',
        'modifiedPc' => 'string',
        'modifiedUser' => 'string',
        'createdUserSystemID' => 'integer',
        'modifiedUserSystemID' => 'integer',
        'isPOSItem' => 'integer',
        'vatSubCategory'=>'integer',
        'pos_type' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function unit(){
        return $this->hasOne('App\Models\Unit','UnitID','unit');
    }

    public function unit_by(){
        return $this->hasOne('App\Models\Unit','UnitID','unit');
    }

    public function financeMainCategory(){
        return $this->hasOne('App\Models\FinanceItemCategoryMaster','itemCategoryID','financeCategoryMaster');
    }

    public function financeSubCategory(){
        return $this->hasOne('App\Models\FinanceItemCategorySub','itemCategorySubID','financeCategorySub');
    }

    public function documentapproved(){
        return $this->hasMany('App\Models\DocumentApproved','documentSystemCode','itemCodeSystem');
    }

    public function finalApprovedBy()
    {
        return $this->belongsTo('App\Models\Employee','itemApprovedBySystemID','employeeSystemID');
    }

    public function created_by()
    {
        return $this->belongsTo('App\Models\Employee', 'createdUserSystemID', 'employeeSystemID');
    }

    public function confirmed_by()
    {
        return $this->belongsTo('App\Models\Employee', 'itemConfirmedByEMPSystemID', 'employeeSystemID');
    }

    public function modified_by()
    {
        return $this->belongsTo('App\Models\Employee', 'modifiedUserSystemID', 'employeeSystemID');
    }

    public function approved_by(){
        return $this->hasMany('App\Models\DocumentApproved','documentSystemCode','itemCodeSystem');
    }
    
}
