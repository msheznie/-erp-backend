<?php

namespace App\Repositories;

use App\Models\CreditNoteReceipt;
use App\Repositories\BaseRepository;

/**
 * Class CreditNoteReceiptRepository
 * @package App\Repositories
 * @version January 15, 2026, 5:13 pm +04
 *
 * @method CreditNoteReceipt findWithoutFail($id, $columns = ['*'])
 * @method CreditNoteReceipt find($id, $columns = ['*'])
 * @method CreditNoteReceipt first($columns = ['*'])
*/
class CreditNoteReceiptRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'creditNoteAutoID',
        'custReceivePaymentAutoID',
        'refundAmount'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CreditNoteReceipt::class;
    }
}
