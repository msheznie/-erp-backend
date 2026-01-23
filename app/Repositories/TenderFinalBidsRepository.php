<?php

namespace App\Repositories;

use App\Models\TenderFinalBids;
use App\Repositories\BaseRepository;

/**
 * Class TenderFinalBidsRepository
 * @package App\Repositories
 * @version December 14, 2022, 11:17 am +04
 *
 * @method TenderFinalBids findWithoutFail($id, $columns = ['*'])
 * @method TenderFinalBids find($id, $columns = ['*'])
 * @method TenderFinalBids first($columns = ['*'])
*/
class TenderFinalBidsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'award',
        'bid_id',
        'com_weightage',
        'status',
        'supplier_id',
        'tech_weightage',
        'tender_id',
        'total_weightage'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TenderFinalBids::class;
    }
}
