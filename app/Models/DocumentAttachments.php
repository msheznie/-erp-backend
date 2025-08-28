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
use Awobaz\Compoships\Compoships;
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
    use Compoships;
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
        'pullFromAnotherDocument',
        'parent_id',
        'envelopType',
        'order_number',
        'isAutoCreateDocument'
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
        'pullFromAnotherDocument' => 'integer',
        'parent_id' => 'integer',
        'isAutoCreateDocument' => 'integer',
        'envelopType' => 'string'
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
        return $this->belongsTo('App\Models\Company', 'companySystemID', 'companySystemID');
    }

    public function document(){
        return $this->belongsTo('App\Models\DocumentMaster','documentSystemID','documentSystemID');
    }

    public function type(){
        return $this->belongsTo('App\Models\DocumentAttachmentType','attachmentType','travelClaimAttachmentTypeID');
    }

    public function request(){
        return $this->belongsTo('App\Models\PurchaseRequest','documentSystemCode','purchaseRequestID')->whereIn('documentSystemID',[1,50,51]);
    }

    public function order(){
        return $this->belongsTo('App\Models\ProcumentOrder','documentSystemCode','purchaseOrderID')->whereIn('documentSystemID',[2,5,52]);
    }

    public function grv(){
        return $this->belongsTo('App\Models\GRVMaster','documentSystemCode','grvAutoID')->where('documentSystemID',3);
    }

    public function payment_voucher(){
        return $this->belongsTo('App\Models\PaySupplierInvoiceMaster','documentSystemCode','PayMasterAutoId')->where('documentSystemID',4);
    }

    public function expense_claim(){
        return $this->belongsTo('App\Models\ExpenseClaim','documentSystemCode','expenseClaimMasterAutoID')->where('documentSystemID',6);
    }

    public function stock_adjustment(){
        return $this->belongsTo('App\Models\StockAdjustment','documentSystemCode','stockAdjustmentAutoID')->where('documentSystemID',7);
    }

    public function material_issue(){
        return $this->belongsTo('App\Models\ItemIssueMaster','documentSystemCode','itemIssueAutoID')->where('documentSystemID',8);
    }

    public function material_request(){
        return $this->belongsTo('App\Models\MaterielRequest','documentSystemCode','RequestID')->where('documentSystemID',9);
    }

    public function receive_stock(){
        return $this->belongsTo('App\Models\StockReceive','documentSystemCode','stockReceiveAutoID')->where('documentSystemID',10);
    }

    public function supplier_invoice(){
        return $this->belongsTo('App\Models\BookInvSuppMaster','documentSystemCode','bookingSuppMasInvAutoID')->where('documentSystemID',11);
    }

    public function stock_return(){
        return $this->belongsTo('App\Models\ItemReturnMaster','documentSystemCode','itemReturnAutoID')->where('documentSystemID',12);
    }

    public function stock_transfer(){
        return $this->belongsTo('App\Models\StockTransfer','documentSystemCode','stockTransferAutoID')->where('documentSystemID',13);
    }

    public function logistic(){
        return $this->belongsTo('App\Models\Logistic','documentSystemCode','logisticMasterID')->where('documentSystemID',14);
    }

    public function debit_note(){
        return $this->belongsTo('App\Models\DebitNote','documentSystemCode','debitNoteAutoID')->where('documentSystemID',15);
    }

    public function direct_payment(){
//        return $this->belongsTo('App\Models\ExpenseClaim','documentSystemCode','expenseClaimMasterAutoID')->where('documentSystemID',16);
    }

    public function journal_entries(){
        return $this->belongsTo('App\Models\JvMaster','documentSystemCode','jvMasterAutoId')->where('documentSystemID',17);
    }

    public function direct_invoice(){
//        return $this->belongsTo('App\Models\ExpenseClaim','documentSystemCode','expenseClaimMasterAutoID')->where('documentSystemID',18);
    }

    public function credit_note(){
        return $this->belongsTo('App\Models\CreditNote','documentSystemCode','creditNoteAutoID')->where('documentSystemID',19);
    }

    public function customer_invoice(){
        return $this->belongsTo('App\Models\CustomerInvoiceDirect','documentSystemCode','custInvoiceDirectAutoID')->where('documentSystemID',20);
    }

    public function bank_receipt(){
        return $this->belongsTo('App\Models\CustomerReceivePayment','documentSystemCode','custReceivePaymentAutoID')->where('documentSystemID',21);
    }

    public function fixed_asset(){
        return $this->belongsTo('App\Models\FixedAssetMaster','documentSystemCode','faID')->where('documentSystemID',22);
    }

    public function fixed_asset_dep(){
        return $this->belongsTo('App\Models\FixedAssetDepreciationMaster','documentSystemCode','depMasterAutoID')->where('documentSystemID',23);
    }

    public function purchase_return(){
        return $this->belongsTo('App\Models\PurchaseReturn','documentSystemCode','purhaseReturnAutoID')->where('documentSystemID',24);
    }

    public function job_bonus(){
//        return $this->belongsTo('App\Models\ExpenseClaim','documentSystemCode','expenseClaimMasterAutoID')->where('documentSystemID',25);
    }

    public function desert_allowance(){
//        return $this->belongsTo('App\Models\ExpenseClaim','documentSystemCode','expenseClaimMasterAutoID')->where('documentSystemID',26);
    }

    public function salary_dec(){
//        return $this->belongsTo('App\Models\ExpenseClaim','documentSystemCode','expenseClaimMasterAutoID')->where('documentSystemID',27);
    }

    public function monthly_addition(){
        return $this->belongsTo('App\Models\MonthlyAdditionsMaster','documentSystemCode','monthlyAdditionsMasterID')->where('documentSystemID',28);
    }

    public function monthly_deduction(){
//        return $this->belongsTo('App\Models\ExpenseClaim','documentSystemCode','expenseClaimMasterAutoID')->where('documentSystemID',29);
    }

    public function job_bonus_calculation(){
//        return $this->belongsTo('App\Models\ExpenseClaim','documentSystemCode','expenseClaimMasterAutoID')->where('documentID','JBC');
    }

    public function desert_allowance_calculation(){
//        return $this->belongsTo('App\Models\CustomerReceivePayment','documentSystemCode','custReceivePaymentAutoID')->where('documentSystemID',31);
    }

    public function over_time_calculation(){
//        return $this->belongsTo('App\Models\FixedAssetMaster','documentSystemCode','faID')->where('documentSystemID',32);
    }

    public function loan_management(){
//        return $this->belongsTo('App\Models\FixedAssetDepreciationMaster','documentSystemCode','depMasterAutoID')->where('documentSystemID',33);
    }

    public function extra_pay(){
//        return $this->belongsTo('App\Models\PurchaseReturn','documentSystemCode','purhaseReturnAutoID')->where('documentSystemID',34);
    }

    public function salary_process(){
//        return $this->belongsTo('App\Models\ExpenseClaim','documentSystemCode','expenseClaimMasterAutoID')->where('documentSystemID',35);
    }

    public function split_salary(){
//        return $this->belongsTo('App\Models\ExpenseClaim','documentSystemCode','expenseClaimMasterAutoID')->where('documentSystemID',36);
    }

    public function leave_application(){
        return $this->belongsTo('App\Models\LeaveDataMaster','documentSystemCode','leavedatamasterID')->where('documentID','LA');
    }

    public function leave_accrual(){
//        return $this->belongsTo('App\Models\MonthlyAdditionsMaster','documentSystemCode','monthlyAdditionsMasterID')->where('documentSystemID',38);
    }

    public function batch_submission(){
        return $this->belongsTo('App\Models\CustomerInvoiceTracking','documentSystemCode','customerInvoiceTrackingID')->where('documentSystemID',39);
    }

    public function bonus_sheet(){
//        return $this->belongsTo('App\Models\ExpenseClaim','documentSystemCode','expenseClaimMasterAutoID')->where('documentSystemID',40);
    }

    public function fixed_asset_disposal(){
        return $this->belongsTo('App\Models\AssetDisposalMaster','documentSystemCode','assetdisposalMasterAutoID')->where('documentSystemID',41);
    }

    public function non_salary_payment(){
//        return $this->belongsTo('App\Models\FixedAssetMaster','documentSystemCode','faID')->where('documentSystemID',42);
    }

    public function final_settlement(){
//        return $this->belongsTo('App\Models\FixedAssetDepreciationMaster','documentSystemCode','depMasterAutoID')->where('documentSystemID',43);
    }

    public function travel_claim_request(){
//        return $this->belongsTo('App\Models\PurchaseReturn','documentSystemCode','purhaseReturnAutoID')->where('documentSystemID',44);
    }

    public function travel_claim_accrual(){
//        return $this->belongsTo('App\Models\ExpenseClaim','documentSystemCode','expenseClaimMasterAutoID')->where('documentSystemID',45);
    }

    public function budget_transfer_notes(){
        return $this->belongsTo('App\Models\BudgetTransferForm','documentSystemCode','budgetTransferFormAutoID')->where('documentSystemID',46);
    }

    public function probation_form(){
//        return $this->belongsTo('App\Models\ExpenseClaim','documentSystemCode','expenseClaimMasterAutoID')->where('documentSystemID',47);
    }

    public function radio_active_allowance(){
//        return $this->belongsTo('App\Models\MonthlyAdditionsMaster','documentSystemCode','monthlyAdditionsMasterID')->where('documentSystemID',48);
    }

    public function job_profile(){
//        return $this->belongsTo('App\Models\FixedAssetDepreciationMaster','documentSystemCode','depMasterAutoID')->where('documentSystemID',53);
    }

    public function journey_plan(){
//        return $this->belongsTo('App\Models\PurchaseReturn','documentSystemCode','purhaseReturnAutoID')->where('documentSystemID',54);
    }

    public function recruitment_request(){
//        return $this->belongsTo('App\Models\ExpenseClaim','documentSystemCode','expenseClaimMasterAutoID')->where('documentSystemID',55);
    }

    public function supplier_master(){
        return $this->belongsTo('App\Models\SupplierMaster','documentSystemCode','supplierCodeSystem')->where('documentSystemID',56);
    }

    public function item_master(){
        return $this->belongsTo('App\Models\ItemMaster','documentSystemCode','itemCodeSystem')->where('documentSystemID',57);
    }

    public function customer_master(){
        return $this->belongsTo('App\Models\CustomerMaster','documentSystemCode','customerCodeSystem')->where('documentSystemID',58);
    }

    public function chart_of_account_master(){
        return $this->belongsTo('App\Models\ChartOfAccount','documentSystemCode','chartOfAccountSystemID')->where('documentSystemID',59);
    }

    public function po_logistic(){
//        return $this->belongsTo('App\Models\ExpenseClaim','documentSystemCode','expenseClaimMasterAutoID')->where('documentSystemID',60);
    }

    public function inventory_reclassification(){
        return $this->belongsTo('App\Models\InventoryReclassification','documentSystemCode','inventoryreclassificationID')->where('documentSystemID',61);
    }

    public function bank_reconciliation(){
        return $this->belongsTo('App\Models\BankReconciliation','documentSystemCode','bankRecAutoID')->where('documentSystemID',62);
    }

    public function asset_capitalization(){
        return $this->belongsTo('App\Models\AssetCapitalization','documentSystemCode','capitalizationID')->where('documentSystemID',63);
    }

    public function payment_bank_transfer(){
        return $this->belongsTo('App\Models\PaymentBankTransfer','documentSystemCode','paymentBankTransferID')->where('documentSystemID',64);
    }

    /*public function budget(){
        return $this->belongsTo('App\Models\BudgetMaster','documentSystemCode','budgetmasterID')->where('documentSystemID',65);
    }*/

    /*public function bank_account(){
        return $this->belongsTo('App\Models\BankAccount','documentSystemCode','bankAccountAutoID')->where('documentSystemID',66);
    }*/

    public function sales_quotation(){
        return $this->belongsTo('App\Models\QuotationMaster','documentSystemCode','quotationMasterID')->whereIn('documentSystemID',[67,68]);
    }

    public function console_jv(){
        return $this->belongsTo('App\Models\ConsoleJVMaster','documentSystemCode','consoleJvMasterAutoId')->where('documentSystemID',69);
    }

    public function matching(){
        return $this->belongsTo('App\Models\MatchDocumentMaster','documentSystemCode','matchDocumentMasterAutoID')->where('documentSystemID',70);
    }

    public function delivery_order(){
        return $this->belongsTo('App\Models\DeliveryOrder','documentSystemCode','deliveryOrderID')->where('documentSystemID',71);
    }

    public function contracts(){
//        return $this->belongsTo('App\Models\FixedAssetMaster','documentSystemCode','faID')->where('documentSystemID',72);
    }

    public function contract_details(){
//        return $this->belongsTo('App\Models\FixedAssetDepreciationMaster','documentSystemCode','depMasterAutoID')->where('documentSystemID',73);
    }

    public function mobile_bill(){
        return $this->belongsTo('App\Models\MobileBillMaster','documentSystemCode','mobilebillMasterID')->where('documentSystemID',74);
    }

    public function proforma(){
//        return $this->belongsTo('App\Models\ExpenseClaim','documentSystemCode','expenseClaimMasterAutoID')->where('documentSystemID',75);
    }

    public function material_outward_ticket(){
//        return $this->belongsTo('App\Models\ExpenseClaim','documentSystemCode','expenseClaimMasterAutoID')->where('documentSystemID',76);
    }

    public function material_inward_ticket(){
//        return $this->belongsTo('App\Models\ExpenseClaim','documentSystemCode','expenseClaimMasterAutoID')->where('documentSystemID',77);
    }

    public function damage_beyond_repair(){
//        return $this->belongsTo('App\Models\MonthlyAdditionsMaster','documentSystemCode','monthlyAdditionsMasterID')->where('documentSystemID',78);
    }

    public function lost_in_hole(){
//        return $this->belongsTo('App\Models\ExpenseClaim','documentSystemCode','expenseClaimMasterAutoID')->where('documentSystemID',79);
    }

    public function job_card(){
//        return $this->belongsTo('App\Models\ExpenseClaim','documentSystemCode','expenseClaimMasterAutoID')->where('documentSystemID',80);
    }

    public function evaluation(){
//        return $this->belongsTo('App\Models\MonthlyAdditionsMaster','documentSystemCode','monthlyAdditionsMasterID')->where('documentSystemID',82);
    }

    public function registered_supplier(){
//        return $this->belongsTo('App\Models\ExpenseClaim','documentSystemCode','expenseClaimMasterAutoID')->where('documentSystemID',85);
    }

    public function sales_return(){
        return $this->belongsTo('App\Models\SalesReturn','documentSystemCode','id')->where('documentSystemID',87);
    }

    public function tender_document_types()
    {
        return $this->hasOne('App\Models\TenderDocumentTypes', 'id', 'attachmentType');
    }

    public function document_attachments()
    {
        return $this->hasMany('App\Models\DocumentAttachments', 'parent_id', 'attachmentID');
    }

    public function document_parent()
    {
        return $this->hasOne('App\Models\DocumentAttachments', 'attachmentID', 'parent_id');
    }

    public function bid_verify()
    {
        return $this->hasOne('App\Models\BidDocumentVerification', 'attachment_id', 'attachmentID');
    }
    
    public function tender(){
        return $this->belongsTo('App\Models\TenderMaster','documentSystemCode','id');
    }

    public static function documentAttachmentById($attachmentId)
    {
        return self::where('attachmentID', $attachmentId)->first();
    }

    public static function evaluationAttachment($companyId, $tenderId)
    {
        return DocumentAttachments::where('documentSystemID', 130)
            ->where('companySystemID', $companyId)
            ->where('documentSystemCode', $tenderId)->exists();
    }

    public static function getOriginalFileName($companyId, $tenderId)
    {
        $originalFileName = DocumentAttachments::select('originalFileName', 'attachmentID')
            ->where('documentSystemID', 130)
            ->where('companySystemID', $companyId)
            ->where('documentSystemCode', $tenderId)->first();

        return $originalFileName;
    }

    public static function getTenderAttachments($documentSystemCode, $documentSystemID){
        return self::where('documentSystemID', $documentSystemID)->where('documentSystemCode', $documentSystemCode)->get();
    }
    public static function checkDocumentExists($companySystemID, $documentSystemID, $attachmentType, $documentSystemCode, $attachmentDescription, $id){
        return self::where('companySystemID',$companySystemID)
            ->when($id > 0, function ($q) use ($id) {
                $q->where('attachmentID', '!=', $id);
        })
            ->where('documentSystemID',$documentSystemID)
            ->where('attachmentType',$attachmentType)
            ->where('documentSystemCode',$documentSystemCode)
            ->where('attachmentDescription',$attachmentDescription)
            ->exists();
    }
    public static function getAttachmentDocumentTypeBase($companySystemID, $documentSystemID,$attachmentType, $documentSystemCode){
        return self::where('companySystemID',$companySystemID)
            ->where('documentSystemID',$documentSystemID)
            ->where('attachmentType',$attachmentType)
            ->where('documentSystemCode',$documentSystemCode)
            ->orderBy('attachmentID', 'asc')
            ->get();
    }

    public static function getAttachmentForCirculars($attachmentArray, $documentSystemID, $tenderMasterId)
    {
        return self::whereNotIn('attachmentID', $attachmentArray)
            ->where('documentSystemID', $documentSystemID)
            ->where('attachmentType',3)
            ->where('parent_id', null)
            ->where('documentSystemCode', $tenderMasterId)->orderBy('attachmentID', 'asc')->get()->toArray();
    }
    public static function getNotUsedAttachmentForCirculars($circularAttachmentIDs){
        return self::whereIn('attachmentID', $circularAttachmentIDs)->get();
    }

    public static function getBidAttachmentList($doucments, $tenderId, $documentSystemId, $envelopType){
        return DocumentAttachments::select('attachmentID', 'attachmentType',
            'parent_id', 'attachmentDescription')
            ->whereHas('tender_document_types', function ($q) use ($doucments){
                $q->whereIn('id',$doucments);
                $q->where('srm_action', 1);
            })
            ->where('documentSystemCode', $tenderId)
            ->where('documentSystemID', $documentSystemId)
            ->where('parent_id', null)
            ->where('envelopType', $envelopType);
    }

    public static function getBidMultipleAttachmentList($id, $documentSystemId, $envelopType, $parentId){
        return DocumentAttachments::with(['bid_verify', 'document_parent'])
            ->where('documentSystemCode', $id)
            ->where('documentSystemID', $documentSystemId)
            ->where('attachmentType',0)
            ->where('envelopType', $envelopType)
            ->where('parent_id',$parentId);
    }
}
