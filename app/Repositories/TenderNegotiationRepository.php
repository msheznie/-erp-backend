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
        'comments',
        'no_to_approve',
        'currencyId'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TenderNegotiation::class;
    }

    public function withRelations($id,$relations) {
        return $this->where('id',$id)->select(['srm_tender_master_id','status','approved_yn','comments','confirmed_yn','no_to_approve','id','confirmed_by'])->with($relations)->get();
    }

    public function getVersion($id)
    {
        return TenderNegotiation::where('srm_tender_master_id', $id)
            ->orderByDesc('version')
            ->first();
    }
}
