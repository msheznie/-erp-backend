<?php

namespace App\Repositories;

use App\Models\POSStagInvoicePayment;
use App\Repositories\BaseRepository;

/**
 * Class POSStagInvoicePaymentRepository
 * @package App\Repositories
 * @version July 21, 2022, 12:24 pm +04
 *
 * @method POSStagInvoicePayment findWithoutFail($id, $columns = ['*'])
 * @method POSStagInvoicePayment find($id, $columns = ['*'])
 * @method POSStagInvoicePayment first($columns = ['*'])
*/
class POSStagInvoicePaymentRepository extends BaseRepository
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
        return POSStagInvoicePayment::class;
    }
}
