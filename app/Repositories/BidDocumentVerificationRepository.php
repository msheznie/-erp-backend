<?php

namespace App\Repositories;

use App\Models\BidDocumentVerification;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class BidDocumentVerificationRepository
 * @package App\Repositories
 * @version October 20, 2022, 4:20 pm +04
 *
 * @method BidDocumentVerification findWithoutFail($id, $columns = ['*'])
 * @method BidDocumentVerification find($id, $columns = ['*'])
 * @method BidDocumentVerification first($columns = ['*'])
*/
class BidDocumentVerificationRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'attachment_id',
        'bis_submission_master_id',
        'document_submit_type',
        'submit_remarks',
        'verified_by',
        'verified_date',
        'status',
        'remarks'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return BidDocumentVerification::class;
    }
}
