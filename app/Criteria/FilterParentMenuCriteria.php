<?php

namespace App\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;
use Illuminate\Http\Request;

/**
 * Class FilterParentMenuCriteria.
 *
 * @package namespace App\Criteria;
 */
class FilterParentMenuCriteria implements CriteriaInterface
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

        $companyId = $this->request['companyId'];
        $userGroupId = $this->request['userGroupId'];

        if (isset($this->request['langCode'])){
            $langCode = $this->request['langCode'];
            return $model->where('masterID',NULL)
                ->whereIn('isPortalYN',array(0))
                ->where('isActive',1)
                ->whereIn('userGroupID',$userGroupId)
                ->where('companyID',$companyId)
                ->with(['language'=> function($query) use ($langCode) {
                    $query->where('languageCode', $langCode);
                },'child' => function ($query) use($companyId,$userGroupId, $langCode) {
                    $query->whereIn('userGroupID',$userGroupId)
                        ->where('companyID',$companyId)
                        ->where('isActive',1)
                        ->with(['language'=> function($query) use ($langCode) {
                            $query->where('languageCode', $langCode);
                        },'child' => function ($query) use($companyId,$userGroupId, $langCode) {
                            $query->with(['language' => function($query) use ($langCode){
                                $query->where('languageCode', $langCode);
                            }])->whereIn('userGroupID',$userGroupId)
                                ->where('companyID',$companyId)
                                ->where('isActive',1)
                                ->orderBy("sortOrder","asc");
                        }])
                        ->orderBy("sortOrder","asc");
                }])
                ->orderBy("sortOrder","asc");
        }
        else{
            return $model->where('masterID',NULL)
                ->where('isActive',1)
                ->whereIn('isPortalYN',array(0))
                ->whereIn('userGroupID',$userGroupId)
                ->where('companyID',$companyId)
                ->with(['child' => function ($query) use($companyId,$userGroupId) {
                    $query->whereIn('userGroupID',$userGroupId)
                        ->where('companyID',$companyId)
                        ->where('isActive',1)
                        ->with(['child' => function ($query) use($companyId,$userGroupId) {
                            $query->whereIn('userGroupID',$userGroupId)
                                ->where('companyID',$companyId)
                                ->where('isActive',1)
                                ->orderBy("sortOrder","asc");
                        }])
                        ->orderBy("sortOrder","asc");
                }])
                ->orderBy("sortOrder","asc");
        }

    }
}
