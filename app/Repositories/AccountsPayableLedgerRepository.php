<?php

namespace App\Repositories;

use App\Models\AccountsPayableLedger;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class AccountsPayableLedgerRepository
 * @package App\Repositories
 * @version July 3, 2018, 10:00 am UTC
 *
 * @method AccountsPayableLedger findWithoutFail($id, $columns = ['*'])
 * @method AccountsPayableLedger find($id, $columns = ['*'])
 * @method AccountsPayableLedger first($columns = ['*'])
*/
class AccountsPayableLedgerRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companySystemID',
        'companyID',
        'documentSystemID',
        'documentID',
        'documentSystemCode',
        'documentCode',
        'documentDate',
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
        'isInvoiceLockedYN',
        'lockedBy',
        'lockedByEmpName',
        'lockedDate',
        'lockedComments',
        'invoiceType',
        'selectedToPaymentInv',
        'fullyInvoice',
        'advancePaymentTypeID',
        'createdDateTime',
        'createdUserID',
        'createdPcID',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return AccountsPayableLedger::class;
    }
}
