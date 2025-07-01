<?php
/**
 * =============================================
 * -- File Name : PurchaseRequest.php
 * -- Project Name : ERP
 * -- Module Name : Purchase Request
 * -- Author : Mohamed Fayas
 * -- Create date : 04- May 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Awobaz\Compoships\Compoships;

/**
 * Class PurchaseRequest
 * @package App\Models
 * @version March 26, 2018, 7:00 am UTC
 *
 * @property integer companySystemID
 * @property string companyID
 * @property string departmentID
 * @property integer serviceLineSystemID
 * @property string serviceLineCode
 * @property integer documentSystemID
 * @property string documentID
 * @property integer companyJobID
 * @property integer serialNumber
 * @property string purchaseRequestCode
 * @property string comments
 * @property integer location
 * @property integer priority
 * @property integer deliveryLocation
 * @property string|\Carbon\Carbon PRRequestedDate
 * @property string docRefNo
 * @property string invoiceNumber
 * @property integer currency
 * @property string buyerEmpID
 * @property integer buyerEmpSystemID
 * @property string buyerEmpName
 * @property string buyerEmpEmail
 * @property integer supplierCodeSystem
 * @property string supplierName
 * @property string supplierAddress
 * @property integer supplierTransactionCurrencyID
 * @property string supplierCountryID
 * @property integer financeCategory
 * @property integer PRConfirmedYN
 * @property string PRConfirmedBy
 * @property integer PRConfirmedBySystemID
 * @property string|\Carbon\Carbon PRConfirmedDate
 * @property integer isActive
 * @property integer approved
 * @property string|\Carbon\Carbon approvedDate
 * @property integer timesReferred
 * @property integer prClosedYN
 * @property string prClosedComments
 * @property string prClosedByEmpID
 * @property string|\Carbon\Carbon prClosedDate
 * @property integer cancelledYN
 * @property string cancelledByEmpID
 * @property string cancelledByEmpName
 * @property string cancelledComments
 * @property string|\Carbon\Carbon cancelledDate
 * @property integer selectedForPO
 * @property string selectedForPOByEmpID
 * @property integer supplyChainOnGoing
 * @property integer poTrackID
 * @property integer RollLevForApp_curr
 * @property integer hidePOYN
 * @property string hideByEmpID
 * @property string hideByEmpName
 * @property string|\Carbon\Carbon hideDate
 * @property string hideComments
 * @property string PreviousBuyerEmpID
 * @property string|\Carbon\Carbon delegatedDate
 * @property string delegatedComments
 * @property integer fromWeb
 * @property integer wo_status
 * @property integer doc_type
 * @property integer refferedBackYN
 * @property integer isAccrued
 * @property integer budgetYear
 * @property integer prBelongsYear
 * @property integer budgetBlockYN
 * @property string budgetBlockByEmpID
 * @property string budgetBlockByEmpEmailID
 * @property integer checkBudgetYN
 * @property string createdUserGroup
 * @property string createdPcID
 * @property string createdUserID
 * @property string modifiedPc
 * @property string modifiedUser
 * @property string|\Carbon\Carbon createdDateTime
 * @property string|\Carbon\Carbon timeStamp
 */
class PurchaseRequest extends Model
{
    //use SoftDeletes;
    use Compoships;
    public $table = 'erp_purchaserequest';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timeStamp';
    protected $primaryKey  = 'purchaseRequestID';


    protected $dates = ['deleted_at'];


