<?php

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Requests\API\CreateYearAPIRequest;
use App\Http\Requests\API\UpdateYearAPIRequest;
use App\Models\Company;
use App\Models\CustomerAssigned;
use App\Models\DocumentMaster;
use App\Models\SupplierAssigned;
use App\Models\TaxVatMainCategories;
use App\Models\Year;
use App\Repositories\YearRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class VATReportAPIController extends AppBaseController
{

    public function __construct()
    {
    }

    public function getVATFilterFormData(Request$request){
        $selectedCompanyId = $request['selectedCompanyId'];
/*
        $isGroup = Helper::checkIsCompanyGroup($selectedCompanyId);
        if ($isGroup) {
            $companiesByGroup = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $companiesByGroup = (array)$selectedCompanyId;
        }

        $companies = Company::whereIN('companySystemID', $companiesByGroup)->where('isGroup',0)->get();*/

        $listOfDocuments = [3,7,8,10,12,13,24,61,4,11,15,19,20,21,17 ];
        $documentTypes = DocumentMaster::whereIn('documentSystemID',$listOfDocuments)->get();

        $vatTypes = TaxVatMainCategories::whereHas('tax',function ($query) use($selectedCompanyId){
            $query->where('companySystemID',$selectedCompanyId);
        })->where('isActive',1)->get();

        $suppliers = SupplierAssigned::where('companySystemID', $selectedCompanyId)->get();
        $customers = CustomerAssigned::where('companySystemID', $selectedCompanyId)->get();

        $output = array(
            'documentTypes' => $documentTypes,
            'vatTypes' => $vatTypes,
            'customers' => $customers,
            'suppliers' => $suppliers,
        );
        return  $this->sendResponse($output,'Data retrieved successfully');
    }

    public function validateVATReport(Request $request){

        $reportTypeID = $request->reportTypeID;
        /*switch ($reportTypeID) {
            case 1:
                $validator = \Validator::make($request->all(), [
                    'toDate' => 'required',
                    'fromDate' => 'required',
                    'customers' => 'required'
                ]);

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }

                break;

            case 2:
                $validator = \Validator::make($request->all(), [
                    'toDate' => 'required',
                    'fromDate' => 'required',
                    'customers' => 'required'
                ]);

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }

                break;

            default:
                return $this->sendError('No report ID found');
        }*/
        return $this->sendResponse([],'Data Retrieved Successfully');
    }

    public function generateVATReport(Request $request){
        return $this->sendResponse([], 'Success');
    }
}
