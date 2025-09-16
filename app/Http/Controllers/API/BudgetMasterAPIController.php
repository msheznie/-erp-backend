<?php
/**
 * =============================================
 * -- File Name : BudgetMasterAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Budget
 * -- Author : Mohamed Fayas
 * -- Create date : 16 - October 2018
 * -- Description : This file contains the all CRUD for Budget Master
 * -- REVISION HISTORY
 * -- Date: 16 -October 2018 By: Fayas Description: Added new function getBudgetsByCompany(),reportBudgetGLCodeWise(),budgetGLCodeWiseDetails()
 * -- Date: 17 -October 2018 By: Fayas Description: Added new function reportBudgetTemplateCategoryWise()
 * -- Date: 23 -October 2018 By: Fayas Description: Added new function getBudgetFormData()
 * -- Date: 29 -October 2018 By: Fayas Description: Added new function getBudgetAudit(),budgetReopen(),getBudgetApprovedByUser(),
 *                   getBudgetApprovalByUser()
 */
namespace App\Http\Controllers\API;

use App\helper\CreateExcel;
use App\Http\Requests\API\CreateBudgetMasterAPIRequest;
use App\Http\Requests\API\UpdateBudgetMasterAPIRequest;
use App\Jobs\AddBudgetDetails;
use App\Jobs\BudgetSegmentBulkInsert;
use App\Models\BudgetConsumedData;
use App\Models\DirectPaymentDetails;
use App\Models\DirectInvoiceDetails;
use App\Models\BudgetMaster;
use App\Models\BudgetMasterRefferedHistory;
use App\Models\BudgetDetailsRefferedHistory;
use App\Models\SegmentRights;
use App\Models\Budjetdetails;
use App\Models\Company;
use App\Models\CompanyDocumentAttachment;
use App\Models\CompanyFinanceYear;
use App\Models\PurchaseRequest;
use App\Models\ProcumentOrder;
use App\Models\CompanyPolicyMaster;
use App\Models\DebitNoteDetails;
use App\Models\ReportTemplateDetails;
use App\Models\CurrencyMaster;
use App\Models\DocumentApproved;
use App\Models\DocumentReferedHistory;
use App\Models\ReportTemplate;
use App\Models\GRVDetails;
use App\Models\ReportTemplateLinks;
use App\Models\SupplierInvoiceDirectItem;
use App\Models\ChartOfAccount;
use App\Models\DocumentMaster;
use App\Models\EmployeesDepartment;
use App\Models\JvDetail;
use App\Models\Months;
use App\Models\PurchaseReturnDetails;
use App\Models\PurchaseOrderDetails;
use App\Models\SegmentMaster;
use App\Models\TemplatesGLCode;
use App\Models\TemplatesMaster;
use App\Models\UploadBudgets;
use App\Models\Year;
use App\Models\YesNoSelection;
use App\helper\BudgetConsumptionService;
use App\Models\YesNoSelectionForMinus;
use App\Repositories\BudgetMasterRepository;
use App\Traits\AuditTrial;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use PHPExcel_IOFactory;
use App\Models\FixedAssetMaster;
use App\Models\logUploadBudget;

/**
 * Class BudgetMasterController
 * @package App\Http\Controllers\API
 */
class BudgetMasterAPIController extends AppBaseController
{
    /** @var  BudgetMasterRepository */
    private $budgetMasterRepository;

    public function __construct(BudgetMasterRepository $budgetMasterRepo)
    {
        $this->budgetMasterRepository = $budgetMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/budgetMasters",
     *      summary="Get a listing of the BudgetMasters.",
     *      tags={"BudgetMaster"},
     *      description="Get all BudgetMasters",
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
     *                  @SWG\Items(ref="#/definitions/BudgetMaster")
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
        $this->budgetMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->budgetMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $budgetMasters = $this->budgetMasterRepository->all();

        return $this->sendResponse($budgetMasters->toArray(), trans('custom.budget_masters_retrieved_successfully'));
    }

    /**
     * @param CreateBudgetMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/budgetMasters",
     *      summary="Store a newly created BudgetMaster in storage",
     *      tags={"BudgetMaster"},
     *      description="Store BudgetMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BudgetMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BudgetMaster")
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
     *                  ref="#/definitions/BudgetMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateBudgetMasterAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);
        $employee = \Helper::getEmployeeInfo();

        $input['createdByUserID'] = $employee->empID;
        $input['createdByUserSystemID'] = $employee->employeeSystemID;
        $input['confirmedByEmpName'] = $employee->empName;

        $validator = \Validator::make($input, [
            'serviceLineSystemID' => 'required|numeric|min:1',
            'companySystemID' => 'required',
            'templateMasterID' => 'required|numeric|min:1',
            'sentNotificationAt' => 'required|numeric|min:1',
            'companyFinanceYearID' => 'required|numeric|min:1'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        if ($input['sentNotificationAt'] > 100) {
            return $this->sendError(trans('custom.send_notification_at_percentage_cannot_be_greater_'), 500);
        }


        $segment = SegmentMaster::find($input['serviceLineSystemID']);
        if (empty($segment)) {
            return $this->sendError(trans('custom.segment_not_found'), 500);
        }

        $template = ReportTemplate::find($input['templateMasterID']);
        if (empty($template)) {
            return $this->sendError(trans('custom.template_not_found'), 500);
        }

        if ($segment->isActive == 0) {
            return $this->sendError('Please select a active Segment', 500);
        }
        $input['serviceLineCode'] = $segment->ServiceLineCode;

        $company = Company::where('companySystemID', $input['companySystemID'])->first();
        if (empty($segment)) {
            return $this->sendError(trans('custom.company_not_found'), 500);
        }

        $input['companyID'] = $company->CompanyID;
        $input['documentSystemID'] = 65;
        $input['documentID'] = 'BUD';

        $companyFinanceYear = CompanyFinanceYear::find($input['companyFinanceYearID']);
        if (empty($companyFinanceYear)) {
            return $this->sendError(trans('custom.selected_financial_year_is_not_found'), 500);
        }

        $input['Year'] = Carbon::parse($companyFinanceYear->bigginingDate)->format('Y');



        $result = CarbonPeriod::create($companyFinanceYear->bigginingDate, '1 month', $companyFinanceYear->endingDate);
        $monthArray = [];
        foreach ($result as $dt) {
            $temp['year'] = $dt->format("Y");
            $temp['monthID'] = floatval($dt->format("m"));

            $monthArray[] = $temp;
        }

        $checkAlreadyExist = BudgetMaster::where('companySystemID', $input['companySystemID'])
            ->where('serviceLineSystemID', $input['serviceLineSystemID'])
            ->where('companyFinanceYearID', $input['companyFinanceYearID'])
            ->where('templateMasterID', $input['templateMasterID'])
            ->count();
        if ($checkAlreadyExist > 0) {
            return $this->sendError(trans('custom.already_created_budgets_for_selected_template'), 500);
        }

        $checkDuplicateTypeBudget = BudgetMaster::where('companySystemID', $input['companySystemID'])
                                                ->where('serviceLineSystemID', $input['serviceLineSystemID'])
                                                ->where('companyFinanceYearID', $input['companyFinanceYearID'])
                                                ->whereHas('template_master', function($query) use ($template) {
                                                    $query->where('reportID', $template->reportID);
                                                })
                                                ->count();

        if ($checkDuplicateTypeBudget > 0) {
            if ($template->reportID == 2) {
                return $this->sendError(trans('custom.already_budget_created_for_pl_type_template'), 500);
            } else {
                return $this->sendError(trans('custom.already_budget_created_for_bs_type_template'), 500);
            }
        }

        $glData = TemplatesGLCode::where('templateMasterID', $input['templateMasterID'])
            ->whereNotNull('chartOfAccountSystemID')
            ->whereHas('chart_of_account', function ($q) use ($input) {
                $q->where('companySystemID', $input['companySystemID'])
                    ->where('isActive', 1)
                    ->where('isAssigned', -1);
            })
            ->get();

        $glData = ReportTemplateLinks::where('templateMasterID', $input['templateMasterID'])
                                    ->whereNotNull('glAutoID')
                                    ->whereHas('chart_of_account', function ($q) use ($input) {
                                        $q->where('companySystemID', $input['companySystemID'])
                                            ->where('isActive', 1)
                                            ->where('isAssigned', -1);
                                    })
                                    ->whereHas('template_category', function ($q) use ($input) {
                                        $q->where('itemType', '!=',4);
                                    })
                                    ->with(['chart_of_account' => function ($q) use ($input) {
                                        $q->where('companySystemID', $input['companySystemID'])
                                            ->where('isActive', 1)
                                            ->where('isAssigned', -1);
                                    }])
                                    ->get();

        $input['month'] = 1; //$month->monthID;
        $budgetMasters = $this->budgetMasterRepository->create($input);
        AddBudgetDetails::dispatch($budgetMasters,$glData, $monthArray);
        return $this->sendResponse($budgetMasters->toArray(), trans('custom.budget_master_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/budgetMasters/{id}",
     *      summary="Display the specified BudgetMaster",
     *      tags={"BudgetMaster"},
     *      description="Get BudgetMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BudgetMaster",
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
     *                  ref="#/definitions/BudgetMaster"
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
        /** @var BudgetMaster $budgetMaster */
        $budgetMaster = $this->budgetMasterRepository->with(['confirmed_by','segment_by', 'template_master', 'finance_year_by'])->findWithoutFail($id);

        if (empty($budgetMaster)) {
            return $this->sendError(trans('custom.budget_master_not_found'));
        }

