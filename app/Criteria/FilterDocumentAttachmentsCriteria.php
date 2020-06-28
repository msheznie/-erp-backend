<?php

namespace App\Criteria;
use Illuminate\Http\Request;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FilterDocumentAttachmentsCriteria.
 *
 * @package namespace App\Criteria;
 */
class FilterDocumentAttachmentsCriteria implements CriteriaInterface
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
                        ->where('documentSystemCode',$this->request['documentSystemCode']);
    }
}
