<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="FreeBilling",
 *      required={""},
 *      @SWG\Property(
 *          property="idBillingNO",
 *          description="idBillingNO",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="billProcessNo",
 *          description="billProcessNo",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="TicketNo",
 *          description="TicketNo",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="motID",
 *          description="motID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="mitID",
 *          description="mitID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="AssetUnitID",
 *          description="AssetUnitID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="assetSerialNo",
 *          description="assetSerialNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="unitID",
 *          description="unitID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="rateCurrencyID",
 *          description="rateCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="StandardTimeOnLoc",
 *          description="StandardTimeOnLoc",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="StandardTimeOnLocInitial",
 *          description="StandardTimeOnLocInitial",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="standardRate",
 *          description="standardRate",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="operationTimeOnLoc",
 *          description="operationTimeOnLoc",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="operationTimeOnLocInitial",
 *          description="operationTimeOnLocInitial",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="operationRate",
 *          description="operationRate",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="UsageTimeOnLoc",
 *          description="UsageTimeOnLoc",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="UsageTimeOnLocInitial",
 *          description="UsageTimeOnLocInitial",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="usageRate",
 *          description="usageRate",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="lostInHoleYN",
 *          description="lostInHoleYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="lostInHoleYNinitial",
 *          description="lostInHoleYNinitial",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="lostInHoleRate",
 *          description="lostInHoleRate",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="dbrYN",
 *          description="dbrYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="dbrYNinitial",
 *          description="dbrYNinitial",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="dbrRate",
 *          description="dbrRate",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="performaInvoiceNo",
 *          description="performaInvoiceNo",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="InvoiceNo",
 *          description="InvoiceNo",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="usedYN",
 *          description="usedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="usedYNinitial",
 *          description="usedYNinitial",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="ContractDetailID",
 *          description="ContractDetailID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="lihInspectionStartedYN",
 *          description="lihInspectionStartedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="dbrInspectionStartedYN",
 *          description="dbrInspectionStartedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="mitQty",
 *          description="mitQty",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="assetDescription",
 *          description="assetDescription",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="assetDescriptionAmend",
 *          description="assetDescriptionAmend",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="amendHistory",
 *          description="amendHistory",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="stdGLcode",
 *          description="stdGLcode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="operatingGLcode",
 *          description="operatingGLcode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="usageGLcode",
 *          description="usageGLcode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="lihGLcode",
 *          description="lihGLcode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="dbrGLcode",
 *          description="dbrGLcode",
 *          type="string"
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
 *          property="qtyServiceProduct",
 *          description="qtyServiceProduct",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="opPerformaCaptionLink",
 *          description="opPerformaCaptionLink",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="unitOP",
 *          description="unitOP",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="unitUsage",
 *          description="unitUsage",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="unitLIH",
 *          description="unitLIH",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="unitDBR",
 *          description="unitDBR",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyID",
 *          description="companyID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="serviceLine",
 *          description="serviceLine",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="UsageLinkID",
 *          description="UsageLinkID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="subContDetID",
 *          description="subContDetID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="subContDetails",
 *          description="subContDetails",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="usageType",
 *          description="usageType",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="usageTypeDes",
 *          description="usageTypeDes",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="ticketDetDes",
 *          description="ticketDetDes",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="groupOnRptYN",
 *          description="groupOnRptYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isConsumable",
 *          description="isConsumable",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="motDetailID",
 *          description="motDetailID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="freeBillingComment",
 *          description="freeBillingComment",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="StbHrRate",
 *          description="StbHrRate",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="OpHrRate",
 *          description="OpHrRate",
 *          type="number",
 *          format="float"
 *      )
 * )
 */
class FreeBilling extends Model
{

    public $table = 'freebilling';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $primaryKey  = 'idBillingNO';

