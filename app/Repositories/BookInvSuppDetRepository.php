<?php

namespace App\Repositories;

use App\Models\BookInvSuppDet;
use App\Repositories\BaseRepository;

/**
 * Class BookInvSuppDetRepository
 * @package App\Repositories
 * @version August 8, 2018, 6:52 am UTC
 *
 * @method BookInvSuppDet findWithoutFail($id, $columns = ['*'])
 * @method BookInvSuppDet find($id, $columns = ['*'])
 * @method BookInvSuppDet first($columns = ['*'])
*/
class BookInvSuppDetRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
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
        return BookInvSuppDet::class;
    }
}
