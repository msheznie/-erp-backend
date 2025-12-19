<?php

namespace App\Criteria;

use Illuminate\Http\Request;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FilterBudgetTemplateAttachmentCriteria.
 *
 * @package namespace App\Criteria;
 */
class FilterBudgetTemplateAttachmentCriteria implements CriteriaInterface
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Apply criteria in query repository
     *
     * @param mixed               $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        if ($this->request->has('entry_id')) {
            $model = $model->where('entry_id', $this->request->get('entry_id'));
        }

        return $model;
    }
}
