<?php

namespace App\Repositories;

use App\Models\PurchaseOrderAdvPaymentRefferedback;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class PurchaseOrderAdvPaymentRefferedbackRepository
 * @package App\Repositories
 * @version July 23, 2018, 12:22 pm UTC
 *
 * @method PurchaseOrderAdvPaymentRefferedback findWithoutFail($id, $columns = ['*'])
 * @method PurchaseOrderAdvPaymentRefferedback find($id, $columns = ['*'])
 * @method PurchaseOrderAdvPaymentRefferedback first($columns = ['*'])
*/
class PurchaseOrderAdvPaymentRefferedbackRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'poAdvPaymentID',
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
        'timesReferred',
        'createdDateTime',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PurchaseOrderAdvPaymentRefferedback::class;
    }
}
