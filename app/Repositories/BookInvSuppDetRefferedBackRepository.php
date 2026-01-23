<?php

namespace App\Repositories;

use App\Models\BookInvSuppDetRefferedBack;
use App\Repositories\BaseRepository;

/**
 * Class BookInvSuppDetRefferedBackRepository
 * @package App\Repositories
 * @version September 27, 2018, 10:31 am UTC
 *
 * @method BookInvSuppDetRefferedBack findWithoutFail($id, $columns = ['*'])
 * @method BookInvSuppDetRefferedBack find($id, $columns = ['*'])
 * @method BookInvSuppDetRefferedBack first($columns = ['*'])
*/
class BookInvSuppDetRefferedBackRepository extends BaseRepository
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
        'supplierID',
        'purchaseOrderID',
        'grvAutoID',
        'grvType',
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
        'isAddon',
        'invoiceBeforeGRVYN',
        'timesReferred',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return BookInvSuppDetRefferedBack::class;
    }
}
