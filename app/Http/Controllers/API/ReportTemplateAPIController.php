<?php
/**
 * =============================================
 * -- File Name : ReportTemplateAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Report Template
 * -- Author : Mubashir
 * -- Create date : 20 - December 2018
 * -- Description :  This file contains the all CRUD for Report template
 * -- REVISION HISTORY
 * -- Date: 20 - December 2018 By: Mubashir Description: Added new functions named as getAllReportTemplate(),getReportTemplateFormData()
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateReportTemplateAPIRequest;
use App\Http\Requests\API\UpdateReportTemplateAPIRequest;
use App\Models\AccountsType;
use App\Models\ChartOfAccount;
use App\Models\BudgetMaster;
use App\Models\Company;
use App\Models\Employee;
use App\Models\ReportTemplate;
use App\Models\ReportTemplateDetails;
use App\Models\ReportTemplateLinks;
use App\Models\ReportTemplateNumbers;
use App\Models\UserGroup;
use App\Repositories\ReportTemplateRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ReportTemplateController
 * @package App\Http\Controllers\API
 */
class ReportTemplateAPIController extends AppBaseController
{
    /** @var  ReportTemplateRepository */
    private $reportTemplateRepository;

    public function __construct(ReportTemplateRepository $reportTemplateRepo)
    {
        $this->reportTemplateRepository = $reportTemplateRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/reportTemplates",
     *      summary="Get a listing of the ReportTemplates.",
     *      tags={"ReportTemplate"},
     *      description="Get all ReportTemplates",
     *      produces={"application/json"},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(ref="#/definitions/ReportTemplate")
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->reportTemplateRepository->pushCriteria(new RequestCriteria($request));
        $this->reportTemplateRepository->pushCriteria(new LimitOffsetCriteria($request));
        $reportTemplates = $this->reportTemplateRepository->all();

        return $this->sendResponse($reportTemplates->toArray(), 'Report Templates retrieved successfully');
    }

