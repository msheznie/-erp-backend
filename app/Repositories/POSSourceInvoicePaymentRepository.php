<?php

namespace App\Repositories;

use App\Models\POSSourceInvoicePayment;
use App\Repositories\BaseRepository;

/**
 * Class POSSourceInvoicePaymentRepository
 * @package App\Repositories
 * @version July 21, 2022, 12:26 pm +04
 *
 * @method POSSourceInvoicePayment findWithoutFail($id, $columns = ['*'])
 * @method POSSourceInvoicePayment find($id, $columns = ['*'])
 * @method POSSourceInvoicePayment first($columns = ['*'])
*/
class POSSourceInvoicePaymentRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'invoiceID',
        'paymentConfigMasterID',
        'paymentConfigDetailID',
        'glAccountType',
        'GLCode',
        'amount',
        'reference',
        'customerAutoID',
        'isAdvancePayment',
        'createdUserGroup',
        'createdPCID',
        'createdUserID',
        'createdUserName',
        'createdDateTime',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedUserName',
        'modifiedDateTime',
        'timestamp',
        'transaction_log_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return POSSourceInvoicePayment::class;
    }
}
