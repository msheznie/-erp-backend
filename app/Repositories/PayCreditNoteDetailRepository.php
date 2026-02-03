<?php

namespace App\Repositories;

use App\Models\PayCreditNoteDetail;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class PayCreditNoteDetailRepository
 * @package App\Repositories
 * @version January 19, 2026, 10:39 am +04
 *
 * @method PayCreditNoteDetail findWithoutFail($id, $columns = ['*'])
 * @method PayCreditNoteDetail find($id, $columns = ['*'])
 * @method PayCreditNoteDetail first($columns = ['*'])
*/
class PayCreditNoteDetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'creditNoteAutoID',
        'companySystemID',
        'creditNotePaymentAmount'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PayCreditNoteDetail::class;
    }
}