    /**
     * @param CreateReportTemplateAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/reportTemplates",
     *      summary="Store a newly created ReportTemplate in storage",
     *      tags={"ReportTemplate"},
     *      description="Store ReportTemplate",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ReportTemplate that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ReportTemplate")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/ReportTemplate"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateReportTemplateAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);
        DB::beginTransaction();
        try {
            $validator = \Validator::make($request->all(), [
                'description' => 'required',
                'chartOfAccountSerialLength' => 'required',
                'reportID' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->messages(), 422);
            }


            if (isset($input['chartOfAccountSerialLength']) && ($input['chartOfAccountSerialLength'] < 0 || $input['chartOfAccountSerialLength'] == 0 || $input['chartOfAccountSerialLength'] == null)) {
                return $this->sendError('Serial Length should be greater than zero', 500);
            }

            $company = Company::find($input['companySystemID']);
            if ($company) {
                $input['companyID'] = $company->CompanyID;
            }

            $accountsType = AccountsType::find($input['reportID']);
            if ($accountsType) {
                $input['categoryBLorPL'] = $accountsType->code;
            }

            $input['isActive'] = 1;
            $input['createdPCID'] = gethostname();
            $input['createdUserID'] = \Helper::getEmployeeID();
            $input['createdUserSystemID'] = \Helper::getEmployeeSystemID();
            $reportTemplates = $this->reportTemplateRepository->create($input);

            if ($input['reportID'] == 1) {
                $data['companyReportTemplateID'] = $reportTemplates->companyReportTemplateID;
                $data['description'] = 'Equity';
                $data['itemType'] = 4;
                $data['sortOrder'] = 1;
                $data['companySystemID'] = $input['companySystemID'];
                $data['companyID'] = $input['companyID'];
                $data['createdPCID'] = gethostname();
                $data['createdUserID'] = \Helper::getEmployeeID();
                $data['createdUserSystemID'] = \Helper::getEmployeeSystemID();
                $reportTemplateDetailsMaster = ReportTemplateDetails::create($data);

                $data2['companyReportTemplateID'] = $reportTemplates->companyReportTemplateID;
                $data2['description'] = 'Retained Earning (Automated)';
                $data2['itemType'] = 4;
                $data2['sortOrder'] = 1;
                $data2['masterID'] = $reportTemplateDetailsMaster->detID;
                $data2['companySystemID'] = $input['companySystemID'];
                $data2['companyID'] = $input['companyID'];
                $data2['createdPCID'] = gethostname();
                $data2['createdUserID'] = \Helper::getEmployeeID();
                $data2['createdUserSystemID'] = \Helper::getEmployeeSystemID();
                $reportTemplateDetails = ReportTemplateDetails::create($data2);

                $data3['companyReportTemplateID'] = $reportTemplates->companyReportTemplateID;
                $data3['description'] = 'Retained Earning';
                $data3['itemType'] = 4;
                $data3['sortOrder'] = 2;
                $data3['masterID'] = $reportTemplateDetailsMaster->detID;
                $data3['companySystemID'] = $input['companySystemID'];
                $data3['companyID'] = $input['companyID'];
                $data3['createdPCID'] = gethostname();
                $data3['createdUserID'] = \Helper::getEmployeeID();
                $data3['createdUserSystemID'] = \Helper::getEmployeeSystemID();
                $reportTemplateDetailsRetained = ReportTemplateDetails::create($data3);

                $chartofaccount = ChartOfAccount::where('isApproved', 1)->where('catogaryBLorPL', 'PL')->get();
                if (count($chartofaccount) > 0) {
                    foreach ($chartofaccount as $key => $val) {
                        $data4['templateMasterID'] = $reportTemplates->companyReportTemplateID;
                        $data4['templateDetailID'] = $reportTemplateDetails->detID;
                        $data4['sortOrder'] = $key + 1;
                        $data4['glAutoID'] = $val['chartOfAccountSystemID'];
                        $data4['glCode'] = $val['AccountCode'];
                        $data4['glDescription'] = $val['AccountDescription'];
                        $data4['companySystemID'] = $input['companySystemID'];
                        $data4['companyID'] = $input['companyID'];
                        $data4['createdPCID'] = gethostname();
                        $data4['createdUserID'] = \Helper::getEmployeeID();
                        $data4['createdUserSystemID'] = \Helper::getEmployeeSystemID();
                        ReportTemplateLinks::create($data4);
                    }

                    $updateTemplateDetailAsFinal = ReportTemplateDetails::where('detID', $reportTemplateDetails->detID)->update(['isFinalLevel' => 1]);
                }


                $chartofaccountRetained = ChartOfAccount::where('isApproved', 1)->where('catogaryBLorPL', 'BS')->where('is_retained_earnings',1)->first();
                if ($chartofaccountRetained) {
                  
                        $data5['templateMasterID'] = $reportTemplates->companyReportTemplateID;
                        $data5['templateDetailID'] = $reportTemplateDetailsRetained->detID;
                        $data5['sortOrder'] = 1;
                        $data5['glAutoID'] = $chartofaccountRetained->chartOfAccountSystemID;
                        $data5['glCode'] = $chartofaccountRetained->AccountCode;
                        $data5['glDescription'] = $chartofaccountRetained->AccountDescription;
                        $data5['companySystemID'] = $input['companySystemID'];
                        $data5['companyID'] = $input['companyID'];
                        $data5['createdPCID'] = gethostname();
                        $data5['createdUserID'] = \Helper::getEmployeeID();
                        $data5['createdUserSystemID'] = \Helper::getEmployeeSystemID();
                        ReportTemplateLinks::create($data5);
                    

                    $updateTemplateDetailAsFinal = ReportTemplateDetails::where('detID', $reportTemplateDetailsRetained->detID)->update(['isFinalLevel' => 1]);
                }

            }

            if ($input['reportID'] == 2 && (isset($input['isConsolidation']) && $input['isConsolidation'])) {
                $data5['companyReportTemplateID'] = $reportTemplates->companyReportTemplateID;
                $data5['description'] = 'Share of Associates Profit/Loss';
                $data5['itemType'] = 5;
                $data5['sortOrder'] = 1;
                $data5['companySystemID'] = $input['companySystemID'];
                $data5['companyID'] = $input['companyID'];
                $data5['createdPCID'] = gethostname();
                $data5['createdUserID'] = \Helper::getEmployeeID();
                $data5['createdUserSystemID'] = \Helper::getEmployeeSystemID();
                $reportTemplateDetails1 = ReportTemplateDetails::create($data5);


                $data6['companyReportTemplateID'] = $reportTemplates->companyReportTemplateID;
                $data6['description'] = 'Share of Profit Attributable To';
                $data6['itemType'] = 6;
                $data6['sortOrder'] = 2;
                $data6['companySystemID'] = $input['companySystemID'];
                $data6['companyID'] = $input['companyID'];
                $data6['createdPCID'] = gethostname();
                $data6['createdUserID'] = \Helper::getEmployeeID();
                $data6['createdUserSystemID'] = \Helper::getEmployeeSystemID();
                $reportTemplateDetails2 = ReportTemplateDetails::create($data6);


                $data7['companyReportTemplateID'] = $reportTemplates->companyReportTemplateID;
                $data7['description'] = 'Share Holder';
                $data7['itemType'] = 7;
                $data7['sortOrder'] = 1;
                $data7['masterID'] = $reportTemplateDetails2->detID;
                $data7['companySystemID'] = $input['companySystemID'];
                $data7['companyID'] = $input['companyID'];
                $data7['createdPCID'] = gethostname();
                $data7['createdUserID'] = \Helper::getEmployeeID();
                $data7['createdUserSystemID'] = \Helper::getEmployeeSystemID();
                $reportTemplateDetails3 = ReportTemplateDetails::create($data7);

                $data8['companyReportTemplateID'] = $reportTemplates->companyReportTemplateID;
                $data8['description'] = 'NCI';
                $data8['itemType'] = 8;
                $data8['sortOrder'] = 2;
                $data8['masterID'] = $reportTemplateDetails2->detID;
                $data8['companySystemID'] = $input['companySystemID'];
                $data8['companyID'] = $input['companyID'];
                $data8['createdPCID'] = gethostname();
                $data8['createdUserID'] = \Helper::getEmployeeID();
                $data8['createdUserSystemID'] = \Helper::getEmployeeSystemID();
                $reportTemplateDetails4 = ReportTemplateDetails::create($data8);
            }
            if ($input['reportID'] == 4) {

                $equity = [
                    ["name" => "Opening Balance"],
                    ["name" => "Profit after tax"],
                    ["name" => "Comprehensive income"],
                    ["name" => "Other changes"],
                    ["name" => "Closing balance"]
                ];
                foreach($equity as $det)
                {
                    $data['companyReportTemplateID'] = $reportTemplates->companyReportTemplateID;
                    $data['description'] = $det['name'];
                    $data['itemType'] = 5;
                    $data['sortOrder'] = 1;
                    $data['companySystemID'] = $input['companySystemID'];
                    $data['companyID'] = $input['companyID'];
                    $data['createdPCID'] = gethostname();
                    $data['createdUserID'] = \Helper::getEmployeeID();
                    $data['createdUserSystemID'] = \Helper::getEmployeeSystemID();
                    $reportTemplateDetails = ReportTemplateDetails::create($data);
                }

            }
            DB::commit();
            return $this->sendResponse($reportTemplates->toArray(), 'Report Template saved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }

    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/reportTemplates/{id}",
     *      summary="Display the specified ReportTemplate",
     *      tags={"ReportTemplate"},
     *      description="Get ReportTemplate",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ReportTemplate",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/ReportTemplate"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var ReportTemplate $reportTemplate */
        $reportTemplate = $this->reportTemplateRepository->findWithoutFail($id);

