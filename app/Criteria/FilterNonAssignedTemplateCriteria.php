<?php

namespace App\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;
use App\Models\ChequeTemplateBank;
/**
 * Class FilterNonAssignedTemplateCriteria.
 *
 * @package namespace App\Criteria;
 */
class FilterNonAssignedTemplateCriteria implements CriteriaInterface
{
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
        
        $is_exist = false;
        $template_bank = ChequeTemplateBank::get();
        foreach($template_bank as $template)
        {
            if($template->bank_id == $model['id'])
            {
                $is_exist = true;
                break;
            }
        }
        if(!$is_exist)
        {
            return $model;
        }
        else
        {
            return false;
        }
    }
}
