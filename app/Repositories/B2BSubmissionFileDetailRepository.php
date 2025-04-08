<?php

namespace App\Repositories;

use App\Models\B2BSubmissionFileDetail;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class B2BSubmissionFileDetailRepository
 * @package App\Repositories
 * @version March 25, 2025, 8:28 am +04
 *
 * @method B2BSubmissionFileDetail findWithoutFail($id, $columns = ['*'])
 * @method B2BSubmissionFileDetail find($id, $columns = ['*'])
 * @method B2BSubmissionFileDetail first($columns = ['*'])
*/
class B2BSubmissionFileDetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'bank_transfer_id',
        'document_date',
        'latest_downloaded_id',
        'latest_submitted_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return B2BSubmissionFileDetail::class;
    }
}
