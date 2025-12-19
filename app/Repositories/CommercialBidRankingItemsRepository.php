<?php

namespace App\Repositories;

use App\Models\CommercialBidRankingItems;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class CommercialBidRankingItemsRepository
 * @package App\Repositories
 * @version December 8, 2022, 6:05 pm +04
 *
 * @method CommercialBidRankingItems findWithoutFail($id, $columns = ['*'])
 * @method CommercialBidRankingItems find($id, $columns = ['*'])
 * @method CommercialBidRankingItems first($columns = ['*'])
*/
class CommercialBidRankingItemsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'bid_format_detail_id',
        'bid_id',
        'status',
        'tender_id',
        'value'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CommercialBidRankingItems::class;
    }
}
