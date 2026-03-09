<?php

namespace App\Repositories;

use App\Models\TenderNegotiationArea;
use App\Repositories\BaseRepository;

/**
 * Class TenderNegotiationAreaRepository
 * @package App\Repositories
 * @version April 25, 2023, 9:21 am +04
 *
 * @method TenderNegotiationArea findWithoutFail($id, $columns = ['*'])
 * @method TenderNegotiationArea find($id, $columns = ['*'])
 * @method TenderNegotiationArea first($columns = ['*'])
*/
class TenderNegotiationAreaRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'tender_negotiation_id',
        'pricing_schedule',
        'technical_evaluation',
        'tender_documents'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TenderNegotiationArea::class;
    }

    public function getTenderNegotiationAreaBySupplierNegotiationID($id) {
        return $this->select('tender_negotiation_id','pricing_schedule','technical_evaluation','tender_documents','id')->where('tender_negotiation_id',$id)->get();
    }
}
