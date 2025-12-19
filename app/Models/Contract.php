<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="Contract",
 *      required={""},
 *      @SWG\Property(
 *          property="contractUID",
 *          description="contractUID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="ContractNumber",
 *          description="ContractNumber",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="companySystemID",
 *          description="companySystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="CompanyID",
 *          description="CompanyID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="clientID",
 *          description="clientID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="CutomerCode",
 *          description="CutomerCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="ServiceLineCode",
 *          description="ServiceLineCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="contractDescription",
 *          description="contractDescription",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="ContCurrencyID",
 *          description="ContCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="contValue",
 *          description="contValue",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="isInitialExtCont",
 *          description="isInitialExtCont",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="LineTechnicalIncharge",
 *          description="LineTechnicalIncharge",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="LineFinanceIncharge",
 *          description="LineFinanceIncharge",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="LineOthersIncharge",
 *          description="LineOthersIncharge",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdPcID",
 *          description="createdPcID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserGroup",
 *          description="createdUserGroup",
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
 *          property="allowMultipleBillingYN",
 *          description="allowMultipleBillingYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isContract",
 *          description="isContract",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="allowRentalWithoutMITyn",
 *          description="allowRentalWithoutMITyn",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="allowEditRentalDes",
 *          description="allowEditRentalDes",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="defaultRateInRental",
 *          description="defaultRateInRental",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="allowEditUOM",
 *          description="allowEditUOM",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="rentalTemplate",
 *          description="rentalTemplate",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="contractType",
 *          description="contractType",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="contractSubType",
 *          description="contractSubType",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="bankID",
 *          description="bankID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="accountID",
 *          description="accountID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="vendonCode",
 *          description="vendonCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="paymentInDaysForJob",
 *          description="paymentInDaysForJob",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="ticketClientSerialStart",
 *          description="ticketClientSerialStart",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="secondaryLogoComp",
 *          description="secondaryLogoComp",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="secondaryLogName",
 *          description="secondaryLogName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="secondaryLogoActive",
 *          description="secondaryLogoActive",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="estRevServiceGLcode",
 *          description="estRevServiceGLcode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="estRevProductGLcode",
 *          description="estRevProductGLcode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isFormulaApplicable",
 *          description="isFormulaApplicable",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="opHrsRounding",
 *          description="opHrsRounding",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="formulaOphrsFromField",
 *          description="formulaOphrsFromField",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="formulaOphrsToField",
 *          description="formulaOphrsToField",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="formulaStandbyField",
 *          description="formulaStandbyField",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isStandByApplicable",
 *          description="isStandByApplicable",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="customerRepName",
 *          description="customerRepName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="customerRepEmail",
 *          description="customerRepEmail",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="showContDetInMOT",
 *          description="showContDetInMOT",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="showContDetInMIT",
 *          description="showContDetInMIT",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="performaTempID",
 *          description="performaTempID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="contInvTemplate",
 *          description="contInvTemplate",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isAllowGenerateTransRental",
 *          description="isAllowGenerateTransRental",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isAllowServiceEntryInPerforma",
 *          description="isAllowServiceEntryInPerforma",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isAllowedDefauldUsage",
 *          description="isAllowedDefauldUsage",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="actionTrackerEnabled",
 *          description="actionTrackerEnabled",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="webTemplate",
 *          description="webTemplate",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isRequiredStamp",
 *          description="isRequiredStamp",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="showSystemNo",
 *          description="showSystemNo",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isAllowedToolsWithoutMOT",
 *          description="isAllowedToolsWithoutMOT",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isDispacthAvailable",
 *          description="isDispacthAvailable",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isRequireAppNewWell",
 *          description="isRequireAppNewWell",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isMorningReportAvailable",
 *          description="isMorningReportAvailable",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isContractActive",
 *          description="isContractActive",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="allowMutipleTicketsInProforma",
 *          description="allowMutipleTicketsInProforma",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isServiceEntryApplicable",
 *          description="isServiceEntryApplicable",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isTicketKPIApplicable",
 *          description="isTicketKPIApplicable",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isTicketTotalApplicable",
 *          description="isTicketTotalApplicable",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isMotAssetDescEditable",
 *          description="isMotAssetDescEditable",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="motTemplate",
 *          description="motTemplate",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="mitTemplate",
 *          description="mitTemplate",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="rentalDates",
 *          description="rentalDates",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="invoiceTemplate",
 *          description="invoiceTemplate",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="rentalSheetTemplate",
 *          description="rentalSheetTemplate",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isRequiredNetworkRefNo",
 *          description="isRequiredNetworkRefNo",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="formulaLocHrsFromField",
 *          description="formulaLocHrsFromField",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="formulaLocHrsToField",
 *          description="formulaLocHrsToField",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isServiceApplicable",
 *          description="isServiceApplicable",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isAllowToEditHours",
 *          description="isAllowToEditHours",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="contractStatus",
 *          description="contractStatus",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="ticketTemplates",
 *          description="ticketTemplates",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="allowOpStdyDaysinMIT",
 *          description="allowOpStdyDaysinMIT",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="motprintTemplate",
 *          description="motprintTemplate",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class Contract extends Model
{

    public $table = 'contractmaster';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $primaryKey = 'contractUID';

    public $fillable = [
        'ContractNumber',
        'companySystemID',
        'CompanyID',
        'clientID',
        'CutomerCode',
        'ServiceLineCode',
        'contractDescription',
        'ContStartDate',
        'ContEndDate',
        'ContCurrencyID',
        'contValue',
        'isInitialExtCont',
        'ContExtUpto',
        'LineTechnicalIncharge',
        'LineFinanceIncharge',
        'LineOthersIncharge',
        'ContractCreatedON',
        'createdPcID',
        'createdUserGroup',
        'createdUserID',
        'createdDateTime',
        'modifiedPc',
        'modifiedUser',
        'allowMultipleBillingYN',
        'isContract',
        'allowRentalWithoutMITyn',
        'allowEditRentalDes',
        'defaultRateInRental',
        'allowEditUOM',
        'rentalTemplate',
        'contractType',
        'contractSubType',
        'bankID',
        'accountID',
        'secondaryBankID',
        'secondaryAccountID',
        'vendonCode',
        'paymentInDaysForJob',
        'ticketClientSerialStart',
        'secondaryLogoComp',
        'secondaryLogName',
        'secondaryLogoActive',
        'estRevServiceGLcode',
        'estRevProductGLcode',
        'isFormulaApplicable',
        'opHrsRounding',
        'formulaOphrsFromField',
        'formulaOphrsToField',
        'formulaStandbyField',
        'isStandByApplicable',
        'customerRepName',
        'customerRepEmail',
        'showContDetInMOT',
        'showContDetInMIT',
        'performaTempID',
        'timeStamp',
        'contInvTemplate',
        'isAllowGenerateTransRental',
        'isAllowServiceEntryInPerforma',
        'isAllowedDefauldUsage',
        'actionTrackerEnabled',
        'webTemplate',
        'isRequiredStamp',
        'showSystemNo',
        'isAllowedToolsWithoutMOT',
        'isDispacthAvailable',
        'isRequireAppNewWell',
        'isMorningReportAvailable',
        'isContractActive',
        'allowMutipleTicketsInProforma',
        'isServiceEntryApplicable',
        'isTicketKPIApplicable',
        'isTicketTotalApplicable',
        'isMotAssetDescEditable',
        'motTemplate',
        'mitTemplate',
        'rentalDates',
        'invoiceTemplate',
        'rentalSheetTemplate',
        'isRequiredNetworkRefNo',
        'formulaLocHrsFromField',
        'formulaLocHrsToField',
        'isServiceApplicable',
        'isAllowToEditHours',
        'contractStatus',
        'ticketTemplates',
        'allowOpStdyDaysinMIT',
        'motprintTemplate'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'contractUID' => 'integer',
        'ContractNumber' => 'string',
        'companySystemID' => 'integer',
        'CompanyID' => 'string',
        'clientID' => 'integer',
        'CutomerCode' => 'string',
        'ServiceLineCode' => 'string',
        'contractDescription' => 'string',
        'ContCurrencyID' => 'integer',
        'contValue' => 'float',
        'isInitialExtCont' => 'integer',
        'LineTechnicalIncharge' => 'string',
        'LineFinanceIncharge' => 'string',
        'LineOthersIncharge' => 'string',
        'createdPcID' => 'string',
        'createdUserGroup' => 'string',
        'createdUserID' => 'string',
        'modifiedPc' => 'string',
        'modifiedUser' => 'string',
        'allowMultipleBillingYN' => 'integer',
        'isContract' => 'integer',
        'allowRentalWithoutMITyn' => 'integer',
        'allowEditRentalDes' => 'integer',
        'defaultRateInRental' => 'integer',
        'allowEditUOM' => 'integer',
        'rentalTemplate' => 'string',
        'contractType' => 'integer',
        'contractSubType' => 'integer',
        'bankID' => 'integer',
        'accountID' => 'integer',
        'vendonCode' => 'string',
        'paymentInDaysForJob' => 'integer',
        'ticketClientSerialStart' => 'integer',
        'secondaryLogoComp' => 'string',
        'secondaryLogName' => 'string',
        'secondaryLogoActive' => 'integer',
        'estRevServiceGLcode' => 'string',
        'estRevProductGLcode' => 'string',
        'isFormulaApplicable' => 'integer',
        'opHrsRounding' => 'integer',
        'formulaOphrsFromField' => 'string',
        'formulaOphrsToField' => 'string',
        'formulaStandbyField' => 'string',
        'isStandByApplicable' => 'integer',
        'customerRepName' => 'string',
        'customerRepEmail' => 'string',
        'showContDetInMOT' => 'integer',
        'showContDetInMIT' => 'integer',
        'performaTempID' => 'integer',
        'contInvTemplate' => 'string',
        'isAllowGenerateTransRental' => 'integer',
        'isAllowServiceEntryInPerforma' => 'integer',
        'isAllowedDefauldUsage' => 'integer',
        'actionTrackerEnabled' => 'integer',
        'webTemplate' => 'string',
        'isRequiredStamp' => 'integer',
        'showSystemNo' => 'integer',
        'isAllowedToolsWithoutMOT' => 'integer',
        'isDispacthAvailable' => 'integer',
        'isRequireAppNewWell' => 'integer',
        'isMorningReportAvailable' => 'integer',
        'isContractActive' => 'integer',
        'allowMutipleTicketsInProforma' => 'integer',
        'isServiceEntryApplicable' => 'integer',
        'isTicketKPIApplicable' => 'integer',
        'isTicketTotalApplicable' => 'integer',
        'isMotAssetDescEditable' => 'integer',
        'motTemplate' => 'string',
        'mitTemplate' => 'string',
        'rentalDates' => 'integer',
        'invoiceTemplate' => 'integer',
        'rentalSheetTemplate' => 'string',
        'isRequiredNetworkRefNo' => 'integer',
        'formulaLocHrsFromField' => 'string',
        'formulaLocHrsToField' => 'string',
        'isServiceApplicable' => 'integer',
        'isAllowToEditHours' => 'integer',
        'contractStatus' => 'integer',
        'ticketTemplates' => 'string',
        'allowOpStdyDaysinMIT' => 'integer',
        'motprintTemplate' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function segment(){
        return $this->belongsTo('App\Models\SegmentMaster','ServiceLineCode','ServiceLineCode');
    }

    public function secondary_bank_account()
    {
        return $this->belongsTo('App\Models\BankAccount', 'secondaryAccountID', 'bankAccountAutoID');
    }
    
}