    public $fillable = [
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
        'internalNotes',
        'location',
        'priority',
        'deliveryLocation',
        'PRRequestedDate',
        'budgetYearID',
        'prBelongsYearID',
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
        'approvedByUserID',
        'approvedByUserSystemID',
        'approval_remarks',
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
        'allocateItemToSegment',
        'manuallyClosedComment',
        'isBulkItemJobRun',
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
        'purchaseRequestID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'departmentID' => 'string',
        'serviceLineSystemID' => 'integer',
        'budgetYearID' => 'integer',
        'prBelongsYearID' => 'integer',
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
        'approvedByUserID' => 'string',
        'approvedByUserSystemID' => 'integer',
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
        'allocateItemToSegment' => 'integer',
        'manuallyClosedByEmpID' => 'string',
        'manuallyClosedByEmpName' => 'string',
        'manuallyClosedDate' => 'string',
        'manuallyClosedComment' => 'string',
        'approval_remarks' => 'string',
        'requested_by' =>'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];


    public function currency_by()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'currency', 'currencyID');
    }
    
    public function pulled_from_mr()
    {
        return $this->belongsTo('App\Models\PulledItemFromMR', 'purchaseRequestID', 'purcahseRequestID');
    }

    public function created_by(){
        return $this->belongsTo('App\Models\Employee','createdUserSystemID','employeeSystemID');
    }

    public function buyer(){
        return $this->belongsTo('App\Models\Employee','buyerEmpSystemID','employeeSystemID');
    }

    public function cancelled_by(){
        return $this->belongsTo('App\Models\Employee','cancelledByEmpSystemID','employeeSystemID');
    }

    public function manually_closed_by(){
        return $this->belongsTo('App\Models\Employee','manuallyClosedByEmpSystemID','employeeSystemID');
    }

    public function modified_by(){
        return $this->belongsTo('App\Models\Employee','modifiedUserSystemID','employeeSystemID');
    }

    public function confirmed_by(){
        return $this->belongsTo('App\Models\Employee','PRConfirmedBySystemID','employeeSystemID');
    }

    public function priority(){
        return $this->belongsTo('App\Models\Priority','priority','priorityID');
    }
    public function priority_pdf(){
        return $this->belongsTo('App\Models\Priority','priority','priorityID');
    }
    public function location(){
        return $this->belongsTo('App\Models\Location','location','locationID');
    }
    public function location_pdf(){
        return $this->belongsTo('App\Models\Location','location','locationID');
    }
    public function segment(){
        return $this->belongsTo('App\Models\SegmentMaster','serviceLineSystemID','serviceLineSystemID');
    }

   public function financeCategory(){
       return $this->belongsTo('App\Models\FinanceItemCategoryMaster','financeCategory','itemCategoryID');
   }

    public function details(){
        return $this->hasMany('App\Models\PurchaseRequestDetails','purchaseRequestID','purchaseRequestID');
    }

    public function company(){
        return $this->belongsTo('App\Models\Company','companySystemID','companySystemID');
    }

    public function approved_by(){
        return $this->hasMany('App\Models\DocumentApproved','documentSystemCode','purchaseRequestID');
    }

    public function rejected_by(){
        return $this->hasMany('App\Models\DocumentApproved','documentSystemCode','purchaseRequestID');
    }

    public function po_details(){
        return $this->hasMany('App\Models\PurchaseOrderDetails','purchaseRequestID','purchaseRequestID');
    }

    public function audit_trial()
    {
        return $this->hasMany('App\Models\AuditTrail', 'documentSystemCode', 'purchaseRequestID')->whereIn('documentSystemID',[1,50,51]);
    }

    public function document_by()
    {
        return $this->belongsTo('App\Models\DocumentMaster', 'documentSystemID', 'documentSystemID');
    }

    public function budget_transfer_addition()
    {
        return $this->hasMany('App\Models\BudgetReviewTransferAddition', ['documentSystemCode', 'documentSystemID'], ['purchaseRequestID', 'documentSystemID']);
    }

    public function scopeDetailJoin($q)
    {
        return $q->join('erp_purchaserequestdetails','erp_purchaserequestdetails.purchaseRequestID','erp_purchaserequest.purchaseRequestID');
    }

    public function scopeEmployeeJoin($q,$as = 'employees' ,$column = 'createdUserSystemID',$columnAs = 'empName'){
        $q->leftJoin('employees as '. $as, $as.'.employeeSystemID', '=', 'erp_purchaserequest.'.$column)
            ->addSelect($as.".empName as ".$columnAs);
    }

    public function scopeDepartmentJoin($q,$as = 'department', $column = 'serviceLineSystemID' , $columnAs = 'ServiceLineDes')
    {
        return $q->leftJoin('serviceline as '.$as,$as.'.serviceLineSystemID','erp_purchaserequest.'.$column);
    }

    public function scopeCategoryJoin($q,$as = 'category', $column = 'financeCategory' , $columnAs = 'categoryDescription')
    {
        return $q->leftJoin('financeitemcategorymaster as '.$as,$as.'.itemCategoryID','erp_purchaserequest.'.$column)
                  ->addSelect($as.".categoryDescription as ".$columnAs);
    }

    public function scopeCurrencyJoin($q,$as = 'currencymaster' ,$column = 'currency',$columnAs = 'currencyByName'){
            return $q->leftJoin('currencymaster as '.$as,$as.'.currencyID','=','erp_purchaserequest.'.$column)
            ->addSelect($as.".CurrencyName as ".$columnAs);

    }

    public function scopeLocationJoin($q,$as = 'erp_location' ,$column = 'location',$columnAs = 'locationByName'){
        return $q->leftJoin('erp_location as '.$as,$as.'.locationID','=','erp_purchaserequest.'.$column)
        ->addSelect($as.".locationName as ".$columnAs);

}
    public function tender_purchase_request(){
        return $this->hasOne(TenderPurchaseRequest::class,'purchase_request_id','purchaseRequestID');
    }
    public function tender_purchase_request_log(){
        return $this->hasOne(TenderPurchaseRequestEditLog::class,'purchase_request_id','purchaseRequestID');
    }

    public function requestedby(){
        return $this->belongsTo('App\Models\Employee','requested_by','employeeSystemID');
    }

    public static function getPurchaseRequestData($companySystemID, $tenderMasterID, $document_type){
        $purchaseRequest = self::select('purchaseRequestID as id', 'purchaseRequestCode')
            ->with(['tender_purchase_request','details.podetail'])
            ->where('approved', '-1')
            ->where('companySystemID', $companySystemID);

        if($document_type == 0){
            $purchaseRequest = $purchaseRequest->whereDoesntHave('tender_purchase_request', function ($query) use ($tenderMasterID) {
                $query->where(function ($subQuery) use ($tenderMasterID) {
                    $subQuery->where('tender_id', '!=', $tenderMasterID)
                        ->whereHas('tender', function ($subSubQuery) {
                            $subSubQuery->where('document_type', 0);
                        });
                });
            });
        } elseif ($document_type != 0){
            $purchaseRequest = $purchaseRequest->whereDoesntHave('tender_purchase_request', function ($query) use ($tenderMasterID) {
                $query->where(function ($subQuery) use ($tenderMasterID) {
                    $subQuery->where('tender_id', '!=', $tenderMasterID)
                        ->whereHas('tender', function ($subSubQuery) {
                            $subSubQuery->where('document_type', '!=', 0);
                        });
                });
            });
        }

        return $purchaseRequest->whereDoesntHave('details.podetail')
            ->get();

    }
    public static function getPurchaseRequestForTender($tenderId, $companyId, $versionID, $editOrAmend)
    {
        return self::select('purchaseRequestID', 'companyID', 'purchaseRequestCode')
            ->when($editOrAmend, function ($q) use ($tenderId, $versionID) {
                $q->with(['tender_purchase_request_log' => function ($query) use ($tenderId, $versionID) {
                    $query->where('tender_id', $tenderId)
                        ->where('version_id', $versionID)
                        ->where('is_deleted', 0);
                }])
                    ->whereHas('tender_purchase_request_log', function ($query) use ($tenderId, $versionID) {
                        $query->where('tender_id', $tenderId)
                            ->where('version_id', $versionID)
                            ->where('is_deleted', 0);
                    });
            })
            ->when(!$editOrAmend, function ($q) use ($tenderId) {
                $q->with(['tender_purchase_request' => function ($query) use ($tenderId) {
                    $query->where('tender_id', $tenderId);
                }])
                    ->whereHas('tender_purchase_request', function ($query) use ($tenderId) {
                        $query->where('tender_id', $tenderId);
                    });
            })
            ->where('companySystemID', $companyId)
            ->get();
    }
}
