<?php

namespace App\Repositories;

use App\Models\PaySupplierInvoiceDetail;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class PaySupplierInvoiceDetailRepository
 * @package App\Repositories
 * @version August 9, 2018, 9:58 am UTC
 *
 * @method PaySupplierInvoiceDetail findWithoutFail($id, $columns = ['*'])
 * @method PaySupplierInvoiceDetail find($id, $columns = ['*'])
 * @method PaySupplierInvoiceDetail first($columns = ['*'])
*/
class PaySupplierInvoiceDetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
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
        'employeeSystemID',
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
        'isRetention',
        'modifiedUserID',
        'modifiedPCID',
        'createdDateTime',
        'createdUserID',
        'createdPcID',
        'timeStamp',
        'purchaseOrderID'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PaySupplierInvoiceDetail::class;
    }
}
