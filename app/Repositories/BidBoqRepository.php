<?php

namespace App\Repositories;

use App\Models\BidBoq;
use App\Repositories\BaseRepository;

/**
 * Class BidBoqRepository
 * @package App\Repositories
 * @version June 22, 2022, 3:56 pm +04
 *
 * @method BidBoq findWithoutFail($id, $columns = ['*'])
 * @method BidBoq find($id, $columns = ['*'])
 * @method BidBoq first($columns = ['*'])
*/
class BidBoqRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'boq_id',
        'bid_master_id',
        'qty',
        'unit_amount',
        'total_amount',
        'remarks',
        'supplier_registration_id',
        'created_by',
        'updated_by'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return BidBoq::class;
    }
}
