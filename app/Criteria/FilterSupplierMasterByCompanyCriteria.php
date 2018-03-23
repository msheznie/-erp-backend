<?php

namespace App\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;
use Illuminate\Http\Request;


/**
 * Class FilterSuppilerMasterByCompanyCriteria.
 *
 * @package namespace App\Criteria;
 */
class FilterSupplierMasterByCompanyCriteria implements CriteriaInterface
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
        return $model->where('primaryCompanySystemID',$this->request->get('companyId'));

    }
}
