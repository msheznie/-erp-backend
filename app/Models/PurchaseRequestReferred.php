<?php
/**
 * =============================================
 * -- File Name : PurchaseRequestReferred.php
 * -- Project Name : ERP
 * -- Module Name : Purchase Request Referred
 * -- Author : Mohamed Nazir
 * -- Create date : 01- August 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="PurchaseRequestReferred",
 *      required={""},
 *      @SWG\Property(
 *          property="purchaseRequestReferredID",
 *          description="purchaseRequestReferredID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="purchaseRequestID",
 *          description="purchaseRequestID",
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
 *          property="departmentID",
 *          description="departmentID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="serviceLineSystemID",
 *          description="serviceLineSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="serviceLineCode",
 *          description="serviceLineCode",
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
 *          property="companyJobID",
 *          description="companyJobID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="serialNumber",
 *          description="serialNumber",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="purchaseRequestCode",
 *          description="purchaseRequestCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="comments",
 *          description="comments",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="location",
 *          description="location",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="priority",
 *          description="priority",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="deliveryLocation",
 *          description="deliveryLocation",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="docRefNo",
 *          description="docRefNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="invoiceNumber",
 *          description="invoiceNumber",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="currency",
 *          description="currency",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="buyerEmpID",
 *          description="buyerEmpID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="buyerEmpSystemID",
 *          description="buyerEmpSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="buyerEmpName",
 *          description="buyerEmpName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="buyerEmpEmail",
 *          description="buyerEmpEmail",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="supplierCodeSystem",
 *          description="supplierCodeSystem",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supplierName",
 *          description="supplierName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="supplierAddress",
 *          description="supplierAddress",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="supplierTransactionCurrencyID",
 *          description="supplierTransactionCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supplierCountryID",
 *          description="supplierCountryID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="financeCategory",
 *          description="financeCategory",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="PRConfirmedYN",
 *          description="PRConfirmedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="PRConfirmedBy",
 *          description="PRConfirmedBy",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isActive",
 *          description="isActive",
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
 *          property="selectedForPO",
 *          description="selectedForPO",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="approved",
 *          description="approved",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="timesReferred",
 *          description="timesReferred",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="prClosedYN",
 *          description="prClosedYN",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class PurchaseRequestReferred extends Model
{

    public $table = 'erp_purchaserequestreferred';

    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timeStamp';

    protected $primaryKey = 'purchaseRequestReferredID';


    public $fillable = [
        'purchaseRequestID',
        'companySystemID',
        'companyID',
        'departmentID',
        'serviceLineSystemID',
        'serviceLineCode',
        'documentSystemID',
        'documentID',
        'companyJobID',
        'serialNumber',
        'purchaseRequestCode',
        'comments',
        'location',
        'priority',
        'deliveryLocation',
        'PRRequestedDate',
        'docRefNo',
        'invoiceNumber',
        'currency',
        'buyerEmpID',
        'buyerEmpSystemID',
        'buyerEmpName',
        'buyerEmpEmail',
        'supplierCodeSystem',
        'supplierName',
        'supplierAddress',
        'supplierTransactionCurrencyID',
        'supplierCountryID',
        'financeCategory',
        'PRConfirmedYN',
        'PRConfirmedByEmpName',
        'PRConfirmedBy',
        'PRConfirmedBySystemID',
        'PRConfirmedDate',
        'isActive',
        'approved',
        'approvedDate',
        'timesReferred',
        'refferedBackYN',
        'prClosedYN',
        'prClosedComments',
        'prClosedByEmpID',
        'prClosedDate',
        'cancelledYN',
        'cancelledByEmpID',
        'cancelledByEmpName',
        'cancelledComments',
        'cancelledDate',
        'selectedForPO',
        'selectedForPOByEmpID',
        'supplyChainOnGoing',
        'poTrackID',
        'RollLevForApp_curr',
        'hidePOYN',
        'hideByEmpID',
        'hideByEmpName',
        'hideDate',
        'hideComments',
        'PreviousBuyerEmpID',
        'delegatedDate',
        'delegatedComments',
        'fromWeb',
        'wo_status',
        'doc_type',
        'refferedBackYN',
        'isAccrued',
        'budgetYear',
        'prBelongsYear',
        'budgetBlockYN',
        'budgetBlockByEmpID',
        'budgetBlockByEmpEmailID',
        'checkBudgetYN',
        'createdUserGroup',
        'createdPcID',
        'createdUserSystemID',
        'createdUserID',
        'modifiedPc',
        'modifiedUser',
        'createdDateTime',
        'timeStamp',
        'modifiedUserSystemID',
        'manuallyClosed',
        'manuallyClosedByEmpSystemID',
        'manuallyClosedByEmpID',
        'manuallyClosedByEmpName',
        'manuallyClosedDate',
        'manuallyClosedComment',
        'timesReferred',
        'counter',
        'requested_by',
          'isDelegation'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'purchaseRequestReferredID' => 'integer',
        'purchaseRequestID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'departmentID' => 'string',
        'serviceLineSystemID' => 'integer',
        'serviceLineCode' => 'string',
        'documentSystemID' => 'integer',
        'documentID' => 'string',
        'companyJobID' => 'integer',
        'serialNumber' => 'integer',
        'purchaseRequestCode' => 'string',
        'comments' => 'string',
        'location' => 'integer',
        'priority' => 'integer',
        'deliveryLocation' => 'integer',
        'docRefNo' => 'string',
        'invoiceNumber' => 'string',
        'currency' => 'integer',
        'buyerEmpID' => 'string',
        'buyerEmpSystemID' => 'integer',
        'buyerEmpName' => 'string',
        'buyerEmpEmail' => 'string',
        'supplierCodeSystem' => 'integer',
        'supplierName' => 'string',
        'supplierAddress' => 'string',
        'supplierTransactionCurrencyID' => 'integer',
        'supplierCountryID' => 'string',
        'financeCategory' => 'integer',
        'PRConfirmedYN' => 'integer',
        'PRConfirmedBy' => 'string',
        'PRConfirmedByEmpName' => 'string',
        'PRConfirmedBySystemID' => 'integer',
        'isActive' => 'integer',
        'approved' => 'integer',
        'timesReferred' => 'integer',
        'refferedBackYN' => 'integer',
        'prClosedYN' => 'integer',
        'prClosedComments' => 'string',
        'prClosedByEmpID' => 'string',
        'cancelledYN' => 'integer',
        'cancelledByEmpID' => 'string',
        'cancelledByEmpName' => 'string',
        'cancelledComments' => 'string',
        'selectedForPO' => 'integer',
        'selectedForPOByEmpID' => 'string',
        'supplyChainOnGoing' => 'integer',
        'poTrackID' => 'integer',
        'RollLevForApp_curr' => 'integer',
        'hidePOYN' => 'integer',
        'hideByEmpID' => 'string',
        'hideByEmpName' => 'string',
        'hideComments' => 'string',
        'PreviousBuyerEmpID' => 'string',
        'delegatedComments' => 'string',
        'fromWeb' => 'integer',
        'wo_status' => 'integer',
        'doc_type' => 'integer',
        'isAccrued' => 'integer',
        'budgetYear' => 'integer',
        'prBelongsYear' => 'integer',
        'budgetBlockYN' => 'integer',
        'budgetBlockByEmpID' => 'string',
        'budgetBlockByEmpEmailID' => 'string',
        'checkBudgetYN' => 'integer',
        'createdUserGroup' => 'string',
        'createdPcID' => 'string',
        'createdUserID' => 'string',
        'modifiedPc' => 'string',
        'modifiedUser' => 'string',
        'manuallyClosed' => 'integer',
        'manuallyClosedByEmpSystemID' => 'integer',
        'manuallyClosedByEmpID' => 'string',
        'manuallyClosedByEmpName' => 'string',
        'manuallyClosedDate' => 'string',
        'manuallyClosedComment' => 'string',
        'timesReferred' => 'integer',
        'requested_by' => 'integer'

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

    public function cancelled_by()
    {
        return $this->belongsTo('App\Models\Employee', 'cancelledByEmpSystemID', 'employeeSystemID');
    }

    public function manually_closed_by()
    {
        return $this->belongsTo('App\Models\Employee', 'manuallyClosedByEmpSystemID', 'employeeSystemID');
    }

    public function modified_by()
    {
        return $this->belongsTo('App\Models\Employee', 'modifiedUserSystemID', 'employeeSystemID');
    }

    public function confirmed_by()
    {
        return $this->belongsTo('App\Models\Employee', 'PRConfirmedBySystemID', 'employeeSystemID');
    }

    public function priority()
    {
        return $this->belongsTo('App\Models\Priority', 'priority', 'priorityID');
    }

    public function priority_pdf()
    {
        return $this->belongsTo('App\Models\Priority', 'priority', 'priorityID');
    }

    public function location()
    {
        return $this->belongsTo('App\Models\Location', 'location', 'locationID');
    }

    public function location_pdf()
    {
        return $this->belongsTo('App\Models\Location', 'location', 'locationID');
    }

    public function segment()
    {
        return $this->belongsTo('App\Models\SegmentMaster', 'serviceLineSystemID', 'serviceLineSystemID');
    }

    public function financeCategory()
    {
        return $this->belongsTo('App\Models\FinanceItemCategoryMaster', 'financeCategory', 'itemCategoryID');
    }

    public function details()
    {
        return $this->hasMany('App\Models\PurchaseRequestDetails', 'purchaseRequestID', 'purchaseRequestID');
    }

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'companySystemID', 'companySystemID');
    }

    public function approved_by()
    {
        return $this->hasMany('App\Models\DocumentApproved', 'documentSystemCode', 'purchaseRequestID');
    }

    public function rejected_by()
    {
        return $this->hasMany('App\Models\DocumentApproved', 'documentSystemCode', 'purchaseRequestID');
    }

    public function po_details()
    {
        return $this->hasMany('App\Models\PurchaseOrderDetails', 'purchaseRequestID', 'purchaseRequestID');
    }


}
