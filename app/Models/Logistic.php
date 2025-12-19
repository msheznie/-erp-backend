<?php
/**
 * =============================================
 * -- File Name : Logistic.php
 * -- Project Name : ERP
 * -- Module Name :  Logistic
 * -- Author : Mohamed Fayas
 * -- Create date : 20- June 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="Logistic",
 *      required={""},
 *      @SWG\Property(
 *          property="logisticMasterID",
 *          description="logisticMasterID",
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
 *          property="serviceLineSystemID",
 *          description="serviceLineSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="serviceLineID",
 *          description="serviceLineID",
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
 *          property="serialNo",
 *          description="serialNo",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="logisticDocCode",
 *          description="logisticDocCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="comments",
 *          description="comments",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="supplierID",
 *          description="supplierID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="logisticShippingModeID",
 *          description="logisticShippingModeID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="modeOfImportID",
 *          description="modeOfImportID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="customDocRenewalHistory",
 *          description="customDocRenewalHistory",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="customInvoiceNo",
 *          description="customInvoiceNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="customInvoiceCurrencyID",
 *          description="customInvoiceCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="customInvoiceAmount",
 *          description="customInvoiceAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="customInvoiceLocalCurrencyID",
 *          description="customInvoiceLocalCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="customInvoiceLocalER",
 *          description="customInvoiceLocalER",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="customInvoiceLocalAmount",
 *          description="customInvoiceLocalAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="customInvoiceRptCurrencyID",
 *          description="customInvoiceRptCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="customInvoiceRptER",
 *          description="customInvoiceRptER",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="customInvoiceRptAmount",
 *          description="customInvoiceRptAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="airwayBillNo",
 *          description="airwayBillNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="totalWeight",
 *          description="totalWeight",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="totalWeightUOM",
 *          description="totalWeightUOM",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="totalVolume",
 *          description="totalVolume",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="totalVolumeUOM",
 *          description="totalVolumeUOM",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="billofEntryNo",
 *          description="billofEntryNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="agentDeliveryLocationID",
 *          description="agentDeliveryLocationID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="agentDOnumber",
 *          description="agentDOnumber",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="agentID",
 *          description="agentID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="agentFeeCurrencyID",
 *          description="agentFeeCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="agentFee",
 *          description="agentFee",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="agentFeeLocalAmount",
 *          description="agentFeeLocalAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="agenFeeRptAmount",
 *          description="agenFeeRptAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="customDutyFeeCurrencyID",
 *          description="customDutyFeeCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="customDutyFeeAmount",
 *          description="customDutyFeeAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="customDutyFeeLocalAmount",
 *          description="customDutyFeeLocalAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="customDutyFeeRptAmount",
 *          description="customDutyFeeRptAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="customDutyTotalAmount",
 *          description="customDutyTotalAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="shippingOriginPort",
 *          description="shippingOriginPort",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="shippingOriginCountry",
 *          description="shippingOriginCountry",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="shippingDestinationPort",
 *          description="shippingDestinationPort",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="shippingDestinationCountry",
 *          description="shippingDestinationCountry",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="ftaOrDF",
 *          description="ftaOrDF",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdUserID",
 *          description="createdUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserSystemID",
 *          description="createdUserSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdPCid",
 *          description="createdPCid",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserID",
 *          description="modifiedUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserSystemID",
 *          description="modifiedUserSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="modifiedPCID",
 *          description="modifiedPCID",
 *          type="string"
 *      )
 * )
 */
class Logistic extends Model
{

    public $table = 'erp_logisticmaster';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timestamp';
    protected $primaryKey  = 'logisticMasterID';


