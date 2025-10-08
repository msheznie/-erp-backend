<?php
namespace App\Http\Controllers\API;

use App\Models\CompanyPolicyMaster;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

use Response;

/**
 * Class CommonPoliciesAPIController
 * @package App\Http\Controllers\API
 */

class CommonPoliciesAPIController extends AppBaseController
{
    public function checkPolicyForExchangeRates(Request $request){

        $companyId = $request->companyId;

        $policy = CompanyPolicyMaster::where('companySystemID', $companyId)
            ->where('companyPolicyCategoryID', 67)
            ->where('isYesNO', 1)
            ->first();

        $policy = isset($policy->isYesNO) && $policy->isYesNO == 1;

        return $this->sendResponse($policy, trans('custom.policy_details_retrieved_successfully'));

    }
}
