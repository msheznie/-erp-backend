<?php

namespace App\Repositories;

use App\Models\BidSubmissionDetail;
use App\Repositories\BaseRepository;

/**
 * Class BidSubmissionDetailRepository
 * @package App\Repositories
 * @version June 15, 2022, 9:01 am +04
 *
 * @method BidSubmissionDetail findWithoutFail($id, $columns = ['*'])
 * @method BidSubmissionDetail find($id, $columns = ['*'])
 * @method BidSubmissionDetail first($columns = ['*'])
*/
class BidSubmissionDetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'bid_master_id',
        'tender_id',
        'evaluation_detail_id',
        'score_id',
        'score',
        'created_by',
        'updated_by'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return BidSubmissionDetail::class;
    }
}
