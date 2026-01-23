<?php

namespace App\Repositories;

use App\Models\CustReceivePaymentDetRefferedHistory;
use App\Repositories\BaseRepository;

/**
 * Class CustReceivePaymentDetRefferedHistoryRepository
 * @package App\Repositories
 * @version November 21, 2018, 10:54 am UTC
 *
 * @method CustReceivePaymentDetRefferedHistory findWithoutFail($id, $columns = ['*'])
 * @method CustReceivePaymentDetRefferedHistory find($id, $columns = ['*'])
 * @method CustReceivePaymentDetRefferedHistory first($columns = ['*'])
*/
class CustReceivePaymentDetRefferedHistoryRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'custRecivePayDetAutoID',
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
        return CustReceivePaymentDetRefferedHistory::class;
    }
}
