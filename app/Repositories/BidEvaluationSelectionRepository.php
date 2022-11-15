<?php

namespace App\Repositories;

use App\Models\BidEvaluationSelection;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class BidEvaluationSelectionRepository
 * @package App\Repositories
 * @version November 2, 2022, 8:52 am +04
 *
 * @method BidEvaluationSelection findWithoutFail($id, $columns = ['*'])
 * @method BidEvaluationSelection find($id, $columns = ['*'])
 * @method BidEvaluationSelection first($columns = ['*'])
*/
class BidEvaluationSelectionRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'bids',
        'created_by',
        'description',
        'status',
        'tender_id',
        'updated_by'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return BidEvaluationSelection::class;
    }
}
