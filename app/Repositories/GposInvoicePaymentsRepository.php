<?php

namespace App\Repositories;

use App\Models\GposInvoicePayments;
use App\Repositories\BaseRepository;

/**
 * Class GposInvoicePaymentsRepository
 * @package App\Repositories
 * @version January 22, 2019, 10:14 am +04
 *
 * @method GposInvoicePayments findWithoutFail($id, $columns = ['*'])
 * @method GposInvoicePayments find($id, $columns = ['*'])
 * @method GposInvoicePayments first($columns = ['*'])
*/
class GposInvoicePaymentsRepository extends BaseRepository
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
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return GposInvoicePayments::class;
    }
}