    public $fillable = [
        'billProcessNo',
        'TicketNo',
        'motID',
        'mitID',
        'AssetUnitID',
        'assetSerialNo',
        'unitID',
        'rateCurrencyID',
        'StandardTimeOnLoc',
        'StandardTimeOnLocInitial',
        'standardRate',
        'operationTimeOnLoc',
        'operationTimeOnLocInitial',
        'operationRate',
        'UsageTimeOnLoc',
        'UsageTimeOnLocInitial',
        'usageRate',
        'lostInHoleYN',
        'lostInHoleYNinitial',
        'lostInHoleRate',
        'lihDate',
        'dbrYN',
        'dbrYNinitial',
        'dbrRate',
        'performaInvoiceNo',
        'InvoiceNo',
        'usedYN',
        'usedYNinitial',
        'ContractDetailID',
        'lihInspectionStartedYN',
        'dbrInspectionStartedYN',
        'mitQty',
        'assetDescription',
        'motDate',
        'mitDate',
        'rentalStartDate',
        'rentalEndDate',
        'assetDescriptionAmend',
        'amendHistory',
        'stdGLcode',
        'operatingGLcode',
        'usageGLcode',
        'lihGLcode',
        'dbrGLcode',
        'createdUserGroup',
        'createdPcID',
        'createdUserID',
        'modifiedPc',
        'modifiedUser',
        'createdDateTime',
        'qtyServiceProduct',
        'opPerformaCaptionLink',
        'timeStamp',
        'unitOP',
        'unitUsage',
        'unitLIH',
        'unitDBR',
        'companyID',
        'serviceLine',
        'UsageLinkID',
        'subContDetID',
        'subContDetails',
        'usageType',
        'usageTypeDes',
        'ticketDetDes',
        'groupOnRptYN',
        'isConsumable',
        'motDetailID',
        'freeBillingComment',
        'StbHrRate',
        'OpHrRate'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'idBillingNO' => 'integer',
        'billProcessNo' => 'integer',
        'TicketNo' => 'integer',
        'motID' => 'integer',
        'mitID' => 'integer',
        'AssetUnitID' => 'integer',
        'assetSerialNo' => 'string',
        'unitID' => 'integer',
        'rateCurrencyID' => 'integer',
        'StandardTimeOnLoc' => 'float',
        'StandardTimeOnLocInitial' => 'float',
        'standardRate' => 'float',
        'operationTimeOnLoc' => 'float',
        'operationTimeOnLocInitial' => 'float',
        'operationRate' => 'float',
        'UsageTimeOnLoc' => 'float',
        'UsageTimeOnLocInitial' => 'float',
        'usageRate' => 'float',
        'lostInHoleYN' => 'integer',
        'lostInHoleYNinitial' => 'integer',
        'lostInHoleRate' => 'float',
        'dbrYN' => 'integer',
        'dbrYNinitial' => 'integer',
        'dbrRate' => 'float',
        'performaInvoiceNo' => 'integer',
        'InvoiceNo' => 'integer',
        'usedYN' => 'integer',
        'usedYNinitial' => 'integer',
        'ContractDetailID' => 'integer',
        'lihInspectionStartedYN' => 'integer',
        'dbrInspectionStartedYN' => 'integer',
        'mitQty' => 'float',
        'assetDescription' => 'string',
        'assetDescriptionAmend' => 'string',
        'amendHistory' => 'string',
        'stdGLcode' => 'string',
        'operatingGLcode' => 'string',
        'usageGLcode' => 'string',
        'lihGLcode' => 'string',
        'dbrGLcode' => 'string',
        'createdUserGroup' => 'string',
        'createdPcID' => 'string',
        'createdUserID' => 'string',
        'modifiedPc' => 'string',
        'modifiedUser' => 'string',
        'qtyServiceProduct' => 'float',
        'opPerformaCaptionLink' => 'integer',
        'unitOP' => 'integer',
        'unitUsage' => 'integer',
        'unitLIH' => 'integer',
        'unitDBR' => 'integer',
        'companyID' => 'string',
        'serviceLine' => 'string',
        'UsageLinkID' => 'integer',
        'subContDetID' => 'integer',
        'subContDetails' => 'string',
        'usageType' => 'integer',
        'usageTypeDes' => 'string',
        'ticketDetDes' => 'string',
        'groupOnRptYN' => 'integer',
        'isConsumable' => 'integer',
        'motDetailID' => 'integer',
        'freeBillingComment' => 'string',
        'StbHrRate' => 'float',
        'OpHrRate' => 'float'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
