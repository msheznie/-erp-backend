<?php

namespace App\Repositories;

use App\Models\POSSourceMenuSalesItem;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class POSSourceMenuSalesItemRepository
 * @package App\Repositories
 * @version July 27, 2022, 8:23 am +04
 *
 * @method POSSourceMenuSalesItem findWithoutFail($id, $columns = ['*'])
 * @method POSSourceMenuSalesItem find($id, $columns = ['*'])
 * @method POSSourceMenuSalesItem first($columns = ['*'])
*/
class POSSourceMenuSalesItemRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'wareHouseAutoID',
        'menuSalesID',
        'menuID',
        'menuCategoryID',
        'warehouseMenuID',
        'warehouseMenuCategoryID',
        'defaultUOM',
        'unitOfMeasure',
        'conversionRateUOM',
        'qty',
        'menuSalesPrice',
        'salesPriceSubTotal',
        'salesPriceAfterDiscount',
        'salesPriceNetTotal',
        'netRevenueTotal',
        'totalMenuTaxAmount',
        'totalMenuTaxAmountAfterDiscount',
        'totalMenuServiceCharge',
        'totalMenuServiceChargeAfterDiscount',
        'discountPer',
        'discountAmount',
        'menuCost',
        'kotID',
        'kitchenNote',
        'KOTAlarm',
        'KOTFrontPrint',
        'KOTStartDateTime',
        'KOTEndDateTime',
        'KOTDoneByEmpID',
        'parentMenuSalesItemID',
        'isSamplePrinted',
        'TAXpercentage',
        'TAXAmount',
        'taxMasterID',
        'isTaxEnabled',
        'transactionCurrencyID',
        'transactionCurrency',
        'transactionAmount',
        'transactionCurrencyDecimalPlaces',
        'transactionExchangeRate',
        'companyLocalCurrencyID',
        'companyLocalCurrency',
        'companyLocalAmount',
        'companyLocalExchangeRate',
        'companyLocalCurrencyDecimalPlaces',
        'companyReportingCurrencyID',
        'companyReportingCurrency',
        'companyReportingAmount',
        'companyReportingCurrencyDecimalPlaces',
        'companyReportingExchangeRate',
        'isOrderPending',
        'isOrderInProgress',
        'isOrderCompleted',
        'companyID',
        'companyCode',
        'revenueGLAutoID',
        'remarkes',
        'createdUserGroup',
        'createdPCID',
        'createdUserID',
        'createdDateTime',
        'createdUserName',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedDateTime',
        'modifiedUserName',
        'timestamp',
        'is_sync',
        'id_store',
        'isUpdated',
        'transaction_log_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return POSSourceMenuSalesItem::class;
    }
}