    public $fillable = [
        'companySystemID',
        'companyID',
        'serviceLineSystemID',
        'serviceLineID',
        'documentSystemID',
        'documentID',
        'serialNo',
        'logisticDocCode',
        'comments',
        'supplierID',
        'logisticShippingModeID',
        'modeOfImportID',
        'nextCustomDocRenewalDate',
        'customDocRenewalHistory',
        'customInvoiceNo',
        'customInvoiceDate',
        'customInvoiceCurrencyID',
        'customInvoiceAmount',
        'customInvoiceLocalCurrencyID',
        'customInvoiceLocalER',
        'customInvoiceLocalAmount',
        'customInvoiceRptCurrencyID',
        'customInvoiceRptER',
        'customInvoiceRptAmount',
        'airwayBillNo',
        'totalWeight',
        'totalWeightUOM',
        'totalVolume',
        'totalVolumeUOM',
        'customeArrivalDate',
        'deliveryDate',
        'billofEntryDate',
        'billofEntryNo',
        'agentDeliveryLocationID',
        'agentDOnumber',
        'agentDOdate',
        'agentID',
        'agentFeeCurrencyID',
        'agentFee',
        'agentFeeLocalAmount',
        'agenFeeRptAmount',
        'customDutyFeeCurrencyID',
        'customDutyFeeAmount',
        'customDutyFeeLocalAmount',
        'customDutyFeeRptAmount',
        'customDutyTotalAmount',
        'shippingOriginPort',
        'shippingOriginCountry',
        'shippingOriginDate',
        'shippingDestinationPort',
        'shippingDestinationCountry',
        'shippingDestinationDate',
        'ftaOrDF',
        'createdUserID',
        'createdUserSystemID',
        'createdPCid',
        'createdDateTime',
        'modifiedUserID',
        'modifiedUserSystemID',
        'modifiedPCID',
        'modifiedDate',
        'timestamp',
        'isDelegation'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'logisticMasterID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'serviceLineSystemID' => 'integer',
        'serviceLineID' => 'string',
        'documentSystemID' => 'integer',
        'documentID' => 'string',
        'serialNo' => 'integer',
        'logisticDocCode' => 'string',
        'comments' => 'string',
        'supplierID' => 'integer',
        'logisticShippingModeID' => 'integer',
        'modeOfImportID' => 'integer',
        'customDocRenewalHistory' => 'string',
        'customInvoiceNo' => 'string',
        'customInvoiceCurrencyID' => 'integer',
        'customInvoiceAmount' => 'float',
        'customInvoiceLocalCurrencyID' => 'integer',
        'customInvoiceLocalER' => 'float',
        'customInvoiceLocalAmount' => 'float',
        'customInvoiceRptCurrencyID' => 'integer',
        'customInvoiceRptER' => 'float',
        'customInvoiceRptAmount' => 'float',
        'airwayBillNo' => 'string',
        'totalWeight' => 'float',
        'totalWeightUOM' => 'integer',
        'totalVolume' => 'float',
        'totalVolumeUOM' => 'integer',
        'billofEntryNo' => 'string',
        'agentDeliveryLocationID' => 'integer',
        'agentDOnumber' => 'string',
        'agentID' => 'integer',
        'agentFeeCurrencyID' => 'integer',
        'agentFee' => 'float',
        'agentFeeLocalAmount' => 'float',
        'agenFeeRptAmount' => 'float',
        'customDutyFeeCurrencyID' => 'integer',
        'customDutyFeeAmount' => 'float',
        'customDutyFeeLocalAmount' => 'float',
        'customDutyFeeRptAmount' => 'float',
        'customDutyTotalAmount' => 'float',
        'shippingOriginPort' => 'string',
        'shippingOriginCountry' => 'string',
        'shippingDestinationPort' => 'string',
        'shippingDestinationCountry' => 'string',
        'ftaOrDF' => 'integer',
        'createdUserID' => 'string',
        'createdUserSystemID' => 'integer',
        'createdPCid' => 'string',
        'modifiedUserID' => 'string',
        'modifiedUserSystemID' => 'integer',
        'modifiedPCID' => 'string'
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

    public function modified_by()
    {
        return $this->belongsTo('App\Models\Employee', 'modifiedUserSystemID', 'employeeSystemID');
    }

    public function supplier_by()
    {
        return $this->belongsTo('App\Models\SupplierMaster', 'supplierID', 'supplierCodeSystem');
    }

    public function shipping_mode()
    {
        return $this->belongsTo('App\Models\LogisticShippingMode', 'logisticShippingModeID', 'logisticShippingModeID');
    }

    public function company(){
        return $this->belongsTo('App\Models\Company','companySystemID','companySystemID');
    }

    public function details()
    {
        return $this->hasMany('App\Models\LogisticDetails','logisticMasterID','logisticMasterID');
    }

    public function local_currency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'customInvoiceLocalCurrencyID','currencyID');
    }

    public function reporting_currency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'customInvoiceRptCurrencyID','currencyID');
    }

    public function location()
    {
        return $this->belongsTo('App\Models\WarehouseMaster','agentDeliveryLocationID','wareHouseSystemCode');
    }

}
