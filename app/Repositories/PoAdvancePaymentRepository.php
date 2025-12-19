<?php

namespace App\Repositories;

use App\Models\PoAdvancePayment;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class PoAdvancePaymentRepository
 * @package App\Repositories
 * @version April 10, 2018, 11:09 am UTC
 *
 * @method PoAdvancePayment findWithoutFail($id, $columns = ['*'])
 * @method PoAdvancePayment find($id, $columns = ['*'])
 * @method PoAdvancePayment first($columns = ['*'])
*/
class PoAdvancePaymentRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companySystemID',
        'companyID',
        'serviceLineSystemID',
        'serviceLineID',
        'poID',
        'grvAutoID',
        'poCode',
        'poTermID',
        'supplierID',
        'SupplierPrimaryCode',
        'reqDate',
        'narration',
        'currencyID',
        'reqAmount',
        'reqAmountTransCur_amount',
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
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PoAdvancePayment::class;
    }
}
