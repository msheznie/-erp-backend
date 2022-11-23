<?php

namespace App\Repositories;

use App\Models\POSSourceMenuSalesMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class POSSourceMenuSalesMasterRepository
 * @package App\Repositories
 * @version July 26, 2022, 2:47 pm +04
 *
 * @method POSSourceMenuSalesMaster findWithoutFail($id, $columns = ['*'])
 * @method POSSourceMenuSalesMaster find($id, $columns = ['*'])
 * @method POSSourceMenuSalesMaster first($columns = ['*'])
*/
class POSSourceMenuSalesMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
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
     * Configure the Model
     **/
    public function model()
    {
        return POSSourceMenuSalesMaster::class;
    }
}