        return $this->sendResponse($budgetMaster->toArray(), trans('custom.budget_master_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateBudgetMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/budgetMasters/{id}",
     *      summary="Update the specified BudgetMaster in storage",
     *      tags={"BudgetMaster"},
     *      description="Update BudgetMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BudgetMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BudgetMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BudgetMaster")
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
     *                  ref="#/definitions/BudgetMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateBudgetMasterAPIRequest $request)
    {
        $input = $request->all();
        $input = array_except($input, ['confirmed_by','segment_by', 'template_master', 'finance_year_by',
                                        'confirmedByEmpSystemID','confirmedByEmpID','confirmedDate',]);

        $input = $this->convertArrayToValue($input);

        /** @var BudgetMaster $budgetMaster */
        $budgetMaster = $this->budgetMasterRepository->findWithoutFail($id);

        if (empty($budgetMaster)) {
            return $this->sendError(trans('custom.budget_master_not_found'));
        }

        if ($budgetMaster->confirmedYN == 1) {
            return $this->sendError(trans('custom.this_document_already_confirmed_1'), 500);
        }

        if ($budgetMaster->confirmedYN == 0 && $input['confirmedYN'] == 1) {


            $checkItems = Budjetdetails::where('budgetmasterID', $id)
                ->count();
            if ($checkItems == 0) {
                return $this->sendError('Every budget should have at least one item', 500);
            }

            $params = array('autoID' => $id,
                'company' => $budgetMaster->companySystemID,
                'document' => $budgetMaster->documentSystemID,
                'segment' => $budgetMaster->serviceLineSystemID,
                'category' => 0,
                'amount' => 0
            );

            $confirm = \Helper::confirmDocument($params);
            if (!$confirm["success"]) {
                return $this->sendError($confirm["message"], 500);
            }
        }

        //$budgetMaster = $this->budgetMasterRepository->update($input, $id);

        return $this->sendReponseWithDetails($budgetMaster->toArray(), trans('custom.budget_master_updated_successfully'),1,$confirm['data'] ?? null);
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/budgetMasters/{id}",
     *      summary="Remove the specified BudgetMaster from storage",
     *      tags={"BudgetMaster"},
     *      description="Delete BudgetMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BudgetMaster",
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
        /** @var BudgetMaster $budgetMaster */
        $budgetMaster = $this->budgetMasterRepository->findWithoutFail($id);

        if (empty($budgetMaster)) {
            return $this->sendError(trans('custom.budget_master_not_found'));
        }

        $deleteBudgetDetails = Budjetdetails::where('budgetmasterID', $id)->delete();

        $budgetMaster->delete();

        return $this->sendResponse($id, trans('custom.budget_master_deleted_successfully'));
    }

    public function getBudgetsByCompany(Request $request)
    {

        $input = $request->all();

        $input = $this->convertArrayToSelectedValue($input, array('confirmedYN', 'serviceLineSystemID', 'approvedYN', 'Year', 'templateMasterID', 'Year'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $serviceLineSystemID = $request['serviceLineSystemID'];
        $serviceLineSystemID = (array)$serviceLineSystemID;
        $serviceLineSystemID = collect($serviceLineSystemID)->pluck('id');

        $templateMasterID = $request['templateMasterID'];
        $templateMasterID = (array)$templateMasterID;
        $templateMasterID = collect($templateMasterID)->pluck('id');

        $selectedCompanyId = $request['companyId'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $checkSegmentAccess = CompanyDocumentAttachment::companyDocumentAttachemnt($selectedCompanyId, 65);
        $isServiceLineAccess = false;
        if ($checkSegmentAccess && $checkSegmentAccess->isServiceLineAccess) {
            $isServiceLineAccess = true;
        }

        $employeeSystemID = \Helper::getEmployeeSystemID();

        $accessibleSegments = SegmentRights::where('employeeSystemID', $employeeSystemID)
                                           ->where('companySystemID', $selectedCompanyId)
                                           ->get()
                                           ->pluck('serviceLineSystemID')
                                           ->toArray();


        $budgets = BudgetMaster::whereIn('companySystemID', $subCompanies)
                                ->where('month',1)
                                ->with(['segment_by', 'template_master', 'finance_year_by'])
                                ->when(request('serviceLineSystemID') && !is_null($input['serviceLineSystemID']), function ($q) use ($serviceLineSystemID) {
                                    return $q->whereIn('serviceLineSystemID', $serviceLineSystemID);
                                })
                                ->when(request('templateMasterID') && !is_null($input['templateMasterID']), function ($q) use ($templateMasterID) {
                                    return $q->whereIn('templateMasterID', $templateMasterID);
                                })
                                ->when(request('companyFinanceYearID') && !is_null($input['companyFinanceYearID']), function ($q) use ($input) {
                                    return $q->where('companyFinanceYearID', $input['companyFinanceYearID']);
                                })
                                 ->when(($isServiceLineAccess == true && (!request('serviceLineSystemID') || is_null($input['serviceLineSystemID']))), function ($q) use ($accessibleSegments) {
                                    return $q->whereIn('serviceLineSystemID', $accessibleSegments);
                                });

        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $budgets = $budgets->where(function ($query) use ($search) {
                $query->where('Year', 'like', "%{$search}%")
                    ->orWhereHas('segment_by', function ($q1) use ($search) {
                        $q1->where('ServiceLineDes', 'like', "%{$search}%");
                    })->orWhereHas('template_master', function ($q2) use ($search) {
                        $q2->where('description', 'like', "%{$search}%");
                    });
            });
        }

        if(isset($input['checkApprovedYN']) && $input['checkApprovedYN'] == 1){
            $budgets = $budgets->where('approvedYN', -1);
        }

        $budgets = $budgets->groupBy(['Year', 'serviceLineSystemID', 'templateMasterID']);

        return \DataTables::of($budgets)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('Year', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function reportBudgetGLCodeWise(Request $request)
    {
        $input = $request->all();


        /** @var BudgetMaster $budgetMaster */
        $budgetMaster = $this->budgetMasterRepository->with(['segment_by', 'template_master', 'finance_year_by'])->findWithoutFail($input['id']);

        if (empty($budgetMaster)) {
            return $this->sendError(trans('custom.budget_master_not_found'));
        }

        // policy check -> Department wise budget check
        $DLBCPolicy = CompanyPolicyMaster::where('companySystemID', $budgetMaster->companySystemID)
            ->where('companyPolicyCategoryID', 33)
            ->where('isYesNO', 1)
            ->exists();

        $reportData = Budjetdetails::select(DB::raw("(SUM(budjetAmtLocal) * -1) as totalLocal,
                                       (SUM(budjetAmtRpt) * -1) as totalRpt,
                                       chartofaccounts.AccountCode,chartofaccounts.AccountDescription,chartofaccounts.controlAccountsSystemID,
                                       erp_companyreporttemplatedetails.description as templateDetailDescription,
                                       erp_companyreporttemplatedetails.detID as templatesMasterAutoID,
                                       erp_budjetdetails.*,ifnull(ca.consumed_amount,0) as consumed_amount,ifnull(ppo.rptAmt,0) as pending_po_amount,
                                       ((SUM(budjetAmtRpt) * -1) - (ifnull(ca.consumed_amount,0) + ifnull(ppo.rptAmt,0))) AS balance,ifnull(adj.SumOfadjustmentRptAmount,0) AS adjusted_amount"))
                                    ->where('erp_budjetdetails.companySystemID', $budgetMaster->companySystemID)
                                    ->where('erp_budjetdetails.serviceLineSystemID', $budgetMaster->serviceLineSystemID)
                                    ->where('erp_budjetdetails.companyFinanceYearID', $budgetMaster->companyFinanceYearID)
                                    ->where('erp_companyreporttemplatedetails.companyReportTemplateID', $budgetMaster->templateMasterID)
                                    ->leftJoin('chartofaccounts', 'chartOfAccountID', '=', 'chartOfAccountSystemID')
                                    ->leftJoin('erp_budgetmaster', 'erp_budgetmaster.budgetmasterID', '=', 'erp_budjetdetails.budgetmasterID')
                                    ->leftJoin('erp_companyreporttemplatedetails', 'templateDetailID', '=', 'detID');

        $reportData = $reportData->leftJoin(DB::raw('(SELECT erp_budgetconsumeddata.companySystemID, erp_budgetconsumeddata.serviceLineSystemID, 
                                        erp_budgetconsumeddata.chartOfAccountID, erp_budgetconsumeddata.Year,erp_budgetconsumeddata.companyFinanceYearID, 
                                        Sum(erp_budgetconsumeddata.consumedRptAmount) AS consumed_amount FROM
                                        erp_budgetconsumeddata WHERE erp_budgetconsumeddata.consumeYN = -1 AND (erp_budgetconsumeddata.projectID = 0 OR erp_budgetconsumeddata.projectID IS NULL)
                                        GROUP BY erp_budgetconsumeddata.companySystemID, erp_budgetconsumeddata.serviceLineSystemID, 
                                        erp_budgetconsumeddata.chartOfAccountID, erp_budgetconsumeddata.companyFinanceYearID) as ca'),
                                        function ($join) {
                                            $join->on('erp_budjetdetails.companySystemID', '=', 'ca.companySystemID')
                                                ->on('erp_budjetdetails.serviceLineSystemID', '=', 'ca.serviceLineSystemID')
                                                ->on('erp_budjetdetails.companyFinanceYearID', '=', 'ca.companyFinanceYearID')
                                                ->on('erp_budjetdetails.chartOfAccountID', '=', 'ca.chartOfAccountID');
                                        })
                                ->leftJoin(DB::raw('(SELECT erp_purchaseordermaster.companySystemID, erp_purchaseordermaster.serviceLineSystemID, 
                                           erp_purchaseorderdetails.financeGLcodePLSystemID, Sum(GRVcostPerUnitLocalCur * noQty) AS localAmt, 
                                           Sum(GRVcostPerUnitComRptCur * noQty) AS rptAmt, erp_purchaseordermaster.budgetYear FROM 
                                           erp_purchaseordermaster INNER JOIN erp_purchaseorderdetails ON erp_purchaseordermaster.purchaseOrderID = erp_purchaseorderdetails.purchaseOrderMasterID WHERE (((erp_purchaseordermaster.approved)=0) 
                                           AND ((erp_purchaseordermaster.poCancelledYN)=0) AND (erp_purchaseordermaster.projectID = 0 OR erp_purchaseordermaster.projectID IS NULL)) GROUP BY erp_purchaseordermaster.companySystemID, erp_purchaseordermaster.serviceLineSystemID, erp_purchaseorderdetails.financeGLcodePL, erp_purchaseorderdetails.budgetYear HAVING 
                                           (((erp_purchaseorderdetails.financeGLcodePLSystemID) Is Not Null))) as ppo'),
                                    function ($join) {
                                        $join->on('erp_budjetdetails.companySystemID', '=', 'ppo.companySystemID')
                                            ->on('erp_budjetdetails.serviceLineSystemID', '=', 'ppo.serviceLineSystemID')
                                            ->on('erp_budgetmaster.Year', '=', 'ppo.budgetYear')
                                            ->on('erp_budjetdetails.chartOfAccountID', '=', 'ppo.financeGLcodePLSystemID');
                                    });


        $reportData = $reportData->leftJoin(DB::raw('(SELECT
                                erp_budgetadjustment.companySystemID,
                                erp_budgetadjustment.serviceLineSystemID,
                                erp_budgetadjustment.adjustedGLCodeSystemID,
                                erp_budgetadjustment.YEAR,
                                Sum( erp_budgetadjustment.adjustmentRptAmount ) AS SumOfadjustmentRptAmount 
                                FROM
                                    erp_budgetadjustment 
                                GROUP BY
                                erp_budgetadjustment.companySystemID,
                                erp_budgetadjustment.serviceLineSystemID,
                                erp_budgetadjustment.adjustedGLCodeSystemID,
                                erp_budgetadjustment.YEAR ) as adj'),
                                function ($join) {
                                    $join->on('erp_budjetdetails.companySystemID', '=', 'adj.companySystemID')
                                        ->on('erp_budjetdetails.serviceLineSystemID', '=', 'adj.serviceLineSystemID')
                                        ->on('erp_budgetmaster.Year', '=', 'adj.YEAR')
                                        ->on('erp_budjetdetails.chartOfAccountID', '=', 'adj.adjustedGLCodeSystemID');
                                })
                            ->groupBy(['erp_budjetdetails.companySystemID', 'erp_budjetdetails.serviceLineSystemID',
                                'erp_budjetdetails.chartOfAccountID', 'erp_budjetdetails.companyFinanceYearID'])
                            ->orderBy('erp_companyreporttemplatedetails.description','ASC')
                            ->get();
                                
        foreach ($reportData as $key => $value) {
            $commitedConsumedAmount = BudgetConsumptionService::getCommitedConsumedAmount($value, $DLBCPolicy, true);
            $value['actuallConsumptionAmount'] = $commitedConsumedAmount['actuallConsumptionAmount'];
            $value['committedAmount'] = $commitedConsumedAmount['committedAmount'];
            $value['pendingDocumentAmount'] = $commitedConsumedAmount['pendingDocumentAmount'];
            $value['balance'] = $value['totalRpt'] - ($commitedConsumedAmount['pendingDocumentAmount'] + $commitedConsumedAmount['committedAmount'] + $commitedConsumedAmount['actuallConsumptionAmount']);
        }

        $total = array();
        $total['totalLocal'] = array_sum(collect($reportData)->pluck('totalLocal')->toArray());
        $total['totalRpt'] = array_sum(collect($reportData)->pluck('totalRpt')->toArray());
        $total['committedAmount'] = array_sum(collect($reportData)->pluck('committedAmount')->toArray());
        $total['actuallConsumptionAmount'] = array_sum(collect($reportData)->pluck('actuallConsumptionAmount')->toArray());
        $total['pendingDocumentAmount'] = array_sum(collect($reportData)->pluck('pendingDocumentAmount')->toArray());
        $total['balance'] = $total['totalRpt'] - ($total['committedAmount'] + $total['actuallConsumptionAmount'] + $total['pendingDocumentAmount']);

        $company = Company::where('companySystemID', $budgetMaster->companySystemID)->first();

        $localCurrency = CurrencyMaster::where('currencyID', $company->localCurrencyID)->first();
        $rptCurrency = CurrencyMaster::where('currencyID', $company->reportingCurrency)->first();

        $decimalPlaceLocal = !empty($localCurrency) ? $localCurrency->DecimalPlaces : 3;
        $decimalPlaceRpt = !empty($rptCurrency) ? $rptCurrency->DecimalPlaces : 2;


        $data = array('entity' => $budgetMaster->toArray(), 'reportData' => $reportData,
            'total' => $total, 'decimalPlaceLocal' => $decimalPlaceLocal, 'decimalPlaceRpt' => $decimalPlaceRpt);

        return $this->sendResponse($data, trans('custom.details_retrieved_successfully_1'));
    }

    public function exportBudgetGLCodeWise(Request $request)
    {
        $input = $request->all();

        /** @var BudgetMaster $budgetMaster */
        $budgetMaster = $this->budgetMasterRepository->with(['segment_by', 'template_master', 'finance_year_by'])->findWithoutFail($input['id']);

        if (empty($budgetMaster)) {
            return $this->sendError(trans('custom.budget_master_not_found'));
        }

        // policy check -> Department wise budget check
        $DLBCPolicy = true;

        $reportData = Budjetdetails::select(DB::raw("(SUM(budjetAmtLocal) * -1) as totalLocal,
                                       (SUM(budjetAmtRpt) * -1) as totalRpt,
                                       chartofaccounts.AccountCode,chartofaccounts.AccountDescription,chartofaccounts.controlAccountsSystemID,
                                       erp_companyreporttemplatedetails.description as templateDetailDescription,
                                       erp_companyreporttemplatedetails.detID as templatesMasterAutoID,
                                       erp_budjetdetails.*,ifnull(ca.consumed_amount,0) as consumed_amount,ifnull(ppo.rptAmt,0) as pending_po_amount,
                                       ((SUM(budjetAmtRpt) * -1) - (ifnull(ca.consumed_amount,0) + ifnull(ppo.rptAmt,0))) AS balance,ifnull(adj.SumOfadjustmentRptAmount,0) AS adjusted_amount"))
            ->where('erp_budjetdetails.companySystemID', $budgetMaster->companySystemID)
            ->where('erp_budjetdetails.serviceLineSystemID', $budgetMaster->serviceLineSystemID)
            ->where('erp_budjetdetails.companyFinanceYearID', $budgetMaster->companyFinanceYearID)
            ->where('erp_companyreporttemplatedetails.companyReportTemplateID', $budgetMaster->templateMasterID)
            ->leftJoin('chartofaccounts', 'chartOfAccountID', '=', 'chartOfAccountSystemID')
            ->leftJoin('erp_budgetmaster', 'erp_budgetmaster.budgetmasterID', '=', 'erp_budjetdetails.budgetmasterID')
            ->leftJoin('erp_companyreporttemplatedetails', 'templateDetailID', '=', 'detID');

        // IF Policy on filter consumed amounta and pending amount by service
        if($DLBCPolicy){
            $reportData = $reportData->leftJoin(DB::raw('(SELECT erp_budgetconsumeddata.companySystemID, erp_budgetconsumeddata.serviceLineSystemID, 
                                            erp_budgetconsumeddata.chartOfAccountID, erp_budgetconsumeddata.Year,erp_budgetconsumeddata.companyFinanceYearID, 
                                            Sum(erp_budgetconsumeddata.consumedRptAmount) AS consumed_amount FROM
                                            erp_budgetconsumeddata WHERE erp_budgetconsumeddata.consumeYN = -1 AND (erp_budgetconsumeddata.projectID = 0 OR erp_budgetconsumeddata.projectID IS NULL)
                                            GROUP BY erp_budgetconsumeddata.companySystemID, erp_budgetconsumeddata.serviceLineSystemID, 
                                            erp_budgetconsumeddata.chartOfAccountID, erp_budgetconsumeddata.companyFinanceYearID) as ca'),
                function ($join) {
                    $join->on('erp_budjetdetails.companySystemID', '=', 'ca.companySystemID')
                        ->on('erp_budjetdetails.serviceLineSystemID', '=', 'ca.serviceLineSystemID')
                        ->on('erp_budjetdetails.companyFinanceYearID', '=', 'ca.companyFinanceYearID')
                        ->on('erp_budjetdetails.chartOfAccountID', '=', 'ca.chartOfAccountID');
                })
                ->leftJoin(DB::raw('(SELECT erp_purchaseordermaster.companySystemID, erp_purchaseordermaster.serviceLineSystemID, 
                                               erp_purchaseorderdetails.financeGLcodePLSystemID, Sum(GRVcostPerUnitLocalCur * noQty) AS localAmt, 
                                               Sum(GRVcostPerUnitComRptCur * noQty) AS rptAmt, erp_purchaseordermaster.budgetYear FROM 
                                               erp_purchaseordermaster INNER JOIN erp_purchaseorderdetails ON erp_purchaseordermaster.purchaseOrderID = erp_purchaseorderdetails.purchaseOrderMasterID WHERE (((erp_purchaseordermaster.approved)=0) 
                                               AND ((erp_purchaseordermaster.poCancelledYN)=0) AND (erp_purchaseordermaster.projectID = 0 OR erp_purchaseordermaster.projectID IS NULL)) GROUP BY erp_purchaseordermaster.companySystemID, erp_purchaseordermaster.serviceLineSystemID, erp_purchaseorderdetails.financeGLcodePL, erp_purchaseorderdetails.budgetYear HAVING 
                                               (((erp_purchaseorderdetails.financeGLcodePLSystemID) Is Not Null))) as ppo'),
                    function ($join) {
                        $join->on('erp_budjetdetails.companySystemID', '=', 'ppo.companySystemID')
                            ->on('erp_budjetdetails.serviceLineSystemID', '=', 'ppo.serviceLineSystemID')
                            ->on('erp_budgetmaster.Year', '=', 'ppo.budgetYear')
                            ->on('erp_budjetdetails.chartOfAccountID', '=', 'ppo.financeGLcodePLSystemID');
                    });

        } else {

            $reportData = $reportData->leftJoin(DB::raw('(SELECT erp_budgetconsumeddata.companySystemID, erp_budgetconsumeddata.serviceLineSystemID, 
                                            erp_budgetconsumeddata.chartOfAccountID, erp_budgetconsumeddata.Year,erp_budgetconsumeddata.companyFinanceYearID, 
                                            Sum(erp_budgetconsumeddata.consumedRptAmount) AS consumed_amount FROM
                                            erp_budgetconsumeddata WHERE erp_budgetconsumeddata.consumeYN = -1 AND (erp_budgetconsumeddata.projectID = 0 OR erp_budgetconsumeddata.projectID IS NULL)
                                            GROUP BY erp_budgetconsumeddata.companySystemID, 
                                            erp_budgetconsumeddata.chartOfAccountID, erp_budgetconsumeddata.companyFinanceYearID) as ca'),
                function ($join) {
                    $join->on('erp_budjetdetails.companySystemID', '=', 'ca.companySystemID')
                        ->on('erp_budjetdetails.companyFinanceYearID', '=', 'ca.companyFinanceYearID')
                        ->on('erp_budjetdetails.chartOfAccountID', '=', 'ca.chartOfAccountID');
                })
                ->leftJoin(DB::raw('(SELECT erp_purchaseordermaster.companySystemID, erp_purchaseordermaster.serviceLineSystemID, 
                                               erp_purchaseorderdetails.financeGLcodePLSystemID, Sum(GRVcostPerUnitLocalCur * noQty) AS localAmt, 
                                               Sum(GRVcostPerUnitComRptCur * noQty) AS rptAmt, erp_purchaseordermaster.budgetYear FROM 
                                               erp_purchaseordermaster INNER JOIN erp_purchaseorderdetails ON erp_purchaseordermaster.purchaseOrderID = erp_purchaseorderdetails.purchaseOrderMasterID WHERE (((erp_purchaseordermaster.approved)=0) 
                                               AND ((erp_purchaseordermaster.poCancelledYN)=0) AND (erp_purchaseordermaster.projectID = 0 OR erp_purchaseordermaster.projectID IS NULL))GROUP BY erp_purchaseordermaster.companySystemID, erp_purchaseorderdetails.financeGLcodePL, erp_purchaseorderdetails.budgetYear HAVING 
                                               (((erp_purchaseorderdetails.financeGLcodePLSystemID) Is Not Null))) as ppo'),
                    function ($join) {
                        $join->on('erp_budjetdetails.companySystemID', '=', 'ppo.companySystemID')
                            ->on('erp_budgetmaster.Year', '=', 'ppo.budgetYear')
                            ->on('erp_budjetdetails.chartOfAccountID', '=', 'ppo.financeGLcodePLSystemID');
                    });
        }

        $reportData = $reportData->leftJoin(DB::raw('(SELECT
                                erp_budgetadjustment.companySystemID,
                                erp_budgetadjustment.serviceLineSystemID,
                                erp_budgetadjustment.adjustedGLCodeSystemID,
                                erp_budgetadjustment.YEAR,
                                Sum( erp_budgetadjustment.adjustmentRptAmount ) AS SumOfadjustmentRptAmount 
                                FROM
                                    erp_budgetadjustment 
                                GROUP BY
                                erp_budgetadjustment.companySystemID,
                                erp_budgetadjustment.serviceLineSystemID,
                                erp_budgetadjustment.adjustedGLCodeSystemID,
                                erp_budgetadjustment.YEAR ) as adj'),
            function ($join) {
                $join->on('erp_budjetdetails.companySystemID', '=', 'adj.companySystemID')
                    ->on('erp_budjetdetails.serviceLineSystemID', '=', 'adj.serviceLineSystemID')
                    ->on('erp_budgetmaster.Year', '=', 'adj.YEAR')
                    ->on('erp_budjetdetails.chartOfAccountID', '=', 'adj.adjustedGLCodeSystemID');
            })
            ->groupBy(['erp_budjetdetails.companySystemID', 'erp_budjetdetails.serviceLineSystemID',
                'erp_budjetdetails.chartOfAccountID', 'erp_budjetdetails.companyFinanceYearID'])
            ->orderBy('erp_companyreporttemplatedetails.description','ASC')
            ->get();

        foreach ($reportData as $key => $value) {
            $commitedConsumedAmount = BudgetConsumptionService::getCommitedConsumedAmount($value, $DLBCPolicy);
            $value['actuallConsumptionAmount'] = $commitedConsumedAmount['actuallConsumptionAmount'];
            $value['committedAmount'] = $commitedConsumedAmount['committedAmount'];
            $value['pendingDocumentAmount'] = $commitedConsumedAmount['pendingDocumentAmount'];
            $value['balance'] = $value['totalRpt'] - ($commitedConsumedAmount['pendingDocumentAmount'] + $commitedConsumedAmount['committedAmount'] + $commitedConsumedAmount['actuallConsumptionAmount']);
        }

        $total = array();
        $total['totalLocal'] = array_sum(collect($reportData)->pluck('totalLocal')->toArray());
        $total['totalRpt'] = array_sum(collect($reportData)->pluck('totalRpt')->toArray());
        $total['committedAmount'] = array_sum(collect($reportData)->pluck('committedAmount')->toArray());
        $total['actuallConsumptionAmount'] = array_sum(collect($reportData)->pluck('actuallConsumptionAmount')->toArray());
        $total['pendingDocumentAmount'] = array_sum(collect($reportData)->pluck('pendingDocumentAmount')->toArray());
        $total['balance'] = $total['totalRpt'] - ($total['committedAmount'] + $total['actuallConsumptionAmount'] + $total['pendingDocumentAmount']);

        $company = Company::where('companySystemID', $budgetMaster->companySystemID)->first();

        $localCurrency = CurrencyMaster::where('currencyID', $company->localCurrencyID)->first();
        $rptCurrency = CurrencyMaster::where('currencyID', $company->reportingCurrency)->first();

        $decimalPlaceLocal = !empty($localCurrency) ? $localCurrency->DecimalPlaces : 3;
        $decimalPlaceRpt = !empty($rptCurrency) ? $rptCurrency->DecimalPlaces : 2;


        $data = array('entity' => $budgetMaster->toArray(), 'reportData' => $reportData,
            'total' => $total, 'decimalPlaceLocal' => $decimalPlaceLocal, 'decimalPlaceRpt' => $decimalPlaceRpt);

        $templateName = "export_report.budget_summary_gl_code_wise";

        \Excel::create('finance', function ($excel) use ($data, $templateName) {
            $excel->sheet('New sheet', function ($sheet) use ($data, $templateName) {
                $sheet->loadView($templateName, $data);
            });
        })->download('csv');

        return $this->sendResponse($data, trans('custom.details_retrieved_successfully_1'));
    }

    public function budgetGLCodeWiseDetails(Request $request)
    {
        $input = $request->all();

        $result = $this->budgetGLCodeWiseDetailsData($input);

        return $this->sendResponse($result, trans('custom.details_retrieved_successfully_1'));
    }


    public function budgetGLCodeWiseDetailsData($input)
    {
         $total = 0;
        $glColumnName = "";
        // policy check -> Department wise budget check
        $DLBCPolicy = true; // new requiremnt no need to conider the policy
        $chartOfAccountControl = ChartOfAccount::where('chartOfAccountSystemID', $input['chartOfAccountID'])->select('controlAccountsSystemID')->first();

        if ($input['type'] == 1) {

        if($input['controlAccountsSystemID'] == 3)
        {
                $data = FixedAssetMaster::selectRaw('DATE_FORMAT(documentDate, "%Y") as year,companyID,serviceLineCode,COSTGLCODE as GLCode,documentSystemID,documentID,faCode as documentCode,faID as documentSystemCode,costUnitRpt as consumedRptAmount, costUnitRpt as actualConsumption')
                ->where('costglCodeSystemID', $input['chartOfAccountID'])
                ->where('serviceLineSystemID', $input['serviceLineSystemID'])
                ->where('approved',-1)
                ->get();
     
         }
         else
         {
            $data = BudgetConsumedData::where('companySystemID', $input['companySystemID'])
                                    ->where('serviceLineSystemID', $input['serviceLineSystemID'])
                                    ->when($chartOfAccountControl->controlAccountsSystemID != 3,function($query){
                                        $query->where(function($query) {
                                            $query->where(function($query) {
                                                    $query->where('documentSystemID', 2)
                                                          ->whereHas('purchase_order', function ($query) {
                                                                $query->whereIn('grvRecieved', [2, 1]);
                                                            })
                                                            ->with(['purchase_order' => function ($query) {
                                                                        $query->whereIn('grvRecieved', [2, 1]);
                                                            }]);
                                                })
                                                ->orWhere('documentSystemID', '!=', 2);
                                        });
                                    })
                                    ->when($chartOfAccountControl->controlAccountsSystemID == 3,function($query){
                                        $query->where('documentSystemID',22);
                                    })
                     
                                    ->where(function($query) {
                                        $query->whereNull('projectID')
                                              ->orWhere('projectID', 0);
                                      })
                                    ->where('companyFinanceYearID', $input['companyFinanceYearID'])
                                    ->where('chartOfAccountID', $input['chartOfAccountID'])
                                    ->where('consumeYN', -1)
                                    ->orderBy('budgetConsumedDataAutoID','desc')
                                    ->get();


            foreach ($data as $key => $value) {
                $actualConsumption = 0;
                if ($value->documentSystemID == 2 && isset($value->purchase_order->grvRecieved) && $value->purchase_order->grvRecieved == 1) {
                    
                    // $notRecivedPoFixedAsset = PurchaseOrderDetails::selectRaw('SUM((GRVcostPerUnitSupTransCur * segment_allocated_items.allocatedQty) - (GRVcostPerUnitSupTransCur * receivedQty)) as remainingAmount, SUM(GRVcostPerUnitSupTransCur * receivedQty) as receivedAmount')
                    //                                     ->where('financeGLcodebBSSystemID', $input['chartOfAccountID'])
                    //                                     ->where('purchaseOrderMasterID', $value->documentSystemCode)
                    //                                     ->where('itemFinanceCategoryID', 3)
                    //                                     ->join('segment_allocated_items', 'documentDetailAutoID', '=', 'purchaseOrderDetailsID')
                    //                                     ->where('segment_allocated_items.serviceLineSystemID', $input['serviceLineSystemID'])
                    //                                     ->whereHas('order', function($query) {
                    //                                         $query->where(function($query) {
                    //                                                 $query->where('projectID', 0)
                    //                                                       ->orWhereNull('projectID');
                    //                                             });
                    //                                     })
                    //                                     ->where('segment_allocated_items.documentSystemID', $value->documentSystemID)
                    //                                     ->groupBy('purchaseOrderMasterID')
                    //                                     ->first();

                    // if ($notRecivedPoFixedAsset) {
                    //     $currencyConversionRptAmount = \Helper::currencyConversion($input['companySystemID'], $value->purchase_order->supplierTransactionCurrencyID, $value->purchase_order->supplierTransactionCurrencyID, $notRecivedPoFixedAsset->remainingAmount);
                    //     $actualConsumption += $value->consumedRptAmount - $currencyConversionRptAmount['reportingAmount'];
                    // }

                    $notRecivedPoNonFixedAsset = PurchaseOrderDetails::selectRaw('SUM((GRVcostPerUnitSupTransCur * segment_allocated_items.allocatedQty)) as totalAmount,SUM((GRVcostPerUnitSupTransCur * segment_allocated_items.allocatedQty) - (GRVcostPerUnitSupTransCur * receivedQty)) as remainingAmount, SUM(GRVcostPerUnitSupTransCur * receivedQty) as receivedAmount')
                                                        ->where('financeGLcodePLSystemID', $input['chartOfAccountID'])
                                                        ->where('purchaseOrderMasterID', $value->documentSystemCode)
                                                        ->where('itemFinanceCategoryID','!=', 3)
                                                        ->join('segment_allocated_items', 'documentDetailAutoID', '=', 'purchaseOrderDetailsID')
                                                        ->where('segment_allocated_items.serviceLineSystemID', $input['serviceLineSystemID'])
                                                        ->whereHas('order', function($query) {
                                                            $query->where(function($query) {
                                                                    $query->where('projectID', 0)
                                                                          ->orWhereNull('projectID');
                                                                });
                                                        })
                                                        ->where('segment_allocated_items.documentSystemID', $value->documentSystemID)
                                                        ->groupBy('purchaseOrderMasterID')
                                                        ->first();

                                                   
                    if ($notRecivedPoNonFixedAsset) {
                        $grvApprovedPoAmount = 0;
						$grvDetails =  $value->purchase_order->grv_details;
                        foreach($grvDetails as $grv)
						{
							if($grv->grv_master->approved == -1)
							{
								//$grvApprovedPoAmount += $grv->grv_master->grvTotalComRptCurrency;
                                if($grv->financeGLcodePLSystemID == $value->chartOfAccountID)
                                {
                                    $grvApprovedPoAmount += $grv->netAmount;
                                }
							}
						}
                        $grvCommitedAmount = $notRecivedPoNonFixedAsset->totalAmount;
                        $currencyConversionGrvApprovedPoAmount = \Helper::currencyConversion($input['companySystemID'], $value->purchase_order->supplierTransactionCurrencyID, $value->purchase_order->supplierTransactionCurrencyID, $grvApprovedPoAmount);

                        $currencyConversionRptAmount = \Helper::currencyConversion($input['companySystemID'], $value->purchase_order->supplierTransactionCurrencyID, $value->purchase_order->supplierTransactionCurrencyID, $grvCommitedAmount);
                        $committedAmount = $currencyConversionRptAmount['reportingAmount'] - $currencyConversionGrvApprovedPoAmount['reportingAmount'];
                        $actualConsumption += $value->consumedRptAmount - $committedAmount;
                      

                    }



                } else {
                    $actualConsumption = $value->consumedRptAmount;
                }

                $value->actualConsumption = $actualConsumption;
            }
         }
            $total = array_sum(collect($data)->pluck('actualConsumption')->toArray());
        } else if ($input['type'] == 2) {
            $glIds = [$input['chartOfAccountID']];

            $fixed_assets =  FixedAssetMaster::where('companySystemID', $input['companySystemID'])
                            ->where('serviceLineSystemID', $input['serviceLineSystemID'])
                            ->whereIn('costglCodeSystemID', $glIds)
                            ->where('approved', 0)
                            ->where('confirmedYN', 1)
                            ->get();


            $data1 = PurchaseOrderDetails::whereHas('order', function ($q) use ($input,$DLBCPolicy) {
                                            $q->where('companySystemID', $input['companySystemID'])
                                            ->where('serviceLineSystemID', $input['serviceLineSystemID'])
                                            ->where('approved', 0)
                                            ->where('poCancelledYN', 0)
                                            ->where(function($query) {
                                                $query->where('projectID', 0)
                                                      ->orWhereNull('projectID');
                                            });
                                    })
                                    ->where('budgetYear', $input['Year'])
                                    ->where('itemFinanceCategoryID', '!=', 3)
                                    ->whereIn('financeGLcodePLSystemID', $glIds)
                                    ->whereNotNull('financeGLcodePLSystemID')
                                    ->with(['order'])
                                    ->get();

            $data2 = PurchaseOrderDetails::whereHas('order', function ($q) use ($input,$DLBCPolicy) {
                                                $q->where('companySystemID', $input['companySystemID'])
                                                ->where('serviceLineSystemID', $input['serviceLineSystemID'])
                                                ->where('approved', 0)
                                                ->where('poCancelledYN', 0)
                                                ->where(function($query) {
                                                    $query->where('projectID', 0)
                                                          ->orWhereNull('projectID');
                                                });
                                        })
                                        ->where('budgetYear', $input['Year'])
                                        ->where('itemFinanceCategoryID', 3)
                                        ->whereIn('financeGLcodebBSSystemID', $glIds)
                                        ->whereNotNull('financeGLcodebBSSystemID')
                                        ->with(['order'])
                                        ->get();


            $pendingDirectGRV1 = GRVDetails::whereHas('grv_master', function ($q) use ($input,$DLBCPolicy) {
                                                $q->where('companySystemID', $input['companySystemID'])
                                                ->where('approved', 0)
                                                ->where('grvCancelledYN', 0)
                                                ->where('serviceLineSystemID', $input['serviceLineSystemID'])
                                                ->whereHas('financeyear_by', function($query) use ($input) {
                                                    $query->whereYear('bigginingDate', $input['Year']);
                                                })
                                                ->where(function($query) {
                                                    $query->where('projectID', 0)
                                                          ->orWhereNull('projectID');
                                                });
                                        })
                                        ->where('itemFinanceCategoryID', '!=', 3)
                                        ->whereIn('financeGLcodePLSystemID', $glIds)
                                        ->whereNotNull('financeGLcodePLSystemID')
                                        ->where(function($query) {
                                            $query->where('detail_project_id', 0)
                                                  ->orWhereNull('detail_project_id');
                                        })
                                        ->with(['grv_master' => function($query) {
                                            $query->with(['financeyear_by']);
                                        }])
                                        ->get();

            $pendingDirectGRV2 = GRVDetails::whereHas('grv_master', function ($q) use ($input,$DLBCPolicy) {
                                                $q->where('companySystemID', $input['companySystemID'])
                                                ->where('approved', 0)
                                                ->where('grvCancelledYN', 0)
                                                ->where('serviceLineSystemID', $input['serviceLineSystemID'])
                                                ->whereHas('financeyear_by', function($query) use ($input) {
                                                    $query->whereYear('bigginingDate', $input['Year']);
                                                })
                                                ->where(function($query) {
                                                    $query->where('projectID', 0)
                                                          ->orWhereNull('projectID');
                                                });
                                        })
                                        ->where('itemFinanceCategoryID', 3)
                                        ->whereIn('financeGLcodebBSSystemID', $glIds)
                                        ->whereNotNull('financeGLcodebBSSystemID')
                                        ->where(function($query) {
                                            $query->where('detail_project_id', 0)
                                                  ->orWhereNull('detail_project_id');
                                        })
                                        ->with(['grv_master' => function($query) {
                                            $query->with(['financeyear_by']);
                                        }])
                                        ->get();

            $pendingSupplierInvoiceAmount = DirectInvoiceDetails::where('companySystemID', $input['companySystemID'])
                                                            ->whereIn('erp_directinvoicedetails.chartOfAccountSystemID', $glIds)
                                                            ->where('serviceLineSystemID', $input['serviceLineSystemID'])
                                                            ->whereHas('supplier_invoice_master', function($query) use ($input) {
                                                                $query->where('approved', 0)
                                                                      ->where('cancelYN', 0)
                                                                      ->whereIn('documentType', [1, 4])
                                                                      ->where('companySystemID', $input['companySystemID'])
                                                                       ->whereHas('financeyear_by', function($query) use ($input) {
                                                                        $query->whereYear('bigginingDate', $input['Year']);
                                                                      })
                                                                      ->where(function($query) {
                                                                        $query->whereNull('projectID')
                                                                              ->orWhere('projectID', 0);
                                                                      });
                                                             })
                                                            ->with(['supplier_invoice_master'])
                                                            ->get();

            $pendingSupplierItemInvoiceAmount1 = SupplierInvoiceDirectItem::whereHas('master', function($query) use ($input) {
                                                                $query->where('approved', 0)
                                                                      ->where('cancelYN', 0)
                                                                      ->where('documentType', 3)
                                                                      ->where('serviceLineSystemID', $input['serviceLineSystemID'])
                                                                      ->where('companySystemID', $input['companySystemID'])
                                                                      ->whereHas('financeyear_by', function($query) use ($input) {
                                                                        $query->whereYear('bigginingDate', $input['Year']);
                                                                      })
                                                                      ->where(function($query) {
                                                                        $query->whereNull('projectID')
                                                                              ->orWhere('projectID', 0);
                                                                      });
                                                             })
                                                            ->where('itemFinanceCategoryID', '!=', 3)
                                                            ->whereIn('financeGLcodePLSystemID', $glIds)
                                                            ->whereNotNull('financeGLcodePLSystemID')
                                                            ->with(['master' => function($query) {
                                                                $query->with(['financeyear_by']);
                                                            }])
                                                            ->get();

            $pendingSupplierItemInvoiceAmount2 = SupplierInvoiceDirectItem::whereHas('master', function($query) use ($input) {
                                                                $query->where('approved', 0)
                                                                      ->where('cancelYN', 0)
                                                                      ->where('documentType', 3)
                                                                      ->where('serviceLineSystemID', $input['serviceLineSystemID'])
                                                                      ->where('companySystemID', $input['companySystemID'])
                                                                      ->whereHas('financeyear_by', function($query) use ($input) {
                                                                        $query->whereYear('bigginingDate', $input['Year']);
                                                                      })
                                                                      ->where(function($query) {
                                                                        $query->whereNull('projectID')
                                                                              ->orWhere('projectID', 0);
                                                                      });
                                                             })
                                                            ->where('itemFinanceCategoryID', 3)
                                                            ->whereIn('financeGLcodebBSSystemID', $glIds)
                                                            ->whereNotNull('financeGLcodebBSSystemID')
                                                            ->with(['master' => function($query) {
                                                                $query->with(['financeyear_by']);
                                                            }])
                                                            ->get();

            $pendingPvAmount = DirectPaymentDetails::where('companySystemID', $input['companySystemID'])
                                                ->with(['master'])
                                                ->whereIn('erp_directpaymentdetails.chartOfAccountSystemID', $glIds)
                                                ->where('serviceLineSystemID', $input['serviceLineSystemID'])
                                                ->whereHas('master', function($query) use ($input) {
                                                    $query->where('approved', 0)
                                                          ->where('cancelYN', 0)
                                                          ->where('invoiceType', 3)
                                                          ->whereHas('financeyear_by', function($query) use ($input) {
                                                            $query->whereYear('bigginingDate', $input['Year']);
                                                          })
                                                          ->where('companySystemID', $input['companySystemID']);
                                                 })
                                                ->get();


            $pendingPurchaseRetuenAmount1 = PurchaseReturnDetails::whereHas('master', function($query) use ($input) {
                                                                     $query->where('approved', 0)
                                                                          ->where('serviceLineSystemID', $input['serviceLineSystemID'])
                                                                          ->where('companySystemID', $input['companySystemID'])
                                                                          ->whereHas('finance_year_by', function($query) use ($input) {
                                                                                $query->whereYear('bigginingDate', $input['Year']);
                                                                          });
                                                                })
                                                            ->where('itemFinanceCategoryID', '!=', 3)
                                                            ->whereIn('financeGLcodePLSystemID', $glIds)
                                                            ->whereNotNull('financeGLcodePLSystemID')
                                                            ->with(['master' => function($query) {
                                                                $query->with(['finance_year_by']);
                                                            }])
                                                            ->get();

            $pendingPurchaseRetuenAmount2 = PurchaseReturnDetails::whereHas('master', function($query) use ($input) {
                                                                     $query->where('approved', 0)
                                                                            ->where('serviceLineSystemID', $input['serviceLineSystemID'])
                                                                            ->where('companySystemID', $input['companySystemID'])
                                                                            ->whereHas('finance_year_by', function($query) use ($input) {
                                                                                $query->whereYear('bigginingDate', $input['Year']);
                                                                            });
                                                                })
                                                                ->where('itemFinanceCategoryID', 3)
                                                                ->whereIn('financeGLcodebBSSystemID', $glIds)
                                                                ->whereNotNull('financeGLcodebBSSystemID')
                                                                ->with(['master' => function($query) {
                                                                    $query->with(['finance_year_by']);
                                                                }])
                                                                ->get();


            $pendingJVAmount = JvDetail::whereHas('master', function($query) use ($input) {
                                                        $query->where('approved', 0)
                                                              ->where('companySystemID', $input['companySystemID'])
                                                              ->whereHas('financeyear_by', function($query) use ($input) {
                                                                    $query->whereYear('bigginingDate', $input['Year']);
                                                              });
                                                     })
                                                    ->where('serviceLineSystemID', $input['serviceLineSystemID'])
                                                    ->whereIn('erp_jvdetail.chartOfAccountSystemID', $glIds)
                                                    ->whereNotNull('erp_jvdetail.chartOfAccountSystemID')
                                                    ->with(['master' => function($query) {
                                                        $query->with(['financeyear_by']);
                                                    }])
                                                    ->get();


            $pendingDebitNoteAmount = DebitNoteDetails::whereHas('master', function($query) use ($input) {
                                                        $query->where('approved', 0)
                                                              ->where('companySystemID', $input['companySystemID'])
                                                              ->whereHas('finance_year_by', function($query) use ($input) {
                                                                    $query->whereYear('bigginingDate', $input['Year']);
                                                              });
                                                     })
                                                    ->where('serviceLineSystemID', $input['serviceLineSystemID'])
                                                    ->where('budgetYear', $input['Year'])
                                                    ->whereIn('erp_debitnotedetails.chartOfAccountSystemID', $glIds)
                                                    ->whereNotNull('erp_debitnotedetails.chartOfAccountSystemID')
                                                    ->with(['master' => function($query) {
                                                        $query->with(['finance_year_by']);
                                                    }])
                                                    ->get();


                $data = [];
                if($chartOfAccountControl->controlAccountsSystemID == 3)
                {
                    foreach ($fixed_assets as $key => $value) {
                        $temp = [];
                        $temp['companyID'] = $value->companyID;
                        $temp['serviceLine'] = $value->serviceLineCode;
                        $temp['financeGLcodePL'] = $value->COSTGLCODE;
                        $temp['budgetYear'] = Carbon::parse($value->documentDate)->format('Y');
                        $temp['documentCode'] = $value->faCode;
                        $temp['documentSystemCode'] = $value->docOrigin;
                        $temp['documentSystemID'] = $value->documentSystemID;
                        $temp['lineTotal'] = $value->costUnitRpt;
            
                        $data[] = $temp;
                    }
                }
                else
                {

        

                foreach ($pendingDebitNoteAmount as $key => $value) {
                    $temp = [];
                    $temp['companyID'] = $value->master->companyID;
                    $temp['serviceLine'] = $value->serviceLineCode;
                    $temp['financeGLcodePL'] = $value->glCode;
                    $temp['budgetYear'] = Carbon::parse($value->master->finance_year_by->bigginingDate)->format('Y');
                    $temp['documentCode'] = $value->master->debitNoteCode;
                    $temp['documentSystemCode'] = $value->master->debitNoteAutoID;
                    $temp['documentSystemID'] = $value->master->documentSystemID;
                    $temp['lineTotal'] = $value->comRptAmount * -1;

                    $data[] = $temp;
                }


                foreach ($pendingJVAmount as $key => $value) {
                    $temp = [];
                    $temp['companyID'] = $value->master->companyID;
                    $temp['serviceLine'] = $value->serviceLineCode;
                    $temp['financeGLcodePL'] = ChartOfAccount::getAccountCode($value->chartOfAccountSystemID);
                    $temp['budgetYear'] = Carbon::parse($value->master->financeyear_by->bigginingDate)->format('Y');
                    $temp['documentCode'] = $value->master->JVcode;
                    $temp['documentSystemCode'] = $value->master->jvMasterAutoId;
                    $temp['documentSystemID'] = $value->master->documentSystemID;

                    $chartOfAccounts = ChartOfAccount::where('chartOfAccountSystemID', $value->chartOfAccountSystemID)->select('controlAccounts')->first();

                    if ($chartOfAccounts->controlAccounts == 'PLI'){
                        if($value->creditAmount > 0 && $value->debitAmount == 0) {
                            $amount = $value->creditAmount;
                        } else {
                            $amount = $value->debitAmount * -1;
                        }
                    } else {
                        if($value->debitAmount > 0 && $value->creditAmount == 0) {
                            $amount = $value->debitAmount;
                        } else {
                            $amount = $value->creditAmount * -1;
                        }
                    }

                    $currencyConversionRptAmount = \Helper::currencyConversion($value->companySystemID, $value->currencyID, $value->currencyID, $amount);

                    $temp['lineTotal'] = $currencyConversionRptAmount['reportingAmount'];

                    $data[] = $temp;
                }

                 foreach ($pendingPurchaseRetuenAmount1 as $key => $value) {
                    $temp = [];
                    $temp['companyID'] = $value->master->companyID;
                    $temp['serviceLine'] = $value->master->serviceLineCode;
                    $temp['financeGLcodePL'] = $value->financeGLcodePL;
                    $temp['budgetYear'] = Carbon::parse($value->master->finance_year_by->bigginingDate)->format('Y');
                    $temp['documentCode'] = $value->master->purchaseReturnCode;
                    $temp['documentSystemCode'] = $value->master->purhaseReturnAutoID;
                    $temp['documentSystemID'] = $value->master->documentSystemID;
                    $temp['lineTotal'] = $value->GRVcostPerUnitComRptCur * $value->noQty * -1;

                    $data[] = $temp;
                }

                //  foreach ($pendingPurchaseRetuenAmount2 as $key => $value) {
                //     $temp = [];
                //     $temp['companyID'] = $value->master->companyID;
                //     $temp['serviceLine'] = $value->master->serviceLineCode;
                //     $temp['financeGLcodePL'] = $value->financeGLcodebBS;
                //     $temp['budgetYear'] = Carbon::parse($value->master->finance_year_by->bigginingDate)->format('Y');
                //     $temp['documentCode'] = $value->master->purchaseReturnCode;
                //     $temp['documentSystemCode'] = $value->master->purhaseReturnAutoID;
                //     $temp['documentSystemID'] = $value->master->documentSystemID;
                //     $temp['lineTotal'] = $value->GRVcostPerUnitComRptCur * $value->noQty * -1;

                //     $data[] = $temp;
                // }

                foreach ($data1 as $key => $value) {
                    $temp = [];
                    $temp['companyID'] = $value->order->companyID;
                    $temp['serviceLine'] = $value->order->serviceLine;
                    $temp['financeGLcodePL'] = $value->financeGLcodePL;
                    $temp['budgetYear'] = $value->budgetYear;
                    $temp['documentCode'] = $value->order->purchaseOrderCode;
                    $temp['documentSystemCode'] = $value->order->purchaseOrderID;
                    $temp['documentSystemID'] = $value->order->documentSystemID;
                    $temp['lineTotal'] = $value->GRVcostPerUnitComRptCur * $value->noQty;

                    $data[] = $temp;
                }

                //  foreach ($data2 as $key => $value) {
                //     $temp = [];
                //     $temp['companyID'] = $value->order->companyID;
                //     $temp['serviceLine'] = $value->order->serviceLine;
                //     $temp['financeGLcodePL'] = $value->financeGLcodebBS;
                //     $temp['budgetYear'] = $value->budgetYear;
                //     $temp['documentCode'] = $value->order->purchaseOrderCode;
                //     $temp['documentSystemCode'] = $value->order->purchaseOrderID;
                //     $temp['documentSystemID'] = $value->order->documentSystemID;
                //     $temp['lineTotal'] = $value->GRVcostPerUnitComRptCur * $value->noQty;

                //     $data[] = $temp;
                // }

                foreach ($pendingDirectGRV1 as $key => $value) {
                    $temp = [];
                    $temp['companyID'] = $value->grv_master->companyID;
                    $temp['serviceLine'] = $value->grv_master->serviceLineCode;
                    $temp['financeGLcodePL'] = $value->financeGLcodePL;
                    $temp['budgetYear'] = Carbon::parse($value->grv_master->financeyear_by->bigginingDate)->format('Y');
                    $temp['documentCode'] = $value->grv_master->grvPrimaryCode;
                    $temp['documentSystemCode'] = $value->grv_master->grvAutoID;
                    $temp['documentSystemID'] = $value->grv_master->documentSystemID;
                    $temp['lineTotal'] = $value->GRVcostPerUnitComRptCur * $value->noQty;

                    $data[] = $temp;
                }

                //  foreach ($pendingDirectGRV2 as $key => $value) {
                //     $temp = [];
                //     $temp['companyID'] = $value->grv_master->companyID;
                //     $temp['serviceLine'] = $value->grv_master->serviceLineCode;
                //     $temp['financeGLcodePL'] = $value->financeGLcodebBS;
                //     $temp['budgetYear'] = Carbon::parse($value->grv_master->financeyear_by->bigginingDate)->format('Y');
                //     $temp['documentCode'] = $value->grv_master->grvPrimaryCode;
                //     $temp['documentSystemCode'] = $value->grv_master->grvAutoID;
                //     $temp['documentSystemID'] = $value->grv_master->documentSystemID;
                //     $temp['lineTotal'] = $value->GRVcostPerUnitComRptCur * $value->noQty;

                //     $data[] = $temp;
                // }

                foreach ($pendingSupplierItemInvoiceAmount1 as $key => $value) {
                    $temp = [];
                    $temp['companyID'] = $value->master->companyID;
                    $temp['serviceLine'] = $value->master->serviceLine;
                    $temp['financeGLcodePL'] = ChartOfAccount::getAccountCode($value->financeGLcodePLSystemID);
                    $temp['budgetYear'] = Carbon::parse($value->master->financeyear_by->bigginingDate)->format('Y');
                    $temp['documentCode'] = $value->master->bookingInvCode;
                    $temp['documentSystemCode'] = $value->master->bookingSuppMasInvAutoID;
                    $temp['documentSystemID'] = $value->master->documentSystemID;
                    $temp['lineTotal'] = $value->costPerUnitComRptCur * $value->noQty;

                    $data[] = $temp;
                }

                // foreach ($pendingSupplierItemInvoiceAmount2 as $key => $value) {
                //     $temp = [];
                //     $temp['companyID'] = $value->master->companyID;
                //     $temp['serviceLine'] = SegmentMaster::getSegmentCode($value->master->serviceLineSystemID);
                //     $temp['financeGLcodePL'] = ChartOfAccount::getAccountCode($value->financeGLcodebBSSystemID);
                //     $temp['budgetYear'] = Carbon::parse($value->master->financeyear_by->bigginingDate)->format('Y');
                //     $temp['documentCode'] = $value->master->bookingInvCode;
                //     $temp['documentSystemCode'] = $value->master->bookingSuppMasInvAutoID;
                //     $temp['documentSystemID'] = $value->master->documentSystemID;
                //     $temp['lineTotal'] = $value->costPerUnitComRptCur * $value->noQty;

                //     $data[] = $temp;
                // }

                foreach ($pendingSupplierInvoiceAmount as $key => $value) {
                    $temp = [];
                    $temp['companyID'] = $value->supplier_invoice_master->companyID;
                    $temp['serviceLine'] = $value->serviceLineCode;
                    $temp['financeGLcodePL'] = $value->glCode;
                    $temp['budgetYear'] = $value->budgetYear;
                    $temp['documentCode'] = $value->supplier_invoice_master->bookingInvCode;
                    $temp['documentSystemCode'] = $value->supplier_invoice_master->bookingSuppMasInvAutoID;
                    $temp['documentSystemID'] = $value->supplier_invoice_master->documentSystemID;
                    $temp['lineTotal'] = $value->netAmountRpt;

                    $data[] = $temp;
                }

                foreach ($pendingPvAmount as $key => $value) {
                    $temp = [];
                    $temp['lineTotal'] = $value->comRptAmount;
                    $temp['companyID'] = $value->master->companyID;
                    $temp['serviceLine'] = $value->serviceLineCode;
                    $temp['financeGLcodePL'] = $value->glCode;
                    $temp['budgetYear'] = $value->budgetYear;
                    $temp['documentCode'] = $value->master->BPVcode;
                    $temp['documentSystemCode'] = $value->master->PayMasterAutoId;
                    $temp['documentSystemID'] = $value->master->documentSystemID;

                    $data[] = $temp;
                }
            }
                $total = array_sum(collect($data)->pluck('lineTotal')->toArray());
        } else if ($input['type'] == 3) {
             $data = BudgetConsumedData::where('companySystemID', $input['companySystemID'])
                                    ->where('serviceLineSystemID', $input['serviceLineSystemID'])
                                    ->where('companyFinanceYearID', $input['companyFinanceYearID'])
                                    ->where('consumeYN', -1)
                                    ->with(['purchase_order' => function ($query) use($chartOfAccountControl){
                                        $query->with(['detail','grv_details'=>function($query){
                                            $query->select('grvDetailsID','grvAutoID','purchaseOrderMastertID','purchaseOrderDetailsID','financeGLcodePLSystemID','netAmount')->with(['grv_master'=>function($query){
                                                $query->select('grvAutoID','grvPrimaryCode','approved','grvConfirmedYN','grvTotalComRptCurrency');
                                            }]);
                                        }])->when($chartOfAccountControl->controlAccountsSystemID != 3,function($query){
                                           // $query->where('grvRecieved', '!=', 2);
                                        });
                                    }])
                                    ->where('documentSystemID', 2)
                                    ->when($chartOfAccountControl->controlAccountsSystemID != 3,function($query){
                                        $query->whereHas('purchase_order', function ($query) {
                                            //$query->where('grvRecieved', '!=', 2);
                                        });
                                    })
                                    ->when($chartOfAccountControl->controlAccountsSystemID == 3,function($query){
                                        $query->groupBy('documentCode');
                                    })
                                    ->where(function($query) {
                                        $query->whereNull('projectID')
                                              ->orWhere('projectID', 0);
                                      })
                                    ->join(DB::raw('(SELECT
                                                        erp_companyreporttemplatelinks.templateDetailID as templatesDetailsAutoID,
                                                        erp_companyreporttemplatelinks.templateMasterID,
                                                        erp_companyreporttemplatelinks.glAutoID as chartOfAccountSystemID,
                                                        erp_companyreporttemplatelinks.glCode 
                                                        FROM
                                                        erp_companyreporttemplatelinks
                                                        WHERE erp_companyreporttemplatelinks.templateMasterID =' . $input['templatesMasterAutoID'] . ' AND erp_companyreporttemplatelinks.templateDetailID = ' . $input['templateDetailID'] . ' AND erp_companyreporttemplatelinks.glAutoID is not null) as tem_gl'),
                                        function ($join) {
                                            $join->on('erp_budgetconsumeddata.chartOfAccountID', '=', 'tem_gl.chartOfAccountSystemID');
                                        })
                                    ->get();
    
            $gl_details = [];
            foreach ($data as $key => $value) {
         
                $committedAmount = 0;
                $fixedCOmmitedAmount = 0;
                $totalCommitedAmount = 0;
                $com = 0;
                if (isset($value->purchase_order->grvRecieved) && $value->purchase_order->grvRecieved == 0) {
                    $committedAmount += $value->consumedRptAmount;
                } else {
                    // $notRecivedPoFixedAsset = PurchaseOrderDetails::selectRaw('SUM((GRVcostPerUnitSupTransCur * segment_allocated_items.allocatedQty) - (GRVcostPerUnitSupTransCur * receivedQty)) as remainingAmount, SUM(GRVcostPerUnitSupTransCur * receivedQty) as receivedAmount')
                    //                                     ->join(DB::raw('(SELECT
                    //                                                     erp_companyreporttemplatelinks.templateDetailID as templatesDetailsAutoID,
                    //                                                     erp_companyreporttemplatelinks.templateMasterID,
                    //                                                     erp_companyreporttemplatelinks.glAutoID as chartOfAccountSystemID,
                    //                                                     erp_companyreporttemplatelinks.glCode
                    //                                                     FROM
                    //                                                     erp_companyreporttemplatelinks
                    //                                                     WHERE erp_companyreporttemplatelinks.templateMasterID =' . $input['templatesMasterAutoID'] . ' AND erp_companyreporttemplatelinks.templateDetailID = ' . $input['templateDetailID'] . ' AND erp_companyreporttemplatelinks.glAutoID is not null) as tem_gl'),
                    //                                     function ($join) {
                    //                                         $join->on('erp_purchaseorderdetails.financeGLcodebBSSystemID', '=', 'tem_gl.chartOfAccountSystemID');
                    //                                     })
                    //                                     ->where('purchaseOrderMasterID', $value->documentSystemCode)
                    //                                     ->where('itemFinanceCategoryID', 3)
                    //                                     ->join('segment_allocated_items', 'documentDetailAutoID', '=', 'purchaseOrderDetailsID')
                    //                                     ->where('segment_allocated_items.serviceLineSystemID', $input['serviceLineSystemID'])
                    //                                     ->whereHas('order', function($query) {
                    //                                         $query->where(function($query) {
                    //                                                 $query->where('projectID', 0)
                    //                                                       ->orWhereNull('projectID');
                    //                                             });
                    //                                     })
                    //                                     ->where('segment_allocated_items.documentSystemID', $value->documentSystemID)
                    //                                     ->groupBy('purchaseOrderMasterID')
                    //                                     ->first();

                    // if ($notRecivedPoFixedAsset) {
                    //     $currencyConversionRptAmount = \Helper::currencyConversion($input['companySystemID'], $value->purchase_order->supplierTransactionCurrencyID, $value->purchase_order->supplierTransactionCurrencyID, $notRecivedPoFixedAsset->remainingAmount);
                    //     $committedAmount += $currencyConversionRptAmount['reportingAmount'];
                    // }

                    $notRecivedPoNonFixedAsset = PurchaseOrderDetails::selectRaw('SUM((GRVcostPerUnitSupTransCur * segment_allocated_items.allocatedQty)) as totalAmount,SUM((GRVcostPerUnitSupTransCur * segment_allocated_items.allocatedQty) - (GRVcostPerUnitSupTransCur * receivedQty)) as remainingAmount, SUM(GRVcostPerUnitSupTransCur * receivedQty) as receivedAmount')
                                                        ->join(DB::raw('(SELECT
                                                                        erp_companyreporttemplatelinks.templateDetailID as templatesDetailsAutoID,
                                                                        erp_companyreporttemplatelinks.templateMasterID,
                                                                        erp_companyreporttemplatelinks.glAutoID as chartOfAccountSystemID,
                                                                        erp_companyreporttemplatelinks.glCode 
                                                                        FROM
                                                                        erp_companyreporttemplatelinks
                                                                        WHERE erp_companyreporttemplatelinks.templateMasterID =' . $input['templatesMasterAutoID'] . ' AND erp_companyreporttemplatelinks.templateDetailID = ' . $input['templateDetailID'] . ' AND erp_companyreporttemplatelinks.glAutoID is not null) as tem_gl'),
                                                        function ($join) {
                                                            $join->on('erp_purchaseorderdetails.financeGLcodePLSystemID', '=', 'tem_gl.chartOfAccountSystemID');
                                                        })
                                                        ->where('purchaseOrderMasterID', $value->documentSystemCode)
                                                        //->where('itemFinanceCategoryID', '!=',3)
                                                        ->join('segment_allocated_items', 'documentDetailAutoID', '=', 'purchaseOrderDetailsID')
                                                        ->where('segment_allocated_items.serviceLineSystemID', $input['serviceLineSystemID'])
                                                        ->whereHas('order', function($query) {
                                                            $query->where(function($query) {
                                                                    $query->where('projectID', 0)
                                                                          ->orWhereNull('projectID');
                                                                });
                                                        })
                                                        ->where('segment_allocated_items.documentSystemID', $value->documentSystemID)
                                                        ->groupBy('purchaseOrderMasterID')
                                                        ->first();


                     $notRecivedPoNon = PurchaseOrderDetails::selectRaw('SUM((GRVcostPerUnitSupTransCur * segment_allocated_items.allocatedQty)) as totalAmount,SUM((GRVcostPerUnitSupTransCur * segment_allocated_items.allocatedQty) - (GRVcostPerUnitSupTransCur * receivedQty)) as remainingAmount, SUM(GRVcostPerUnitSupTransCur * receivedQty) as receivedAmount')
                                                        ->join(DB::raw('(SELECT
                                                                        erp_companyreporttemplatelinks.templateDetailID as templatesDetailsAutoID,
                                                                        erp_companyreporttemplatelinks.templateMasterID,
                                                                        erp_companyreporttemplatelinks.glAutoID as chartOfAccountSystemID,
                                                                        erp_companyreporttemplatelinks.glCode 
                                                                        FROM
                                                                        erp_companyreporttemplatelinks
                                                                        WHERE erp_companyreporttemplatelinks.templateMasterID =' . $input['templatesMasterAutoID'] . ' AND erp_companyreporttemplatelinks.templateDetailID = ' . $input['templateDetailID'] . ' AND erp_companyreporttemplatelinks.glAutoID is not null) as tem_gl'),
                                                        function ($join) {
                                                            $join->on('erp_purchaseorderdetails.financeGLcodePLSystemID', '=', 'tem_gl.chartOfAccountSystemID');
                                                        })
                                                        ->where('purchaseOrderMasterID', $value->documentSystemCode)
                                                        //->where('itemFinanceCategoryID', '!=',3)
                                                        ->join('segment_allocated_items', 'documentDetailAutoID', '=', 'purchaseOrderDetailsID')
                                                        ->where('segment_allocated_items.serviceLineSystemID', $input['serviceLineSystemID'])
                                                        ->whereHas('order', function($query) {
                                                            $query->where(function($query) {
                                                                    $query->where('projectID', 0)
                                                                          ->orWhereNull('projectID');
                                                                });
                                                        })
                                                        ->where('segment_allocated_items.documentSystemID', $value->documentSystemID)
                                                        ->where('financeGLcodePLSystemID', $value->chartOfAccountID)
                                                        ->groupBy('purchaseOrderMasterID')
                                                        ->first();

                    if ($notRecivedPoNonFixedAsset) {

                        if($chartOfAccountControl->controlAccountsSystemID == 3)
                        {


                            if(isset($value->purchase_order->grv_details))
                            {   
                                $grvDetails =  $value->purchase_order->grv_details;

                           
                                        foreach($value->purchase_order->detail as $gl)
                                        {   

                                            $poDetailAllocatedAmount = PurchaseOrderDetails::selectRaw('itemFinanceCategoryID,SUM((GRVcostPerUnitSupTransCur * segment_allocated_items.allocatedQty) - (GRVcostPerUnitSupTransCur * receivedQty)) as remainingAmount, SUM(GRVcostPerUnitSupTransCur * receivedQty) as receivedAmount')
                                            ->join('segment_allocated_items', 'documentDetailAutoID', '=', 'purchaseOrderDetailsID')
                                            ->where('purchaseOrderDetailsID', $gl->purchaseOrderDetailsID)
                                            ->first();

                                            if (!in_array($gl->financeGLcodePLSystemID, $gl_details))
                                            {
                                                if($gl->itemFinanceCategoryID == 3)
                                                {
                                                   
                                                    $fixed_assets =  FixedAssetMaster::where('costglCodeSystemID',$gl->financeGLcodePLSystemID)
                                                    ->where('docOriginDocumentSystemID',3)
                                                    ->get();
                
                                                        if($fixed_assets)
                                                        {
                                                        
                                                            foreach($fixed_assets as $asset)
                                                            {
                                                                if($asset->approved == -1)
                                                                {
                                                                    $fixedCOmmitedAmount += $asset->COSTUNIT;
                                                                }
                                                            }
                                                        }
    
    
                                                     array_push($gl_details,$gl->financeGLcodePLSystemID);
                                                }
                           
                                            }
                                            $totalCommitedAmount += $poDetailAllocatedAmount->remainingAmount + $poDetailAllocatedAmount->receivedAmount;


                                        }
                         
                          
                            }
                            $finalCommitment =     $totalCommitedAmount - $fixedCOmmitedAmount;
                            $currencyConversionRptAmount = \Helper::currencyConversion($input['companySystemID'], $value->purchase_order->supplierTransactionCurrencyID, $value->purchase_order->supplierTransactionCurrencyID, $finalCommitment);
                            $committedAmount += $currencyConversionRptAmount['reportingAmount'];
                          

                        }
                        else
                        {
                            // $currencyConversionRptAmount = \Helper::currencyConversion($input['companySystemID'], $value->purchase_order->supplierTransactionCurrencyID, $value->purchase_order->supplierTransactionCurrencyID, $notRecivedPoNonFixedAsset->remainingAmount);
                            // $committedAmount += $currencyConversionRptAmount['reportingAmount'];


                            
                            $grvApprovedPoAmount = 0;
                            $grvDetails =  $value->purchase_order->grv_details;
                            foreach($grvDetails as $grv)
                            {
                                if($grv->grv_master->approved == -1)
                                {
                                    if($grv->financeGLcodePLSystemID == $value->chartOfAccountID)
                                    {
                                        $grvApprovedPoAmount += $grv->netAmount;
                                    }
                                }
                            }

                           

                            $currencyConversionGrvApprovedPoAmount = \Helper::currencyConversion($input['companySystemID'], $value->purchase_order->supplierTransactionCurrencyID, $value->purchase_order->supplierTransactionCurrencyID, $grvApprovedPoAmount);

                            $grvCommitedAmount = $notRecivedPoNon->totalAmount;
                            $currencyConversionRptAmount = \Helper::currencyConversion($input['companySystemID'], $value->purchase_order->supplierTransactionCurrencyID, $value->purchase_order->supplierTransactionCurrencyID, $grvCommitedAmount);
                            $committedAmount += $currencyConversionRptAmount['reportingAmount'] - $currencyConversionGrvApprovedPoAmount['reportingAmount'];        
                           
                        }
               
                    }
                }

                    $value->committedAmount = $committedAmount;
              
              
               
            }
            $total = array_sum(collect($data)->pluck('committedAmount')->toArray());
        } else if ($input['type'] == 4) {
            $glData = ReportTemplateLinks::where('templateMasterID', $input['templatesMasterAutoID'])
                                ->where('templateDetailID', $input['templateDetailID'])
                                ->whereNotNull('glAutoID')->get();

            $glIds = collect($glData)->pluck('glAutoID')->toArray();

            $fixed_assets =  FixedAssetMaster::where('companySystemID', $input['companySystemID'])
                            ->where('serviceLineSystemID', $input['serviceLineSystemID'])
                            ->whereIn('costglCodeSystemID', $glIds)
                            ->where('approved', 0)
                            ->where('confirmedYN', 1)
                            ->get();

            $data1 = [];
            

            $data2 = PurchaseOrderDetails::whereHas('order', function ($q) use ($input,$DLBCPolicy) {
                                                $q->where('companySystemID', $input['companySystemID'])
                                                ->where('serviceLineSystemID', $input['serviceLineSystemID'])
                                                ->where('approved', 0)
                                                ->where('poCancelledYN', 0)
                                                ->where(function($query) {
                                                    $query->where('projectID', 0)
                                                          ->orWhereNull('projectID');
                                                });
                                        })
                                        ->where('budgetYear', $input['Year'])
                                        ->where('itemFinanceCategoryID', 3)
                                        // ->whereIn('financeGLcodebBSSystemID', $glIds)
                                        ->whereNotNull('financeGLcodebBSSystemID')
                                        ->join(DB::raw('(SELECT
                                                        erp_companyreporttemplatelinks.templateDetailID as templatesDetailsAutoID,
                                                        erp_companyreporttemplatelinks.templateMasterID,
                                                        erp_companyreporttemplatelinks.glAutoID as chartOfAccountSystemID,
                                                        erp_companyreporttemplatelinks.glCode 
                                                        FROM
                                                        erp_companyreporttemplatelinks
                                                        WHERE erp_companyreporttemplatelinks.templateMasterID =' . $input['templatesMasterAutoID'] . ' AND erp_companyreporttemplatelinks.templateDetailID = ' . $input['templateDetailID'] . ' AND erp_companyreporttemplatelinks.glAutoID is not null) as tem_gl'),
                                        function ($join) {
                                            $join->on('erp_purchaseorderdetails.financeGLcodebBSSystemID', '=', 'tem_gl.chartOfAccountSystemID');
                                        })
                                        ->with(['order'])
                                        ->get();


            $pendingDirectGRV1 = GRVDetails::whereHas('grv_master', function ($q) use ($input,$DLBCPolicy) {
                                                $q->where('companySystemID', $input['companySystemID'])
                                                ->where('approved', 0)
                                                ->where('grvConfirmedYN', 1)
                                                ->where('grvCancelledYN', 0)
                                                ->where('serviceLineSystemID', $input['serviceLineSystemID'])
                                                ->whereHas('financeyear_by', function($query) use ($input) {
                                                    $query->whereYear('bigginingDate', $input['Year']);
                                                })
                                                ->where(function($query) {
                                                    $query->where('projectID', 0)
                                                          ->orWhereNull('projectID');
                                                });
                                        })
                                        ->where('itemFinanceCategoryID', '!=', 3)
                                        // ->whereIn('financeGLcodePLSystemID', $glIds)
                                        ->whereNotNull('financeGLcodePLSystemID')
                                         ->join(DB::raw('(SELECT
                                                        erp_companyreporttemplatelinks.templateDetailID as templatesDetailsAutoID,
                                                        erp_companyreporttemplatelinks.templateMasterID,
                                                        erp_companyreporttemplatelinks.glAutoID as chartOfAccountSystemID,
                                                        erp_companyreporttemplatelinks.glCode 
                                                        FROM
                                                        erp_companyreporttemplatelinks
                                                        WHERE erp_companyreporttemplatelinks.templateMasterID =' . $input['templatesMasterAutoID'] . ' AND erp_companyreporttemplatelinks.templateDetailID = ' . $input['templateDetailID'] . ' AND erp_companyreporttemplatelinks.glAutoID is not null) as tem_gl'),
                                        function ($join) {
                                            $join->on('erp_grvdetails.financeGLcodePLSystemID', '=', 'tem_gl.chartOfAccountSystemID');
                                        })
                                        ->where(function($query) {
                                            $query->where('detail_project_id', 0)
                                                  ->orWhereNull('detail_project_id');
                                        })
                                        ->with(['grv_master' => function($query) {
                                            $query->with(['financeyear_by']);
                                        }])
                                        ->get();

            $pendingDirectGRV2 = GRVDetails::whereHas('grv_master', function ($q) use ($input,$DLBCPolicy) {
                                                $q->where('companySystemID', $input['companySystemID'])
                                                ->where('approved', 0)
                                                ->where('grvCancelledYN', 0)
                                                ->where('serviceLineSystemID', $input['serviceLineSystemID'])
                                                ->whereHas('financeyear_by', function($query) use ($input) {
                                                    $query->whereYear('bigginingDate', $input['Year']);
                                                })
                                                ->where(function($query) {
                                                    $query->where('projectID', 0)
                                                          ->orWhereNull('projectID');
                                                });
                                        })
                                        ->where('itemFinanceCategoryID', 3)
                                        // ->whereIn('financeGLcodebBSSystemID', $glIds)
                                        ->whereNotNull('financeGLcodebBSSystemID')
                                        ->join(DB::raw('(SELECT
                                                        erp_companyreporttemplatelinks.templateDetailID as templatesDetailsAutoID,
                                                        erp_companyreporttemplatelinks.templateMasterID,
                                                        erp_companyreporttemplatelinks.glAutoID as chartOfAccountSystemID,
                                                        erp_companyreporttemplatelinks.glCode 
                                                        FROM
                                                        erp_companyreporttemplatelinks
                                                        WHERE erp_companyreporttemplatelinks.templateMasterID =' . $input['templatesMasterAutoID'] . ' AND erp_companyreporttemplatelinks.templateDetailID = ' . $input['templateDetailID'] . ' AND erp_companyreporttemplatelinks.glAutoID is not null) as tem_gl'),
                                        function ($join) {
                                            $join->on('erp_grvdetails.financeGLcodebBSSystemID', '=', 'tem_gl.chartOfAccountSystemID');
                                        })
                                        ->where(function($query) {
                                            $query->where('detail_project_id', 0)
                                                  ->orWhereNull('detail_project_id');
                                        })
                                        ->with(['grv_master' => function($query) {
                                            $query->with(['financeyear_by']);
                                        }])
                                        ->get();

            $pendingSupplierInvoiceAmount = DirectInvoiceDetails::where('companySystemID', $input['companySystemID'])
                                                            // ->whereIn('erp_directinvoicedetails.chartOfAccountSystemID', $glIds)
                                                            ->where('serviceLineSystemID', $input['serviceLineSystemID'])
                                                            ->whereHas('supplier_invoice_master', function($query) use ($input) {
                                                                $query->where('approved', 0)
                                                                      ->where('cancelYN', 0)
                                                                      ->whereIn('documentType', [1, 4])
                                                                      ->where('companySystemID', $input['companySystemID'])
                                                                       ->whereHas('financeyear_by', function($query) use ($input) {
                                                                        $query->whereYear('bigginingDate', $input['Year']);
                                                                      })
                                                                      ->where(function($query) {
                                                                        $query->whereNull('projectID')
                                                                              ->orWhere('projectID', 0);
                                                                      });
                                                             })
                                                            ->join(DB::raw('(SELECT
                                                                            erp_companyreporttemplatelinks.templateDetailID as templatesDetailsAutoID,
                                                                            erp_companyreporttemplatelinks.templateMasterID,
                                                                            erp_companyreporttemplatelinks.glAutoID as chartOfAccountSystemID,
                                                                            erp_companyreporttemplatelinks.glCode 
                                                                            FROM
                                                                            erp_companyreporttemplatelinks
                                                                            WHERE erp_companyreporttemplatelinks.templateMasterID =' . $input['templatesMasterAutoID'] . ' AND erp_companyreporttemplatelinks.templateDetailID = ' . $input['templateDetailID'] . ' AND erp_companyreporttemplatelinks.glAutoID is not null) as tem_gl'),
                                                            function ($join) {
                                                                $join->on('erp_directinvoicedetails.chartOfAccountSystemID', '=', 'tem_gl.chartOfAccountSystemID');
                                                            })
                                                            ->with(['supplier_invoice_master'])
                                                            ->get();

            $pendingSupplierItemInvoiceAmount1 = SupplierInvoiceDirectItem::whereHas('master', function($query) use ($input) {
                                                                $query->where('approved', 0)
                                                                      ->where('cancelYN', 0)
                                                                      ->where('documentType', 3)
                                                                      ->where('serviceLineSystemID', $input['serviceLineSystemID'])
                                                                      ->where('companySystemID', $input['companySystemID'])
                                                                      ->whereHas('financeyear_by', function($query) use ($input) {
                                                                        $query->whereYear('bigginingDate', $input['Year']);
                                                                      })
                                                                      ->where(function($query) {
                                                                        $query->whereNull('projectID')
                                                                              ->orWhere('projectID', 0);
                                                                      });
                                                             })
                                                            ->where('itemFinanceCategoryID', '!=', 3)
                                                            // ->whereIn('financeGLcodePLSystemID', $glIds)
                                                            ->whereNotNull('financeGLcodePLSystemID')
                                                            ->join(DB::raw('(SELECT
                                                                            erp_companyreporttemplatelinks.templateDetailID as templatesDetailsAutoID,
                                                                            erp_companyreporttemplatelinks.templateMasterID,
                                                                            erp_companyreporttemplatelinks.glAutoID as chartOfAccountSystemID,
                                                                            erp_companyreporttemplatelinks.glCode 
                                                                            FROM
                                                                            erp_companyreporttemplatelinks
                                                                            WHERE erp_companyreporttemplatelinks.templateMasterID =' . $input['templatesMasterAutoID'] . ' AND erp_companyreporttemplatelinks.templateDetailID = ' . $input['templateDetailID'] . ' AND erp_companyreporttemplatelinks.glAutoID is not null) as tem_gl'),
                                                            function ($join) {
                                                                $join->on('supplier_invoice_items.financeGLcodePLSystemID', '=', 'tem_gl.chartOfAccountSystemID');
                                                            })
                                                            ->with(['master' => function($query) {
                                                                $query->with(['financeyear_by']);
                                                            }])
                                                            ->get();

            $pendingSupplierItemInvoiceAmount2 = SupplierInvoiceDirectItem::whereHas('master', function($query) use ($input) {
                                                                $query->where('approved', 0)
                                                                      ->where('cancelYN', 0)
                                                                      ->where('documentType', 3)
                                                                      ->where('serviceLineSystemID', $input['serviceLineSystemID'])
                                                                      ->where('companySystemID', $input['companySystemID'])
                                                                      ->whereHas('financeyear_by', function($query) use ($input) {
                                                                        $query->whereYear('bigginingDate', $input['Year']);
                                                                      })
                                                                      ->where(function($query) {
                                                                        $query->whereNull('projectID')
                                                                              ->orWhere('projectID', 0);
                                                                      });
                                                             })
                                                            ->where('itemFinanceCategoryID', 3)
                                                            // ->whereIn('financeGLcodebBSSystemID', $glIds)
                                                            ->whereNotNull('financeGLcodebBSSystemID')
                                                             ->join(DB::raw('(SELECT
                                                                            erp_companyreporttemplatelinks.templateDetailID as templatesDetailsAutoID,
                                                                            erp_companyreporttemplatelinks.templateMasterID,
                                                                            erp_companyreporttemplatelinks.glAutoID as chartOfAccountSystemID,
                                                                            erp_companyreporttemplatelinks.glCode 
                                                                            FROM
                                                                            erp_companyreporttemplatelinks
                                                                            WHERE erp_companyreporttemplatelinks.templateMasterID =' . $input['templatesMasterAutoID'] . ' AND erp_companyreporttemplatelinks.templateDetailID = ' . $input['templateDetailID'] . ' AND erp_companyreporttemplatelinks.glAutoID is not null) as tem_gl'),
                                                            function ($join) {
                                                                $join->on('supplier_invoice_items.financeGLcodebBSSystemID', '=', 'tem_gl.chartOfAccountSystemID');
                                                            })
                                                            ->with(['master' => function($query) {
                                                                $query->with(['financeyear_by']);
                                                            }])
                                                            ->get();

            $pendingPvAmount = DirectPaymentDetails::where('companySystemID', $input['companySystemID'])
                                                ->with(['master'])
                                                // ->whereIn('erp_directpaymentdetails.chartOfAccountSystemID', $glIds)
                                                ->where('serviceLineSystemID', $input['serviceLineSystemID'])
                                                ->whereHas('master', function($query) use ($input) {
                                                    $query->where('approved', 0)
                                                          ->where('cancelYN', 0)
                                                          ->where('invoiceType', 3)
                                                          ->whereHas('financeyear_by', function($query) use ($input) {
                                                            $query->whereYear('bigginingDate', $input['Year']);
                                                          })
                                                          ->where('companySystemID', $input['companySystemID']);
                                                 })
                                                 ->join(DB::raw('(SELECT
                                                                erp_companyreporttemplatelinks.templateDetailID as templatesDetailsAutoID,
                                                                erp_companyreporttemplatelinks.templateMasterID,
                                                                erp_companyreporttemplatelinks.glAutoID as chartOfAccountSystemID,
                                                                erp_companyreporttemplatelinks.glCode 
                                                                FROM
                                                                erp_companyreporttemplatelinks
                                                                WHERE erp_companyreporttemplatelinks.templateMasterID =' . $input['templatesMasterAutoID'] . ' AND erp_companyreporttemplatelinks.templateDetailID = ' . $input['templateDetailID'] . ' AND erp_companyreporttemplatelinks.glAutoID is not null) as tem_gl'),
                                                function ($join) {
                                                    $join->on('erp_directpaymentdetails.chartOfAccountSystemID', '=', 'tem_gl.chartOfAccountSystemID');
                                                })
                                                ->get();


            $pendingPurchaseRetuenAmount1 = PurchaseReturnDetails::whereHas('master', function($query) use ($input) {
                                                                     $query->where('approved', 0)
                                                                          ->where('serviceLineSystemID', $input['serviceLineSystemID'])
                                                                          ->where('companySystemID', $input['companySystemID'])
                                                                          ->whereHas('finance_year_by', function($query) use ($input) {
                                                                                $query->whereYear('bigginingDate', $input['Year']);
                                                                          });
                                                                })
                                                            ->where('itemFinanceCategoryID', '!=', 3)
                                                            // ->whereIn('financeGLcodePLSystemID', $glIds)
                                                            ->whereNotNull('financeGLcodePLSystemID')
                                                             ->join(DB::raw('(SELECT
                                                                            erp_companyreporttemplatelinks.templateDetailID as templatesDetailsAutoID,
                                                                            erp_companyreporttemplatelinks.templateMasterID,
                                                                            erp_companyreporttemplatelinks.glAutoID as chartOfAccountSystemID,
                                                                            erp_companyreporttemplatelinks.glCode 
                                                                            FROM
                                                                            erp_companyreporttemplatelinks
                                                                            WHERE erp_companyreporttemplatelinks.templateMasterID =' . $input['templatesMasterAutoID'] . ' AND erp_companyreporttemplatelinks.templateDetailID = ' . $input['templateDetailID'] . ' AND erp_companyreporttemplatelinks.glAutoID is not null) as tem_gl'),
                                                            function ($join) {
                                                                $join->on('erp_purchasereturndetails.financeGLcodePLSystemID', '=', 'tem_gl.chartOfAccountSystemID');
                                                            })
                                                            ->with(['master' => function($query) {
                                                                $query->with(['finance_year_by']);
                                                            }])
                                                            ->get();

            $pendingPurchaseRetuenAmount2 = PurchaseReturnDetails::whereHas('master', function($query) use ($input) {
                                                                     $query->where('approved', 0)
                                                                            ->where('serviceLineSystemID', $input['serviceLineSystemID'])
                                                                            ->where('companySystemID', $input['companySystemID'])
                                                                            ->whereHas('finance_year_by', function($query) use ($input) {
                                                                                $query->whereYear('bigginingDate', $input['Year']);
                                                                            });
                                                                })
                                                                ->where('itemFinanceCategoryID', 3)
                                                                // ->whereIn('financeGLcodebBSSystemID', $glIds)
                                                                ->whereNotNull('financeGLcodebBSSystemID')
                                                                 ->join(DB::raw('(SELECT
                                                                            erp_companyreporttemplatelinks.templateDetailID as templatesDetailsAutoID,
                                                                            erp_companyreporttemplatelinks.templateMasterID,
                                                                            erp_companyreporttemplatelinks.glAutoID as chartOfAccountSystemID,
                                                                            erp_companyreporttemplatelinks.glCode 
                                                                            FROM
                                                                            erp_companyreporttemplatelinks
                                                                                WHERE erp_companyreporttemplatelinks.templateMasterID =' . $input['templatesMasterAutoID'] . ' AND erp_companyreporttemplatelinks.templateDetailID = ' . $input['templateDetailID'] . ' AND erp_companyreporttemplatelinks.glAutoID is not null) as tem_gl'),
                                                                function ($join) {
                                                                    $join->on('erp_purchasereturndetails.financeGLcodebBSSystemID', '=', 'tem_gl.chartOfAccountSystemID');
                                                                })
                                                                ->with(['master' => function($query) {
                                                                    $query->with(['finance_year_by']);
                                                                }])
                                                                ->get();


            $pendingJVAmount = JvDetail::whereHas('master', function($query) use ($input) {
                                                        $query->where('approved', 0)
                                                              ->where('companySystemID', $input['companySystemID'])
                                                              ->whereHas('financeyear_by', function($query) use ($input) {
                                                                    $query->whereYear('bigginingDate', $input['Year']);
                                                              });
                                                     })
                                                    ->where('serviceLineSystemID', $input['serviceLineSystemID'])
                                                    // ->whereIn('erp_jvdetail.chartOfAccountSystemID', $glIds)
                                                    ->whereNotNull('erp_jvdetail.chartOfAccountSystemID')
                                                     ->join(DB::raw('(SELECT
                                                                erp_companyreporttemplatelinks.templateDetailID as templatesDetailsAutoID,
                                                                erp_companyreporttemplatelinks.templateMasterID,
                                                                erp_companyreporttemplatelinks.glAutoID as chartOfAccountSystemID,
                                                                erp_companyreporttemplatelinks.glCode 
                                                                FROM
                                                                erp_companyreporttemplatelinks
                                                                    WHERE erp_companyreporttemplatelinks.templateMasterID =' . $input['templatesMasterAutoID'] . ' AND erp_companyreporttemplatelinks.templateDetailID = ' . $input['templateDetailID'] . ' AND erp_companyreporttemplatelinks.glAutoID is not null) as tem_gl'),
                                                    function ($join) {
                                                        $join->on('erp_jvdetail.chartOfAccountSystemID', '=', 'tem_gl.chartOfAccountSystemID');
                                                    })
                                                    ->with(['master' => function($query) {
                                                        $query->with(['financeyear_by']);
                                                    }])
                                                    ->get();


            $pendingDebitNoteAmount = DebitNoteDetails::whereHas('master', function($query) use ($input) {
                                                        $query->where('approved', 0)
                                                              ->where('companySystemID', $input['companySystemID'])
                                                              ->whereHas('finance_year_by', function($query) use ($input) {
                                                                    $query->whereYear('bigginingDate', $input['Year']);
                                                              });
                                                     })
                                                    ->where('serviceLineSystemID', $input['serviceLineSystemID'])
                                                    ->where('budgetYear', $input['Year'])
                                                    // ->whereIn('erp_debitnotedetails.chartOfAccountSystemID', $glIds)
                                                    ->whereNotNull('erp_debitnotedetails.chartOfAccountSystemID')
                                                     ->join(DB::raw('(SELECT
                                                                erp_companyreporttemplatelinks.templateDetailID as templatesDetailsAutoID,
                                                                erp_companyreporttemplatelinks.templateMasterID,
                                                                erp_companyreporttemplatelinks.glAutoID as chartOfAccountSystemID,
                                                                erp_companyreporttemplatelinks.glCode 
                                                                FROM
                                                                erp_companyreporttemplatelinks
                                                                    WHERE erp_companyreporttemplatelinks.templateMasterID =' . $input['templatesMasterAutoID'] . ' AND erp_companyreporttemplatelinks.templateDetailID = ' . $input['templateDetailID'] . ' AND erp_companyreporttemplatelinks.glAutoID is not null) as tem_gl'),
                                                    function ($join) {
                                                        $join->on('erp_debitnotedetails.chartOfAccountSystemID', '=', 'tem_gl.chartOfAccountSystemID');
                                                    })
                                                    ->with(['master' => function($query) {
                                                        $query->with(['finance_year_by']);
                                                    }])
                                                    ->get();


            $data = [];

            if($chartOfAccountControl->controlAccountsSystemID == 3)
            {
                foreach ($fixed_assets as $key => $value) {
                    $temp = [];
                    $temp['companyID'] = $value->companyID;
                    $temp['serviceLine'] = $value->serviceLineCode;
                    $temp['financeGLcodePL'] = $value->COSTGLCODE;
                    $temp['budgetYear'] = Carbon::parse($value->documentDate)->format('Y');
                    $temp['documentCode'] = $value->faCode;
                    $temp['documentSystemCode'] = $value->docOrigin;
                    $temp['documentSystemID'] = $value->documentSystemID;
                    $temp['lineTotal'] = $value->costUnitRpt;
        
                    $data[] = $temp;
                }
            }                              
            else
            {

            foreach ($pendingDebitNoteAmount as $key => $value) {
                $temp = [];
                $temp['companyID'] = $value->master->companyID;
                $temp['serviceLine'] = $value->serviceLineCode;
                $temp['financeGLcodePL'] = $value->glCode;
                $temp['budgetYear'] = Carbon::parse($value->master->finance_year_by->bigginingDate)->format('Y');
                $temp['documentCode'] = $value->master->debitNoteCode;
                $temp['documentSystemCode'] = $value->master->debitNoteAutoID;
                $temp['documentSystemID'] = $value->master->documentSystemID;
                $temp['lineTotal'] = $value->comRptAmount * -1;

                $data[] = $temp;
            }


            foreach ($pendingJVAmount as $key => $value) {
                $temp = [];
                $temp['companyID'] = $value->master->companyID;
                $temp['serviceLine'] = $value->serviceLineCode;
                $temp['financeGLcodePL'] = ChartOfAccount::getAccountCode($value->chartOfAccountSystemID);
                $temp['budgetYear'] = Carbon::parse($value->master->financeyear_by->bigginingDate)->format('Y');
                $temp['documentCode'] = $value->master->JVcode;
                $temp['documentSystemCode'] = $value->master->jvMasterAutoId;
                $temp['documentSystemID'] = $value->master->documentSystemID;

                $amount = $value->debitAmount + $value->creditAmount * -1;

                $currencyConversionRptAmount = \Helper::currencyConversion($value->companySystemID, $value->currencyID, $value->currencyID, $amount);

                $temp['lineTotal'] = $currencyConversionRptAmount['reportingAmount'];

                $data[] = $temp;
            }

             foreach ($pendingPurchaseRetuenAmount1 as $key => $value) {
                $temp = [];
                $temp['companyID'] = $value->master->companyID;
                $temp['serviceLine'] = $value->master->serviceLineCode;
                $temp['financeGLcodePL'] = $value->financeGLcodePL;
                $temp['budgetYear'] = Carbon::parse($value->master->finance_year_by->bigginingDate)->format('Y');
                $temp['documentCode'] = $value->master->purchaseReturnCode;
                $temp['documentSystemCode'] = $value->master->purhaseReturnAutoID;
                $temp['documentSystemID'] = $value->master->documentSystemID;
                $temp['lineTotal'] = $value->GRVcostPerUnitComRptCur * $value->noQty * -1;

                $data[] = $temp;
            }

            //  foreach ($pendingPurchaseRetuenAmount2 as $key => $value) {
            //     $temp = [];
            //     $temp['companyID'] = $value->master->companyID;
            //     $temp['serviceLine'] = $value->master->serviceLineCode;
            //     $temp['financeGLcodePL'] = $value->financeGLcodebBS;
            //     $temp['budgetYear'] = Carbon::parse($value->master->finance_year_by->bigginingDate)->format('Y');
            //     $temp['documentCode'] = $value->master->purchaseReturnCode;
            //     $temp['documentSystemCode'] = $value->master->purhaseReturnAutoID;
            //     $temp['documentSystemID'] = $value->master->documentSystemID;
            //     $temp['lineTotal'] = $value->GRVcostPerUnitComRptCur * $value->noQty * -1;

            //     $data[] = $temp;
            // }

            foreach ($data1 as $key => $value) {
                $temp = [];
                $temp['companyID'] = $value->order->companyID;
                $temp['serviceLine'] = $value->order->serviceLine;
                $temp['financeGLcodePL'] = $value->financeGLcodePL;
                $temp['budgetYear'] = $value->budgetYear;
                $temp['documentCode'] = $value->order->purchaseOrderCode;
                $temp['documentSystemCode'] = $value->order->purchaseOrderID;
                $temp['documentSystemID'] = $value->order->documentSystemID;
                $temp['lineTotal'] = $value->GRVcostPerUnitComRptCur * $value->noQty;

                $data[] = $temp;
            }

            //  foreach ($data2 as $key => $value) {
            //     $temp = [];
            //     $temp['companyID'] = $value->order->companyID;
            //     $temp['serviceLine'] = $value->order->serviceLine;
            //     $temp['financeGLcodePL'] = $value->financeGLcodebBS;
            //     $temp['budgetYear'] = $value->budgetYear;
            //     $temp['documentCode'] = $value->order->purchaseOrderCode;
            //     $temp['documentSystemCode'] = $value->order->purchaseOrderID;
            //     $temp['documentSystemID'] = $value->order->documentSystemID;
            //     $temp['lineTotal'] = $value->GRVcostPerUnitComRptCur * $value->noQty;

            //     $data[] = $temp;
            // }

            foreach ($pendingDirectGRV1 as $key => $value) {
                $temp = [];
                $temp['companyID'] = $value->grv_master->companyID;
                $temp['serviceLine'] = $value->grv_master->serviceLineCode;
                $temp['financeGLcodePL'] = $value->financeGLcodePL;
                $temp['budgetYear'] = Carbon::parse($value->grv_master->financeyear_by->bigginingDate)->format('Y');
                $temp['documentCode'] = $value->grv_master->grvPrimaryCode;
                $temp['documentSystemCode'] = $value->grv_master->grvAutoID;
                $temp['documentSystemID'] = $value->grv_master->documentSystemID;
                $temp['lineTotal'] = $value->GRVcostPerUnitComRptCur * $value->noQty;

                $data[] = $temp;
            }

            //  foreach ($pendingDirectGRV2 as $key => $value) {
            //     $temp = [];
            //     $temp['companyID'] = $value->grv_master->companyID;
            //     $temp['serviceLine'] = $value->grv_master->serviceLineCode;
            //     $temp['financeGLcodePL'] = $value->financeGLcodebBS;
            //     $temp['budgetYear'] = Carbon::parse($value->grv_master->financeyear_by->bigginingDate)->format('Y');
            //     $temp['documentCode'] = $value->grv_master->grvPrimaryCode;
            //     $temp['documentSystemCode'] = $value->grv_master->grvAutoID;
            //     $temp['documentSystemID'] = $value->grv_master->documentSystemID;
            //     $temp['lineTotal'] = $value->GRVcostPerUnitComRptCur * $value->noQty;

            //     $data[] = $temp;
            // }

            foreach ($pendingSupplierItemInvoiceAmount1 as $key => $value) {
                $temp = [];
                $temp['companyID'] = $value->master->companyID;
                $temp['serviceLine'] = $value->master->serviceLine;
                $temp['financeGLcodePL'] = ChartOfAccount::getAccountCode($value->financeGLcodePLSystemID);
                $temp['budgetYear'] = Carbon::parse($value->master->financeyear_by->bigginingDate)->format('Y');
                $temp['documentCode'] = $value->master->bookingInvCode;
                $temp['documentSystemCode'] = $value->master->bookingSuppMasInvAutoID;
                $temp['documentSystemID'] = $value->master->documentSystemID;
                $temp['lineTotal'] = $value->costPerUnitComRptCur * $value->noQty;

                $data[] = $temp;
            }

            // foreach ($pendingSupplierItemInvoiceAmount2 as $key => $value) {
            //     $temp = [];
            //     $temp['companyID'] = $value->master->companyID;
            //     $temp['serviceLine'] = SegmentMaster::getSegmentCode($value->master->serviceLineSystemID);
            //     $temp['financeGLcodePL'] = ChartOfAccount::getAccountCode($value->financeGLcodebBSSystemID);
            //     $temp['budgetYear'] = Carbon::parse($value->master->financeyear_by->bigginingDate)->format('Y');
            //     $temp['documentCode'] = $value->master->bookingInvCode;
            //     $temp['documentSystemCode'] = $value->master->bookingSuppMasInvAutoID;
            //     $temp['documentSystemID'] = $value->master->documentSystemID;
            //     $temp['lineTotal'] = $value->costPerUnitComRptCur * $value->noQty;

            //     $data[] = $temp;
            // }

            foreach ($pendingSupplierInvoiceAmount as $key => $value) {
                $temp = [];
                $temp['companyID'] = $value->supplier_invoice_master->companyID;
                $temp['serviceLine'] = $value->serviceLineCode;
                $temp['financeGLcodePL'] = $value->glCode;
                $temp['budgetYear'] = $value->budgetYear;
                $temp['documentCode'] = $value->supplier_invoice_master->bookingInvCode;
                $temp['documentSystemCode'] = $value->supplier_invoice_master->bookingSuppMasInvAutoID;
                $temp['documentSystemID'] = $value->supplier_invoice_master->documentSystemID;
                $temp['lineTotal'] = $value->netAmountRpt;

                $data[] = $temp;
            }

            foreach ($pendingPvAmount as $key => $value) {
                $temp = [];
                $temp['lineTotal'] = $value->comRptAmount;
                $temp['companyID'] = $value->master->companyID;
                $temp['serviceLine'] = $value->serviceLineCode;
                $temp['financeGLcodePL'] = $value->glCode;
                $temp['budgetYear'] = $value->budgetYear;
                $temp['documentCode'] = $value->master->BPVcode;
                $temp['documentSystemCode'] = $value->master->PayMasterAutoId;
                $temp['documentSystemID'] = $value->master->documentSystemID;

                $data[] = $temp;
            }
        }
            $total = array_sum(collect($data)->pluck('lineTotal')->toArray());
        } else if ($input['type'] == 5) {
            $data =BudgetConsumedData::with(['purchase_order' => function ($query) use($chartOfAccountControl){
                                        $query->with(['grv_details'=>function($query){
                                            $query->select('grvDetailsID','grvAutoID','purchaseOrderMastertID','purchaseOrderDetailsID','financeGLcodePLSystemID','netAmount')->with(['grv_master'=>function($query){
                                                $query->select('grvAutoID','grvPrimaryCode','approved','grvConfirmedYN','grvTotalComRptCurrency');
                                            }]);
                                        }])->when($chartOfAccountControl->controlAccountsSystemID != 3,function($query){
                                           // $query->where('grvRecieved', '!=', 2);
                                        });
                                    }])
                                    ->where('consumeYN', -1)
                                    ->where('companySystemID', $input['companySystemID'])
                                    ->where('serviceLineSystemID', $input['serviceLineSystemID'])
                                    ->where('chartOfAccountID', $input['chartOfAccountID'])
                                    ->where('companyFinanceYearID', $input['companyFinanceYearID'])
                                    ->where('documentSystemID', 2)
                                    ->when($chartOfAccountControl->controlAccountsSystemID != 3,function($query){
                                        $query->whereHas('purchase_order', function ($query) {
                                            //$query->where('grvRecieved', '!=', 2);
                                        });
                                    })
                                     ->where(function($query) {
                                        $query->whereNull('projectID')
                                              ->orWhere('projectID', 0);
                                      })
                                    ->get();
             $grv_details = [];		
             $isAssets = false;
           
            foreach ($data as $key => $value) {
                $committedAmount = 0;
                    
                if (isset($value->purchase_order->grvRecieved) && $value->purchase_order->grvRecieved == 0 && $chartOfAccountControl->controlAccountsSystemID != 3) {
                    $committedAmount += $value->consumedRptAmount;
                } else {
                    // $notRecivedPoFixedAsset = PurchaseOrderDetails::selectRaw('SUM((GRVcostPerUnitSupTransCur * segment_allocated_items.allocatedQty) - (GRVcostPerUnitSupTransCur * receivedQty)) as remainingAmount, SUM(GRVcostPerUnitSupTransCur * receivedQty) as receivedAmount')
                    //                                     ->where('financeGLcodebBSSystemID', $input['chartOfAccountID'])
                    //                                     ->where('purchaseOrderMasterID', $value->documentSystemCode)
                    //                                     ->where('itemFinanceCategoryID', 3)
                    //                                     ->join('segment_allocated_items', 'documentDetailAutoID', '=', 'purchaseOrderDetailsID')
                    //                                     ->where('segment_allocated_items.serviceLineSystemID', $input['serviceLineSystemID'])
                    //                                     ->where('segment_allocated_items.documentSystemID', $value->documentSystemID)
                    //                                     ->groupBy('purchaseOrderMasterID')
                    //                                     ->whereHas('order', function($query) {
                    //                                         $query->where(function($query) {
                    //                                                 $query->where('projectID', 0)
                    //                                                       ->orWhereNull('projectID');
                    //                                             });
                    //                                     })
                    //                                     ->first();

                    // if ($notRecivedPoFixedAsset) {
                    //     $currencyConversionRptAmount = \Helper::currencyConversion($input['companySystemID'], $value->purchase_order->supplierTransactionCurrencyID, $value->purchase_order->supplierTransactionCurrencyID, $notRecivedPoFixedAsset->remainingAmount);
                    //     $committedAmount += $currencyConversionRptAmount['reportingAmount'];
                    // }

                    $notRecivedPoNonFixedAsset = PurchaseOrderDetails::selectRaw('itemFinanceCategoryID,SUM((GRVcostPerUnitSupTransCur * segment_allocated_items.allocatedQty)) as totalAmount,SUM((GRVcostPerUnitSupTransCur * segment_allocated_items.allocatedQty) - (GRVcostPerUnitSupTransCur * receivedQty)) as remainingAmount, SUM(GRVcostPerUnitSupTransCur * receivedQty) as receivedAmount')
                                                        ->where('financeGLcodePLSystemID', $input['chartOfAccountID'])
                                                        ->where('purchaseOrderMasterID', $value->documentSystemCode)
                                                        //->where('itemFinanceCategoryID', '!=',3)
                                                        ->join('segment_allocated_items', 'documentDetailAutoID', '=', 'purchaseOrderDetailsID')
                                                        ->where('segment_allocated_items.serviceLineSystemID', $input['serviceLineSystemID'])
                                                        ->where('segment_allocated_items.documentSystemID', $value->documentSystemID)
                                                        ->groupBy('purchaseOrderMasterID')
                                                        ->whereHas('order', function($query) {
                                                            $query->where(function($query) {
                                                                    $query->where('projectID', 0)
                                                                          ->orWhereNull('projectID');
                                                                });
                                                        })
                                                        ->first();

                    if ($notRecivedPoNonFixedAsset) {
                        if($notRecivedPoNonFixedAsset->itemFinanceCategoryID == 3)
					    {
                            $fixedCOmmitedAmount = 0;
                            $isAssets = true;
                            if(isset($value->purchase_order->grv_details))
                            {
                                $grvDetails =  $value->purchase_order->grv_details;
                                foreach($grvDetails as $grv)
                                {


                                    if (!in_array($grv->grv_master->grvAutoID, $grv_details))
                                    {
                                        $fixed_assets =  FixedAssetMaster::where('costglCodeSystemID',$value->chartOfAccountID)
                                        ->where('docOriginDocumentSystemID',3)
                                        ->where('docOriginSystemCode',$grv->grv_master->grvAutoID)->get();

                                            if($fixed_assets)
                                            {
                                            
                                                foreach($fixed_assets as $asset)
                                                {
                                                    if($asset->approved == -1)
                                                    {
                                                        $fixedCOmmitedAmount += $asset->COSTUNIT;
                                                    }
                                                }
                                            }
                                        array_push($grv_details,$grv->grv_master->grvAutoID);
                                    }

                             
        
                                }
                            }
                         

                            $totalCommitedAmount = $notRecivedPoNonFixedAsset->remainingAmount + $notRecivedPoNonFixedAsset->receivedAmount;
						    $commited_amount = $totalCommitedAmount - $fixedCOmmitedAmount;
                            $commited_amount = $commited_amount < 1?0:$commited_amount;
                            $currencyConversionRptAmount = \Helper::currencyConversion($input['companySystemID'], $value->purchase_order->supplierTransactionCurrencyID, $value->purchase_order->supplierTransactionCurrencyID, $commited_amount);
                            $committedAmount += $currencyConversionRptAmount['reportingAmount'];


                        } else {

                            $grvApprovedPoAmount = 0;
                            $grvDetails =  $value->purchase_order->grv_details;
                            foreach($grvDetails as $grv)
                            {
                                if($grv->grv_master->approved == -1)
                                {
                                   // $grvApprovedPoAmount += $grv->grv_master->grvTotalComRptCurrency;
                                    if($grv->financeGLcodePLSystemID == $value->chartOfAccountID)
                                    {
                                        $grvApprovedPoAmount += $grv->netAmount;
                                    }
                                }
                            }

                            $currencyConversionGrvApprovedPoAmount = \Helper::currencyConversion($input['companySystemID'], $value->purchase_order->supplierTransactionCurrencyID, $value->purchase_order->supplierTransactionCurrencyID, $grvApprovedPoAmount);

                            $grvCommitedAmount = $notRecivedPoNonFixedAsset->totalAmount;
                            $currencyConversionRptAmount = \Helper::currencyConversion($input['companySystemID'], $value->purchase_order->supplierTransactionCurrencyID, $value->purchase_order->supplierTransactionCurrencyID, $grvCommitedAmount);
                            $committedAmount += $currencyConversionRptAmount['reportingAmount'] - $currencyConversionGrvApprovedPoAmount['reportingAmount'];               
                        }
       
                    }
                }
                $value->committedAmount = $committedAmount;
            }
            $total = array_sum(collect($data)->pluck('committedAmount')->toArray());
        } else if ($input['type'] == 6) {

         if($input['controlAccountsSystemID'] == 3)
         {
                       
            $glData = ReportTemplateLinks::where('templateMasterID', $input['templatesMasterAutoID'])
            ->where('templateDetailID', $input['templateDetailID'])
            ->whereNotNull('glAutoID')->get();

            $glIds = collect($glData)->pluck('glAutoID')->toArray();

                    $data = FixedAssetMaster::selectRaw('DATE_FORMAT(documentDate, "%Y") as year,companyID,serviceLineCode,COSTGLCODE as GLCode,documentSystemID,documentID,faCode as documentCode,faID as documentSystemCode,costUnitRpt as consumedRptAmount, costUnitRpt as actualConsumption')
                    ->whereIn('costglCodeSystemID', $glIds)
                    ->where('serviceLineSystemID', $input['serviceLineSystemID'])
                    ->where('approved',-1)
                    ->get();

         
         }
         else
         {

            $data = BudgetConsumedData::where('companySystemID', $input['companySystemID'])
                                    ->where('serviceLineSystemID', $input['serviceLineSystemID'])
                                    ->where('companyFinanceYearID', $input['companyFinanceYearID'])
                                    ->where('consumeYN', -1)
                                    ->when($chartOfAccountControl->controlAccountsSystemID != 3,function($query){
                                        $query->where(function($query) {
                                            $query->where(function($query) {
                                                    $query->where('documentSystemID', 2)
                                                          ->whereHas('purchase_order', function ($query) {
                                                                $query->whereIn('grvRecieved', [2, 1]);
                                                            })
                                                            ->with(['purchase_order' => function ($query) {
                                                                        $query->whereIn('grvRecieved', [2, 1]);
                                                            }]);
                                                })
                                                ->orWhere('documentSystemID', '!=', 2);
                                        });
                                    })
                                    ->when($chartOfAccountControl->controlAccountsSystemID == 3,function($query){
                                        $query->where('documentSystemID',22);
                                    })
                                    ->where(function($query) {
                                        $query->whereNull('projectID')
                                              ->orWhere('projectID', 0);
                                      })
                                    ->join(DB::raw('(SELECT
                                                        erp_companyreporttemplatelinks.templateDetailID as templatesDetailsAutoID,
                                                        erp_companyreporttemplatelinks.templateMasterID,
                                                        erp_companyreporttemplatelinks.glAutoID as chartOfAccountSystemID,
                                                        erp_companyreporttemplatelinks.glCode 
                                                        FROM
                                                        erp_companyreporttemplatelinks
                                                        WHERE erp_companyreporttemplatelinks.templateMasterID =' . $input['templatesMasterAutoID'] . ' AND erp_companyreporttemplatelinks.templateDetailID = ' . $input['templateDetailID'] . ' AND erp_companyreporttemplatelinks.glAutoID is not null) as tem_gl'),
                                        function ($join) {
                                            $join->on('erp_budgetconsumeddata.chartOfAccountID', '=', 'tem_gl.chartOfAccountSystemID');
                                        })
                                    ->orderBy('budgetConsumedDataAutoID','DESC')    
                                    ->get();

            foreach ($data as $key => $value) {
                $actualConsumption = 0;

                $notRecivedPoNonFixedAsset = PurchaseOrderDetails::selectRaw('itemFinanceCategoryID,SUM((GRVcostPerUnitSupTransCur * segment_allocated_items.allocatedQty) - (GRVcostPerUnitSupTransCur * receivedQty)) as remainingAmount, SUM(GRVcostPerUnitSupTransCur * receivedQty) as receivedAmount')
                ->join(DB::raw('(SELECT
                                erp_companyreporttemplatelinks.templateDetailID as templatesDetailsAutoID,
                                erp_companyreporttemplatelinks.templateMasterID,
                                erp_companyreporttemplatelinks.glAutoID as chartOfAccountSystemID,
                                erp_companyreporttemplatelinks.glCode 
                                FROM
                                erp_companyreporttemplatelinks
                                WHERE erp_companyreporttemplatelinks.templateMasterID =' . $input['templatesMasterAutoID'] . ' AND erp_companyreporttemplatelinks.templateDetailID = ' . $input['templateDetailID'] . ' AND erp_companyreporttemplatelinks.glAutoID is not null) as tem_gl'),
                function ($join) use ($glColumnName){
                    $join->on('erp_purchaseorderdetails.financeGLcodePLSystemID', '=', 'tem_gl.chartOfAccountSystemID');
                })
                ->where('itemFinanceCategoryID','!=', 3)
                ->where('purchaseOrderMasterID', $value->documentSystemCode)
                ->join('segment_allocated_items', 'documentDetailAutoID', '=', 'purchaseOrderDetailsID')
                ->where('segment_allocated_items.serviceLineSystemID', $input['serviceLineSystemID'])
                ->where('segment_allocated_items.documentSystemID', $value->documentSystemID)
                ->whereHas('order', function($query) {
                    $query->where(function($query) {
                            $query->where('projectID', 0)
                                  ->orWhereNull('projectID');
                        });
                })
                ->groupBy('purchaseOrderMasterID')
                ->first();

                $notRecivedPoInventory = PurchaseOrderDetails::selectRaw('itemFinanceCategoryID,SUM((GRVcostPerUnitSupTransCur * segment_allocated_items.allocatedQty)) as totalAmount,SUM((GRVcostPerUnitSupTransCur * segment_allocated_items.allocatedQty) - (GRVcostPerUnitSupTransCur * receivedQty)) as remainingAmount, SUM(GRVcostPerUnitSupTransCur * receivedQty) as receivedAmount')
                ->join(DB::raw('(SELECT
                                erp_companyreporttemplatelinks.templateDetailID as templatesDetailsAutoID,
                                erp_companyreporttemplatelinks.templateMasterID,
                                erp_companyreporttemplatelinks.glAutoID as chartOfAccountSystemID,
                                erp_companyreporttemplatelinks.glCode 
                                FROM
                                erp_companyreporttemplatelinks
                                WHERE erp_companyreporttemplatelinks.templateMasterID =' . $input['templatesMasterAutoID'] . ' AND erp_companyreporttemplatelinks.templateDetailID = ' . $input['templateDetailID'] . ' AND erp_companyreporttemplatelinks.glAutoID is not null) as tem_gl'),
                function ($join) use ($glColumnName){
                    $join->on('erp_purchaseorderdetails.financeGLcodePLSystemID', '=', 'tem_gl.chartOfAccountSystemID');
                })
                ->where('itemFinanceCategoryID','!=', 3)
                ->where('purchaseOrderMasterID', $value->documentSystemCode)
                ->join('segment_allocated_items', 'documentDetailAutoID', '=', 'purchaseOrderDetailsID')
                ->where('segment_allocated_items.serviceLineSystemID', $input['serviceLineSystemID'])
                ->where('segment_allocated_items.documentSystemID', $value->documentSystemID)
                ->whereHas('order', function($query) {
                    $query->where(function($query) {
                            $query->where('projectID', 0)
                                  ->orWhereNull('projectID');
                        });
                })
                ->where('financeGLcodePLSystemID', $value->chartOfAccountID)
                ->groupBy('purchaseOrderMasterID')
                ->first();


                if ($value->documentSystemID == 2 && isset($value->purchase_order->grvRecieved) && $value->purchase_order->grvRecieved == 1) {
                    // $notRecivedPoFixedAsset = PurchaseOrderDetails::selectRaw('SUM((GRVcostPerUnitSupTransCur * segment_allocated_items.allocatedQty) - (GRVcostPerUnitSupTransCur * receivedQty)) as remainingAmount, SUM(GRVcostPerUnitSupTransCur * receivedQty) as receivedAmount')
                    //                                     ->join(DB::raw('(SELECT
                    //                                                     erp_companyreporttemplatelinks.templateDetailID as templatesDetailsAutoID,
                    //                                                     erp_companyreporttemplatelinks.templateMasterID,
                    //                                                     erp_companyreporttemplatelinks.glAutoID as chartOfAccountSystemID,
                    //                                                     erp_companyreporttemplatelinks.glCode
                    //                                                     FROM
                    //                                                     erp_companyreporttemplatelinks
                    //                                                     WHERE erp_companyreporttemplatelinks.templateMasterID =' . $input['templatesMasterAutoID'] . ' AND erp_companyreporttemplatelinks.templateDetailID = ' . $input['templateDetailID'] . ' AND erp_companyreporttemplatelinks.glAutoID is not null) as tem_gl'),
                    //                                     function ($join) use ($glColumnName){
                    //                                         $join->on('erp_purchaseorderdetails.financeGLcodebBSSystemID', '=', 'tem_gl.chartOfAccountSystemID');
                    //                                     })
                    //                                     ->where('itemFinanceCategoryID', 3)
                    //                                     ->where('purchaseOrderMasterID', $value->documentSystemCode)
                    //                                     ->join('segment_allocated_items', 'documentDetailAutoID', '=', 'purchaseOrderDetailsID')
                    //                                     ->where('segment_allocated_items.serviceLineSystemID', $input['serviceLineSystemID'])
                    //                                     ->where('segment_allocated_items.documentSystemID', $value->documentSystemID)
                    //                                     ->whereHas('order', function($query) {
                    //                                         $query->where(function($query) {
                    //                                                 $query->where('projectID', 0)
                    //                                                       ->orWhereNull('projectID');
                    //                                             });
                    //                                     })
                    //                                     ->groupBy('purchaseOrderMasterID')
                    //                                     ->first();

                    // if ($notRecivedPoFixedAsset) {
                    //     $currencyConversionRptAmount = \Helper::currencyConversion($input['companySystemID'], $value->purchase_order->supplierTransactionCurrencyID, $value->purchase_order->supplierTransactionCurrencyID, $notRecivedPoFixedAsset->remainingAmount);
                    //     $actualConsumption += $value->consumedRptAmount - $currencyConversionRptAmount['reportingAmount'];
                    // }
                   
          




                    if ($notRecivedPoNonFixedAsset) {
                        if($notRecivedPoNonFixedAsset->itemFinanceCategoryID == 3)
                        {
                            $currencyConversionRptAmount = \Helper::currencyConversion($input['companySystemID'], $value->purchase_order->supplierTransactionCurrencyID, $value->purchase_order->supplierTransactionCurrencyID, $notRecivedPoNonFixedAsset->remainingAmount);
                            $actualConsumption += $value->consumedRptAmount - $currencyConversionRptAmount['reportingAmount'];
                        }
                        else
                        {
                            $grvApprovedPoAmount = 0;
                            $grvDetails =  $value->purchase_order->grv_details;
                            foreach($grvDetails as $grv)
                            {
                                if($grv->grv_master->approved == -1)
                                {
                                    if($grv->financeGLcodePLSystemID == $value->chartOfAccountID)
                                    {
                                        $grvApprovedPoAmount += $grv->netAmount;
                                    }
                                }
                            }
                            
                            $currencyConversionGrvApprovedPoAmount = \Helper::currencyConversion($input['companySystemID'], $value->purchase_order->supplierTransactionCurrencyID, $value->purchase_order->supplierTransactionCurrencyID, $grvApprovedPoAmount);
                            $actualConsumption +=$currencyConversionGrvApprovedPoAmount['reportingAmount'];
                         
                        }

                    }
                } else {

                    if ($notRecivedPoNonFixedAsset) {
                        if($notRecivedPoNonFixedAsset->itemFinanceCategoryID == 3)
                        {
                          $actualConsumption = $value->consumedRptAmount;
                        }
                        else
                        {
                            $grvApprovedPoAmount = 0;
                            $grvDetails =  $value->purchase_order->grv_details;
                            foreach($grvDetails as $grv)
                            {
                            
                                if($grv->grv_master->approved == -1)
                                {
                                    if($grv->financeGLcodePLSystemID == $value->chartOfAccountID)
                                    {
                                        $grvApprovedPoAmount += $grv->netAmount;
                                    }
                                }
                            }

                            $grvCommitedAmount = $notRecivedPoInventory->totalAmount;
                            $currencyConversionRptAmount = \Helper::currencyConversion($input['companySystemID'], $value->purchase_order->supplierTransactionCurrencyID, $value->purchase_order->supplierTransactionCurrencyID, $grvCommitedAmount);
                            $currencyConversionGrvApprovedPoAmount = \Helper::currencyConversion($input['companySystemID'], $value->purchase_order->supplierTransactionCurrencyID, $value->purchase_order->supplierTransactionCurrencyID, $grvApprovedPoAmount);
                            $committedAmount = $currencyConversionRptAmount['reportingAmount'] - $currencyConversionGrvApprovedPoAmount['reportingAmount'];
                            $actualConsumption += $value->consumedRptAmount - $committedAmount;    

                        }
                    }
             
              
                }

                $value->actualConsumption = $actualConsumption;
            }
         }
            $total = array_sum(collect($data)->pluck('actualConsumption')->toArray());
        }


        $companyId = $input['companySystemID'];

        if ($companyId) {
            $company = Company::where('companySystemID', $companyId)->first();
            $localCurrency = CurrencyMaster::where('currencyID', $company->localCurrencyID)->first();
            $rptCurrency = CurrencyMaster::where('currencyID', $company->reportingCurrency)->first();
        } else {
            $localCurrency = [];
            $rptCurrency = [];
        }


        $decimalPlaceLocal = !empty($localCurrency) ? $localCurrency->DecimalPlaces : 3;
        $decimalPlaceRpt = !empty($rptCurrency) ? $rptCurrency->DecimalPlaces : 2;

        $result = array('reportData' => $data, 'total' => $total, 'decimalPlaceLocal' => $decimalPlaceLocal, 'decimalPlaceRpt' => $decimalPlaceRpt);

        return $result;
    }

    public function exportBudgetGLCodeWiseDetails(Request $request)
    {
        $input = $request->all();
        $result = $this->budgetGLCodeWiseDetailsData($input);
        $templateName = "export_report.budget_summary_details";

        \Excel::create('finance', function ($excel) use ($result, $templateName) {
            $excel->sheet('New sheet', function ($sheet) use ($result, $templateName) {
                $sheet->loadView($templateName, $result);
            });
        })->download('csv');

        return $this->sendResponse($result, trans('custom.details_retrieved_successfully_1'));
    }


    public function reportBudgetTemplateCategoryWise(Request $request)
    {
        $input = $request->all();


        /** @var BudgetMaster $budgetMaster */
        $budgetMaster = $this->budgetMasterRepository->with(['segment_by', 'template_master', 'finance_year_by'])->findWithoutFail($input['id']);

        if (empty($budgetMaster)) {
            return $this->sendError(trans('custom.budget_master_not_found'));
        }

        // policy check -> Department wise budget check
        $DLBCPolicy = true;

        $reportData = Budjetdetails::select(DB::raw("(SUM(budjetAmtLocal) * -1) as totalLocal,
                                       if((SUM(budjetAmtRpt) * -1) < 0,(SUM(budjetAmtRpt) * -1),(SUM(budjetAmtRpt) * -1)) as totalRpt,
                                       chartofaccounts.AccountCode,chartofaccounts.AccountDescription,chartofaccounts.controlAccountsSystemID,
                                       erp_companyreporttemplatedetails.description as templateDetailDescription,
                                       erp_companyreporttemplatedetails.companyReportTemplateID as templatesMasterAutoID,
                                       erp_budjetdetails.*
                                        /*,ifnull(ca.consumed_amount,0) as consumed_amount
                                         ,ifnull(ppo.rptAmt,0) as pending_po_amount,
                                       ((SUM(budjetAmtRpt) * -1) - (ifnull(ca.consumed_amount,0) + ifnull(ppo.rptAmt,0))) AS balance*/
                                       "))
            ->where('erp_budjetdetails.companySystemID', $budgetMaster->companySystemID)
            ->where('erp_budjetdetails.serviceLineSystemID', $budgetMaster->serviceLineSystemID)
            ->where('erp_budjetdetails.companyFinanceYearID', $budgetMaster->companyFinanceYearID)
            ->where('erp_companyreporttemplatedetails.companyReportTemplateID', $budgetMaster->templateMasterID)
            ->leftJoin('chartofaccounts', 'chartOfAccountID', '=', 'chartOfAccountSystemID')
            ->join('erp_companyreporttemplatedetails', 'templateDetailID', '=', 'detID')
            ->groupBy(['erp_budjetdetails.companySystemID', 'erp_budjetdetails.serviceLineSystemID',
                'erp_budjetdetails.templateDetailID', 'erp_budjetdetails.companyFinanceYearID'])
            ->orderBy('erp_companyreporttemplatedetails.description')
            ->get();

        foreach ($reportData as $data) {        
    
            $glData = ReportTemplateLinks::where('templateMasterID', $budgetMaster->templateMasterID)
                                            ->where('templateDetailID', $data['templateDetailID'])
                                            ->whereNotNull('glAutoID')->get();

            $glIds = collect($glData)->pluck('glAutoID')->toArray();

            $data->committedAmount = $this->getGlCodeWiseCommitedBudgetAmount($data, $glIds, $DLBCPolicy);
            $data->actualConsumption = $this->getGlCodeWiseActualConsumption($data, $glIds, $DLBCPolicy);

            $data->pendingDocumentAmount = $this->getGlCodeWisePendingDocAmount($data, $glIds, $DLBCPolicy);

            $pos = PurchaseOrderDetails::whereHas('order', function ($q) use ($data, $glIds, $DLBCPolicy, $budgetMaster) {
                                                    $q->where('serviceLineSystemID', $data['serviceLineSystemID'])
                                                        ->where('companySystemID', $data['companySystemID'])
                                                        ->where('approved', 0)
                                                        ->where('poCancelledYN', 0)
                                                        ->where('budgetYear', $budgetMaster->Year);
                                                 })
                                                ->whereIn('financeGLcodePLSystemID', $glIds)
                                                ->whereNotNull('financeGLcodePLSystemID')
                                                ->with(['order'])
                                                ->get();

            $data->pending_po_amount = $pos->sum(function ($product) {
                return $product->GRVcostPerUnitComRptCur * $product->noQty;
            });

            $data->balance =  ($data->totalRpt) - ($data->committedAmount + $data->actualConsumption + $data->pendingDocumentAmount);
            
        }

        $total = array();
        $total['totalLocal'] = array_sum(collect($reportData)->pluck('totalLocal')->toArray());
        $total['totalRpt'] = array_sum(collect($reportData)->pluck('totalRpt')->toArray());
        $total['committedAmount'] = array_sum(collect($reportData)->pluck('committedAmount')->toArray());
        $total['actualConsumption'] = array_sum(collect($reportData)->pluck('actualConsumption')->toArray());
        $total['pendingDocumentAmount'] = array_sum(collect($reportData)->pluck('pendingDocumentAmount')->toArray());
        $total['balance'] = $total['totalRpt'] - ($total['committedAmount'] + $total['actualConsumption'] + $total['pendingDocumentAmount']);

        $company = Company::where('companySystemID', $budgetMaster->companySystemID)->first();

        $localCurrency = CurrencyMaster::where('currencyID', $company->localCurrencyID)->first();
        $rptCurrency = CurrencyMaster::where('currencyID', $company->reportingCurrency)->first();

        $decimalPlaceLocal = !empty($localCurrency) ? $localCurrency->DecimalPlaces : 3;
        $decimalPlaceRpt = !empty($rptCurrency) ? $rptCurrency->DecimalPlaces : 2;

        $data = array('entity' => $budgetMaster->toArray(), 'reportData' => $reportData,
            'total' => $total, 'decimalPlaceLocal' => $decimalPlaceLocal, 'decimalPlaceRpt' => $decimalPlaceRpt, 'rptCurrency' => $rptCurrency);

        return $this->sendResponse($data, trans('custom.details_retrieved_successfully_1'));
    }

    public function exportBudgetTemplateCategoryWise(Request $request)
    {
        $input = $request->all();


        /** @var BudgetMaster $budgetMaster */
        $budgetMaster = $this->budgetMasterRepository->with(['segment_by', 'template_master', 'finance_year_by'])->findWithoutFail($input['id']);

        if (empty($budgetMaster)) {
            return $this->sendError(trans('custom.budget_master_not_found'));
        }

        // policy check -> Department wise budget check
        $DLBCPolicy = true;

        $reportData = Budjetdetails::select(DB::raw("(SUM(budjetAmtLocal) * -1) as totalLocal,
                                       if((SUM(budjetAmtRpt) * -1) < 0,(SUM(budjetAmtRpt) * -1),(SUM(budjetAmtRpt) * -1)) as totalRpt,
                                       chartofaccounts.AccountCode,chartofaccounts.AccountDescription,
                                       erp_companyreporttemplatedetails.description as templateDetailDescription,
                                       erp_companyreporttemplatedetails.companyReportTemplateID as templatesMasterAutoID,
                                       erp_budjetdetails.*
                                        /*,ifnull(ca.consumed_amount,0) as consumed_amount
                                         ,ifnull(ppo.rptAmt,0) as pending_po_amount,
                                       ((SUM(budjetAmtRpt) * -1) - (ifnull(ca.consumed_amount,0) + ifnull(ppo.rptAmt,0))) AS balance*/
                                       "))
            ->where('erp_budjetdetails.companySystemID', $budgetMaster->companySystemID)
            ->where('erp_budjetdetails.serviceLineSystemID', $budgetMaster->serviceLineSystemID)
            ->where('erp_budjetdetails.companyFinanceYearID', $budgetMaster->companyFinanceYearID)
            ->where('erp_companyreporttemplatedetails.companyReportTemplateID', $budgetMaster->templateMasterID)
            ->leftJoin('chartofaccounts', 'chartOfAccountID', '=', 'chartOfAccountSystemID')
            ->join('erp_companyreporttemplatedetails', 'templateDetailID', '=', 'detID')
            ->groupBy(['erp_budjetdetails.companySystemID', 'erp_budjetdetails.serviceLineSystemID',
                'erp_budjetdetails.templateDetailID', 'erp_budjetdetails.companyFinanceYearID'])
            ->orderBy('erp_companyreporttemplatedetails.description')
            ->get();

        foreach ($reportData as $data) {

            $glData = ReportTemplateLinks::where('templateMasterID', $budgetMaster->templateMasterID)
                                            ->where('templateDetailID', $data['templateDetailID'])
                                            ->whereNotNull('glAutoID')->get();

            $glIds = collect($glData)->pluck('glAutoID')->toArray();


            $data->committedAmount = $this->getGlCodeWiseCommitedBudgetAmount($data, $glIds, $DLBCPolicy);

            $data->actualConsumption = $this->getGlCodeWiseActualConsumption($data, $glIds, $DLBCPolicy);

            $data->pendingDocumentAmount = $this->getGlCodeWisePendingDocAmount($data, $glIds, $DLBCPolicy);

            $pos = PurchaseOrderDetails::whereHas('order', function ($q) use ($data, $glIds, $DLBCPolicy, $budgetMaster) {
                                                    if($DLBCPolicy){
                                                        $q->where('serviceLineSystemID', $data['serviceLineSystemID']);
                                                    }
                                                    $q->where('companySystemID', $data['companySystemID'])
                                                        ->where('approved', 0)
                                                        ->where('poCancelledYN', 0)
                                                        ->where('budgetYear', $budgetMaster->Year);
                                                 })
                                                ->whereIn('financeGLcodePLSystemID', $glIds)
                                                ->whereNotNull('financeGLcodePLSystemID')
                                                ->with(['order'])
                                                ->get();

            $data->pending_po_amount = $pos->sum(function ($product) {
                return $product->GRVcostPerUnitComRptCur * $product->noQty;
            });

            $data->balance =  ($data->totalRpt) - ($data->committedAmount + $data->actualConsumption + $data->pendingDocumentAmount);
        }

        $total = array();
        $total['totalLocal'] = array_sum(collect($reportData)->pluck('totalLocal')->toArray());
        $total['totalRpt'] = array_sum(collect($reportData)->pluck('totalRpt')->toArray());
        $total['committedAmount'] = array_sum(collect($reportData)->pluck('committedAmount')->toArray());
        $total['actualConsumption'] = array_sum(collect($reportData)->pluck('actualConsumption')->toArray());
        $total['pendingDocumentAmount'] = array_sum(collect($reportData)->pluck('pendingDocumentAmount')->toArray());
        $total['balance'] = $total['totalRpt'] - ($total['committedAmount'] + $total['actualConsumption'] + $total['pendingDocumentAmount']);

        $company = Company::where('companySystemID', $budgetMaster->companySystemID)->first();

        $localCurrency = CurrencyMaster::where('currencyID', $company->localCurrencyID)->first();
        $rptCurrency = CurrencyMaster::where('currencyID', $company->reportingCurrency)->first();

        $decimalPlaceLocal = !empty($localCurrency) ? $localCurrency->DecimalPlaces : 3;
        $decimalPlaceRpt = !empty($rptCurrency) ? $rptCurrency->DecimalPlaces : 2;

        $data = array('entity' => $budgetMaster->toArray(), 'reportData' => $reportData,
            'total' => $total, 'decimalPlaceLocal' => $decimalPlaceLocal, 'decimalPlaceRpt' => $decimalPlaceRpt, 'rptCurrency' => $rptCurrency);

        $templateName = "export_report.budget_summary_category_wise";

        \Excel::create('finance', function ($excel) use ($data, $templateName) {
            $excel->sheet('New sheet', function ($sheet) use ($data, $templateName) {
                $sheet->loadView($templateName, $data);
            });
        })->download('csv');

        return $this->sendResponse($data, trans('custom.details_retrieved_successfully_1'));
    }

    public function getGlCodeWiseCommitedBudgetAmount($data, $glIds, $DLBCPolicy)
    {

            $consumedData = BudgetConsumedData::where('companySystemID', $data['companySystemID'])
                            ->where('serviceLineSystemID', $data['serviceLineSystemID'])
                            ->with(['purchase_order' => function ($query) use($data){
                                
                                $query->with(['detail','grv_details'=>function($query) use($data){
                                    $query->select('grvDetailsID','grvAutoID','purchaseOrderMastertID','purchaseOrderDetailsID','financeGLcodePLSystemID','netAmount')->with(['grv_master'=>function($query){
                                        $query->select('grvAutoID','grvPrimaryCode','approved','grvConfirmedYN','grvTotalComRptCurrency');
                                    }]);
                                }])->when($data['controlAccountsSystemID'] != 3,function($query){
                                    //$query->where('grvRecieved', '!=', 2);
                                });
                            }])
                            ->where('companyFinanceYearID', $data['companyFinanceYearID'])
                            ->whereIn('chartOfAccountID', $glIds)
                            ->where('consumeYN', -1)
                            ->where('documentSystemID', 2)
                            ->when($data['controlAccountsSystemID'] != 3,function($query){
                                $query->whereHas('purchase_order', function ($query) {
                                  //$query->where('grvRecieved', '!=', 2);
                                });
                            })
                            ->where(function($query) {
                                $query->whereNull('projectID')
                                    ->orWhere('projectID', 0);
                            })
                            ->when($data['controlAccountsSystemID'] == 3,function($query){
                                $query->groupBy('documentSystemCode');
                            })
                  
                            ->orderBy('budgetConsumedDataAutoID','DESC')
                            ->get();

  
        $committedAmount = 0;
        $isAssets = false;
        $fixedCOmmitedAmount = 0;
        $grv_details = [];	
        $tot = 0;
        foreach ($consumedData as $key => $value) {

            $notRecivedPoNonFixedAsset = PurchaseOrderDetails::selectRaw('itemFinanceCategoryID,SUM((GRVcostPerUnitSupTransCur * segment_allocated_items.allocatedQty)) as totalAmount,SUM((GRVcostPerUnitSupTransCur * segment_allocated_items.allocatedQty) - (GRVcostPerUnitSupTransCur * receivedQty)) as remainingAmount, SUM(GRVcostPerUnitSupTransCur * receivedQty) as receivedAmount')
                                    ->whereIn('financeGLcodePLSystemID', $glIds)
                                    ->where('purchaseOrderMasterID', $value->documentSystemCode)
                                    //->where('itemFinanceCategoryID', '!=',3)
                                    ->join('segment_allocated_items', 'documentDetailAutoID', '=', 'purchaseOrderDetailsID')
                                    ->where('segment_allocated_items.serviceLineSystemID', $data['serviceLineSystemID'])
                                    ->where('segment_allocated_items.documentSystemID', $value->documentSystemID)
                                    ->whereHas('order', function($query) {
                                        $query->where(function($query) {
                                                $query->where('projectID', 0)
                                                    ->orWhereNull('projectID');
                                            });
                                    })
                                    ->groupBy('purchaseOrderMasterID')
                                    ->first();


            $notRecivedPoInventory = PurchaseOrderDetails::selectRaw('itemFinanceCategoryID,SUM((GRVcostPerUnitSupTransCur * segment_allocated_items.allocatedQty)) as totalAmount,SUM((GRVcostPerUnitSupTransCur * segment_allocated_items.allocatedQty) - (GRVcostPerUnitSupTransCur * receivedQty)) as remainingAmount, SUM(GRVcostPerUnitSupTransCur * receivedQty) as receivedAmount')
                                    ->where('financeGLcodePLSystemID', $value->chartOfAccountID)
                                    ->where('purchaseOrderMasterID', $value->documentSystemCode)
                                    //->where('itemFinanceCategoryID', '!=',3)
                                    ->join('segment_allocated_items', 'documentDetailAutoID', '=', 'purchaseOrderDetailsID')
                                    ->where('segment_allocated_items.serviceLineSystemID', $data['serviceLineSystemID'])
                                    ->where('segment_allocated_items.documentSystemID', $value->documentSystemID)
                                    ->whereHas('order', function($query) {
                                        $query->where(function($query) {
                                                $query->where('projectID', 0)
                                                    ->orWhereNull('projectID');
                                            });
                                    })
                                    ->groupBy('purchaseOrderMasterID')
                                    ->first();


            if (isset($value->purchase_order->grvRecieved) && $value->purchase_order->grvRecieved == 0 && $data['controlAccountsSystemID'] != 3) {
                $committedAmount += $value->consumedRptAmount;
            } else {
                // $notRecivedPoFixedAsset = PurchaseOrderDetails::selectRaw('SUM((GRVcostPerUnitSupTransCur * segment_allocated_items.allocatedQty) - (GRVcostPerUnitSupTransCur * receivedQty)) as remainingAmount, SUM(GRVcostPerUnitSupTransCur * receivedQty) as receivedAmount')
                //                                     ->whereIn('financeGLcodebBSSystemID', $glIds)
                //                                     ->where('purchaseOrderMasterID', $value->documentSystemCode)
                //                                     ->where('itemFinanceCategoryID', 3)
                //                                     ->join('segment_allocated_items', 'documentDetailAutoID', '=', 'purchaseOrderDetailsID')
                //                                     ->where('segment_allocated_items.serviceLineSystemID', $data['serviceLineSystemID'])
                //                                     ->where('segment_allocated_items.documentSystemID', $value->documentSystemID)
                //                                     ->whereHas('order', function($query) {
                //                                         $query->where(function($query) {
                //                                                 $query->where('projectID', 0)
                //                                                       ->orWhereNull('projectID');
                //                                             });
                //                                     })
                //                                     ->groupBy('purchaseOrderMasterID')
                //                                     ->first();

                // if ($notRecivedPoFixedAsset) {
                //     $currencyConversionRptAmount = \Helper::currencyConversion($data['companySystemID'], $value->purchase_order->supplierTransactionCurrencyID, $value->purchase_order->supplierTransactionCurrencyID, $notRecivedPoFixedAsset->remainingAmount);
                //     $committedAmount += $currencyConversionRptAmount['reportingAmount'];
                // }


   

                if ($notRecivedPoNonFixedAsset) {
                    if($data['controlAccountsSystemID'] == 3)
					{
                        $isAssets = true;
						$totalCommitedAmount = 0;
						
                        if(isset($value->purchase_order->grv_details))
						{
							$grvDetails =  $value->purchase_order->grv_details;
                            foreach($grvDetails as $grv)
							{
								if (!in_array($grv->grv_master->grvAutoID, $grv_details))
								{
								
                                    foreach($glIds as $gl)
                                    {
                                        $notRecivedPoNonFixedAssett = PurchaseOrderDetails::selectRaw('purchaseOrderMasterID,itemFinanceCategoryID,SUM((GRVcostPerUnitSupTransCur * segment_allocated_items.allocatedQty) - (GRVcostPerUnitSupTransCur * receivedQty)) as remainingAmount, SUM(GRVcostPerUnitSupTransCur * receivedQty) as receivedAmount')
                                        ->where('financeGLcodePLSystemID', $gl)
                                        ->join('segment_allocated_items', 'documentDetailAutoID', '=', 'purchaseOrderDetailsID')
                                        ->where('segment_allocated_items.serviceLineSystemID', $data['serviceLineSystemID'])
                                        ->where('segment_allocated_items.documentSystemID', $value->documentSystemID)
                                        ->whereHas('order', function($query) {
                                            $query->where(function($query) {
                                                    $query->where('projectID', 0)
                                                          ->orWhereNull('projectID');
                                                });
                                        })
                                        //->groupBy('purchaseOrderMasterID')
                                        ->first();
                                        $totalCommitedAmount = $notRecivedPoNonFixedAssett->remainingAmount + $notRecivedPoNonFixedAssett->receivedAmount;

                                        $fixed_assets =  FixedAssetMaster::where('costglCodeSystemID',$gl)
                                        ->where('docOriginDocumentSystemID',3)
                                        ->where('docOriginSystemCode',$grv->grv_master->grvAutoID)->get();
    
                                            if($fixed_assets)
                                            {
                                            
                                                foreach($fixed_assets as $asset)
                                                {
                                                    if($asset->approved == -1)
                                                    {
                                                        $fixedCOmmitedAmount += $asset->COSTUNIT;
                                                    }
                                                }
                                            }
                                       

                                    }
                                    array_push($grv_details,$grv->grv_master->grvAutoID);
                 
								}
								
							}

						}
                        $totalCommitedAmount = $notRecivedPoNonFixedAsset->remainingAmount + $notRecivedPoNonFixedAsset->receivedAmount;
						$tot+=$totalCommitedAmount;


                    }
                    else
                    {   
                        $grvApprovedPoAmount = 0;
						$grvDetails =  $value->purchase_order->grv_details;
                        foreach($grvDetails as $grv)
						{
							if($grv->grv_master->approved == -1)
							{
                                if($grv->financeGLcodePLSystemID == $value->chartOfAccountID)
                                {
                                    $grvApprovedPoAmount += $grv->netAmount;
                                }
							}
						}
                        $grvCommitedAmount = $notRecivedPoInventory->totalAmount;
                     
                        $currencyConversionGrvApprovedPoAmount = \Helper::currencyConversion($data['companySystemID'], $value->purchase_order->supplierTransactionCurrencyID, $value->purchase_order->supplierTransactionCurrencyID, $grvApprovedPoAmount);
                        $currencyConversionRptAmount = \Helper::currencyConversion($data['companySystemID'], $value->purchase_order->supplierTransactionCurrencyID, $value->purchase_order->supplierTransactionCurrencyID, $grvCommitedAmount);
                        $committedAmount += $currencyConversionRptAmount['reportingAmount'] - $currencyConversionGrvApprovedPoAmount['reportingAmount'];
                       

                        
                    }
      
                }
            }
        }

        if($isAssets){

            $commited_amount = $tot - $fixedCOmmitedAmount;
			$commited_amount = $commited_amount < 1?0:$commited_amount;							
			$currencyConversionRptAmount = \Helper::currencyConversion($data['companySystemID'], $value->purchase_order->supplierTransactionCurrencyID, $value->purchase_order->supplierTransactionCurrencyID, $commited_amount);
			$committedAmount = $currencyConversionRptAmount['reportingAmount'];

		}

        return $committedAmount;
    }

    public function getGlCodeWiseActualConsumption($dataParam, $glIds, $DLBCPolicy)
    {
        $final_data = [];

        if($dataParam['controlAccountsSystemID'] == 3)
        {
            $data1 = FixedAssetMaster::selectRaw('SUM(costUnitRpt) as actualConsumption')
			->whereIn('costglCodeSystemID', $glIds)
			->where('serviceLineSystemID', $dataParam['serviceLineSystemID'])
			->where('approved',-1)
			->first();
            if($data1)
            {
                array_push($final_data,$data1);

            }
        }
    else
    {
       $data = BudgetConsumedData::with(['purchase_order' => function ($query) use($dataParam){

                                        $query->with(['grv_details'=>function($query){
                                            $query->select('grvDetailsID','grvAutoID','purchaseOrderMastertID','purchaseOrderDetailsID','financeGLcodePLSystemID','netAmount')->with(['grv_master'=>function($query){
                                                $query->with('details')->select('grvAutoID','grvPrimaryCode','approved','grvConfirmedYN','grvTotalComRptCurrency');
                                            }]);
                                        }])->when($dataParam['controlAccountsSystemID'] != 3,function($query){
                                            //$query->where('grvRecieved', '!=', 2);
                                        });
                                    }])
                                    ->where('companySystemID', $dataParam['companySystemID'])
                                    ->where('serviceLineSystemID', $dataParam['serviceLineSystemID'])
                                    ->when($dataParam['controlAccountsSystemID'] != 3,function($query){
                                        $query->where(function($query) {
                                            $query->where(function($query) {
                                                    $query->where('documentSystemID', 2)
                                                          ->whereHas('purchase_order', function ($query) {
                                                                $query->whereIn('grvRecieved', [2, 1]);
                                                            })
                                                            ->with(['purchase_order' => function ($query) {
                                                                        $query->whereIn('grvRecieved', [2, 1]);
                                                            }]);
                                                })
                                                ->orWhere('documentSystemID', '!=', 2);
                                        });
                                    })
                                    ->when($dataParam['controlAccountsSystemID'] == 3,function($query){
                                        $query->where('documentSystemID',22);
                                    })
                                    ->where(function($query) {
                                        $query->whereNull('projectID')
                                              ->orWhere('projectID', 0);
                                      })
                                    ->where('companyFinanceYearID', $dataParam['companyFinanceYearID'])
                                    ->whereIn('chartOfAccountID', $glIds)
                                    ->where('consumeYN', -1)
                                    ->orderBy('budgetConsumedDataAutoID','DESC')
                                    ->get();


        foreach ($data as $key => $value) {

            $notRecivedPoInventory = PurchaseOrderDetails::selectRaw('itemFinanceCategoryID,SUM((GRVcostPerUnitSupTransCur * segment_allocated_items.allocatedQty)) as totalAmount,SUM((GRVcostPerUnitSupTransCur * segment_allocated_items.allocatedQty) - (GRVcostPerUnitSupTransCur * receivedQty)) as remainingAmount, SUM(GRVcostPerUnitSupTransCur * receivedQty) as receivedAmount')
                                    ->where('financeGLcodePLSystemID', $value->chartOfAccountID)
                                    ->where('purchaseOrderMasterID', $value->documentSystemCode)
                                    ->where('itemFinanceCategoryID', '!=',3)
                                    ->join('segment_allocated_items', 'documentDetailAutoID', '=', 'purchaseOrderDetailsID')
                                    ->where('segment_allocated_items.serviceLineSystemID', $dataParam['serviceLineSystemID'])
                                    ->where('segment_allocated_items.documentSystemID', $value->documentSystemID)
                                        ->whereHas('order', function($query) {
                                        $query->where(function($query) {
                                                $query->where('projectID', 0)
                                                        ->orWhereNull('projectID');
                                            });
                                    })
                                    ->groupBy('purchaseOrderMasterID')
                                    ->first();


             $notRecivedPo = PurchaseOrderDetails::selectRaw('itemFinanceCategoryID,SUM((GRVcostPerUnitSupTransCur * segment_allocated_items.allocatedQty) - (GRVcostPerUnitSupTransCur * receivedQty)) as remainingAmount, SUM(GRVcostPerUnitSupTransCur * receivedQty) as receivedAmount')
                                    ->whereIn('financeGLcodePLSystemID', $glIds)
                                    ->where('purchaseOrderMasterID', $value->documentSystemCode)
                                    ->where('itemFinanceCategoryID', '!=',3)
                                    ->join('segment_allocated_items', 'documentDetailAutoID', '=', 'purchaseOrderDetailsID')
                                    ->where('segment_allocated_items.serviceLineSystemID', $dataParam['serviceLineSystemID'])
                                    ->where('segment_allocated_items.documentSystemID', $value->documentSystemID)
                                     ->whereHas('order', function($query) {
                                        $query->where(function($query) {
                                                $query->where('projectID', 0)
                                                      ->orWhereNull('projectID');
                                            });
                                    })
                                    ->groupBy('purchaseOrderMasterID')
                                    ->first();

            $actualConsumption = 0;
            if ($value->documentSystemID == 2 && isset($value->purchase_order->grvRecieved) && $value->purchase_order->grvRecieved == 1) {
                // $notRecivedPoFixedAsset = PurchaseOrderDetails::selectRaw('SUM((GRVcostPerUnitSupTransCur * segment_allocated_items.allocatedQty) - (GRVcostPerUnitSupTransCur * receivedQty)) as remainingAmount, SUM(GRVcostPerUnitSupTransCur * receivedQty) as receivedAmount')
                //                                     ->whereIn('financeGLcodebBSSystemID', $glIds)
                //                                     ->where('purchaseOrderMasterID', $value->documentSystemCode)
                //                                     ->where('itemFinanceCategoryID', 3)
                //                                     ->join('segment_allocated_items', 'documentDetailAutoID', '=', 'purchaseOrderDetailsID')
                //                                     ->where('segment_allocated_items.serviceLineSystemID', $dataParam['serviceLineSystemID'])
                //                                     ->where('segment_allocated_items.documentSystemID', $value->documentSystemID)
                //                                      ->whereHas('order', function($query) {
                //                                         $query->where(function($query) {
                //                                                 $query->where('projectID', 0)
                //                                                       ->orWhereNull('projectID');
                //                                             });
                //                                     })
                //                                     ->groupBy('purchaseOrderMasterID')
                //                                     ->first();

                // if ($notRecivedPoFixedAsset) {
                //     $currencyConversionRptAmount = \Helper::currencyConversion($dataParam['companySystemID'], $value->purchase_order->supplierTransactionCurrencyID, $value->purchase_order->supplierTransactionCurrencyID, $notRecivedPoFixedAsset->remainingAmount);
                //     $actualConsumption += $value->consumedRptAmount - $currencyConversionRptAmount['reportingAmount'];
                // }





                                               

                                                    
                if ($notRecivedPo) {
                    if($notRecivedPo->itemFinanceCategoryID == 3)
                    {
                        $currencyConversionRptAmount = \Helper::currencyConversion($dataParam['companySystemID'], $value->purchase_order->supplierTransactionCurrencyID, $value->purchase_order->supplierTransactionCurrencyID, $notRecivedPo->remainingAmount);
                        $actualConsumption += $value->consumedRptAmount - $currencyConversionRptAmount['reportingAmount'];
                    }
                    else
                    {
                        $grvApprovedPoAmount = 0;
						$grvDetails =  $value->purchase_order->grv_details;
						foreach($grvDetails as $grv)
						{
							if($grv->grv_master->approved == -1)
							{
								if($grv->financeGLcodePLSystemID == $value->chartOfAccountID)
								{
									$grvApprovedPoAmount += $grv->netAmount;
								}
							}
						}
                    

                        $grvCommitedAmount = $notRecivedPoInventory->totalAmount;
                        $currencyConversionGrvApprovedPoAmount = \Helper::currencyConversion($dataParam['companySystemID'], $value->purchase_order->supplierTransactionCurrencyID, $value->purchase_order->supplierTransactionCurrencyID, $grvApprovedPoAmount);

                        $currencyConversionRptAmount = \Helper::currencyConversion($dataParam['companySystemID'], $value->purchase_order->supplierTransactionCurrencyID, $value->purchase_order->supplierTransactionCurrencyID, $grvCommitedAmount);
                        $committedAmount = $currencyConversionRptAmount['reportingAmount'] - $currencyConversionGrvApprovedPoAmount['reportingAmount'];
                        $actualConsumption += $value->consumedRptAmount - $committedAmount;

                    }
               
                }
               

            } else {
                if ($notRecivedPo) {

                    if($notRecivedPo->itemFinanceCategoryID == 3)
                    {
                        $actualConsumption = $value->consumedRptAmount;
                    }
                    else
                    {
                        $grvApprovedPoAmount = 0;
                        $grvDetails =  $value->purchase_order->grv_details;
                        foreach($grvDetails as $grv)
                        {
                            if($grv->grv_master->approved == -1)
                            {
                                if($grv->financeGLcodePLSystemID == $value->chartOfAccountID)
                                {
                                    $grvApprovedPoAmount += $grv->netAmount;
                                }
                            }
                        }
                        
    
                        $grvCommitedAmount = $notRecivedPoInventory->totalAmount;
                        $currencyConversionGrvApprovedPoAmount = \Helper::currencyConversion($dataParam['companySystemID'], $value->purchase_order->supplierTransactionCurrencyID, $value->purchase_order->supplierTransactionCurrencyID, $grvApprovedPoAmount);
    
                        $currencyConversionRptAmount = \Helper::currencyConversion($dataParam['companySystemID'], $value->purchase_order->supplierTransactionCurrencyID, $value->purchase_order->supplierTransactionCurrencyID, $grvCommitedAmount);
                        $committedAmount = $currencyConversionRptAmount['reportingAmount'] - $currencyConversionGrvApprovedPoAmount['reportingAmount'];
                        $actualConsumption += $value->consumedRptAmount - $committedAmount;
    
                    }
                }
            }
            $value->actualConsumption = $actualConsumption;
            array_push($final_data,$value);
            
        }

        
    }
        return array_sum(collect($final_data)->pluck('actualConsumption')->toArray());
    }

    public function getGlCodeWisePendingDocAmount($dataParam, $glIds, $DLBCPolicy)
    {
        
            	
		 $fixed_assets =  FixedAssetMaster::where('companySystemID', $dataParam['companySystemID'])
                            ->where('serviceLineSystemID', $dataParam['serviceLineSystemID'])
                            ->whereIn('costglCodeSystemID', $glIds)
                            ->where('approved', 0)
                            ->where('confirmedYN', 1)
                            ->get();
                            
        
        $data1 =[];

        $data2 = PurchaseOrderDetails::whereHas('order', function ($q) use ($dataParam,$DLBCPolicy) {
                                            $q->where('companySystemID', $dataParam['companySystemID'])
                                            ->where('serviceLineSystemID', $dataParam['serviceLineSystemID'])
                                            ->where('approved', 0)
                                            ->where('poCancelledYN', 0)
                                            ->where(function($query) {
                                                $query->where('projectID', 0)
                                                      ->orWhereNull('projectID');
                                            });
                                    })
                                    ->where('budgetYear', $dataParam['Year'])
                                    ->where('itemFinanceCategoryID', 3)
                                    ->whereIn('financeGLcodebBSSystemID', $glIds)
                                    ->whereNotNull('financeGLcodebBSSystemID')
                                    ->join(DB::raw('(SELECT
                                                    erp_companyreporttemplatelinks.templateDetailID as templatesDetailsAutoID,
                                                    erp_companyreporttemplatelinks.templateMasterID,
                                                    erp_companyreporttemplatelinks.glAutoID as chartOfAccountSystemID,
                                                    erp_companyreporttemplatelinks.glCode 
                                                    FROM
                                                    erp_companyreporttemplatelinks
                                                    WHERE erp_companyreporttemplatelinks.templateMasterID =' . $dataParam['templatesMasterAutoID'] . ' AND erp_companyreporttemplatelinks.templateDetailID = ' . $dataParam['templateDetailID'] . ' AND erp_companyreporttemplatelinks.glAutoID is not null) as tem_gl'),
                                    function ($join) {
                                        $join->on('erp_purchaseorderdetails.financeGLcodebBSSystemID', '=', 'tem_gl.chartOfAccountSystemID');
                                    })
                                    ->with(['order'])
                                    ->get();


        $pendingDirectGRV1 = GRVDetails::whereHas('grv_master', function ($q) use ($dataParam,$DLBCPolicy) {
                                            $q->where('companySystemID', $dataParam['companySystemID'])
                                            ->where('approved', 0)
                                            ->where('grvConfirmedYN', 1)
                                            ->where('grvCancelledYN', 0)
                                            ->where('serviceLineSystemID', $dataParam['serviceLineSystemID'])
                                            ->whereHas('financeyear_by', function($query) use ($dataParam) {
                                                $query->whereYear('bigginingDate', $dataParam['Year']);
                                            })
                                            ->where(function($query) {
                                                $query->where('projectID', 0)
                                                      ->orWhereNull('projectID');
                                            });
                                    })
                                    ->where('itemFinanceCategoryID', '!=', 3)
                                    ->whereIn('financeGLcodePLSystemID', $glIds)
                                    ->whereNotNull('financeGLcodePLSystemID')
                                     ->join(DB::raw('(SELECT
                                                    erp_companyreporttemplatelinks.templateDetailID as templatesDetailsAutoID,
                                                    erp_companyreporttemplatelinks.templateMasterID,
                                                    erp_companyreporttemplatelinks.glAutoID as chartOfAccountSystemID,
                                                    erp_companyreporttemplatelinks.glCode 
                                                    FROM
                                                    erp_companyreporttemplatelinks
                                                    WHERE erp_companyreporttemplatelinks.templateMasterID =' . $dataParam['templatesMasterAutoID'] . ' AND erp_companyreporttemplatelinks.templateDetailID = ' . $dataParam['templateDetailID'] . ' AND erp_companyreporttemplatelinks.glAutoID is not null) as tem_gl'),
                                    function ($join) {
                                        $join->on('erp_grvdetails.financeGLcodePLSystemID', '=', 'tem_gl.chartOfAccountSystemID');
                                    })
                                    ->where(function($query) {
                                        $query->where('detail_project_id', 0)
                                              ->orWhereNull('detail_project_id');
                                    })
                                    ->with(['grv_master' => function($query) {
                                        $query->with(['financeyear_by']);
                                    }])
                                    ->get();

        $pendingDirectGRV2 = GRVDetails::whereHas('grv_master', function ($q) use ($dataParam,$DLBCPolicy) {
                                            $q->where('companySystemID', $dataParam['companySystemID'])
                                            ->where('approved', 0)
                                            ->where('grvCancelledYN', 0)
                                            ->where('serviceLineSystemID', $dataParam['serviceLineSystemID'])
                                            ->whereHas('financeyear_by', function($query) use ($dataParam) {
                                                $query->whereYear('bigginingDate', $dataParam['Year']);
                                            })
                                            ->where(function($query) {
                                                $query->where('projectID', 0)
                                                      ->orWhereNull('projectID');
                                            });
                                    })
                                    ->where('itemFinanceCategoryID', 3)
                                    ->whereIn('financeGLcodebBSSystemID', $glIds)
                                    ->whereNotNull('financeGLcodebBSSystemID')
                                    ->join(DB::raw('(SELECT
                                                    erp_companyreporttemplatelinks.templateDetailID as templatesDetailsAutoID,
                                                    erp_companyreporttemplatelinks.templateMasterID,
                                                    erp_companyreporttemplatelinks.glAutoID as chartOfAccountSystemID,
                                                    erp_companyreporttemplatelinks.glCode 
                                                    FROM
                                                    erp_companyreporttemplatelinks
                                                    WHERE erp_companyreporttemplatelinks.templateMasterID =' . $dataParam['templatesMasterAutoID'] . ' AND erp_companyreporttemplatelinks.templateDetailID = ' . $dataParam['templateDetailID'] . ' AND erp_companyreporttemplatelinks.glAutoID is not null) as tem_gl'),
                                    function ($join) {
                                        $join->on('erp_grvdetails.financeGLcodebBSSystemID', '=', 'tem_gl.chartOfAccountSystemID');
                                    })
                                    ->where(function($query) {
                                        $query->where('detail_project_id', 0)
                                              ->orWhereNull('detail_project_id');
                                    })
                                    ->with(['grv_master' => function($query) {
                                        $query->with(['financeyear_by']);
                                    }])
                                    ->get();

        $pendingSupplierInvoiceAmount = DirectInvoiceDetails::where('companySystemID', $dataParam['companySystemID'])
                                                        ->whereIn('erp_directinvoicedetails.chartOfAccountSystemID', $glIds)
                                                        ->where('serviceLineSystemID', $dataParam['serviceLineSystemID'])
                                                        ->whereHas('supplier_invoice_master', function($query) use ($dataParam) {
                                                            $query->where('approved', 0)
                                                                  ->where('cancelYN', 0)
                                                                  ->whereIn('documentType', [1, 4])
                                                                  ->where('companySystemID', $dataParam['companySystemID'])
                                                                   ->whereHas('financeyear_by', function($query) use ($dataParam) {
                                                                    $query->whereYear('bigginingDate', $dataParam['Year']);
                                                                  })
                                                                  ->where(function($query) {
                                                                    $query->whereNull('projectID')
                                                                          ->orWhere('projectID', 0);
                                                                  });
                                                         })
                                                        ->join(DB::raw('(SELECT
                                                                        erp_companyreporttemplatelinks.templateDetailID as templatesDetailsAutoID,
                                                                        erp_companyreporttemplatelinks.templateMasterID,
                                                                        erp_companyreporttemplatelinks.glAutoID as chartOfAccountSystemID,
                                                                        erp_companyreporttemplatelinks.glCode 
                                                                        FROM
                                                                        erp_companyreporttemplatelinks
                                                                        WHERE erp_companyreporttemplatelinks.templateMasterID =' . $dataParam['templatesMasterAutoID'] . ' AND erp_companyreporttemplatelinks.templateDetailID = ' . $dataParam['templateDetailID'] . ' AND erp_companyreporttemplatelinks.glAutoID is not null) as tem_gl'),
                                                        function ($join) {
                                                            $join->on('erp_directinvoicedetails.chartOfAccountSystemID', '=', 'tem_gl.chartOfAccountSystemID');
                                                        })
                                                        ->with(['supplier_invoice_master'])
                                                        ->get();

        $pendingSupplierItemInvoiceAmount1 = SupplierInvoiceDirectItem::whereHas('master', function($query) use ($dataParam) {
                                                            $query->where('approved', 0)
                                                                  ->where('cancelYN', 0)
                                                                  ->where('documentType', 3)
                                                                  ->where('serviceLineSystemID', $dataParam['serviceLineSystemID'])
                                                                  ->where('companySystemID', $dataParam['companySystemID'])
                                                                  ->whereHas('financeyear_by', function($query) use ($dataParam) {
                                                                    $query->whereYear('bigginingDate', $dataParam['Year']);
                                                                  })
                                                                  ->where(function($query) {
                                                                    $query->whereNull('projectID')
                                                                          ->orWhere('projectID', 0);
                                                                  });
                                                         })
                                                        ->where('itemFinanceCategoryID', '!=', 3)
                                                        ->whereIn('financeGLcodePLSystemID', $glIds)
                                                        ->whereNotNull('financeGLcodePLSystemID')
                                                        ->join(DB::raw('(SELECT
                                                                        erp_companyreporttemplatelinks.templateDetailID as templatesDetailsAutoID,
                                                                        erp_companyreporttemplatelinks.templateMasterID,
                                                                        erp_companyreporttemplatelinks.glAutoID as chartOfAccountSystemID,
                                                                        erp_companyreporttemplatelinks.glCode 
                                                                        FROM
                                                                        erp_companyreporttemplatelinks
                                                                        WHERE erp_companyreporttemplatelinks.templateMasterID =' . $dataParam['templatesMasterAutoID'] . ' AND erp_companyreporttemplatelinks.templateDetailID = ' . $dataParam['templateDetailID'] . ' AND erp_companyreporttemplatelinks.glAutoID is not null) as tem_gl'),
                                                        function ($join) {
                                                            $join->on('supplier_invoice_items.financeGLcodePLSystemID', '=', 'tem_gl.chartOfAccountSystemID');
                                                        })
                                                        ->with(['master' => function($query) {
                                                            $query->with(['financeyear_by']);
                                                        }])
                                                        ->get();

        $pendingSupplierItemInvoiceAmount2 = SupplierInvoiceDirectItem::whereHas('master', function($query) use ($dataParam) {
                                                            $query->where('approved', 0)
                                                                  ->where('cancelYN', 0)
                                                                  ->where('documentType', 3)
                                                                  ->where('serviceLineSystemID', $dataParam['serviceLineSystemID'])
                                                                  ->where('companySystemID', $dataParam['companySystemID'])
                                                                  ->whereHas('financeyear_by', function($query) use ($dataParam) {
                                                                    $query->whereYear('bigginingDate', $dataParam['Year']);
                                                                  })
                                                                  ->where(function($query) {
                                                                    $query->whereNull('projectID')
                                                                          ->orWhere('projectID', 0);
                                                                  });
                                                         })
                                                        ->where('itemFinanceCategoryID', 3)
                                                        ->whereIn('financeGLcodebBSSystemID', $glIds)
                                                        ->whereNotNull('financeGLcodebBSSystemID')
                                                         ->join(DB::raw('(SELECT
                                                                        erp_companyreporttemplatelinks.templateDetailID as templatesDetailsAutoID,
                                                                        erp_companyreporttemplatelinks.templateMasterID,
                                                                        erp_companyreporttemplatelinks.glAutoID as chartOfAccountSystemID,
                                                                        erp_companyreporttemplatelinks.glCode 
                                                                        FROM
                                                                        erp_companyreporttemplatelinks
                                                                        WHERE erp_companyreporttemplatelinks.templateMasterID =' . $dataParam['templatesMasterAutoID'] . ' AND erp_companyreporttemplatelinks.templateDetailID = ' . $dataParam['templateDetailID'] . ' AND erp_companyreporttemplatelinks.glAutoID is not null) as tem_gl'),
                                                        function ($join) {
                                                            $join->on('supplier_invoice_items.financeGLcodebBSSystemID', '=', 'tem_gl.chartOfAccountSystemID');
                                                        })
                                                        ->with(['master' => function($query) {
                                                            $query->with(['financeyear_by']);
                                                        }])
                                                        ->get();

        $pendingPvAmount = DirectPaymentDetails::where('companySystemID', $dataParam['companySystemID'])
                                            ->with(['master'])
                                            ->whereIn('erp_directpaymentdetails.chartOfAccountSystemID', $glIds)
                                            ->where('serviceLineSystemID', $dataParam['serviceLineSystemID'])
                                            ->whereHas('master', function($query) use ($dataParam) {
                                                $query->where('approved', 0)
                                                      ->where('cancelYN', 0)
                                                      ->where('invoiceType', 3)
                                                      ->whereHas('financeyear_by', function($query) use ($dataParam) {
                                                        $query->whereYear('bigginingDate', $dataParam['Year']);
                                                      })
                                                      ->where('companySystemID', $dataParam['companySystemID']);
                                             })
                                             ->join(DB::raw('(SELECT
                                                            erp_companyreporttemplatelinks.templateDetailID as templatesDetailsAutoID,
                                                            erp_companyreporttemplatelinks.templateMasterID,
                                                            erp_companyreporttemplatelinks.glAutoID as chartOfAccountSystemID,
                                                            erp_companyreporttemplatelinks.glCode 
                                                            FROM
                                                            erp_companyreporttemplatelinks
                                                            WHERE erp_companyreporttemplatelinks.templateMasterID =' . $dataParam['templatesMasterAutoID'] . ' AND erp_companyreporttemplatelinks.templateDetailID = ' . $dataParam['templateDetailID'] . ' AND erp_companyreporttemplatelinks.glAutoID is not null) as tem_gl'),
                                            function ($join) {
                                                $join->on('erp_directpaymentdetails.chartOfAccountSystemID', '=', 'tem_gl.chartOfAccountSystemID');
                                            })
                                            ->get();


        $pendingPurchaseRetuenAmount1 = PurchaseReturnDetails::whereHas('master', function($query) use ($dataParam) {
                                                                 $query->where('approved', 0)
                                                                      ->where('serviceLineSystemID', $dataParam['serviceLineSystemID'])
                                                                      ->where('companySystemID', $dataParam['companySystemID'])
                                                                      ->whereHas('finance_year_by', function($query) use ($dataParam) {
                                                                            $query->whereYear('bigginingDate', $dataParam['Year']);
                                                                      });
                                                            })
                                                        ->where('itemFinanceCategoryID', '!=', 3)
                                                        ->whereIn('financeGLcodePLSystemID', $glIds)
                                                        ->whereNotNull('financeGLcodePLSystemID')
                                                         ->join(DB::raw('(SELECT
                                                                        erp_companyreporttemplatelinks.templateDetailID as templatesDetailsAutoID,
                                                                        erp_companyreporttemplatelinks.templateMasterID,
                                                                        erp_companyreporttemplatelinks.glAutoID as chartOfAccountSystemID,
                                                                        erp_companyreporttemplatelinks.glCode 
                                                                        FROM
                                                                        erp_companyreporttemplatelinks
                                                                        WHERE erp_companyreporttemplatelinks.templateMasterID =' . $dataParam['templatesMasterAutoID'] . ' AND erp_companyreporttemplatelinks.templateDetailID = ' . $dataParam['templateDetailID'] . ' AND erp_companyreporttemplatelinks.glAutoID is not null) as tem_gl'),
                                                        function ($join) {
                                                            $join->on('erp_purchasereturndetails.financeGLcodePLSystemID', '=', 'tem_gl.chartOfAccountSystemID');
                                                        })
                                                        ->with(['master' => function($query) {
                                                            $query->with(['finance_year_by']);
                                                        }])
                                                        ->get();

        $pendingPurchaseRetuenAmount2 = PurchaseReturnDetails::whereHas('master', function($query) use ($dataParam) {
                                                                 $query->where('approved', 0)
                                                                        ->where('serviceLineSystemID', $dataParam['serviceLineSystemID'])
                                                                        ->where('companySystemID', $dataParam['companySystemID'])
                                                                        ->whereHas('finance_year_by', function($query) use ($dataParam) {
                                                                            $query->whereYear('bigginingDate', $dataParam['Year']);
                                                                        });
                                                            })
                                                            ->where('itemFinanceCategoryID', 3)
                                                            ->whereIn('financeGLcodebBSSystemID', $glIds)
                                                            ->whereNotNull('financeGLcodebBSSystemID')
                                                             ->join(DB::raw('(SELECT
                                                                        erp_companyreporttemplatelinks.templateDetailID as templatesDetailsAutoID,
                                                                        erp_companyreporttemplatelinks.templateMasterID,
                                                                        erp_companyreporttemplatelinks.glAutoID as chartOfAccountSystemID,
                                                                        erp_companyreporttemplatelinks.glCode 
                                                                        FROM
                                                                        erp_companyreporttemplatelinks
                                                                            WHERE erp_companyreporttemplatelinks.templateMasterID =' . $dataParam['templatesMasterAutoID'] . ' AND erp_companyreporttemplatelinks.templateDetailID = ' . $dataParam['templateDetailID'] . ' AND erp_companyreporttemplatelinks.glAutoID is not null) as tem_gl'),
                                                            function ($join) {
                                                                $join->on('erp_purchasereturndetails.financeGLcodebBSSystemID', '=', 'tem_gl.chartOfAccountSystemID');
                                                            })
                                                            ->with(['master' => function($query) {
                                                                $query->with(['finance_year_by']);
                                                            }])
                                                            ->get();


        $pendingJVAmount = JvDetail::whereHas('master', function($query) use ($dataParam) {
                                                    $query->where('approved', 0)
                                                          ->where('companySystemID', $dataParam['companySystemID'])
                                                          ->whereHas('financeyear_by', function($query) use ($dataParam) {
                                                                $query->whereYear('bigginingDate', $dataParam['Year']);
                                                          });
                                                 })
                                                ->where('serviceLineSystemID', $dataParam['serviceLineSystemID'])
                                                ->whereIn('erp_jvdetail.chartOfAccountSystemID', $glIds)
                                                ->whereNotNull('erp_jvdetail.chartOfAccountSystemID')
                                                 ->join(DB::raw('(SELECT
                                                            erp_companyreporttemplatelinks.templateDetailID as templatesDetailsAutoID,
                                                            erp_companyreporttemplatelinks.templateMasterID,
                                                            erp_companyreporttemplatelinks.glAutoID as chartOfAccountSystemID,
                                                            erp_companyreporttemplatelinks.glCode 
                                                            FROM
                                                            erp_companyreporttemplatelinks
                                                                WHERE erp_companyreporttemplatelinks.templateMasterID =' . $dataParam['templatesMasterAutoID'] . ' AND erp_companyreporttemplatelinks.templateDetailID = ' . $dataParam['templateDetailID'] . ' AND erp_companyreporttemplatelinks.glAutoID is not null) as tem_gl'),
                                                function ($join) {
                                                    $join->on('erp_jvdetail.chartOfAccountSystemID', '=', 'tem_gl.chartOfAccountSystemID');
                                                })
                                                ->with(['master' => function($query) {
                                                    $query->with(['financeyear_by']);
                                                }])
                                                ->get();


        $pendingDebitNoteAmount = DebitNoteDetails::whereHas('master', function($query) use ($dataParam) {
                                                    $query->where('approved', 0)
                                                          ->where('companySystemID', $dataParam['companySystemID'])
                                                          ->whereHas('finance_year_by', function($query) use ($dataParam) {
                                                                $query->whereYear('bigginingDate', $dataParam['Year']);
                                                          });
                                                 })
                                                ->where('serviceLineSystemID', $dataParam['serviceLineSystemID'])
                                                ->where('budgetYear', $dataParam['Year'])
                                                ->whereIn('erp_debitnotedetails.chartOfAccountSystemID', $glIds)
                                                ->whereNotNull('erp_debitnotedetails.chartOfAccountSystemID')
                                                 ->join(DB::raw('(SELECT
                                                            erp_companyreporttemplatelinks.templateDetailID as templatesDetailsAutoID,
                                                            erp_companyreporttemplatelinks.templateMasterID,
                                                            erp_companyreporttemplatelinks.glAutoID as chartOfAccountSystemID,
                                                            erp_companyreporttemplatelinks.glCode 
                                                            FROM
                                                            erp_companyreporttemplatelinks
                                                                WHERE erp_companyreporttemplatelinks.templateMasterID =' . $dataParam['templatesMasterAutoID'] . ' AND erp_companyreporttemplatelinks.templateDetailID = ' . $dataParam['templateDetailID'] . ' AND erp_companyreporttemplatelinks.glAutoID is not null) as tem_gl'),
                                                function ($join) {
                                                    $join->on('erp_debitnotedetails.chartOfAccountSystemID', '=', 'tem_gl.chartOfAccountSystemID');
                                                })
                                                ->with(['master' => function($query) {
                                                    $query->with(['finance_year_by']);
                                                }])
                                                ->get();


        $data = [];
        if($dataParam['controlAccountsSystemID'] == 3)
		{
            foreach ($fixed_assets as $key => $value) {
                $temp = [];
    			$temp['companyID'] = $value->companyID;
				$temp['serviceLine'] = $value->serviceLineCode;
				$temp['financeGLcodePL'] = $value->COSTGLCODE;
				$temp['budgetYear'] = Carbon::parse($value->documentDate)->format('Y');
				$temp['documentCode'] = $value->faCode;
				$temp['documentSystemCode'] = $value->docOrigin;
				$temp['documentSystemID'] = $value->documentSystemID;
				$temp['lineTotal'] = $value->costUnitRpt;
    
                $data[] = $temp;
            }
        }
        else
        {

        foreach ($pendingDebitNoteAmount as $key => $value) {
            $temp = [];
            $temp['companyID'] = $value->master->companyID;
            $temp['serviceLine'] = $value->serviceLineCode;
            $temp['financeGLcodePL'] = $value->glCode;
            $temp['budgetYear'] = Carbon::parse($value->master->finance_year_by->bigginingDate)->format('Y');
            $temp['documentCode'] = $value->master->debitNoteCode;
            $temp['documentSystemCode'] = $value->master->debitNoteAutoID;
            $temp['documentSystemID'] = $value->master->documentSystemID;
            $temp['lineTotal'] = $value->comRptAmount * -1;

            $data[] = $temp;
        }


        foreach ($pendingJVAmount as $key => $value) {
            $temp = [];
            $temp['companyID'] = $value->master->companyID;
            $temp['serviceLine'] = $value->serviceLineCode;
            $temp['financeGLcodePL'] = ChartOfAccount::getAccountCode($value->chartOfAccountSystemID);
            $temp['budgetYear'] = Carbon::parse($value->master->financeyear_by->bigginingDate)->format('Y');
            $temp['documentCode'] = $value->master->JVcode;
            $temp['documentSystemCode'] = $value->master->jvMasterAutoId;
            $temp['documentSystemID'] = $value->master->documentSystemID;

            $amount = $value->debitAmount + $value->creditAmount * -1;

            $currencyConversionRptAmount = \Helper::currencyConversion($value->companySystemID, $value->currencyID, $value->currencyID, $amount);

            $temp['lineTotal'] = $currencyConversionRptAmount['reportingAmount'];

            $data[] = $temp;
        }

         foreach ($pendingPurchaseRetuenAmount1 as $key => $value) {
            $temp = [];
            $temp['companyID'] = $value->master->companyID;
            $temp['serviceLine'] = $value->master->serviceLineCode;
            $temp['financeGLcodePL'] = $value->financeGLcodePL;
            $temp['budgetYear'] = Carbon::parse($value->master->finance_year_by->bigginingDate)->format('Y');
            $temp['documentCode'] = $value->master->purchaseReturnCode;
            $temp['documentSystemCode'] = $value->master->purhaseReturnAutoID;
            $temp['documentSystemID'] = $value->master->documentSystemID;
            $temp['lineTotal'] = $value->GRVcostPerUnitComRptCur * $value->noQty * -1;

            $data[] = $temp;
        }

        //  foreach ($pendingPurchaseRetuenAmount2 as $key => $value) {
        //     $temp = [];
        //     $temp['companyID'] = $value->master->companyID;
        //     $temp['serviceLine'] = $value->master->serviceLineCode;
        //     $temp['financeGLcodePL'] = $value->financeGLcodebBS;
        //     $temp['budgetYear'] = Carbon::parse($value->master->finance_year_by->bigginingDate)->format('Y');
        //     $temp['documentCode'] = $value->master->purchaseReturnCode;
        //     $temp['documentSystemCode'] = $value->master->purhaseReturnAutoID;
        //     $temp['documentSystemID'] = $value->master->documentSystemID;
        //     $temp['lineTotal'] = $value->GRVcostPerUnitComRptCur * $value->noQty * -1;

        //     $data[] = $temp;
        // }

        foreach ($data1 as $key => $value) {
            $temp = [];
            $temp['companyID'] = $value->order->companyID;
            $temp['serviceLine'] = $value->order->serviceLine;
            $temp['financeGLcodePL'] = $value->financeGLcodePL;
            $temp['budgetYear'] = $value->budgetYear;
            $temp['documentCode'] = $value->order->purchaseOrderCode;
            $temp['documentSystemCode'] = $value->order->purchaseOrderID;
            $temp['documentSystemID'] = $value->order->documentSystemID;
            $temp['lineTotal'] = $value->GRVcostPerUnitComRptCur * $value->noQty;

            $data[] = $temp;
        }

        //  foreach ($data2 as $key => $value) {
        //     $temp = [];
        //     $temp['companyID'] = $value->order->companyID;
        //     $temp['serviceLine'] = $value->order->serviceLine;
        //     $temp['financeGLcodePL'] = $value->financeGLcodebBS;
        //     $temp['budgetYear'] = $value->budgetYear;
        //     $temp['documentCode'] = $value->order->purchaseOrderCode;
        //     $temp['documentSystemCode'] = $value->order->purchaseOrderID;
        //     $temp['documentSystemID'] = $value->order->documentSystemID;
        //     $temp['lineTotal'] = $value->GRVcostPerUnitComRptCur * $value->noQty;

        //     $data[] = $temp;
        // }

        foreach ($pendingDirectGRV1 as $key => $value) {
            $temp = [];
            $temp['companyID'] = $value->grv_master->companyID;
            $temp['serviceLine'] = $value->grv_master->serviceLineCode;
            $temp['financeGLcodePL'] = $value->financeGLcodePL;
            $temp['budgetYear'] = Carbon::parse($value->grv_master->financeyear_by->bigginingDate)->format('Y');
            $temp['documentCode'] = $value->grv_master->grvPrimaryCode;
            $temp['documentSystemCode'] = $value->grv_master->grvAutoID;
            $temp['documentSystemID'] = $value->grv_master->documentSystemID;
            $temp['lineTotal'] = $value->GRVcostPerUnitComRptCur * $value->noQty;

            $data[] = $temp;
        }

        //  foreach ($pendingDirectGRV2 as $key => $value) {
        //     $temp = [];
        //     $temp['companyID'] = $value->grv_master->companyID;
        //     $temp['serviceLine'] = $value->grv_master->serviceLineCode;
        //     $temp['financeGLcodePL'] = $value->financeGLcodebBS;
        //     $temp['budgetYear'] = Carbon::parse($value->grv_master->financeyear_by->bigginingDate)->format('Y');
        //     $temp['documentCode'] = $value->grv_master->grvPrimaryCode;
        //     $temp['documentSystemCode'] = $value->grv_master->grvAutoID;
        //     $temp['documentSystemID'] = $value->grv_master->documentSystemID;
        //     $temp['lineTotal'] = $value->GRVcostPerUnitComRptCur * $value->noQty;

        //     $data[] = $temp;
        // }

        foreach ($pendingSupplierItemInvoiceAmount1 as $key => $value) {
            $temp = [];
            $temp['companyID'] = $value->master->companyID;
            $temp['serviceLine'] = $value->master->serviceLine;
            $temp['financeGLcodePL'] = ChartOfAccount::getAccountCode($value->financeGLcodePLSystemID);
            $temp['budgetYear'] = Carbon::parse($value->master->financeyear_by->bigginingDate)->format('Y');
            $temp['documentCode'] = $value->master->bookingInvCode;
            $temp['documentSystemCode'] = $value->master->bookingSuppMasInvAutoID;
            $temp['documentSystemID'] = $value->master->documentSystemID;
            $temp['lineTotal'] = $value->costPerUnitComRptCur * $value->noQty;

            $data[] = $temp;
        }

        // foreach ($pendingSupplierItemInvoiceAmount2 as $key => $value) {
        //     $temp = [];
        //     $temp['companyID'] = $value->master->companyID;
        //     $temp['serviceLine'] = SegmentMaster::getSegmentCode($value->master->serviceLineSystemID);
        //     $temp['financeGLcodePL'] = ChartOfAccount::getAccountCode($value->financeGLcodebBSSystemID);
        //     $temp['budgetYear'] = Carbon::parse($value->master->financeyear_by->bigginingDate)->format('Y');
        //     $temp['documentCode'] = $value->master->bookingInvCode;
        //     $temp['documentSystemCode'] = $value->master->bookingSuppMasInvAutoID;
        //     $temp['documentSystemID'] = $value->master->documentSystemID;
        //     $temp['lineTotal'] = $value->costPerUnitComRptCur * $value->noQty;

        //     $data[] = $temp;
        // }

        foreach ($pendingSupplierInvoiceAmount as $key => $value) {
            $temp = [];
            $temp['companyID'] = $value->supplier_invoice_master->companyID;
            $temp['serviceLine'] = $value->serviceLineCode;
            $temp['financeGLcodePL'] = $value->glCode;
            $temp['budgetYear'] = $value->budgetYear;
            $temp['documentCode'] = $value->supplier_invoice_master->bookingInvCode;
            $temp['documentSystemCode'] = $value->supplier_invoice_master->bookingSuppMasInvAutoID;
            $temp['documentSystemID'] = $value->supplier_invoice_master->documentSystemID;
            $temp['lineTotal'] = $value->netAmountRpt;

            $data[] = $temp;
        }

        foreach ($pendingPvAmount as $key => $value) {
            $temp = [];
            $temp['lineTotal'] = $value->comRptAmount;
            $temp['companyID'] = $value->master->companyID;
            $temp['serviceLine'] = $value->serviceLineCode;
            $temp['financeGLcodePL'] = $value->glCode;
            $temp['budgetYear'] = $value->budgetYear;
            $temp['documentCode'] = $value->master->BPVcode;
            $temp['documentSystemCode'] = $value->master->PayMasterAutoId;
            $temp['documentSystemID'] = $value->master->documentSystemID;

            $data[] = $temp;
        }
    }
        return  array_sum(collect($data)->pluck('lineTotal')->toArray());
    }

    public function getBudgetFormData(Request $request)
    {
        $input = $request->all();
        $companyId = $request['companyId'];


        $checkSegmentAccess = CompanyDocumentAttachment::companyDocumentAttachemnt($companyId, 65);
        $isServiceLineAccess = false;
        if ($checkSegmentAccess && $checkSegmentAccess->isServiceLineAccess) {
            $isServiceLineAccess = true;
        }

        $employeeSystemID = \Helper::getEmployeeSystemID();

        $accessibleSegments = SegmentRights::where('employeeSystemID', $employeeSystemID)
                                           ->where('companySystemID', $companyId)
                                           ->get()
                                           ->pluck('serviceLineSystemID')
                                           ->toArray();

        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();

        /** all Units*/
        $yesNoSelectionForMinus = YesNoSelectionForMinus::all();

        $month = Months::all();

        // $years = Year::orderBy('year', 'desc')->get();
        $years = CompanyFinanceYear::selectRaw('DATE_FORMAT(bigginingDate,"%M %d %Y") as bigginingDate, DATE_FORMAT(endingDate,"%M %d %Y") as endingDate, companyFinanceYearID')->orderBy('companyFinanceYearID', 'desc')->where('companySystemID', $companyId)->get();

        $companyFinanceYear = \Helper::companyFinanceYear($companyId);

        $segments = SegmentMaster::where("companySystemID", $companyId)
            ->approved()->withAssigned($companyId)
            ->when(request('isFilter') == 0, function ($q) {
                return $q->where('isActive', 1);
            })
            ->when($isServiceLineAccess == true, function ($q) use ($accessibleSegments){
                return $q->whereIn('serviceLineSystemID', $accessibleSegments);
            })
            ->get();

        $masterTemplates = ReportTemplate::when(request('isFilter') == 0, function ($q) {
                                            return $q->where('isActive',1);
                                        })
                                        ->where('companySystemID', $companyId)
                                         ->where('reportID', '!=', 3)
                                         ->get();

        if (count($companyFinanceYear) > 0) {
            $startYear = $companyFinanceYear[0]['financeYear'];
            $finYearExp = explode('/', (explode('|', $startYear))[0]);
            $financeYear = (int)$finYearExp[2];
        } else {
            $financeYear = date("Y");
        }

        $reportTemplates = [];

        if (isset($input['companyFinanceYearID']) && !empty($input['companyFinanceYearID'])  && ((isset($input['serviceLineSystemID']) && !empty($input['serviceLineSystemID'])) || (isset($input['fromDownload']) && $input['fromDownload'] == true))) {

            $checkBudget = BudgetMaster::where('companySystemID', $companyId)
                                            ->where('companyFinanceYearID', $input['companyFinanceYearID'])
                                            ->with(['template_master'])
                                            ->groupBy('templateMasterID')
                                            ->get();

            $templateIDs = (count($checkBudget) > 0) ? $checkBudget->pluck('templateMasterID')->toArray() : [];

            if (count($checkBudget) > 0) {
                $templatTypes = ReportTemplate::whereIn('companyReportTemplateID', $templateIDs)
                                              ->groupBy('reportID')
                                              ->get()
                                              ->pluck('reportID')
                                              ->toArray();

                if (count($templatTypes) == 2) {
                    $reportTemplates = ReportTemplate::whereIn('companyReportTemplateID', $templateIDs)
                                                 ->where('isActive', 1)
                                                 ->get();
                } else {
                    if (in_array(1,$templatTypes)) {
                        foreach ($checkBudget as $key => $value) {
                            if (isset($value->template_master->reportID) && $value->template_master->reportID == 1 && $value->template_master->isActive == 1) {
                                $reportTemplates[] = $value->template_master;
                            }
                        }

                        $pandlTemplates = ReportTemplate::where('isActive', 1)
                                                 ->where('companySystemID', $companyId)
                                                 ->where('reportID', 2)
                                                 ->get();

                        $reportTemplates = collect($reportTemplates)->merge($pandlTemplates);
                    } else {
                        foreach ($checkBudget as $key => $value) {
                            if (isset($value->template_master->reportID) && $value->template_master->reportID == 2 && $value->template_master->isActive == 1) {
                                $reportTemplates[] = $value->template_master;
                            }
                        }

                        $pandlTemplates = ReportTemplate::where('isActive', 1)
                                                 ->where('companySystemID', $companyId)
                                                 ->where('reportID', 1)
                                                 ->get();

                        $reportTemplates = collect($reportTemplates)->merge($pandlTemplates);
                    }
                }
            } else {
                $reportTemplates = ReportTemplate::where('isActive', 1)
                                                 ->where('companySystemID', $companyId)
                                                 ->whereNotIn('reportID', [3, 4])
                                                 ->get();
            }
        }


        $currencyData = \Helper::companyCurrency($companyId);

        $cutOffUpdatePolicy = \Helper::checkRestrictionByPolicy($companyId,12);

        $output = array(
            'reportTemplates' => $reportTemplates,
            'currencyData' => $currencyData,
            'yesNoSelection' => $yesNoSelection,
            'yesNoSelectionForMinus' => $yesNoSelectionForMinus,
            'month' => $month,
            'years' => $years,
            'companyFinanceYear' => $companyFinanceYear,
            'cutOffUpdatePolicy' => $cutOffUpdatePolicy,
            'segments' => $segments,
            'masterTemplates' => $masterTemplates,
            'financeYear' => $financeYear
        );

        return $this->sendResponse($output, trans('custom.record_retrieved_successfully_1'));
    }

    public function updateCutOffPeriod(Request $request)
    {
        $input = $request->all();

        $cutOffUpdatePolicy = \Helper::checkRestrictionByPolicy($input['companySystemID'],12);

        if (!$cutOffUpdatePolicy) {
            return $this->sendError("You cannot update budget cutoff period");
        }

        $input['cutOffPeriod'] = ($input['cutOffPeriod']) ? $input['cutOffPeriod'] : 0;

        BudgetMaster::where('budgetmasterID', $input['budgetmasterID'])->update(['cutOffPeriod' => $input['cutOffPeriod']]);

        return $this->sendResponse([], trans('custom.updated_successfully'));
    }

    public function getBudgetAudit(Request $request)
    {

        $id  = $request->get('id');
        /** @var BudgetMaster $budgetMaster */
        $budgetMaster = $this->budgetMasterRepository->getAudit($id);

        if (empty($budgetMaster)) {
            return $this->sendError(trans('custom.budget_master_not_found'));
        }

        return $this->sendResponse($budgetMaster->toArray(), trans('custom.budget_master_retrieved_successfully'));
    }


    public function budgetReopen(Request $request)
    {
        $input = $request->all();

        $id = $input['budgetmasterID'];
        $budget = $this->budgetMasterRepository->findWithoutFail($id);
        $emails = array();
        if (empty($budget)) {
            return $this->sendError(trans('custom.budget_not_found'));
        }

        if ($budget->approvedYN == -1) {
            return $this->sendError(trans('custom.you_cannot_reopen_this_budget_it_is_already_fully_'));
        }

        if ($budget->RollLevForApp_curr > 1) {
            return $this->sendError(trans('custom.you_cannot_reopen_this_budget_it_is_already_partia'));
        }

        if ($budget->confirmedYN == 0) {
            return $this->sendError(trans('custom.you_cannot_reopen_this_budget_it_is_not_confirmed'));
        }

        $updateInput = ['confirmedYN' => 0, 'confirmedByEmpSystemID' => null, 'confirmedByEmpID' => null,
            'confirmedByEmpName' => null, 'confirmedDate' => null, 'RollLevForApp_curr' => 1];

        $this->budgetMasterRepository->update($updateInput, $id);

        $employee = \Helper::getEmployeeInfo();

        $document = DocumentMaster::where('documentSystemID', $budget->documentSystemID)->first();

        $cancelDocNameBody = $document->documentDescription . ' <b>' . $budget->budgetmasterID . '</b>';
        $cancelDocNameSubject = $document->documentDescription . ' ' . $budget->budgetmasterID;

        $subject = $cancelDocNameSubject . ' ' . trans('email.is_reopened');

        $body = '<p>' . $cancelDocNameBody . ' ' . trans('email.is_reopened_by', ['empID' => $employee->empID, 'empName' => $employee->empFullName]) . '</p><p>' . trans('email.comment') . ' : ' . $input['reopenComments'] . '</p>';

        $documentApproval = DocumentApproved::where('companySystemID', $budget->companySystemID)
                                            ->where('documentSystemCode', $budget->budgetmasterID)
                                            ->where('documentSystemID', $budget->documentSystemID)
                                            ->where('rollLevelOrder', 1)
                                            ->first();

        if ($documentApproval) {
            if ($documentApproval->approvedYN == 0) {
                $companyDocument = CompanyDocumentAttachment::where('companySystemID', $budget->companySystemID)
                    ->where('documentSystemID', $budget->documentSystemID)
                    ->first();

                if (empty($companyDocument)) {
                    return ['success' => false, 'message' => 'Policy not found for this document'];
                }

                $approvalList = EmployeesDepartment::where('employeeGroupID', $documentApproval->approvalGroupID)
                    ->where('companySystemID', $documentApproval->companySystemID)
                    ->where('documentSystemID', $documentApproval->documentSystemID);

                $approvalList = $approvalList
                    ->with(['employee'])
                    ->groupBy('employeeSystemID')
                    ->get();

                foreach ($approvalList as $da) {
                    if ($da->employee) {
                        $emails[] = array('empSystemID' => $da->employee->employeeSystemID,
                            'companySystemID' => $documentApproval->companySystemID,
                            'docSystemID' => $documentApproval->documentSystemID,
                            'alertMessage' => $subject,
                            'emailAlertMessage' => $body,
                            'docSystemCode' => $documentApproval->documentSystemCode);
                    }
                }

                $sendEmail = \Email::sendEmail($emails);
                if (!$sendEmail["success"]) {
                    return ['success' => false, 'message' => $sendEmail["message"]];
                }
            }
        }

        DocumentApproved::where('documentSystemCode', $id)
            ->where('companySystemID', $budget->companySystemID)
            ->where('documentSystemID', $budget->documentSystemID)
            ->delete();

        /*Audit entry*/
        AuditTrial::createAuditTrial($budget->documentSystemID,$id,$input['reopenComments'],'Reopened');

        return $this->sendResponse($budget->toArray(), trans('custom.budget_reopened_successfully'));
    }


    /**
     * get Budget Approved By User
     * POST /getBudgetApprovedByUser
     *
     * @param Request $request
     *
     * @return Response
     */

    public function getBudgetApprovedByUser(Request $request)
    {

        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'confirmedYN', 'approvedYN', 'wareHouseFrom', 'month', 'year'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $input['companyId'];
        $empID = \Helper::getEmployeeSystemID();

        $search = $request->input('search.value');
        $budgets = DB::table('erp_documentapproved')
            ->select(
                'erp_budgetmaster.*',
                'erp_companyreporttemplate.description As templateDescription',
                'serviceline.ServiceLineDes As ServiceLineDes',
                'erp_documentapproved.documentApprovedID',
                'rollLevelOrder',
                'approvalLevelID',
                'documentSystemCode')
            ->join('erp_budgetmaster', function ($query) use ($companyId, $search) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'budgetmasterID')
                    ->where('erp_budgetmaster.companySystemID', $companyId)
                    ->where('erp_budgetmaster.confirmedYN', 1);
            })
            ->where('erp_documentapproved.approvedYN', -1)
            ->leftJoin('erp_companyreporttemplate', 'erp_budgetmaster.templateMasterID', 'erp_companyreporttemplate.companyReportTemplateID')
            ->leftJoin('serviceline', 'erp_budgetmaster.serviceLineSystemID', 'serviceline.serviceLineSystemID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->whereIn('erp_documentapproved.documentSystemID', [65])
            ->where('erp_documentapproved.companySystemID', $companyId)
            ->where('erp_documentapproved.employeeSystemID', $empID);

        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $budgets = $budgets->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('approvedYN', $input)) {
            if (($input['approvedYN'] == 0 || $input['approvedYN'] == -1) && !is_null($input['approvedYN'])) {
                $budgets = $budgets->where('approvedYN', $input['approvedYN']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $budgets = $budgets->whereMonth('createdDateTime', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $budgets = $budgets->whereYear('createdDateTime', '=', $input['year']);
            }
        }


        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $budgets = $budgets->where(function ($query) use ($search) {
                $query->where('ServiceLineDes', 'like', "%{$search}%")
                    ->orWhere('templateDescription', 'like', "%{$search}%");
            });
        }

        return \DataTables::of($budgets)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('budgetmasterID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    /**
     * get Budget Approval By User
     * POST /getBudgetApprovalByUser
     *
     * @param Request $request
     *
     * @return Response
     */

    public function getBudgetApprovalByUser(Request $request)
    {

        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('confirmedYN', 'approvedYN', 'month', 'year'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $input['companyId'];
        $empID = \Helper::getEmployeeSystemID();

        $search = $request->input('search.value');
        $budgets = DB::table('erp_documentapproved')
            ->select(
                'employeesdepartments.approvalDeligated',
                'erp_budgetmaster.*',
                'erp_companyreporttemplate.description As templateDescription',
                'serviceline.ServiceLineDes As ServiceLineDes',
                'erp_documentapproved.documentApprovedID',
                'rollLevelOrder',
                'approvalLevelID',
                'documentSystemCode')
            ->join('employeesdepartments', function ($query) use ($companyId, $empID) {
                $query->on('erp_documentapproved.approvalGroupID', '=', 'employeesdepartments.employeeGroupID')
                    ->on('erp_documentapproved.documentSystemID', '=', 'employeesdepartments.documentSystemID')
                    ->on('erp_documentapproved.companySystemID', '=', 'employeesdepartments.companySystemID');

                $serviceLinePolicy = CompanyDocumentAttachment::where('companySystemID', $companyId)
                    ->where('documentSystemID', 65)
                    ->first();

                if ($serviceLinePolicy && $serviceLinePolicy->isServiceLineApproval == -1) {
                    //$query->on('erp_documentapproved.serviceLineSystemID', '=', 'employeesdepartments.ServiceLineSystemID');
                }

                $query->whereIn('employeesdepartments.documentSystemID', [65])
                    ->where('employeesdepartments.companySystemID', $companyId)
                    ->where('employeesdepartments.employeeSystemID', $empID)
                    ->where('employeesdepartments.isActive', 1)
                    ->where('employeesdepartments.removedYN', 0);
            })
            ->join('erp_budgetmaster', function ($query) use ($companyId, $search) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'budgetmasterID')
                    ->on('erp_documentapproved.rollLevelOrder', '=', 'RollLevForApp_curr')
                    ->where('erp_budgetmaster.companySystemID', $companyId)
                    ->where('erp_budgetmaster.approvedYN', 0)
                    ->where('erp_budgetmaster.confirmedYN', 1);
            })
            ->where('erp_documentapproved.approvedYN', 0)
            ->leftJoin('erp_companyreporttemplate', 'templateMasterID', 'erp_companyreporttemplate.companyReportTemplateID')
            ->leftJoin('serviceline', 'erp_budgetmaster.serviceLineSystemID', 'serviceline.serviceLineSystemID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->whereIn('erp_documentapproved.documentSystemID', [65])
            ->where('erp_documentapproved.companySystemID', $companyId);

        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $budgets = $budgets->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('approvedYN', $input)) {
            if (($input['approvedYN'] == 0 || $input['approvedYN'] == -1) && !is_null($input['approvedYN'])) {
                $budgets = $budgets->where('approvedYN', $input['approvedYN']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $budgets = $budgets->where('month', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $budgets = $budgets->where('Year', '=', $input['year']);
            }
        }


        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $budgets = $budgets->where(function ($query) use ($search) {
                $query->where('ServiceLineDes', 'like', "%{$search}%")
                      ->orWhere('erp_companyreporttemplate.description', 'like', "%{$search}%");
            });
        }

        $isEmployeeDischarched = \Helper::checkEmployeeDischarchedYN();

        if ($isEmployeeDischarched == 'true') {
            $budgets = [];
        }

        return \DataTables::of($budgets)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('budgetmasterID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function downloadBudgetUploadTemplate(Request $request)
    {
        $input = $request->all();

        $budgetMaster = BudgetMaster::find($input['id']);

        if (!$budgetMaster) {
            return $this->sendError("Budget not found", 500);
        }

        $companyFinanceYear = CompanyFinanceYear::find($budgetMaster->companyFinanceYearID);
        if (empty($companyFinanceYear)) {
            return $this->sendError(trans('custom.selected_financial_year_is_not_found'), 500);
        }

        $result = CarbonPeriod::create($companyFinanceYear->bigginingDate, '1 month', $companyFinanceYear->endingDate);
        $monthArray = [];
        foreach ($result as $dt) {
            $temp['year'] = $dt->format("Y");
            $temp['monthID'] = floatval($dt->format("m"));
            $temp['monthName'] = (Months::find(floatval($dt->format("m")))) ? Months::find(floatval($dt->format("m")))->monthDes : "";

            $monthArray[] = $temp;
        }

        $templateMaster = ReportTemplate::find($budgetMaster->templateMasterID);
        if ($templateMaster->reportID == 1) {
            $glCOdes = ReportTemplateDetails::with(['gllink' => function ($query) use ($budgetMaster) {
                $query->whereHas('items', function($query) use ($budgetMaster) {
                    $query->where('companySystemID', $budgetMaster->companySystemID)
                        ->where('serviceLineSystemID', $budgetMaster->serviceLineSystemID)
                        ->where('Year', $budgetMaster->Year);
                })
                    ->orderBy('sortOrder');
            }])
                ->whereHas('gllink',function ($query) use ($budgetMaster) {
                    $query->whereHas('items', function($query) use ($budgetMaster) {
                        $query->where('companySystemID', $budgetMaster->companySystemID)
                            ->where('serviceLineSystemID', $budgetMaster->serviceLineSystemID)
                            ->where('Year', $budgetMaster->Year);
                    });
                })
                ->where('companyReportTemplateID', $budgetMaster->templateMasterID)
                ->where('itemType', '!=', 4)
                ->orderBy('sortOrder')
                ->get();
        } else {
            $glCOdes = ReportTemplateDetails::with(['gllink' => function ($query) use ($budgetMaster) {
                $query->whereHas('items', function($query) use ($budgetMaster) {
                    $query->where('companySystemID', $budgetMaster->companySystemID)
                        ->where('serviceLineSystemID', $budgetMaster->serviceLineSystemID)
                        ->where('Year', $budgetMaster->Year);
                })
                    ->orderBy('sortOrder');
            }])
                ->whereHas('gllink',function ($query) use ($budgetMaster) {
                    $query->whereHas('items', function($query) use ($budgetMaster) {
                        $query->where('companySystemID', $budgetMaster->companySystemID)
                            ->where('serviceLineSystemID', $budgetMaster->serviceLineSystemID)
                            ->where('Year', $budgetMaster->Year);
                    });
                })
                ->where('companyReportTemplateID', $budgetMaster->templateMasterID)
                ->orderBy('sortOrder')
                ->get();
        }

        foreach ($glCOdes as $key => $value) {
            $value->sortOrderOfTopLevel = \Helper::headerCategoryOfReportTemplate($value->detID)['sortOrder'];
        }

        $glCOdesSorted = collect($glCOdes)->sortBy('sortOrderOfTopLevel');

        $reportData['reportData'] = $glCOdesSorted->values()->all();
        $reportData['monthArray'] = $monthArray;

        return \Excel::create('upload_budget_template', function ($excel) use ($reportData) {
                     $excel->sheet('New sheet', function($sheet) use ($reportData) {
                        $sheet->loadView('export_report.budget_upload_template', $reportData);
                    });
                })->download('xlsx');

    }

    public function checkBudgetShowPolicy(Request $request)
    {
        $input = $request->all();

        $checkBudgetPolicy = 0;
        if (isset($input['companySystemID'])) {
            $checkBudget = CompanyPolicyMaster::where('companyPolicyCategoryID', 17)
                                        ->where('companySystemID', $input['companySystemID'])
                                        ->first();

            if ($checkBudget && $checkBudget->isYesNO == 1) {
                $checkBudgetPolicy = 1;
            }
        }

        return $this->sendResponse(['checkBudgetPolicy' => $checkBudgetPolicy], trans('custom.budget_policy_retrieved_successfully'));
    }

    public function getBudgetConsumptionByDocument(Request $request)
    {
        $input = $request->all();

        if (!isset($input['documentSystemID']) || !isset($input['documentSystemID'])) {
            return $this->sendError("Error occured while retrieving budget consumption data");
        }

        $budgetConsumedData = BudgetConsumptionService::getConsumptionData($input['documentSystemID'], $input['documentSystemCode']);

        if (!$budgetConsumedData['status']) {
            return $this->sendError($budgetConsumedData['message']);
        }

        return $this->sendResponse(['budgetConsumedData' => $budgetConsumedData['data'], 'validateArray' => (isset($budgetConsumedData['validateArray']) ? $budgetConsumedData['validateArray'] : []), 'checkBudgetBasedOnGLPolicy' => (isset($budgetConsumedData['checkBudgetBasedOnGLPolicy']) ? $budgetConsumedData['checkBudgetBasedOnGLPolicy'] : false),  'departmentWiseCheckBudgetPolicy' => (isset($budgetConsumedData['departmentWiseCheckBudgetPolicy']) ? $budgetConsumedData['departmentWiseCheckBudgetPolicy'] : false), 'checkBudgetBasedOnGLPolicyProject' => (isset($budgetConsumedData['checkBudgetBasedOnGLPolicyProject']) ? $budgetConsumedData['checkBudgetBasedOnGLPolicyProject'] : false), 'currency' => (isset($budgetConsumedData['rptCurrency']) ? $budgetConsumedData['rptCurrency'] : null)], 'Budget consumption retrieved successfully');
    }

    public function getBudgetBlockedDocuments(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $purchaseRequests = PurchaseRequest::selectRaw('purchaseRequestID as documentSystemCode, documentSystemID, purchaseRequestCode as documentCode, budgetYear, comments, createdDateTime, cancelledYN, manuallyClosed, refferedBackYN, PRConfirmedYN as confirmedYN, approved, prClosedYN as closedYN, financeCategory, serviceLineSystemID, location, priority, createdUserSystemID, "" as amount, 0 as typeID, 0 as rcmActivated,"" as referenceNumber, "" as expectedDeliveryDate, "" as confirmedDate, "" as approvedDate, "" as sentToSupplier, "" as grvRecieved, "" as invoicedBooked, "" as supplierID, "" as supplierTransactionCurrencyID, "" as poType_N, 0 as selected, purchaseRequestID')
                                           ->with(['financeCategory', 'segment', 'location', 'priority','created_by', 'document_by', 'budget_transfer_addition'])
                                           ->with(['financeCategory', 'segment', 'location', 'priority','created_by', 'document_by', 'budget_transfer_addition'])
                                           ->where('companySystemID', $input['companySystemID'])
                                           ->where('cancelledYN', 0)
                                           ->where('approved', 0)
                                           ->where('budgetBlockYN', -1);

        $search = $request->input('search.value');
        // if ($search) {
        //     $search = str_replace("\\", "\\\\", $search);
        //     $purchaseRequests = $purchaseRequests->where(function ($query) use ($search) {
        //         $query->where('purchaseRequestCode', 'LIKE', "%{$search}%")
        //             ->orWhere('comments', 'LIKE', "%{$search}%");
        //     });


        // }

        $purchaseOrders = ProcumentOrder::selectRaw('purchaseOrderID as documentSystemCode, documentSystemID, purchaseOrderCode as documentCode, budgetYear, poTypeID as typeID, rcmActivated, referenceNumber, expectedDeliveryDate, narration as comments,createdDateTime, poConfirmedDate as confirmedDate, approvedDate, poCancelledYN as cancelledYN, manuallyClosed, refferedBackYN, poConfirmedYN as confirmedYN, approved, sentToSupplier, grvRecieved, invoicedBooked, "" as closedYN, financeCategory, serviceLineSystemID, "" as location, "" as priority, createdUserSystemID, poTotalSupplierTransactionCurrency as amount, supplierID, supplierTransactionCurrencyID, poType_N, 0 as selected, purchaseOrderID')
                                           ->with(['financeCategory', 'segment', 'supplier', 'created_by','currency', 'document_by', 'budget_transfer_addition'])
                                           ->where('companySystemID', $input['companySystemID'])
                                           ->where('poCancelledYN', 0)
                                           ->where('approved', 0)
                                           ->where(function($query) {
                                                $query->whereNull('projectID')
                                                      ->orWhere('projectID', 0);
                                              })
                                           ->where('budgetBlockYN', -1);


        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            // $purchaseOrders = ProcumentOrder::selectRaw('purchaseOrderID as documentSystemCode, documentSystemID, purchaseOrderCode as documentCode, budgetYear, poTypeID as typeID, rcmActivated, referenceNumber, expectedDeliveryDate, narration as comments,createdDateTime, poConfirmedDate as confirmedDate, approvedDate, poCancelledYN as cancelledYN, manuallyClosed, refferedBackYN, poConfirmedYN as confirmedYN, approved, sentToSupplier, grvRecieved, invoicedBooked, "" as closedYN, financeCategory, serviceLineSystemID, "" as location, "" as priority, createdUserSystemID, poTotalSupplierTransactionCurrency as amount, supplierID, supplierTransactionCurrencyID, poType_N, 0 as selected, purchaseOrderID')
            //             ->where('companySystemID', $input['companySystemID'])
            //             ->where('poCancelledYN', 0)
            //             ->where('approved', 0)
            //             ->where(function($query) {
            //                 $query->whereNull('projectID')
            //                     ->orWhere('projectID', 0);
            //             })
            //             ->where('budgetBlockYN', -1)->where('purchaseOrderCode', 'LIKE', "%{$search}%")
            //             ->orWhere('narration', 'LIKE', "%{$search}%")
            //             ->orWhere('referenceNumber', 'LIKE', "%{$search}%")
            //             ->orWhere('supplierPrimaryCode', 'LIKE', "%{$search}%")
            //             ->orWhere('supplierName', 'LIKE', "%{$search}%");

             $purchaseRequests =  DB::table('erp_purchaserequest')->selectRaw('CurrencyCode,DecimalPlaces,ServiceLineDes,budget_review_transfer_addition.budgetTransferType,empName,primarySupplierCode,suppliermaster.supplierName,purchaseRequestID as documentSystemCode, erp_purchaserequest.documentSystemID, purchaseRequestCode as documentCode, budgetYear, comments, erp_purchaserequest.createdDateTime, cancelledYN, manuallyClosed, erp_purchaserequest.refferedBackYN, PRConfirmedYN as confirmedYN, approved, prClosedYN as closedYN, financeCategory, erp_purchaserequest.serviceLineSystemID, location, priority, erp_purchaserequest.createdUserSystemID, "" as amount, 0 as typeID, 0 as rcmActivated,"" as referenceNumber, "" as expectedDeliveryDate, "" as confirmedDate, "" as approvedDate, "" as sentToSupplier, "" as grvRecieved, "" as invoicedBooked, "" as supplierID, "" as supplierTransactionCurrencyID, "" as poType_N, 0 as selected, purchaseRequestID')
             ->join('financeitemcategorymaster','itemCategoryID','=','erp_purchaserequest.financeCategory')
             ->join('serviceline','erp_purchaserequest.serviceLineSystemID','=','serviceline.serviceLineSystemID')
             ->join('suppliermaster','erp_purchaserequest.supplierCodeSystem','=','suppliermaster.supplierCodeSystem')
             ->join('employees','erp_purchaserequest.createdUserSystemID','=','employeeSystemID')
             ->join('currencymaster','currencyID','=','erp_purchaserequest.currency')
             ->join('erp_documentmaster','erp_purchaserequest.documentSystemID','=','erp_documentmaster.documentSystemID')
             ->join('budget_review_transfer_addition','documentSystemCode','=','erp_purchaserequest.purchaseRequestID')
             ->where('erp_purchaserequest.companySystemID', $input['companySystemID'])
            ->where('cancelledYN', 0)
            ->where('approved', 0)
            ->where('budgetBlockYN', -1)
            ->where(function ($query) use ($search) {
                $query->where('purchaseRequestCode', 'LIKE', "%{$search}%")
                    ->orWhere('comments', 'LIKE', "%{$search}%");
            });

            $total = (DB::table('erp_purchaseordermaster')->selectRaw('CurrencyCode,DecimalPlaces,ServiceLineDes,budget_review_transfer_addition.budgetTransferType,primarySupplierCode,empName,suppliermaster.supplierName,purchaseOrderID as documentSystemCode, erp_purchaseordermaster.documentSystemID, purchaseOrderCode as documentCode, budgetYear, poTypeID as typeID, rcmActivated, referenceNumber, expectedDeliveryDate, narration as comments,erp_purchaseordermaster.createdDateTime, poConfirmedDate as confirmedDate, erp_purchaseordermaster.approvedDate, poCancelledYN as cancelledYN, manuallyClosed, erp_purchaseordermaster.refferedBackYN, poConfirmedYN as confirmedYN, approved, sentToSupplier, grvRecieved, invoicedBooked, "" as closedYN, financeCategory, erp_purchaseordermaster.serviceLineSystemID, "" as location, "" as priority, erp_purchaseordermaster.createdUserSystemID, erp_purchaseordermaster.poTotalSupplierTransactionCurrency as amount, supplierID, supplierTransactionCurrencyID, poType_N, 0 as selected, purchaseOrderID')
            ->join('financeitemcategorymaster','itemCategoryID','=','financeCategory')
            ->join('serviceline','erp_purchaseordermaster.serviceLineSystemID','=','serviceline.serviceLineSystemID')
            ->join('suppliermaster','erp_purchaseordermaster.supplierID','=','suppliermaster.supplierCodeSystem')
            ->join('employees','erp_purchaseordermaster.createdUserSystemID','=','employeeSystemID')
            ->join('currencymaster','supplierTransactionCurrencyID','=','currencyID')
            ->join('erp_documentmaster','erp_purchaseordermaster.documentSystemID','=','erp_documentmaster.documentSystemID')
            ->join('budget_review_transfer_addition','documentSystemCode','=','erp_purchaseordermaster.purchaseOrderID')
           ->where('erp_purchaseordermaster.companySystemID', $input['companySystemID'])
           ->where('poCancelledYN', 0)
           ->where('approved', 0)
           ->where(function($query) {
                $query->whereNull('projectID')
                      ->orWhere('projectID', 0);
              })
           ->where('budgetBlockYN', -1)->where('budgetBlockYN', -1)
           ->where('purchaseOrderCode', 'LIKE', "%{$search}%")
           ->orWhere('narration', 'LIKE', "%{$search}%")
           ->orWhere('referenceNumber', 'LIKE', "%{$search}%")
           ->orWhere('supplierPrimaryCode', 'LIKE', "%{$search}%")
           ->orWhere('suppliermaster.supplierName', 'LIKE', "%{$search}%")
           ->union($purchaseRequests));


            return DataTables()->query($total)->addIndexColumn()->toJson();
        }


        $mergeResult = collect($purchaseOrders->get())->merge($purchaseRequests->get());

        $mergeAll = $mergeResult->all();
        $data = [];

        return \DataTables::of($mergeAll)
        ->addIndexColumn()
        ->make(true);


    }

    public function downloadBudgetTemplate(Request $request){

        $file_type = $request->type;

        $templateMasterID = $request->templateMasterID;
        $companyFinanceYearID = $request->companyFinanceYearID;
        $companySystemID = $request->companySystemID;
        $sentNotificationAt = $request->sentNotificationAt;

        $templateData = [
            "companySystemID" => $companySystemID,
            "companyFinanceYearID" => $companyFinanceYearID
        ];

        $templateName = "download_template.budget_template";
        $fileName = 'budget_template';
        $path = 'general-ledger/transactions/budget-template/excel/';

        $company = Company::with(['reportingcurrency', 'localcurrency'])->find($companySystemID);
        $templateMaster = ReportTemplate::find($templateMasterID);

        if($templateMaster->reportID == 1) {
            $glCOdes = ReportTemplateDetails::with(['gllink'  => function ($query) {
                $query->orderBy('sortOrder', 'asc');
            }])
                ->where('companySystemID', $templateData['companySystemID'])
                ->where('companyReportTemplateID', $templateMasterID)
                ->orderBy('sortOrder', 'asc')
                ->whereNotIn('itemType', [3, 4])
                ->get();

            $glMasters = ReportTemplateDetails::where('companySystemID', $templateData['companySystemID'])
                ->where('companyReportTemplateID', $templateMasterID)
                ->where('masterID', null)
                ->whereNotIn('itemType', [3, 4])
                ->orderBy('sortOrder', 'asc')
                ->get();
        } else {
            $glCOdes = ReportTemplateDetails::with(['gllink'  => function ($query) {
                $query->orderBy('sortOrder', 'asc');
            }])
                ->where('companySystemID', $templateData['companySystemID'])
                ->where('companyReportTemplateID', $templateMasterID)
                ->orderBy('sortOrder', 'asc')
                ->where('itemType', '!=', 3)
                ->get();

            $glMasters = ReportTemplateDetails::where('companySystemID', $templateData['companySystemID'])
                ->where('companyReportTemplateID', $templateMasterID)
                ->where('masterID', null)
                ->where('itemType', '!=', 3)
                ->orderBy('sortOrder', 'asc')
                ->get();
        }

        function buildTree($elements, $parentId = null) {
            $branch = array();

            foreach ($elements as $element) {
                if ($element['masterID'] == $parentId) {
                    $children = buildTree($elements, $element['detID']);
                    if ($children) {
                        $element['children'] = $children;
                    }
                    $branch[] = $element;
                }
            }

            return $branch;
        }

        function sortTree(&$tree) {
            usort($tree, function ($a, $b) {
                return $a['sortOrder'] <=> $b['sortOrder'];
            });

            foreach ($tree as &$branch) {
                if (isset($branch['children'])) {
                    sortTree($branch['children']);
                }
            }
        }

        function flattenTree($tree) {
            $flat = array();

            foreach ($tree as $node) {
                $flat[] = $node;
                if (isset($node['children'])) {
                    $flat = array_merge($flat, flattenTree($node['children']));
                    unset($node['children']);
                }
            }

            return $flat;
        }

        $tree = buildTree($glCOdes->toArray());
        sortTree($tree);
        $sortedFlat = flattenTree($tree);

        $financeYearMaster = CompanyFinanceYear::find($companyFinanceYearID);

        $companyCode = isset($company->CompanyID) ? $company->CompanyID: null;

        $beginDate = $financeYearMaster->bigginingDate;
        $beginDate = date('d/m/Y', strtotime($beginDate));

        $endDate = $financeYearMaster->endingDate;
        $endDate = date('d/m/Y', strtotime($endDate));

        $budgetMasterSegments = BudgetMaster::where('companySystemID', $templateData['companySystemID'])->where('companyFinanceYearID', $companyFinanceYearID)->where('templateMasterID', $templateMasterID)->pluck('serviceLineSystemID');

        $segments = SegmentMaster::where('isActive', 1)->where('companySystemID', $templateData['companySystemID'])->whereNotIn('serviceLineSystemID', $budgetMasterSegments)->get();

        if($segments->isEmpty()){
            return $this->sendError(trans('custom.the_budget_for_all_segments_has_already_been_uploa'));
        }

        $output = array(
            'segments' => $segments,
            'company' => $company,
            'glMasters' => $glMasters,
            'templateDetails' => $sortedFlat,
            'sentNotificationAt' => $sentNotificationAt,
            'templateMaster' => $templateMaster,
            'financeYearMaster' => $financeYearMaster,
            'companyCode'=>$companyCode,
            'beginDate' => $beginDate,
            'endDate' => $endDate
        );

        $basePath = CreateExcel::loadView($output,$file_type,$fileName,$path,$templateName);

        if($basePath == '')
        {
            return $this->sendError('Unable to export excel');
        }
        else
        {
            return $this->sendResponse($basePath, trans('custom.success_export'));
        }
    }

    public function uploadBudgets(Request $request) {

        $input = $request->all();
        $excelUpload = $input['excelUploadBudget'];
        $input = array_except($request->all(), 'excelUploadBudget');
        $input = $this->convertArrayToValue($input);

        $decodeFile = base64_decode($excelUpload[0]['file']);
        $originalFileName = $excelUpload[0]['filename'];
        $extension = $excelUpload[0]['filetype'];
        $size = $excelUpload[0]['size'];

        $allowedExtensions = ['xlsx','xls'];

        if (!in_array($extension, $allowedExtensions))
        {
            return $this->sendError('This type of file not allow to upload.you can only upload .xlsx (or) .xls',500);
        }

        if ($size > 20000000) {
            return $this->sendError('The maximum size allow to upload is 20 MB',500);
        }

        $employee = \Helper::getEmployeeInfo();

        $uploadArray = array(
            'companySystemID' => $input['companySystemID'],
            'uploadComment' => $input['uploadComment'],
            'uploadedDate' => \Helper::currentDateTime(),
            'uploadedBy' => $employee->empID,
            'uploadStatus' => -1
        );

        $uploadBudget = UploadBudgets::create($uploadArray);

        $db = isset($request->db) ? $request->db : "";

        $disk = 'local';

        Storage::disk($disk)->put($originalFileName, $decodeFile);

        $objPHPExcel = PHPExcel_IOFactory::load(Storage::disk($disk)->path($originalFileName));

        $uploadData = ['objPHPExcel' => $objPHPExcel,
            'uploadBudget' => $uploadBudget,
            'employee' => $employee,
            'uploadedCompany' =>  $input['companySystemID'],
        ];

        BudgetSegmentBulkInsert::dispatch($db, $uploadData);


        return $this->sendResponse([], trans('custom.budget_upload_successfully'));
    }

    public function getBudgetUploads(Request $request) {

        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $uploadBudgets = UploadBudgets::where('companySystemID', $input['companyId'])->with(['uploaded_by','log'])->select('*');


        return \DataTables::eloquent($uploadBudgets)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('id', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function deleteBudgetUploads(Request $request){

        $input = $request->all();

        $budgetUploadID = $input['budgetUploadID'];

        $uploadBudget = UploadBudgets::find($budgetUploadID);

        if($uploadBudget->uploadStatus == -1) {
            return $this->sendError(trans('custom.upload_in_progress_cannot_be_deleted'));
        }

            $isBudgetMaster = BudgetMaster::where('budgetUploadID', $budgetUploadID)->where('confirmedYN', 1)->orWhere('approvedYN', 1)->first();
            if (empty($isBudgetMaster)) {
                $budgetMasters = BudgetMaster::where('budgetUploadID', $budgetUploadID)->get();
                foreach ($budgetMasters as $budgetMaster) {
                    Budjetdetails::where('budgetmasterID', $budgetMaster->budgetmasterID)->delete();
                }
                BudgetMaster::where('budgetUploadID', $budgetUploadID)->delete();
                UploadBudgets::where('id', $budgetUploadID)->delete();
                return $this->sendResponse([], trans('custom.budget_upload_deleted_successfully'));
            } else {
                return $this->sendError(trans('custom.the_budget_details_have_already_been_saved_cannot_'));
            }

    }

    public function budgetReferBack(Request $request)
    {
        $input = $request->all();
        ini_set('max_execution_time', 21600);
        ini_set('memory_limit', -1);
        $budgetMasterID = $input['budgetMasterID'];

        $budgetMaster = BudgetMaster::find($budgetMasterID);
        if (empty($budgetMaster)) {
            return $this->sendError(trans('custom.budget_not_found'));
        }

        if ($budgetMaster->refferedBackYN != -1) {
            return $this->sendError(trans('custom.you_cannot_refer_back_this_budget'));
        }

        $budgetMasterArray = $budgetMaster->toArray();

        $storePOMasterHistory = BudgetMasterRefferedHistory::insert($budgetMasterArray);


        Budjetdetails::where('budgetmasterID', $budgetMasterID)->chunk(500, function($budgetDetails) use ($budgetMaster) {
            foreach ($budgetDetails as $budgetDetail){
                $budgetDetail['timesReferred'] = $budgetMaster->timesReferred;
                $budgetDetail = $budgetDetail->toArray();
                BudgetDetailsRefferedHistory::insert($budgetDetail);
            }
        });

        $fetchDocumentApproved = DocumentApproved::where('documentSystemCode', $budgetMasterID)
            ->where('companySystemID', $budgetMaster->companySystemID)
            ->where('documentSystemID', $budgetMaster->documentSystemID)
            ->get();

        if (!empty($fetchDocumentApproved)) {
            foreach ($fetchDocumentApproved as $DocumentApproved) {
                $DocumentApproved['refTimes'] = $budgetMaster->timesReferred;
            }
        }

        $DocumentApprovedArray = $fetchDocumentApproved->toArray();

        $storeDocumentReferedHistory = DocumentReferedHistory::insert($DocumentApprovedArray);

        $deleteApproval = DocumentApproved::where('documentSystemCode', $budgetMasterID)
            ->where('companySystemID', $budgetMaster->companySystemID)
            ->where('documentSystemID', $budgetMaster->documentSystemID)
            ->delete();

        if ($deleteApproval) {
            $budgetMaster->refferedBackYN = 0;
            $budgetMaster->confirmedYN = 0;
            $budgetMaster->confirmedByEmpSystemID = null;
            $budgetMaster->confirmedByEmpID = null;
            $budgetMaster->confirmedDate = null;
            $budgetMaster->RollLevForApp_curr = 1;
            $budgetMaster->save();
        }

        return $this->sendResponse($budgetMaster->toArray(), trans('custom.budget_amend_successfully'));
    }
}
