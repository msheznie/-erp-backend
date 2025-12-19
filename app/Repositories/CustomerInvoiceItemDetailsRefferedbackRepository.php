<?php

namespace App\Repositories;

use App\Models\CustomerInvoiceItemDetailsRefferedback;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class CustomerInvoiceItemDetailsRefferedbackRepository
 * @package App\Repositories
 * @version September 2, 2020, 1:04 pm +04
 *
 * @method CustomerInvoiceItemDetailsRefferedback findWithoutFail($id, $columns = ['*'])
 * @method CustomerInvoiceItemDetailsRefferedback find($id, $columns = ['*'])
 * @method CustomerInvoiceItemDetailsRefferedback first($columns = ['*'])
*/
class CustomerInvoiceItemDetailsRefferedbackRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'customerItemDetailID',
        'custInvoiceDirectAutoID',
        'itemCodeSystem',
        'itemPrimaryCode',
        'itemDescription',
        'itemUnitOfMeasure',
        'unitOfMeasureIssued',
        'convertionMeasureVal',
        'qtyIssued',
        'qtyIssuedDefaultMeasure',
        'currentStockQty',
        'currentWareHouseStockQty',
        'currentStockQtyInDamageReturn',
        'comments',
        'itemFinanceCategoryID',
        'itemFinanceCategorySubID',
        'financeGLcodebBSSystemID',
        'financeGLcodebBS',
        'financeGLcodePLSystemID',
        'financeGLcodePL',
        'financeGLcodeRevenueSystemID',
        'financeGLcodeRevenue',
        'includePLForGRVYN',
        'localCurrencyID',
        'localCurrencyER',
        'issueCostLocal',
        'issueCostLocalTotal',
        'reportingCurrencyID',
        'reportingCurrencyER',
        'issueCostRpt',
        'issueCostRptTotal',
        'marginPercentage',
        'sellingCurrencyID',
        'sellingCurrencyER',
        'sellingCost',
        'sellingCostAfterMargin',
        'sellingTotal',
        'sellingCostAfterMarginLocal',
        'sellingCostAfterMarginRpt',
        'customerCatalogDetailID',
        'customerCatalogMasterID',
        'deliveryOrderDetailID',
        'deliveryOrderID',
        'quotationMasterID',
        'quotationDetailsID',
        'timesReferred',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CustomerInvoiceItemDetailsRefferedback::class;
    }
}
