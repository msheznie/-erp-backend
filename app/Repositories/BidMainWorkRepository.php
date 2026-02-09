<?php

namespace App\Repositories;

use App\Models\BidMainWork;
use App\Repositories\BaseRepository;

/**
 * Class BidMainWorkRepository
 * @package App\Repositories
 * @version June 21, 2022, 4:43 pm +04
 *
 * @method BidMainWork findWithoutFail($id, $columns = ['*'])
 * @method BidMainWork find($id, $columns = ['*'])
 * @method BidMainWork first($columns = ['*'])
*/
class BidMainWorkRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'main_works_id',
        'bid_master_id',
        'tender_id',
        'bid_format_detail_id',
        'qty',
        'amount',
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
        return BidMainWork::class;
    }
}