        if (empty($reportTemplate)) {
            return $this->sendError('Report Template not found');
        }

        return $this->sendResponse($reportTemplate->toArray(), 'Report Template retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateReportTemplateAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/reportTemplates/{id}",
     *      summary="Update the specified ReportTemplate in storage",
     *      tags={"ReportTemplate"},
     *      description="Update ReportTemplate",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ReportTemplate",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ReportTemplate that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ReportTemplate")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/ReportTemplate"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateReportTemplateAPIRequest $request)
    {
        $input = $request->all();
        $input = array_except($input, ['template_type', 'Actions', 'DT_Row_Index']);
        $input = $this->convertArrayToValue($input);
        
        /** @var ReportTemplate $reportTemplate */
        $reportTemplate = $this->reportTemplateRepository->findWithoutFail($id);

        if (empty($reportTemplate)) {
            return $this->sendError('Report Template not found');
        }

        if (isset($input['chartOfAccountSerialLength']) && ($input['chartOfAccountSerialLength'] < 0 || $input['chartOfAccountSerialLength'] == 0 || $input['chartOfAccountSerialLength'] == null)) {
            return $this->sendError('Serial Length should be greater than zero', 500);
        }


            if (isset($input['reportID']) && isset($input['companyReportTemplateID'])) {
                $isDefault = ReportTemplate::find($input['companyReportTemplateID']);
                if($isDefault && isset($input['isDefault'])) {
                    if ($input['isDefault'] != $isDefault->isDefault) {
                        if ($input['reportID'] == 1 || $input['reportID'] == 2) {
                            $templates = ReportTemplate::with(['details' => function ($query) {
                                $query->with(['gllink'=>function($query){
                                    $query->with(['chartofaccount']);
                                }]);
                            }])->where('reportID', $input['reportID'])->where('isDefault', 1)->get();


                            $isCOA = false;
                            foreach ($templates as $template) {
                                foreach ($template->details as $detail) {
                                    if($detail->itemType != 4)
                                    {
                                        foreach ($detail->gllink as $gllink) {
                                            if($gllink->glCode)
                                            {
                                                if($gllink->chartofaccount->isActive)
                                                {
                                                    $isCOA = true;
                                                    break;
                                                }
                                              
    
                                            }
                                        
                                        }
                                    }
                     
                                }
                            }

                            if ($isCOA == true) {
                                return $this->sendError('Cannot change default report template because chart of account is created already', 500);
                            }
                        }
                    }
                }
            }

        $reportTemplate = $this->reportTemplateRepository->update($input, $id);

        if (isset($input['isDefault']) && $input['isDefault']) {


            $updateOtherDefault = ReportTemplate::where('reportID', $input['reportID'])
                                                ->where('companyReportTemplateID', '!=', $input['companyReportTemplateID'])
                                                ->update(['isDefault' => 0]);


        }


        return $this->sendResponse($reportTemplate->toArray(), 'ReportTemplate updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/reportTemplates/{id}",
     *      summary="Remove the specified ReportTemplate from storage",
     *      tags={"ReportTemplate"},
     *      description="Delete ReportTemplate",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ReportTemplate",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var ReportTemplate $reportTemplate */
        DB::beginTransaction();
        try {
            $reportTemplate = $this->reportTemplateRepository->findWithoutFail($id);

            if (empty($reportTemplate)) {
                return $this->sendError('Report Template not found');
            }
            if($reportTemplate->isDefault)
            {
                return $this->sendError('Its a default report template, cannot be deleted!');
            }

            $templates = ReportTemplate::with(['details' => function ($query) {
                $query->with(['gllink']);
            }])->where('companyReportTemplateID', $id)->first();
            foreach ($templates->details as $detail) {
                if($detail->itemType != 4)
                {
                    foreach ($detail->gllink as $gllink) {
                        if($gllink->glCode)
                        {
                            return $this->sendError('Connot be deleted! GL code is linked to this template');
                        }
    
                    }
                }
   
            }
           

            $checkReportInBudget = BudgetMaster::where('templateMasterID', $id)
                                               ->first();

            if ($checkReportInBudget) {
                return $this->sendError('Report Template has linked to budget');
            }

            $templateDetail = ReportTemplateDetails::ofMaster($id)->delete();
            $templateDetailLink = ReportTemplateLinks::ofTemplate($id)->delete();
            $reportTemplate->delete();

            DB::commit();
            return $this->sendResponse($id, 'Report Template deleted successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }


    public function getAllReportTemplate(Request $request)
    {
        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $companyID = $input['companyID'];


        $reportTemplate = ReportTemplate::with(['template_type'])->OfCompany($companyID);



        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $reportTemplate = $reportTemplate->where(function ($query) use ($search) {
                $query->where('description', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($reportTemplate)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('companyReportTemplateID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    function getReportTemplateFormData(Request $request)
    {
        $companySystemID = $request['companySystemID'];
        $accountType = AccountsType::all();
        $numbers = ReportTemplateNumbers::all();
        $output = ['accountType' => $accountType, 'numbers' => $numbers];
        return $this->sendResponse($output, 'Report Template retrieved successfully');
    }

    public function getAllReportTemplateForCopy(Request $request)
    {
        $input = $request->all();
        $companyID = $input['companyID'];
        $companyReportTemplateID = $input['companyReportTemplateID'];
        $reportTemplate = ReportTemplate::with(['template_type'])->OfCompany($companyID)
                                        ->where('companyReportTemplateID','!=', $companyReportTemplateID)
                                        ->get();
        return $this->sendResponse($reportTemplate, 'Report Template retrieved successfully');
    }

    public function getReportTemplatesByCategory(Request $request)
    {
        $input = $request->all();

        $selectedCompanyId = $input['selectedCompanyId'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if($isGroup){
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        }else{
            $subCompanies = [$selectedCompanyId];
        }

        $reportTemplate = ReportTemplate::where('reportID', $input['catogaryBLorPLID'])
                                        ->where('isActive', 1)
                                        ->whereIn('companySystemID', $subCompanies)
                                        ->get();
        return $this->sendResponse($reportTemplate, 'Report Template retrieved successfully');
    }

    public function getAssignedReportTemplatesByGl(Request $request)
    {
        $input = $request->all();
        $selectedCompanyId = $input['selectedCompanyId'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if($isGroup){
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        }else{
            $subCompanies = [$selectedCompanyId];
        }

        $reportTemplate = ReportTemplateDetails::with(['master' => function($query) use ($subCompanies) {
                                                    $query->whereIn('companySystemID', $subCompanies);
                                                }, 'gllink' => function($query) use ($input, $subCompanies) {
                                                     $query->where('glAutoID', $input['chartOfAccountSystemID']);
                                                }])
                                                ->whereHas('master', function($query) use ($subCompanies) {
                                                    $query->whereIn('companySystemID', $subCompanies);
                                                })
                                                ->whereHas('gllink', function($query) use ($input) {
                                                     $query->where('glAutoID', $input['chartOfAccountSystemID']);
                                                })
                                                ->get();
        return $this->sendResponse($reportTemplate, 'Report Template retrieved successfully');
    }

    function getEmployees(Request $request)
    {
        $companySystemID = $request['companySystemID'];
        $parentCompany = Company::find($companySystemID);
        $allCompanies = Company::where('masterCompanySystemIDReorting',$parentCompany->masterCompanySystemIDReorting)->pluck('companySystemID')->toArray();
        $employees = Employee::select('empID', 'empName', 'employeeSystemID','empCompanySystemID')->with(['company'])->whereIN('empCompanySystemID', $allCompanies)->where('discharegedYN', 0)->whereNotExists(function ($query) use ($request) {
            $query
                ->select(DB::raw(1))
                ->from('erp_companyreporttemplateemployees AS te')
                ->whereRaw('te.employeeSystemID = employees.employeeSystemID')
                ->where('te.companyReportTemplateID', '=', $request->companyReportTemplateID);
        })->get();
        return $this->sendResponse($employees, 'Report Template retrieved successfully');
    }

    public function getReportHeaderData(Request $request)
    {
        $input = $request->all();

        $templateData = ReportTemplateDetails::with(['subcategory' => function ($query) {
                                                $query->where('itemType', 2)
                                                      ->orderBy('serialLength', 'sortOrder');
                                            }])->find($input['templateDetailID']);

        return $this->sendResponse($templateData, 'Report Template retrieved successfully');
    }
}
