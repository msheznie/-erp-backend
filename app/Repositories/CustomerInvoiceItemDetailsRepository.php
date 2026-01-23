<?php

namespace App\Repositories;

use App\Models\CustomerInvoiceItemDetails;
use App\Repositories\BaseRepository;

/**
 * Class CustomerInvoiceItemDetailsRepository
 * @package App\Repositories
 * @version February 19, 2020, 9:43 am +04
 *
 * @method CustomerInvoiceItemDetails findWithoutFail($id, $columns = ['*'])
 * @method CustomerInvoiceItemDetails find($id, $columns = ['*'])
 * @method CustomerInvoiceItemDetails first($columns = ['*'])
*/
class CustomerInvoiceItemDetailsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
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
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CustomerInvoiceItemDetails::class;
    }
}
