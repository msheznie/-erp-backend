<?php

namespace App\Repositories;

use App\Models\SupplierInvoiceItemDetail;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SupplierInvoiceItemDetailRepository
 * @package App\Repositories
 * @version October 8, 2021, 4:11 pm +04
 *
 * @method SupplierInvoiceItemDetail findWithoutFail($id, $columns = ['*'])
 * @method SupplierInvoiceItemDetail find($id, $columns = ['*'])
 * @method SupplierInvoiceItemDetail first($columns = ['*'])
*/
class SupplierInvoiceItemDetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'bookingSupInvoiceDetAutoID',
        'bookingSuppMasInvAutoID',
        'unbilledgrvAutoID',
        'companySystemID',
        'companyID',
        'grvDetailsID',
        'purchaseOrderID',
        'grvAutoID',
        'supplierTransactionCurrencyID',
        'supplierTransactionCurrencyER',
        'companyReportingCurrencyID',
        'companyReportingER',
        'localCurrencyID',
        'localCurrencyER',
        'supplierInvoOrderedAmount',
        'supplierInvoAmount',
        'transSupplierInvoAmount',
        'localSupplierInvoAmount',
        'rptSupplierInvoAmount',
        'totTransactionAmount',
        'totLocalAmount',
        'totRptAmount',
        'VATAmount',
        'VATAmountLocal',
        'VATAmountRpt',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SupplierInvoiceItemDetail::class;
    }
}
