<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="POSStagMenuSalesMaster",
 *      required={""},
 *      @SWG\Property(
 *          property="menuSalesID",
 *          description="menuSalesID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="invoiceSequenceNo",
 *          description="invoiceSequenceNo",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="invoiceCode",
 *          description="invoiceCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="wareHouseAutoID",
 *          description="wareHouseAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentCode",
 *          description="documentCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="serialNo",
 *          description="serialNo",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="customerName",
 *          description="customerName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="customerTelephone",
 *          description="customerTelephone",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="customerTypeID",
 *          description="FK of customer type master",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="customerID",
 *          description="customerID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="customerCode",
 *          description="customerCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="deliveryPersonID",
 *          description="deliveryPersonID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="deliveryCommission",
 *          description="Percentage",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="deliveryCommissionAmount",
 *          description="deliveryCommissionAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="preparationTime",
 *          description="preparationTime",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="KOTStartDateTime",
 *          description="KOTStartDateTime",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="KOTEndDateTime",
 *          description="KOTEndDateTime",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="KOTDoneByEmpID",
 *          description="KOTDoneByEmpID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="wowFoodYN",
 *          description="0 - No 1 - Yes ",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="wowFoodStatus",
 *          description="0 - Order Placed 1- Being Prepared 2 - Dispatched 3 - Delivered",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="wowFoodCustomerName",
 *          description="wowFoodCustomerName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="wowFoodCustomerTel",
 *          description="wowFoodCustomerTel",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="customerCommission",
 *          description="Percentage",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="counterID",
 *          description="counterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="shiftID",
 *          description="shiftID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="menuSalesDate",
 *          description="menuSalesDate",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="menuCost",
 *          description="actual cost from menu master",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="totalQty",
 *          description="totalQty",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="subTotal",
 *          description="subTotal",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="grossTotal",
 *          description="total without discount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="grossAmount",
 *          description="grossAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="discountPer",
 *          description="discountPer",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="discountAmount",
 *          description="discountAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="totalTaxPercentage",
 *          description="totalTaxPercentage",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="totalTaxAmount",
 *          description="totalTaxAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="serviceCharge",
 *          description="serviceCharge",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="netTotal",
 *          description="netTotal",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="netRevenueTotal",
 *          description="netRevenueTotal",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="paidAmount",
 *          description="actually paid by customer",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="balanceAmount",
 *          description="balanceAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="cashReceivedAmount",
 *          description="actually customer paid by cash there will be return",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="cashAmount",
 *          description="cashAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="chequeAmount",
 *          description="chequeAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="chequeNo",
 *          description="chequeNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="chequeDate",
 *          description="chequeDate",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="cardAmount",
 *          description="cardAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="creditNoteID",
 *          description="creditNoteID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="creditNoteAmount",
 *          description="creditNoteAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="giftCardID",
 *          description="giftCardID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="giftCardAmount",
 *          description="giftCardAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="cardNumber",
 *          description="cardNumber",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="cardRefNo",
 *          description="cardRefNo",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="cardBank",
 *          description="cardBank",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="paymentMethod",
 *          description="1-card , 2-visa card, 3-master, 4-check ",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isHold",
 *          description="isHold",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="holdByUserID",
 *          description="holdByUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="holdByUsername",
 *          description="holdByUsername",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="holdPC",
 *          description="holdPC",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="holdDatetime",
 *          description="holdDatetime",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="holdRemarks",
 *          description="holdRemarks",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="transactionCurrencyID",
 *          description="transactionCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="transactionCurrency",
 *          description="Document transaction currency",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="transactionExchangeRate",
 *          description="Always 1",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="transactionCurrencyDecimalPlaces",
 *          description="Decimal places of transaction currency ",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyLocalCurrencyID",
 *          description="companyLocalCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyLocalCurrency",
 *          description="Local currency of company in company master",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="companyLocalExchangeRate",
 *          description="Exchange rate against transaction currency",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="companyLocalCurrencyDecimalPlaces",
 *          description="Decimal places of company currency",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyReportingCurrencyID",
 *          description="companyReportingCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyReportingCurrency",
 *          description="Reporting currency of company in company master",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="companyReportingExchangeRate",
 *          description="Exchange rate against transaction currency ",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="companyReportingCurrencyDecimalPlaces",
 *          description="Decimal places of company currency",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="customerCurrencyID",
 *          description="customerCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="customerCurrency",
 *          description="Default currency of supplier ",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="customerCurrencyExchangeRate",
 *          description="Exchange rate against transaction currency",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="customerCurrencyAmount",
 *          description="Transaction amount in supplier currency ",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="customerCurrencyDecimalPlaces",
 *          description="Decimal places of Supplier currency",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="segmentID",
 *          description="segmentID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="segmentCode",
 *          description="segmentCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="companyID",
 *          description="companyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyCode",
 *          description="companyCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="customerReceivableAutoID",
 *          description="customerReceivableAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="commissionGLAutoID",
 *          description="commissionGLAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="commisionLiabilityGLAutoID",
 *          description="commisionLiabilityGLAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isOnTimeCommision",
 *          description="isOnTimeCommision",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="bankGLAutoID",
 *          description="bankGLAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="bankCurrencyID",
 *          description="bankCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="bankCurrency",
 *          description="Document transaction currency",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="bankCurrencyExchangeRate",
 *          description="Always 1",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="bankCurrencyDecimalPlaces",
 *          description="Decimal places of transaction currency ",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="bankCurrencyAmount",
 *          description="bankCurrencyAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="salesDay",
 *          description="salesDay",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="salesDayNum",
 *          description="salesDayNum",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isOrderPending",
 *          description="1 - pending , 0 not pending ",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isOrderInProgress",
 *          description="1 - on going/cooking , 0 not taken yet",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isOrderCompleted",
 *          description="1 ready to deliver , 0 not ready",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="tableID",
 *          description="tableID",
 *          type="integer",
 *          format="int32"
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
 *          property="createdDateTime",
 *          description="createdDateTime",
 *          type="string",
 *          format="date-time"
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
 *      ),
 *      @SWG\Property(
 *          property="modifiedDateTime",
 *          description="modifiedDateTime",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="timestamp",
 *          description="timestamp",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="is_sync",
 *          description="is_sync",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="id_store",
 *          description="id_store",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isVoid",
 *          description="0 - not void, 1 canceled",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="voidBy",
 *          description="bill cancelled by",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="voidDatetime",
 *          description="voidDatetime",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="isCreditSales",
 *          description="0 - not credit sales , 1 - credit sales.",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentMasterAutoID",
 *          description="Customer invoice master autoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentSystemCode",
 *          description="documentSystemCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isDelivery",
 *          description="isDelivery",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isPromotion",
 *          description="isPromotion",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="promotionID",
 *          description="promotionID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="promotionDiscount",
 *          description="promotionDiscount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="KOTAlarm",
 *          description="KOTAlarm",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="promotionDiscountAmount",
 *          description="promotionDiscountAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="tabUserID",
 *          description="tabUserID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="waiterID",
 *          description="crew ID ",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="numberOfPacks",
 *          description="numberOfPacks",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="BOT",
 *          description="BOT is used in the Tablet window ",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="BOTCreatedUser",
 *          description="BOTCreatedUser",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="BOTCreatedDatetime",
 *          description="BOTCreatedDatetime",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="isUpdated",
 *          description="0 - No 1 Yes ",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="issueType",
 *          description="issueType",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isFromTablet",
 *          description="if the record created from tablet window",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="deliveryRevenueGLID",
 *          description="deliveryRevenueGLID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="ownDeliveryPercentage",
 *          description="ownDeliveryPercentage",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="ownDeliveryAmount",
 *          description="ownDeliveryAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="isCancelled",
 *          description="isCancelled",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="transaction_log_id",
 *          description="transaction_log_id",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class POSStagMenuSalesMaster extends Model
{

    public $table = 'pos_stag_menusalesmaster';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'invoiceSequenceNo',
        'invoiceCode',
        'wareHouseAutoID',
        'documentCode',
        'serialNo',
        'customerName',
        'customerTelephone',
        'customerTypeID',
        'customerID',
        'customerCode',
        'deliveryPersonID',
        'deliveryCommission',
        'deliveryCommissionAmount',
        'preparationTime',
        'KOTStartDateTime',
        'KOTEndDateTime',
        'KOTDoneByEmpID',
        'wowFoodYN',
        'wowFoodStatus',
        'wowFoodCustomerName',
        'wowFoodCustomerTel',
        'customerCommission',
        'counterID',
        'shiftID',
        'menuSalesDate',
        'menuCost',
        'totalQty',
        'subTotal',
        'grossTotal',
        'grossAmount',
        'discountPer',
        'discountAmount',
        'totalTaxPercentage',
        'totalTaxAmount',
        'serviceCharge',
        'netTotal',
        'netRevenueTotal',
        'paidAmount',
        'balanceAmount',
        'cashReceivedAmount',
        'cashAmount',
        'chequeAmount',
        'chequeNo',
        'chequeDate',
        'cardAmount',
        'creditNoteID',
        'creditNoteAmount',
        'giftCardID',
        'giftCardAmount',
        'cardNumber',
        'cardRefNo',
        'cardBank',
        'paymentMethod',
        'isHold',
        'holdByUserID',
        'holdByUsername',
        'holdPC',
        'holdDatetime',
        'holdRemarks',
        'transactionCurrencyID',
        'transactionCurrency',
        'transactionExchangeRate',
        'transactionCurrencyDecimalPlaces',
        'companyLocalCurrencyID',
        'companyLocalCurrency',
        'companyLocalExchangeRate',
        'companyLocalCurrencyDecimalPlaces',
        'companyReportingCurrencyID',
        'companyReportingCurrency',
        'companyReportingExchangeRate',
        'companyReportingCurrencyDecimalPlaces',
        'customerCurrencyID',
        'customerCurrency',
        'customerCurrencyExchangeRate',
        'customerCurrencyAmount',
        'customerCurrencyDecimalPlaces',
        'segmentID',
        'segmentCode',
        'companyID',
        'companyCode',
        'customerReceivableAutoID',
        'commissionGLAutoID',
        'commisionLiabilityGLAutoID',
        'isOnTimeCommision',
        'bankGLAutoID',
        'bankCurrencyID',
        'bankCurrency',
        'bankCurrencyExchangeRate',
        'bankCurrencyDecimalPlaces',
        'bankCurrencyAmount',
        'salesDay',
        'salesDayNum',
        'isOrderPending',
        'isOrderInProgress',
        'isOrderCompleted',
        'tableID',
        'createdUserGroup',
        'createdPCID',
        'createdUserID',
        'createdUserName',
        'createdDateTime',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedUserName',
        'modifiedDateTime',
        'timestamp',
        'is_sync',
        'id_store',
        'isVoid',
        'voidBy',
        'voidDatetime',
        'isCreditSales',
        'documentMasterAutoID',
        'documentSystemCode',
        'isDelivery',
        'isPromotion',
        'promotionID',
        'promotionDiscount',
        'KOTAlarm',
        'promotionDiscountAmount',
        'tabUserID',
        'waiterID',
        'numberOfPacks',
        'BOT',
        'BOTCreatedUser',
        'BOTCreatedDatetime',
        'isUpdated',
        'issueType',
        'isFromTablet',
        'deliveryRevenueGLID',
        'ownDeliveryPercentage',
        'ownDeliveryAmount',
        'isCancelled',
        'transaction_log_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'menuSalesID' => 'integer',
        'invoiceSequenceNo' => 'integer',
        'invoiceCode' => 'string',
        'wareHouseAutoID' => 'integer',
        'documentCode' => 'string',
        'serialNo' => 'integer',
        'customerName' => 'string',
        'customerTelephone' => 'string',
        'customerTypeID' => 'integer',
        'customerID' => 'integer',
        'customerCode' => 'string',
        'deliveryPersonID' => 'integer',
        'deliveryCommission' => 'float',
        'deliveryCommissionAmount' => 'float',
        'preparationTime' => 'float',
        'KOTStartDateTime' => 'datetime',
        'KOTEndDateTime' => 'datetime',
        'KOTDoneByEmpID' => 'integer',
        'wowFoodYN' => 'integer',
        'wowFoodStatus' => 'integer',
        'wowFoodCustomerName' => 'string',
        'wowFoodCustomerTel' => 'string',
        'customerCommission' => 'integer',
        'counterID' => 'integer',
        'shiftID' => 'integer',
        'menuSalesDate' => 'date',
        'menuCost' => 'float',
        'totalQty' => 'float',
        'subTotal' => 'float',
        'grossTotal' => 'float',
        'grossAmount' => 'float',
        'discountPer' => 'float',
        'discountAmount' => 'float',
        'totalTaxPercentage' => 'float',
        'totalTaxAmount' => 'float',
        'serviceCharge' => 'float',
        'netTotal' => 'float',
        'netRevenueTotal' => 'float',
        'paidAmount' => 'float',
        'balanceAmount' => 'float',
        'cashReceivedAmount' => 'float',
        'cashAmount' => 'float',
        'chequeAmount' => 'float',
        'chequeNo' => 'string',
        'chequeDate' => 'date',
        'cardAmount' => 'float',
        'creditNoteID' => 'integer',
        'creditNoteAmount' => 'float',
        'giftCardID' => 'integer',
        'giftCardAmount' => 'float',
        'cardNumber' => 'integer',
        'cardRefNo' => 'integer',
        'cardBank' => 'integer',
        'paymentMethod' => 'integer',
        'isHold' => 'integer',
        'holdByUserID' => 'string',
        'holdByUsername' => 'string',
        'holdPC' => 'string',
        'holdDatetime' => 'datetime',
        'holdRemarks' => 'string',
        'transactionCurrencyID' => 'integer',
        'transactionCurrency' => 'string',
        'transactionExchangeRate' => 'float',
        'transactionCurrencyDecimalPlaces' => 'integer',
        'companyLocalCurrencyID' => 'integer',
        'companyLocalCurrency' => 'string',
        'companyLocalExchangeRate' => 'float',
        'companyLocalCurrencyDecimalPlaces' => 'integer',
        'companyReportingCurrencyID' => 'integer',
        'companyReportingCurrency' => 'string',
        'companyReportingExchangeRate' => 'float',
        'companyReportingCurrencyDecimalPlaces' => 'integer',
        'customerCurrencyID' => 'integer',
        'customerCurrency' => 'string',
        'customerCurrencyExchangeRate' => 'float',
        'customerCurrencyAmount' => 'float',
        'customerCurrencyDecimalPlaces' => 'integer',
        'segmentID' => 'integer',
        'segmentCode' => 'string',
        'companyID' => 'integer',
        'companyCode' => 'string',
        'customerReceivableAutoID' => 'integer',
        'commissionGLAutoID' => 'integer',
        'commisionLiabilityGLAutoID' => 'integer',
        'isOnTimeCommision' => 'integer',
        'bankGLAutoID' => 'integer',
        'bankCurrencyID' => 'integer',
        'bankCurrency' => 'string',
        'bankCurrencyExchangeRate' => 'float',
        'bankCurrencyDecimalPlaces' => 'integer',
        'bankCurrencyAmount' => 'float',
        'salesDay' => 'string',
        'salesDayNum' => 'integer',
        'isOrderPending' => 'integer',
        'isOrderInProgress' => 'integer',
        'isOrderCompleted' => 'integer',
        'tableID' => 'integer',
        'createdUserGroup' => 'integer',
        'createdPCID' => 'string',
        'createdUserID' => 'string',
        'createdUserName' => 'string',
        'createdDateTime' => 'datetime',
        'modifiedPCID' => 'string',
        'modifiedUserID' => 'string',
        'modifiedUserName' => 'string',
        'modifiedDateTime' => 'datetime',
        'timestamp' => 'datetime',
        'is_sync' => 'integer',
        'id_store' => 'integer',
        'isVoid' => 'integer',
        'voidBy' => 'integer',
        'voidDatetime' => 'datetime',
        'isCreditSales' => 'integer',
        'documentMasterAutoID' => 'integer',
        'documentSystemCode' => 'string',
        'isDelivery' => 'integer',
        'isPromotion' => 'integer',
        'promotionID' => 'integer',
        'promotionDiscount' => 'float',
        'KOTAlarm' => 'integer',
        'promotionDiscountAmount' => 'float',
        'tabUserID' => 'integer',
        'waiterID' => 'integer',
        'numberOfPacks' => 'integer',
        'BOT' => 'integer',
        'BOTCreatedUser' => 'integer',
        'BOTCreatedDatetime' => 'datetime',
        'isUpdated' => 'integer',
        'issueType' => 'string',
        'isFromTablet' => 'integer',
        'deliveryRevenueGLID' => 'integer',
        'ownDeliveryPercentage' => 'float',
        'ownDeliveryAmount' => 'float',
        'isCancelled' => 'integer',
        'transaction_log_id' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'wareHouseAutoID' => 'required',
        'grossAmount' => 'required'
    ];

    
}
