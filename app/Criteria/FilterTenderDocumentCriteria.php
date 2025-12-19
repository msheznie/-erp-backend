<?php

namespace App\Criteria;
use Illuminate\Http\Request;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FilterTenderDocumentCriteria.
 *
 * @package namespace App\Criteria;
 */
class FilterTenderDocumentCriteria implements CriteriaInterface
{

    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }


    /**
     * Apply criteria in query repository
     *
     * @param string              $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        return  $model->where('documentSystemID',$this->request['documentSystemID'])
        ->where('tender_id',$this->request['documentSystemCode'])->where('type',$this->request['type']);
    }
}
