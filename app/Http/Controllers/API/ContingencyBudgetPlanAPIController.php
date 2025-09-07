<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateContingencyBudgetPlanAPIRequest;
use App\Http\Requests\API\UpdateContingencyBudgetPlanAPIRequest;
use App\Models\BudgetTransferForm;
use App\Models\ContingencyBudgetPlan;
use App\Models\SegmentMaster;
use App\Models\Year;
use App\Models\Company;
use App\Models\DocumentMaster;
use App\Models\CompanyFinanceYear;
use App\Models\TemplatesMaster;
use App\Models\CompanyDocumentAttachment;
use App\Models\YesNoSelection;
use App\Models\BudgetMaster;
use App\Repositories\ContingencyBudgetPlanRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\DB;
use App\helper\Helper;
use App\Models\ContingencyBudgetRefferedBack;
use App\Models\DocumentApproved;
use App\Models\DocumentReferedHistory;
use Response;
use Carbon\Carbon;

/**
 * Class ContingencyBudgetPlanController
 * @package App\Http\Controllers\API
 */

class ContingencyBudgetPlanAPIController extends AppBaseController
{
    /** @var  ContingencyBudgetPlanRepository */
    private $contingencyBudgetPlanRepository;

    public function __construct(ContingencyBudgetPlanRepository $contingencyBudgetPlanRepo)
    {
        $this->contingencyBudgetPlanRepository = $contingencyBudgetPlanRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/contingencyBudgetPlans",
     *      summary="Get a listing of the ContingencyBudgetPlans.",
     *      tags={"ContingencyBudgetPlan"},
     *      description="Get all ContingencyBudgetPlans",
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
     *                  @SWG\Items(ref="#/definitions/ContingencyBudgetPlan")
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
        $this->contingencyBudgetPlanRepository->pushCriteria(new RequestCriteria($request));
        $this->contingencyBudgetPlanRepository->pushCriteria(new LimitOffsetCriteria($request));
        $contingencyBudgetPlans = $this->contingencyBudgetPlanRepository->all();

        return $this->sendResponse($contingencyBudgetPlans->toArray(), trans('custom.contingency_budget_plans_retrieved_successfully'));
    }

    /**
     * @param CreateContingencyBudgetPlanAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/contingencyBudgetPlans",
     *      summary="Store a newly created ContingencyBudgetPlan in storage",
     *      tags={"ContingencyBudgetPlan"},
     *      description="Store ContingencyBudgetPlan",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ContingencyBudgetPlan that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ContingencyBudgetPlan")
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
     *                  ref="#/definitions/ContingencyBudgetPlan"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateContingencyBudgetPlanAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);
        $employee = \Helper::getEmployeeInfo();
        $input['createdPcID'] = gethostname();
        $input['createdUserID'] = $employee->empID;
        $input['createdUserSystemID'] = $employee->employeeSystemID;
        $input['createdDate'] = now();

        $validator = \Validator::make($input, [
            'companyFinanceYearID' => 'required|numeric|min:1',
            'comments' => 'required',
            'serviceLineSystemID' => 'required|numeric|min:1',
            'templateMasterID' => 'required|numeric|min:1',
            'contingencyPercentage' => 'required|numeric',
            'contigencyAmount' => 'required|numeric',
            'budgetAmount' => 'required|numeric'
        ]);

        $serialNo = DB::table('erp_budget_contingency')->where('companySystemID', $input['companySystemID'])->max('serialNo');
        $serialNo = $serialNo + 1;

        $companyFinanceYear = CompanyFinanceYear::find($input['companyFinanceYearID']);
        if (empty($companyFinanceYear)) {
            return $this->sendError(trans('custom.selected_financial_year_is_not_found'), 500);
        }

        $input['year'] = Carbon::parse($companyFinanceYear->bigginingDate)->format('Y');

        $input['documentSystemID'] = 100;
        $input['documentID'] = 'CBP';

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $check_recExist = $this->check_validation(0, $input);

        if ($check_recExist != 'success') {
            return $this->sendError($check_recExist, 500);
        }

        $company = Company::where('companySystemID', $input['companySystemID'])->first();

        if (empty($company)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.company')]), 500);
        }

        $documentMaster = DocumentMaster::where('documentSystemID', $input['documentSystemID'])->first();

        if ($documentMaster) {
            $code = ($company->CompanyID . '\\' . $documentMaster['documentID'] . str_pad($serialNo, 6, '0', STR_PAD_LEFT));
            $input['contingencyBudgetNo'] = $code;
        }

        $currency = \Helper::companyCurrency($input['companySystemID']);

        $input['currencyID'] = $currency->reportingCurrency;

        $input['companyID'] = $company->CompanyID;
        $input['serialNo'] = $serialNo;
        $input['RollLevForApp_curr'] = 1;

        $contingencyBudgetPlan = $this->contingencyBudgetPlanRepository->create($input);

        return $this->sendResponse($contingencyBudgetPlan->toArray(), trans('custom.contingency_budget_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/contingencyBudgetPlans/{id}",
     *      summary="Display the specified ContingencyBudgetPlan",
     *      tags={"ContingencyBudgetPlan"},
     *      description="Get ContingencyBudgetPlan",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ContingencyBudgetPlan",
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
     *                  ref="#/definitions/ContingencyBudgetPlan"
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
        /** @var ContingencyBudgetPlan $contingencyBudgetPlan */
        $contingencyBudgetPlan = $this->contingencyBudgetPlanRepository->with(['confirmed_by', 'currency_by'])->findWithoutFail($id);

