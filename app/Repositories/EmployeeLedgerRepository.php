<?php

namespace App\Repositories;

use App\Models\EmployeeLedger;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class EmployeeLedgerRepository
 * @package App\Repositories
 * @version March 7, 2022, 2:13 pm +04
 *
 * @method EmployeeLedger findWithoutFail($id, $columns = ['*'])
 * @method EmployeeLedger find($id, $columns = ['*'])
 * @method EmployeeLedger first($columns = ['*'])
*/
class EmployeeLedgerRepository extends BaseRepository
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
        return EmployeeLedger::class;
    }
}
