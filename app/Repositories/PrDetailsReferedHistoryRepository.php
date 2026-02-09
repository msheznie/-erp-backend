<?php

namespace App\Repositories;

use App\Models\PrDetailsReferedHistory;
use App\Repositories\BaseRepository;

/**
 * Class PrDetailsReferedHistoryRepository
 * @package App\Repositories
 * @version August 1, 2018, 6:10 am UTC
 *
 * @method PrDetailsReferedHistory findWithoutFail($id, $columns = ['*'])
 * @method PrDetailsReferedHistory find($id, $columns = ['*'])
 * @method PrDetailsReferedHistory first($columns = ['*'])
*/
class PrDetailsReferedHistoryRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'purchaseRequestID',
        'itemCode',
        'itemPrimaryCode',
        'itemDescription',
        'itemFinanceCategoryID',
        'itemFinanceCategorySubID',
        'financeGLcodebBS',
        'financeGLcodePL',
        'includePLForGRVYN',
        'quantityRequested',
        'estimatedCost',
        'quantityOnOrder',
        'comments',
        'unitOfMeasure',
        'quantityInHand',
        'timesReffered',
        'timeStamp',
        'partNumber'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PrDetailsReferedHistory::class;
    }
}
