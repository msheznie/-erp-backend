<?php

namespace App\Repositories;

use App\Models\BidSubmissionMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class BidSubmissionMasterRepository
 * @package App\Repositories
 * @version June 15, 2022, 9:00 am +04
 *
 * @method BidSubmissionMaster findWithoutFail($id, $columns = ['*'])
 * @method BidSubmissionMaster find($id, $columns = ['*'])
 * @method BidSubmissionMaster first($columns = ['*'])
*/
class BidSubmissionMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'uuid',
        'tender_id',
        'supplier_registration_id',
        'bid_sequence',
        'status',
        'created_by',
        'updated_by'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return BidSubmissionMaster::class;
    }
}
