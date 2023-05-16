<?php

namespace App\Repositories;

use App\Models\TenderNegotiation;
use Prettus\Repository\Contracts\RepositoryInterface;
use InfyOm\Generator\Common\BaseRepository;

/**
 * class TenderNegotiationRepository.
 *
 * @package namespace App\Repositories;
 */
class TenderNegotiationRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'srm_tender_master_id',
        'status',
        'approved_yn',
        'confirmed_yn',
        'confirmed_by',
        'confirmed_at',
        'started_by',
        'comments'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TenderNegotiation::class;
    }

    public function withRelations($id,$relations) {
        return $this->where('id',$id)->with($relations)->get();
    }
}
