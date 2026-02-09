<?php

namespace App\Repositories;

use App\Models\TenderNegotiationApproval;
use App\Repositories\BaseRepository;

/**
 * Class TenderNegotiationApprovalRepository
 * @package App\Repositories
 * @version May 12, 2023, 2:54 pm +04
 *
 * @method TenderNegotiationApproval findWithoutFail($id, $columns = ['*'])
 * @method TenderNegotiationApproval find($id, $columns = ['*'])
 * @method TenderNegotiationApproval first($columns = ['*'])
*/
class TenderNegotiationApprovalRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'emp_id',
        'tender_negotiation_id',
        'status',
        'comment'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TenderNegotiationApproval::class;
    }
}
