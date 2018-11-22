<?php

namespace App\Repositories;

use App\Models\PaySupplierInvoiceDetailReferback;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class PaySupplierInvoiceDetailReferbackRepository
 * @package App\Repositories
 * @version November 21, 2018, 5:30 am UTC
 *
 * @method PaySupplierInvoiceDetailReferback findWithoutFail($id, $columns = ['*'])
 * @method PaySupplierInvoiceDetailReferback find($id, $columns = ['*'])
 * @method PaySupplierInvoiceDetailReferback first($columns = ['*'])
*/
class PaySupplierInvoiceDetailReferbackRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'payDetailAutoID',
        'PayMasterAutoId',
        'apAutoID',
        'matchingDocID',
        'companySystemID',
        'companyID',
        'addedDocumentSystemID',
        'addedDocumentID',
        'bookingInvSystemCode',
        'bookingInvDocCode',
        'bookingInvoiceDate',
        'addedDocumentType',
        'supplierCodeSystem',
        'supplierInvoiceNo',
        'supplierInvoiceDate',
        'supplierTransCurrencyID',
        'supplierTransER',
        'supplierInvoiceAmount',
        'supplierDefaultCurrencyID',
        'supplierDefaultCurrencyER',
        'supplierDefaultAmount',
        'localCurrencyID',
        'localER',
        'localAmount',
        'comRptCurrencyID',
        'comRptER',
        'comRptAmount',
        'supplierPaymentCurrencyID',
        'supplierPaymentER',
        'supplierPaymentAmount',
        'paymentBalancedAmount',
        'paymentSupplierDefaultAmount',
        'paymentLocalAmount',
        'paymentComRptAmount',
        'timesReferred',
        'modifiedUserID',
        'modifiedPCID',
        'createdDateTime',
        'createdUserSystemID',
        'createdUserID',
        'createdPcID',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PaySupplierInvoiceDetailReferback::class;
    }
}
