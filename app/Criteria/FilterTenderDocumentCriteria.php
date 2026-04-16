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
        $query = $model->where('documentSystemID', $this->request['documentSystemID'])
            ->where('tender_id', $this->request['documentSystemCode'])
            ->where('type', $this->request['type']);

        $isNegotiation = (int)($this->request['isNegotiation'] ?? 0);
        if ($isNegotiation === 1) {
            $roundNo = $this->request['round_no'] ?? null;
            if ($roundNo !== null && $roundNo !== '') {
                $query->where('round_no', (int)$roundNo);
            }
        } else {
            $query->whereNull('round_no');
        }

        return $query;
    }
}
