<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="QuotationDetails",
 *      required={""},
 *      @SWG\Property(
 *          property="quotationDetailsID",
 *          description="quotationDetailsID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="quotationMasterID",
 *          description="quotationMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="itemAutoID",
 *          description="itemAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="itemSystemCode",
 *          description="itemSystemCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="itemDescription",
 *          description="itemDescription",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="itemCategory",
 *          description="itemCategory",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="defaultUOMID",
 *          description="defaultUOMID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="itemReferenceNo",
 *          description="itemReferenceNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="defaultUOM",
 *          description="defaultUOM",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="unitOfMeasureID",
 *          description="unitOfMeasureID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="unitOfMeasure",
 *          description="unitOfMeasure",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="conversionRateUOM",
 *          description="conversionRateUOM",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="requestedQty",
 *          description="requestedQty",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="invoicedYN",
 *          description="invoicedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="comment",
 *          description="comment",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="remarks",
 *          description="remarks",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="unittransactionAmount",
 *          description="unittransactionAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="discountPercentage",
 *          description="discountPercentage",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="discountAmount",
 *          description="discountAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="discountTotal",
 *          description="discountTotal",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="transactionAmount",
 *          description="transactionAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="companyLocalAmount",
 *          description="companyLocalAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="companyReportingAmount",
 *          description="companyReportingAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="customerAmount",
 *          description="customerAmount",
 *          type="number",
 *          format="float"
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
 *          property="createdUserGroup",
 *          description="createdUserGroup",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdPCID",
 *          description="createdPCID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserID",
 *          description="createdUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserName",
 *          description="createdUserName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedPCID",
 *          description="modifiedPCID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserID",
 *          description="modifiedUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserName",
 *          description="modifiedUserName",
 *          type="string"
 *      )
 * )
 */
class QuotationDetails extends Model
{

    public $table = 'erp_quotationdetails';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timestamp';

    protected $primaryKey = 'quotationDetailsID';

    public $fillable = [
        'quotationMasterID',
        'itemAutoID',
        'itemSystemCode',
        'itemDescription',
        'itemCategory',
        'defaultUOMID',
        'soQuotationDetailID',
        'itemReferenceNo',
        'defaultUOM',
        'unitOfMeasureID',
        'unitOfMeasure',
        'conversionRateUOM',
        'requestedQty',
        'invoicedYN',
        'comment',
        'remarks',
        'unittransactionAmount',
        'discountPercentage',
        'discountAmount',
        'discountTotal',
        'transactionAmount',
        'companyLocalAmount',
        'companyReportingAmount',
        'wacValueLocal',
        'wacValueReporting',
        'customerAmount',
        'companySystemID',
        'companyID',
        'createdUserGroup',
        'createdPCID',
        'createdUserID',
        'createdDateTime',
        'createdUserName',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedDateTime',
        'modifiedUserName',
        'timesReferred',
        'fullyOrdered',
        'doQuantity',
        'qtyIssuedDefaultMeasure',
        'soQuantity',
        'soQuotationMasterID',
        'timestamp',
        'VATPercentage',
        'VATAmount',
        'VATAmountLocal',
        'VATAmountRpt',
        'vatMasterCategoryID',
        'vatSubCategoryID',
        'VATApplicableOn',
        'userQty',
        'totalSoBalanceQty'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'quotationDetailsID' => 'integer',
        'quotationMasterID' => 'integer',
        'soQuotationDetailID' => 'integer',
        'vatMasterCategoryID' => 'integer',
        'vatSubCategoryID' => 'integer',
        'itemAutoID' => 'integer',
        'itemSystemCode' => 'string',
        'itemDescription' => 'string',
        'itemCategory' => 'string',
        'defaultUOMID' => 'integer',
        'itemReferenceNo' => 'string',
        'defaultUOM' => 'string',
        'unitOfMeasureID' => 'integer',
        'unitOfMeasure' => 'string',
        'conversionRateUOM' => 'float',
        'requestedQty' => 'float',
        'invoicedYN' => 'integer',
        'comment' => 'string',
        'remarks' => 'string',
        'unittransactionAmount' => 'float',
        'discountPercentage' => 'float',
        'discountAmount' => 'float',
        'discountTotal' => 'float',
        'transactionAmount' => 'float',
        'companyLocalAmount' => 'float',
        'companyReportingAmount' => 'float',
        'wacValueLocal' => 'float',
        'wacValueReporting' => 'float',
        'customerAmount' => 'float',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'createdUserGroup' => 'integer',
        'createdPCID' => 'string',
        'createdUserID' => 'string',
        'createdUserName' => 'string',
        'modifiedPCID' => 'string',
        'modifiedUserID' => 'string',
        'modifiedUserName' => 'string',
        'timesReferred' => 'integer',
        'fullyOrdered' => 'integer',
        'soQuotationMasterID' => 'integer',
        'doQuantity' => 'float',
        'qtyIssuedDefaultMeasure' => 'float',
        'soQuantity' => 'float',
        'VATPercentage' => 'float',
        'VATAmount' => 'float',
        'VATAmountLocal' => 'float',
        'VATAmountRpt' => 'float',
        'VATApplicableOn' => 'integer',
        'userQty' => 'float',
        'totalSoBalanceQty' => 'float'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function invoice_detail() {
        return $this->hasMany('App\Models\CustomerInvoiceItemDetails','quotationDetailsID','quotationDetailsID');
    }

    public function delivery_order_detail(){
        return $this->hasMany('App\Models\DeliveryOrderDetail','quotationDetailsID','quotationDetailsID');
    }

    public function sales_order_detail(){
        return $this->belongsTo('App\Models\QuotationDetails','soQuotationDetailID','quotationDetailsID');
    }

    public function item() {
        return $this->belongsTo('App\Models\ItemMaster','itemAutoID','itemCodeSystem');

    }


     public function uom_issuing(){
        return $this->belongsTo('App\Models\Unit','unitOfMeasureID','UnitID');
    }
    
    public function master(){
        return $this->belongsTo('App\Models\QuotationMaster','quotationMasterID','quotationMasterID');
    }
    
}