        if (empty($contingencyBudgetPlan)) {
            return $this->sendError(trans('custom.contingency_budget_plan_not_found'));
        }

        return $this->sendResponse($contingencyBudgetPlan->toArray(), trans('custom.contingency_budget_plan_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateContingencyBudgetPlanAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/contingencyBudgetPlans/{id}",
     *      summary="Update the specified ContingencyBudgetPlan in storage",
     *      tags={"ContingencyBudgetPlan"},
     *      description="Update ContingencyBudgetPlan",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ContingencyBudgetPlan",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ContingencyBudgetPlan that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ContingencyBudgetPlan")
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
     *                  ref="#/definitions/ContingencyBudgetPlan"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateContingencyBudgetPlanAPIRequest $request)
    {
        $input = $request->all();

        $input = array_except($input, ['currency_by', 'confirmedByEmpSystemID', 'confirmedByEmpID', 'confirmedDate',]);

        $input = $this->convertArrayToValue($input);

        /** @var ContingencyBudgetPlan $contingencyBudgetPlan */
        $contingencyBudgetPlan = $this->contingencyBudgetPlanRepository->findWithoutFail($id);

        if (empty($contingencyBudgetPlan)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => 'Contingency Budget']));
        }

        $employee = \Helper::getEmployeeInfo();

        $validator = \Validator::make($input, [
            'companyFinanceYearID' => 'required|numeric|min:1',
            'comments' => 'required',
            'serviceLineSystemID' => 'required|numeric|min:1',
            'templateMasterID' => 'required|numeric|min:1',
            'contingencyPercentage' => 'required|numeric',
            'contigencyAmount' => 'required|numeric',
            'budgetAmount' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $check_recExist = $this->check_validation($id, $input);

        if ($check_recExist != 'success') {
            return $this->sendError($check_recExist, 500);
        }


        $companyFinanceYear = CompanyFinanceYear::find($input['companyFinanceYearID']);
        if (empty($companyFinanceYear)) {
            return $this->sendError(trans('custom.selected_financial_year_is_not_found'), 500);
        }

        $input['year'] = Carbon::parse($companyFinanceYear->bigginingDate)->format('Y');

        if ($contingencyBudgetPlan->confirmedYN == 0 && $input['confirmedYN'] == 1) {

            $params = array(
                'autoID' => $id,
                'company' => $contingencyBudgetPlan->companySystemID,
                'document' => $contingencyBudgetPlan->documentSystemID,
                'segment' => $contingencyBudgetPlan->serviceLineSystemID,
                'category' => 0,
                'amount' => 0
            );
            //echo '<pre>';print_r($params);'</pre>';exit;
            $confirm = \Helper::confirmDocument($params);
            if (!$confirm["success"]) {
                return $this->sendError($confirm["message"], 500);
            }
        }

        $input['modifiedPc'] = gethostname();
        $input['modifiedUser'] = $employee->empID;
        $input['modifiedUserSystemID'] = $employee->employeeSystemID;

        $contingencyBudgetPlan = $this->contingencyBudgetPlanRepository->update(array_only($input, ['comments', 'year', 'serviceLineSystemID', 'templateMasterID', 'contingencyPercentage', 'budgetAmount', 'contigencyAmount', 'templateMasterID', 'contingencyPercentage', 'budgetID', 'modifiedPc', 'modifiedUser', 'modifiedUserSystemID']), $id);

        return $this->sendReponseWithDetails($contingencyBudgetPlan->toArray(), trans('custom.update', ['attribute' => 'Contingency Budget']),1,$confirm['data'] ?? null);
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/contingencyBudgetPlans/{id}",
     *      summary="Remove the specified ContingencyBudgetPlan from storage",
     *      tags={"ContingencyBudgetPlan"},
     *      description="Delete ContingencyBudgetPlan",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ContingencyBudgetPlan",
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
        /** @var ContingencyBudgetPlan $contingencyBudgetPlan */
        $contingencyBudgetPlan = $this->contingencyBudgetPlanRepository->findWithoutFail($id);

        if (empty($contingencyBudgetPlan)) {
            return $this->sendError(trans('custom.contingency_budget_plan_not_found'));
        }

        $contingencyBudgetPlan->delete();

        return $this->sendSuccess('Contingency Budget Plan deleted successfully');
    }

    public function get_contingency_budget(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $selectedCompanyId = $request['companySystemID'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $budgetTransfer = ContingencyBudgetPlan::whereIn('companySystemID', $subCompanies)
            ->with(['segment_by', 'template_master'])
            ->with([
                'confirmed_by' => function ($q) {
                    $q->select('employeeSystemID', 'empID', 'empName');
                }, 'currency_by' => function ($q) {
                    $q->select('currencyID', 'DecimalPlaces');
                }
            ]);

        return \DataTables::of($budgetTransfer)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('ID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getFormData(Request $request)
    {
        $input = $request->all();
        $companyId = $request['companyId'];

        $yesNoSelection = YesNoSelection::all();

        $years = Year::orderBy('year', 'desc')->get();

        $companyFinanceYear = \Helper::companyFinanceYear($companyId);

        $financeYears = CompanyFinanceYear::selectRaw('DATE_FORMAT(bigginingDate,"%M %d %Y") as bigginingDate, DATE_FORMAT(endingDate,"%M %d %Y") as endingDate, companyFinanceYearID')->orderBy('companyFinanceYearID', 'desc')->where('companySystemID', $companyId)->get();

        $segments = SegmentMaster::where("companySystemID", $companyId)
            ->approved()->withAssigned($companyId)
            ->where('isActive', 1)->get();

        $masterTemplates = TemplatesMaster::all();


        if (count($companyFinanceYear) > 0) {
            $startYear = $companyFinanceYear[0]['financeYear'];
            $finYearExp = explode('/', (explode('|', $startYear))[0]);
            $financeYear = (int)$finYearExp[2];
        } else {
            $financeYear = date("Y");
        }

        $reportTemplates = [];

        if (isset($input['companyFinanceYearID']) && !is_null($input['companyFinanceYearID']) && $input['companyFinanceYearID'] != 'null' && isset($input['serviceLineSystemID']) && !is_null($input['serviceLineSystemID']) && $input['serviceLineSystemID'] != 'null') {

            $checkBudget = BudgetMaster::where('companySystemID', $companyId)
                ->where(['companyFinanceYearID' => $input['companyFinanceYearID'], 'serviceLineSystemID' =>  $input['serviceLineSystemID']])
                ->with(['template_master'])
                ->groupBy('templateMasterID')
                ->get();

            if (count($checkBudget) > 0) {
                $checkBudget = $checkBudget->toArray();
                foreach ($checkBudget as $key => $value) {
                    if ($value['template_master']) {
                        $temp = $value['template_master'];
                        $temp['budgetmasterID'] = $value['budgetmasterID'];
                        $reportTemplates[] = $temp;
                    }
                }
            }
        }

        $currencyData = \Helper::companyCurrency($companyId);

        $output = array(
            'reportTemplates' => $reportTemplates,
            'yesNoSelection' => $yesNoSelection,
            'years' => $years,
            'companyFinanceYear' => $companyFinanceYear,
            'segments' => $segments,
            'financeYears' => $financeYears,
            'financeYear' => $financeYear,
            'currencyData' => $currencyData,
            'masterTemplates' => $masterTemplates,
        );

        return $this->sendResponse($output, trans('custom.retrieve', ['attribute' => trans('custom.record')]));
    }

    public function getBudgetAmount($id)
    {
        $get_budget_amount = \DB::select("SELECT IF(rep.reportID = 1,sum( det.budjetAmtRpt ), sum(det.budjetAmtRpt) * (- 1)) AS budgetamount
                                          FROM erp_budjetdetails det 
                                          INNER JOIN erp_budgetmaster mas on det.budgetmasterID = mas.budgetmasterID
                                          INNER JOIN erp_companyreporttemplate  rep on mas.templateMasterID = rep.companyReportTemplateID
                                          INNER JOIN chartofaccounts acc on det.chartOfAccountID = acc.chartOfAccountSystemID
                                          WHERE det.budgetmasterID = {$id} AND (if(rep.reportID = 1 ,acc.controlAccountsSystemID=3,acc.controlAccountsSystemID=2))
                                          group by det.budgetmasterID");

        $get_budget_amount = collect($get_budget_amount)->first();

        $output = array(
            'budgetAmount' => $get_budget_amount->budgetamount
        );

        return $this->sendResponse($output, trans('custom.retrieve', ['attribute' => trans('custom.record')]));
    }

    public function get_contingency_budget_approved(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $input['companyId'];
        $empID = \Helper::getEmployeeSystemID();

        $search = $request->input('search.value');

        $contingency = DB::table('erp_documentapproved')
            ->select(
                'erp_budget_contingency.*',
                'erp_companyreporttemplate.description As templateDescription',
                'serviceline.ServiceLineDes As ServiceLineDes',
                'erp_documentapproved.documentApprovedID',
                'rollLevelOrder',
                'approvalLevelID',
                'documentSystemCode'
            )
            ->join('employeesdepartments', function ($query) use ($companyId, $empID) {
                $query->on('erp_documentapproved.approvalGroupID', '=', 'employeesdepartments.employeeGroupID')
                    ->on('erp_documentapproved.documentSystemID', '=', 'employeesdepartments.documentSystemID')
                    ->on('erp_documentapproved.companySystemID', '=', 'employeesdepartments.companySystemID');

                $serviceLinePolicy = CompanyDocumentAttachment::where('companySystemID', $companyId)
                    ->where('documentSystemID', 100)
                    ->first();

                if ($serviceLinePolicy && $serviceLinePolicy->isServiceLineApproval == -1) {
                    //$query->on('erp_documentapproved.serviceLineSystemID', '=', 'employeesdepartments.ServiceLineSystemID');
                }

                $query->whereIn('employeesdepartments.documentSystemID', [100])
                    ->where('employeesdepartments.companySystemID', $companyId)
                    ->where('employeesdepartments.employeeSystemID', $empID)
                    ->where('employeesdepartments.isActive', 1)
                    ->where('employeesdepartments.removedYN', 0);
            })
            ->join('erp_budget_contingency', function ($query) use ($companyId, $search) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'ID')
                    ->on('erp_documentapproved.rollLevelOrder', '=', 'RollLevForApp_curr')
                    ->where('erp_budget_contingency.companySystemID', $companyId)
                    ->where('erp_budget_contingency.confirmedYN', 1);
            })
            ->where('erp_documentapproved.approvedYN', -1)
            ->leftJoin('erp_companyreporttemplate', 'erp_budget_contingency.templateMasterID', 'erp_companyreporttemplate.companyReportTemplateID')
            ->leftJoin('serviceline', 'erp_budget_contingency.serviceLineSystemID', 'serviceline.serviceLineSystemID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->whereIn('erp_documentapproved.documentSystemID', [100])
            ->where('erp_documentapproved.companySystemID', $companyId);

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $contingency = $contingency->where(function ($query) use ($search) {
                $query->where('ServiceLineDes', 'like', "%{$search}%")
                    ->orWhere('templateDescription', 'like', "%{$search}%");
            });
        }

        return \DataTables::of($contingency)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('ID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function get_contingency_budget_not_approved(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $input['companyId'];
        $empID = \Helper::getEmployeeSystemID();

        $search = $request->input('search.value');

        $contingency = DB::table('erp_documentapproved')
            ->select(
                'employeesdepartments.approvalDeligated',
                'erp_budget_contingency.*',
                'erp_companyreporttemplate.description As templateDescription',
                'serviceline.ServiceLineDes As ServiceLineDes',
                'erp_documentapproved.documentApprovedID',
                'rollLevelOrder',
                'approvalLevelID',
                'documentSystemCode'
            )
            ->join('employeesdepartments', function ($query) use ($companyId, $empID) {
                $query->on('erp_documentapproved.approvalGroupID', '=', 'employeesdepartments.employeeGroupID')
                    ->on('erp_documentapproved.documentSystemID', '=', 'employeesdepartments.documentSystemID')
                    ->on('erp_documentapproved.companySystemID', '=', 'employeesdepartments.companySystemID');

                $serviceLinePolicy = CompanyDocumentAttachment::where('companySystemID', $companyId)
                    ->where('documentSystemID', 100)
                    ->first();

                if ($serviceLinePolicy && $serviceLinePolicy->isServiceLineApproval == -1) {
                    //$query->on('erp_documentapproved.serviceLineSystemID', '=', 'employeesdepartments.ServiceLineSystemID');
                }

                $query->whereIn('employeesdepartments.documentSystemID', [100])
                    ->where('employeesdepartments.companySystemID', $companyId)
                    ->where('employeesdepartments.employeeSystemID', $empID)
                    ->where('employeesdepartments.isActive', 1)
                    ->where('employeesdepartments.removedYN', 0);
            })
            ->join('erp_budget_contingency', function ($query) use ($companyId, $search) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'ID')
                    ->on('erp_documentapproved.rollLevelOrder', '=', 'RollLevForApp_curr')
                    ->where('erp_budget_contingency.companySystemID', $companyId)
                    ->where('erp_budget_contingency.approvedYN', 0)
                    ->where('erp_budget_contingency.confirmedYN', 1);
            })
            ->where('erp_documentapproved.approvedYN', 0)
            ->leftJoin('erp_companyreporttemplate', 'erp_budget_contingency.templateMasterID', 'erp_companyreporttemplate.companyReportTemplateID')
            ->leftJoin('serviceline', 'erp_budget_contingency.serviceLineSystemID', 'serviceline.serviceLineSystemID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->whereIn('erp_documentapproved.documentSystemID', [100])
            ->where('erp_documentapproved.companySystemID', $companyId);

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $contingency = $contingency->where(function ($query) use ($search) {
                $query->where('ServiceLineDes', 'like', "%{$search}%")
                    ->orWhere('templateDescription', 'like', "%{$search}%");
            });
        }

        return \DataTables::of($contingency)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('ID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function approve_contingency_budget(Request $request)
    {
        $approve = \Helper::approveDocument($request);
        if (!$approve["success"]) {
            return $this->sendError($approve["message"]);
        } else {
            return $this->sendResponse(array(), $approve["message"]);
        }
    }

    public function reject_contingency_budget(Request $request)
    {
        //echo '<pre>';print_r($request->all());'</pre>';exit;
        $reject = \Helper::rejectDocument($request);
        if (!$reject["success"]) {
            return $this->sendError($reject["message"]);
        } else {
            return $this->sendResponse(array(), $reject["message"]);
        }
    }

    public function check_validation($id = 0, $input)
    {

        $check_valid = ContingencyBudgetPlan::where(['companyFinanceYearID' => $input['companyFinanceYearID'], 'templateMasterID' => $input['templateMasterID'], 'budgetID' => $input['budgetID']])
            ->select('ID', 'year', 'budgetID', 'templateMasterID', 'contingencyBudgetNo');
        if ($id != 0) {
            $check_valid = $check_valid->where('ID', '!=', $id);
        }
        $check_valid = $check_valid->first();
        $msg = '';

        if (!empty($check_valid)) {
            $msg = 'Contingency budget already exist.';
        } else {
            $msg = 'success';
        }
        return $msg;
    }

    function budget_list(Request $request)
    {

        $budgetTransID = $request['budgetTransID'];
        $serviceLineID = $request['serviceLineID'];

        $master = BudgetTransferForm::find($budgetTransID);

        if (empty($master)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.budget_transfer_form')]));
        }

        $list = ContingencyBudgetPlan::selectRaw("ID AS `value`, CONCAT(contingencyBudgetNo, ' | ', comments) AS label, 
                    currencyID, contigencyAmount, ID")
            ->where('companySystemID', $master->companySystemID)
            ->where('year', $master->year)
            ->where('templateMasterID', $master->templatesMasterAutoID)
            ->where('serviceLineSystemID', $serviceLineID)
            ->where('approvedYN', 1)
            ->with('currency_by:currencyID,CurrencyCode,DecimalPlaces')
            ->with('budget_transfer:contingencyBudgetID,adjustmentAmountRpt')
            ->get();

        if (empty($list)) {
            return $this->sendResponse([], trans('custom.contingency_budget_not_found_for_these_filters'));
        }

        $list = $list->toArray();

        $data = [];
        foreach ($list as $key => $row) {
            $amt = $row['contigencyAmount'];

            $utilized_sum = 0;
            if ($row['budget_transfer']) {
                $utilized_arr = $row['budget_transfer'];
                $utilized_sum = array_sum(array_column($utilized_arr, 'adjustmentAmountRpt'));
            }

            $balance = $amt - $utilized_sum;

            if ($balance <= 0) {
                continue;
            }

            $label = $row['label'];

            $dPlace = 2;
            $currencyCode = '';
            if ($row['currency_by']) {
                $dPlace = $row['currency_by']['DecimalPlaces'];
                $currencyCode = ' ' . $row['currency_by']['CurrencyCode'];
            }

            $label .= ' | ' . number_format($amt, $dPlace);
            $label .= $currencyCode;

            $balance = number_format($balance, $dPlace, '.', '');

            $data[] = [
                'value' => $row['value'],
                'label' => $label,
                'balanceAmount' => $balance
            ];
        }

        return $this->sendResponse($data, trans('custom.contingency_budget_list_retrieved_successfully'));
    }
    public function amendContingencyBudget(Request $request)
    {
        $input = $request->all();
        $contingencyBudgetID = $input['id'];
        $contingencyBudgetMasterData = ContingencyBudgetPlan::find($contingencyBudgetID);
        if (empty($contingencyBudgetMasterData)) {
            return $this->sendError(trans('custom.contingency_budget_not_found'));
        }

        if ($contingencyBudgetMasterData->refferedBackYN != -1) {
            return $this->sendError(trans('custom.you_cannot_refer_back_this_contingency_budget'));
        }

        $contingencyBudgetArray = $contingencyBudgetMasterData->toArray();
        $contingencyBudgetHistory = ContingencyBudgetRefferedBack::insert($contingencyBudgetArray);


        $fetchDocumentApproved = DocumentApproved::where('documentSystemCode', $contingencyBudgetID)
            ->where('companySystemID', $contingencyBudgetMasterData->companySystemID)
            ->where('documentSystemID', $contingencyBudgetMasterData->documentSystemID)
            ->get();


        if (!empty($fetchDocumentApproved)) {
            foreach ($fetchDocumentApproved as $DocumentApproved) {
                $DocumentApproved['refTimes'] = $contingencyBudgetMasterData->timesReferred;
            }
        }
        $DocumentApprovedArray = $fetchDocumentApproved->toArray();

        $storeDocumentReferedHistory = DocumentReferedHistory::insert($DocumentApprovedArray);

        $deleteApproval = DocumentApproved::where('documentSystemCode', $contingencyBudgetID)
            ->where('companySystemID', $contingencyBudgetMasterData->companySystemID)
            ->where('documentSystemID', $contingencyBudgetMasterData->documentSystemID)
            ->delete();

        if ($deleteApproval) {
            $contingencyBudgetMasterData->refferedBackYN = 0;
            $contingencyBudgetMasterData->confirmedYN = 0;
            $contingencyBudgetMasterData->confirmedDate = null;
            $contingencyBudgetMasterData->confirmedByEmpSystemID = null;
            $contingencyBudgetMasterData->confirmedByEmpID = null;
            $contingencyBudgetMasterData->confirmedByEmpName = null;
            $contingencyBudgetMasterData->RollLevForApp_curr = 1;
            $contingencyBudgetMasterData->save();
        }
        return $this->sendResponse($contingencyBudgetMasterData->toArray(), trans('custom.contingency_budget_amend_successfully'));
    }
}
