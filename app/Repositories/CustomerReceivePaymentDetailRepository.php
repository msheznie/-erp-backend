<?php

namespace App\Repositories;

use App\Models\CustomerReceivePaymentDetail;
use App\Repositories\BaseRepository;

/**
 * Class CustomerReceivePaymentDetailRepository
 * @package App\Repositories
 * @version August 24, 2018, 12:09 pm UTC
 *
 * @method CustomerReceivePaymentDetail findWithoutFail($id, $columns = ['*'])
 * @method CustomerReceivePaymentDetail find($id, $columns = ['*'])
 * @method CustomerReceivePaymentDetail first($columns = ['*'])
*/
class CustomerReceivePaymentDetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'custReceivePaymentAutoID',
        'arAutoID',
        'companySystemID',
        'companyID',
        'matchingDocID',
        'addedDocumentSystemID',
        'addedDocumentID',
        'bookingInvCodeSystem',
        'bookingInvCode',
        'bookingDate',
        'comments',
        'custTransactionCurrencyID',
        'custTransactionCurrencyER',
        'companyReportingCurrencyID',
        'companyReportingER',
        'localCurrencyID',
        'localCurrencyER',
        'bookingAmountTrans',
        'bookingAmountLocal',
        'bookingAmountRpt',
        'custReceiveCurrencyID',
        'custReceiveCurrencyER',
        'custbalanceAmount',
        'receiveAmountTrans',
        'receiveAmountLocal',
        'receiveAmountRpt',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CustomerReceivePaymentDetail::class;
    }
}
