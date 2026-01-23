<?php

namespace App\Repositories;

use App\Models\AccountsReceivableLedger;
use App\Repositories\BaseRepository;

/**
 * Class AccountsReceivableLedgerRepository
 * @package App\Repositories
 * @version June 12, 2018, 10:06 am UTC
 *
 * @method AccountsReceivableLedger findWithoutFail($id, $columns = ['*'])
 * @method AccountsReceivableLedger find($id, $columns = ['*'])
 * @method AccountsReceivableLedger first($columns = ['*'])
*/
class AccountsReceivableLedgerRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companyID',
        'documentID',
        'documentCodeSystem',
        'documentCode',
        'documentDate',
        'customerID',
        'InvoiceNo',
        'InvoiceDate',
        'custTransCurrencyID',
        'custTransER',
        'custInvoiceAmount',
        'custDefaultCurrencyID',
        'custDefaultCurrencyER',
        'custDefaultAmount',
        'localCurrencyID',
        'localER',
        'localAmount',
        'comRptCurrencyID',
        'comRptER',
        'comRptAmount',
        'isInvoiceLockedYN',
        'lockedBy',
        'lockedByEmpName',
        'lockedDate',
        'lockedComments',
        'selectedToPaymentInv',
        'fullyInvoiced',
        'createdDateTime',
        'createdUserID',
        'createdPcID',
        'documentType',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return AccountsReceivableLedger::class;
    }
}
