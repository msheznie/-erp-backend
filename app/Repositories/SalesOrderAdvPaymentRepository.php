<?php

namespace App\Repositories;

use App\Models\SalesOrderAdvPayment;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SalesOrderAdvPaymentRepository
 * @package App\Repositories
 * @version January 15, 2021, 10:53 am +04
 *
 * @method SalesOrderAdvPayment findWithoutFail($id, $columns = ['*'])
 * @method SalesOrderAdvPayment find($id, $columns = ['*'])
 * @method SalesOrderAdvPayment first($columns = ['*'])
*/
class SalesOrderAdvPaymentRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companySystemID',
        'companyID',
        'serviceLineSystemID',
        'serviceLineID',
        'soID',
        'grvAutoID',
        'soCode',
        'soTermID',
        'supplierID',
        'SupplierPrimaryCode',
        'liabilityAccountSysemID',
        'liabilityAccount',
        'UnbilledGRVAccountSystemID',
        'UnbilledGRVAccount',
        'reqDate',
        'narration',
        'currencyID',
        'reqAmount',
        'reqAmountTransCur_amount',
        'logisticCategoryID',
        'confirmedYN',
        'approvedYN',
        'selectedToPayment',
        'fullyPaid',
        'isAdvancePaymentYN',
        'dueDate',
        'LCPaymentYN',
        'requestedByEmpID',
        'requestedByEmpName',
        'reqAmountInPOTransCur',
        'reqAmountInPOLocalCur',
        'reqAmountInPORptCur',
        'createdDateTime',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SalesOrderAdvPayment::class;
    }
}
