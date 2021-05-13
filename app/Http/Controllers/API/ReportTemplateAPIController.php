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
                'reportID' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->messages(), 422);
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
                $reportTemplateDetails = ReportTemplateDetails::create($data);

                $data2['companyReportTemplateID'] = $reportTemplates->companyReportTemplateID;
                $data2['description'] = 'Retained Earning';
                $data2['itemType'] = 4;
                $data2['sortOrder'] = 1;
                $data2['masterID'] = $reportTemplateDetails->detID;
                $data2['companySystemID'] = $input['companySystemID'];
                $data2['companyID'] = $input['companyID'];
                $data2['createdPCID'] = gethostname();
                $data2['createdUserID'] = \Helper::getEmployeeID();
                $data2['createdUserSystemID'] = \Helper::getEmployeeSystemID();
                $reportTemplateDetails = ReportTemplateDetails::create($data2);

                $chartofaccount = ChartOfAccount::where('isApproved', 1)->where('catogaryBLorPL', 'PL')->get();
                if (count($chartofaccount) > 0) {
                    foreach ($chartofaccount as $key => $val) {
                        $data3['templateMasterID'] = $reportTemplates->companyReportTemplateID;
                        $data3['templateDetailID'] = $reportTemplateDetails->detID;
                        $data3['sortOrder'] = $key + 1;
                        $data3['glAutoID'] = $val['chartOfAccountSystemID'];
                        $data3['glCode'] = $val['AccountCode'];
                        $data3['glDescription'] = $val['AccountDescription'];
                        $data3['companySystemID'] = $input['companySystemID'];
                        $data3['companyID'] = $input['companyID'];
                        $data3['createdPCID'] = gethostname();
                        $data3['createdUserID'] = \Helper::getEmployeeID();
                        $data3['createdUserSystemID'] = \Helper::getEmployeeSystemID();
                        ReportTemplateLinks::create($data3);
                    }

                    $updateTemplateDetailAsFinal = ReportTemplateDetails::where('detID', $reportTemplateDetails->detID)->update(['isFinalLevel' => 1]);
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


        $reportTemplate = $this->reportTemplateRepository->update($input, $id);

        if ($input['isDefault']) {
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


}
