<?php
/**
 * =============================================
 * -- File Name : ItemIssueDetails.php
 * -- Project Name : ERP
 * -- Module Name :  Item Issue Details
 * -- Author : Mohamed Fayas
 * -- Create date : 20- June 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="ItemIssueDetails",
 *      required={""},
 *      @SWG\Property(
 *          property="itemIssueDetailID",
 *          description="itemIssueDetailID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="itemIssueAutoID",
 *          description="itemIssueAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="itemIssueCode",
 *          description="itemIssueCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="itemCodeSystem",
 *          description="itemCodeSystem",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="itemPrimaryCode",
 *          description="itemPrimaryCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="itemDescription",
 *          description="itemDescription",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="itemUnitOfMeasure",
 *          description="itemUnitOfMeasure",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="unitOfMeasureIssued",
 *          description="unitOfMeasureIssued",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="clientReferenceNumber",
 *          description="clientReferenceNumber",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="qtyRequested",
 *          description="qtyRequested",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="qtyIssued",
 *          description="qtyIssued",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="comments",
 *          description="comments",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="convertionMeasureVal",
 *          description="convertionMeasureVal",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="qtyIssuedDefaultMeasure",
 *          description="qtyIssuedDefaultMeasure",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="localCurrencyID",
 *          description="localCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="issueCostLocal",
 *          description="issueCostLocal",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="issueCostLocalTotal",
 *          description="issueCostLocalTotal",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="reportingCurrencyID",
 *          description="reportingCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="issueCostRpt",
 *          description="issueCostRpt",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="issueCostRptTotal",
 *          description="issueCostRptTotal",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="currentStockQty",
 *          description="currentStockQty",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="currentWareHouseStockQty",
 *          description="currentWareHouseStockQty",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="currentStockQtyInDamageReturn",
 *          description="currentStockQtyInDamageReturn",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="maxQty",
 *          description="maxQty",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="minQty",
 *          description="minQty",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="selectedForBillingOP",
 *          description="selectedForBillingOP",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="selectedForBillingOPtemp",
 *          description="selectedForBillingOPtemp",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="opTicketNo",
 *          description="opTicketNo",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="del",
 *          description="del",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="backLoad",
 *          description="backLoad",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="used",
 *          description="used",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="grvDocumentNO",
 *          description="grvDocumentNO",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="itemFinanceCategoryID",
 *          description="itemFinanceCategoryID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="itemFinanceCategorySubID",
 *          description="itemFinanceCategorySubID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="financeGLcodebBSSystemID",
 *          description="financeGLcodebBSSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="financeGLcodebBS",
 *          description="financeGLcodebBS",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="financeGLcodePLSystemID",
 *          description="financeGLcodePLSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="financeGLcodePL",
 *          description="financeGLcodePL",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="includePLForGRVYN",
 *          description="includePLForGRVYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="p1",
 *          description="p1",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="p2",
 *          description="p2",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="p3",
 *          description="p3",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="p4",
 *          description="p4",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="p5",
 *          description="p5",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="p6",
 *          description="p6",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="p7",
 *          description="p7",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="p8",
 *          description="p8",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="p9",
 *          description="p9",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="p10",
 *          description="p10",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="p11",
 *          description="p11",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="p12",
 *          description="p12",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="p13",
 *          description="p13",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="pl3",
 *          description="pl3",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class ItemIssueDetails extends Model
{

    public $table = 'erp_itemissuedetails';
    
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';
    protected $primaryKey  = 'itemIssueDetailID';


    public $fillable = [
        'itemIssueAutoID',
        'itemIssueCode',
        'itemCodeSystem',
        'itemPrimaryCode',
        'itemDescription',
        'itemUnitOfMeasure',
        'unitOfMeasureIssued',
        'clientReferenceNumber',
        'qtyRequested',
        'qtyIssued',
        'trackingType',
        'comments',
        'convertionMeasureVal',
        'qtyIssuedDefaultMeasure',
        'localCurrencyID',
        'issueCostLocal',
        'issueCostLocalTotal',
        'reportingCurrencyID',
        'issueCostRpt',
        'issueCostRptTotal',
        'currentStockQty',
        'currentWareHouseStockQty',
        'currentStockQtyInDamageReturn',
        'maxQty',
        'minQty',
        'selectedForBillingOP',
        'selectedForBillingOPtemp',
        'opTicketNo',
        'del',
        'backLoad',
        'used',
        'grvDocumentNO',
        'itemFinanceCategoryID',
        'itemFinanceCategorySubID',
        'financeGLcodebBSSystemID',
        'financeGLcodebBS',
        'financeGLcodePLSystemID',
        'financeGLcodePL',
        'includePLForGRVYN',
        'p1',
        'p2',
        'p3',
        'p4',
        'p5',
        'p6',
        'p7',
        'p8',
        'p9',
        'p10',
        'p11',
        'p12',
        'p13',
        'pl10',
        'pl3',
        'deliveryComments',
        'timestamp',
        'timesReferred',
        'detail_project_id',
        'qtyAvailableToIssue',
        'reqDocID'
    ];
    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'itemIssueDetailID' => 'integer',
        'itemIssueAutoID' => 'integer',
        'itemIssueCode' => 'string',
        'itemCodeSystem' => 'integer',
        'itemPrimaryCode' => 'string',
        'itemDescription' => 'string',
        'itemUnitOfMeasure' => 'integer',
        'unitOfMeasureIssued' => 'integer',
        'trackingType' => 'integer',
        'clientReferenceNumber' => 'string',
        'qtyRequested' => 'float',
        'qtyIssued' => 'float',
        'comments' => 'string',
        'convertionMeasureVal' => 'float',
        'qtyIssuedDefaultMeasure' => 'float',
        'localCurrencyID' => 'integer',
        'issueCostLocal' => 'float',
        'issueCostLocalTotal' => 'float',
        'reportingCurrencyID' => 'integer',
        'issueCostRpt' => 'float',
        'issueCostRptTotal' => 'float',
        'currentStockQty' => 'float',
        'currentWareHouseStockQty' => 'float',
        'currentStockQtyInDamageReturn' => 'float',
        'maxQty' => 'float',
        'minQty' => 'float',
        'selectedForBillingOP' => 'integer',
        'selectedForBillingOPtemp' => 'integer',
        'opTicketNo' => 'integer',
        'del' => 'integer',
        'backLoad' => 'integer',
        'used' => 'integer',
        'grvDocumentNO' => 'string',
        'itemFinanceCategoryID' => 'integer',
        'itemFinanceCategorySubID' => 'integer',
        'financeGLcodebBSSystemID' => 'integer',
        'financeGLcodebBS' => 'string',
        'financeGLcodePLSystemID' => 'integer',
        'financeGLcodePL' => 'string',
        'includePLForGRVYN' => 'integer',
        'p1' => 'string',
        'p2' => 'integer',
        'p3' => 'integer',
        'p4' => 'integer',
        'p5' => 'integer',
        'p6' => 'integer',
        'p7' => 'integer',
        'p8' => 'integer',
        'p9' => 'integer',
        'p10' => 'integer',
        'p11' => 'integer',
        'p12' => 'integer',
        'p13' => 'integer',
        'pl3' => 'integer',
        'pl10' => 'string',
        'deliveryComments' => 'string',
        'timesReferred' => 'integer',
        'detail_project_id' => 'integer',
        'qtyAvailableToIssue' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function uom_default(){
        return $this->belongsTo('App\Models\Unit','itemUnitOfMeasure','UnitID');
    }

    public function uom_issuing(){
        return $this->belongsTo('App\Models\Unit','unitOfMeasureIssued','UnitID');
    }

    public function item_by(){
        return $this->belongsTo('App\Models\ItemMaster','itemCodeSystem','itemCodeSystem');
    }

    public function master(){
        return $this->belongsTo('App\Models\ItemIssueMaster','itemIssueAutoID','itemIssueAutoID');
    }

    public function allocate_employees(){
        return $this->hasMany('App\Models\ExpenseEmployeeAllocation','documentDetailID','itemIssueDetailID');
    }

    public function reportingCurrency() {
        return $this->belongsTo('App\Models\CurrencyMaster', 'reportingCurrencyID', 'currencyID');
    }

    
}
