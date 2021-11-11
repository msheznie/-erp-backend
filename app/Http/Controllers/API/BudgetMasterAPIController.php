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

use App\Http\Requests\API\CreateBudgetMasterAPIRequest;
use App\Http\Requests\API\UpdateBudgetMasterAPIRequest;
use App\Jobs\AddBudgetDetails;
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
use App\Models\ReportTemplateDetails;
use App\Models\CurrencyMaster;
use App\Models\DocumentApproved;
use App\Models\DocumentReferedHistory;
use App\Models\ReportTemplate;
use App\Models\ReportTemplateLinks;
use App\Models\DocumentMaster;
use App\Models\EmployeesDepartment;
use App\Models\Months;
use App\Models\PurchaseOrderDetails;
use App\Models\SegmentMaster;
use App\Models\TemplatesGLCode;
use App\Models\TemplatesMaster;
use App\Models\Year;
use App\Models\YesNoSelection;
use App\helper\BudgetConsumptionService;
use App\Models\YesNoSelectionForMinus;
use App\Repositories\BudgetMasterRepository;
use App\Traits\AuditTrial;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
 use Carbon\Carbon;
 use Carbon\CarbonPeriod;

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

        return $this->sendResponse($budgetMasters->toArray(), 'Budget Masters retrieved successfully');
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
            return $this->sendError('Send Notification at percentage cannot be greater than 100', 500);
        }


        $segment = SegmentMaster::find($input['serviceLineSystemID']);
        if (empty($segment)) {
            return $this->sendError('Segment not found', 500);
        }

        $template = ReportTemplate::find($input['templateMasterID']);
        if (empty($template)) {
            return $this->sendError('Template not found', 500);
        }

        if ($segment->isActive == 0) {
            return $this->sendError('Please select a active Segment', 500);
        }
        $input['serviceLineCode'] = $segment->ServiceLineCode;

        $company = Company::where('companySystemID', $input['companySystemID'])->first();
        if (empty($segment)) {
            return $this->sendError('Company not found', 500);
        }

        $input['companyID'] = $company->CompanyID;
        $input['documentSystemID'] = 65;
        $input['documentID'] = 'BUD';

        $companyFinanceYear = CompanyFinanceYear::find($input['companyFinanceYearID']);
        if (empty($companyFinanceYear)) {
            return $this->sendError('Selected financial year is not found.', 500);
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
            return $this->sendError('Already created budgets for selected template.', 500);
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
                return $this->sendError('Already budget created for P&L type template.', 500);
            } else {
                return $this->sendError('Already budget created for BS type template.', 500);
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
        return $this->sendResponse($budgetMasters->toArray(), 'Budget Master saved successfully');
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
            return $this->sendError('Budget Master not found');
        }

        return $this->sendResponse($budgetMaster->toArray(), 'Budget Master retrieved successfully');
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
            return $this->sendError('Budget Master not found');
        }

        if ($budgetMaster->confirmedYN == 1) {
            return $this->sendError('This document already confirmed.', 500);
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

        return $this->sendResponse($budgetMaster->toArray(), 'Budget Master updated successfully');
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
            return $this->sendError('Budget Master not found');
        }

        $deleteBudgetDetails = Budjetdetails::where('budgetmasterID', $id)->delete();

        $budgetMaster->delete();

        return $this->sendResponse($id, 'Budget Master deleted successfully');
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
                                ->when(request('serviceLineSystemID') && !is_null($input['serviceLineSystemID']), function ($q) use ($input) {
                                    return $q->where('serviceLineSystemID', $input['serviceLineSystemID']);
                                })
                                ->when(request('templateMasterID') && !is_null($input['templateMasterID']), function ($q) use ($input) {
                                    return $q->where('templateMasterID', $input['templateMasterID']);
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
                        $q2->where('templateDescription', 'like', "%{$search}%");
                    });
            });
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
            return $this->sendError('Budget Master not found');
        }

        // policy check -> Department wise budget check
        $DLBCPolicy = CompanyPolicyMaster::where('companySystemID', $budgetMaster->companySystemID)
            ->where('companyPolicyCategoryID', 33)
            ->where('isYesNO', 1)
            ->exists();

        $reportData = Budjetdetails::select(DB::raw("(SUM(budjetAmtLocal) * -1) as totalLocal,
                                       (SUM(budjetAmtRpt) * -1) as totalRpt,
                                       chartofaccounts.AccountCode,chartofaccounts.AccountDescription,
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

        return $this->sendResponse($data, 'details retrieved successfully');
    }

    public function budgetGLCodeWiseDetails(Request $request)
    {
        $input = $request->all();
        $total = 0;
        // policy check -> Department wise budget check
        $DLBCPolicy = CompanyPolicyMaster::where('companySystemID', $input['companySystemID'])
            ->where('companyPolicyCategoryID', 33)
            ->where('isYesNO', 1)
            ->exists();
        if ($input['type'] == 1) {
            $data = BudgetConsumedData::where('companySystemID', $input['companySystemID'])
                                    ->when($DLBCPolicy, function ($q) use($input){
                                        return $q->where('serviceLineSystemID', $input['serviceLineSystemID']);
                                    })
                                    ->where(function($query) {
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
                                    })
                                    ->where(function($query) {
                                        $query->whereNull('projectID')
                                              ->orWhere('projectID', 0);
                                      })
                                    ->where('companyFinanceYearID', $input['companyFinanceYearID'])
                                    ->where('chartOfAccountID', $input['chartOfAccountID'])
                                    ->where('consumeYN', -1)
                                    ->get();


            foreach ($data as $key => $value) {
                $actualConsumption = 0;
                if ($value->documentSystemID == 2 && isset($value->purchase_order->grvRecieved) && $value->purchase_order->grvRecieved == 1) {
                    if (isset($value->purchase_order->financeCategory) && $value->purchase_order->financeCategory != 3) {
                        $glColumnName = 'financeGLcodePLSystemID';
                    } else {
                        $glColumnName = 'financeGLcodebBSSystemID';
                    }

                    $notRecivedPo = PurchaseOrderDetails::selectRaw('SUM((GRVcostPerUnitSupTransCur * segment_allocated_items.allocatedQty) - (GRVcostPerUnitSupTransCur * receivedQty)) as remainingAmount, SUM(GRVcostPerUnitSupTransCur * receivedQty) as receivedAmount')
                                                        ->where($glColumnName, $input['chartOfAccountID'])
                                                        ->where('purchaseOrderMasterID', $value->documentSystemCode)
                                                        ->join('segment_allocated_items', 'documentDetailAutoID', '=', 'purchaseOrderDetailsID')
                                                        ->when($DLBCPolicy, function($query) use ($input) {
                                                            $query->where('segment_allocated_items.serviceLineSystemID', $input['serviceLineSystemID']);
                                                        })
                                                        ->whereHas('order', function($query) {
                                                            $query->where(function($query) {
                                                                    $query->where('projectID', 0)
                                                                          ->orWhereNull('projectID');
                                                                });
                                                        })
                                                        ->where('segment_allocated_items.documentSystemID', $value->documentSystemID)
                                                        ->groupBy('purchaseOrderMasterID')
                                                        ->first();

                    if ($notRecivedPo) {
                        $currencyConversionRptAmount = \Helper::currencyConversion($input['companySystemID'], $value->purchase_order->supplierTransactionCurrencyID, $value->purchase_order->supplierTransactionCurrencyID, $notRecivedPo->remainingAmount);
                        $actualConsumption = $value->consumedRptAmount - $currencyConversionRptAmount['reportingAmount'];
                    }
                } else {
                    $actualConsumption = $value->consumedRptAmount;
                }

                $value->actualConsumption = $actualConsumption;
            }

            $total = array_sum(collect($data)->pluck('actualConsumption')->toArray());
        } else if ($input['type'] == 2) {

            $data1 = PurchaseOrderDetails::whereHas('order', function ($q) use ($input,$DLBCPolicy) {
                                            $q->where('companySystemID', $input['companySystemID'])
                                                ->when($DLBCPolicy, function ($q) use($input){
                                                    return $q->where('serviceLineSystemID', $input['serviceLineSystemID']);
                                                })
                                                ->where('approved', 0)
                                                ->where('poCancelledYN', 0)
                                                ->where(function($query) {
                                                    $query->where('projectID', 0)
                                                          ->orWhereNull('projectID');
                                                });
                                        })
                                        ->where('budgetYear', $input['Year'])
                                        ->where('financeGLcodePLSystemID', $input['chartOfAccountID'])
                                        ->whereNotNull('financeGLcodePLSystemID')
                                        ->with(['order'])
                                        ->get();

            $pendingSupplierInvoiceAmount = DirectInvoiceDetails::where('companySystemID', $input['companySystemID'])
                                                            ->when($DLBCPolicy, function($query) use ($input) {
                                                                $query->where('serviceLineSystemID', $input['serviceLineSystemID']);
                                                            })
                                                            ->where('chartOfAccountSystemID', $input['chartOfAccountID'])
                                                            ->whereHas('supplier_invoice_master', function($query) use ($input) {
                                                                $query->where('approved', 0)
                                                                      ->where('cancelYN', 0)
                                                                      ->where('documentType', 1)
                                                                      ->where('companySystemID', $input['companySystemID']);
                                                             })
                                                            ->with(['supplier_invoice_master'])
                                                            ->get();

            $pendingPvAmount = DirectPaymentDetails::where('companySystemID', $input['companySystemID'])
                                                ->when($DLBCPolicy, function($query) use ($input) {
                                                    $query->where('serviceLineSystemID', $input['serviceLineSystemID']);
                                                })
                                                ->with(['master'])
                                                ->where('chartOfAccountSystemID', $input['chartOfAccountID'])
                                                ->whereHas('master', function($query) use ($input) {
                                                    $query->where('approved', 0)
                                                          ->where('cancelYN', 0)
                                                          ->where('invoiceType', 3)
                                                          ->where('companySystemID', $input['companySystemID']);
                                                 })
                                                ->get();


            $data = [];

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

            $total = array_sum(collect($data)->pluck('lineTotal')->toArray());
        } else if ($input['type'] == 3) {
            $data = BudgetConsumedData::where('companySystemID', $input['companySystemID'])
                                    ->when($DLBCPolicy, function ($q) use($input){
                                        return $q->where('serviceLineSystemID', $input['serviceLineSystemID']);
                                    })
                                    ->where('companyFinanceYearID', $input['companyFinanceYearID'])
                                    ->where('consumeYN', -1)
                                    ->with(['purchase_order' => function ($query) {
                                                $query->where('grvRecieved', '!=', 2);
                                    }])
                                    ->where('documentSystemID', 2)
                                    ->whereHas('purchase_order', function ($query) {
                                        $query->where('grvRecieved', '!=', 2);
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

            foreach ($data as $key => $value) {
                $committedAmount = 0;
                if (isset($value->purchase_order->grvRecieved) && $value->purchase_order->grvRecieved == 0) {
                    $committedAmount += $value->consumedRptAmount;
                } else {
                    if (isset($value->purchase_order->financeCategory) && $value->purchase_order->financeCategory != 3) {
                        $glColumnName = 'financeGLcodePLSystemID';
                    } else {
                        $glColumnName = 'financeGLcodebBSSystemID';
                    }

                    $notRecivedPo = PurchaseOrderDetails::selectRaw('SUM((GRVcostPerUnitSupTransCur * segment_allocated_items.allocatedQty) - (GRVcostPerUnitSupTransCur * receivedQty)) as remainingAmount, SUM(GRVcostPerUnitSupTransCur * receivedQty) as receivedAmount')
                                                        ->join(DB::raw('(SELECT
                                                                        erp_companyreporttemplatelinks.templateDetailID as templatesDetailsAutoID,
                                                                        erp_companyreporttemplatelinks.templateMasterID,
                                                                        erp_companyreporttemplatelinks.glAutoID as chartOfAccountSystemID,
                                                                        erp_companyreporttemplatelinks.glCode 
                                                                        FROM
                                                                        erp_companyreporttemplatelinks
                                                                        WHERE erp_companyreporttemplatelinks.templateMasterID =' . $input['templatesMasterAutoID'] . ' AND erp_companyreporttemplatelinks.templateDetailID = ' . $input['templateDetailID'] . ' AND erp_companyreporttemplatelinks.glAutoID is not null) as tem_gl'),
                                                        function ($join) use ($glColumnName){
                                                            $join->on('erp_purchaseorderdetails.'.$glColumnName, '=', 'tem_gl.chartOfAccountSystemID');
                                                        })
                                                        ->where('purchaseOrderMasterID', $value->documentSystemCode)
                                                        ->join('segment_allocated_items', 'documentDetailAutoID', '=', 'purchaseOrderDetailsID')
                                                        ->when($DLBCPolicy, function($query) use ($input) {
                                                            $query->where('segment_allocated_items.serviceLineSystemID', $input['serviceLineSystemID']);
                                                        })
                                                        ->whereHas('order', function($query) {
                                                            $query->where(function($query) {
                                                                    $query->where('projectID', 0)
                                                                          ->orWhereNull('projectID');
                                                                });
                                                        })
                                                        ->where('segment_allocated_items.documentSystemID', $value->documentSystemID)
                                                        ->groupBy('purchaseOrderMasterID')
                                                        ->first();

                    if ($notRecivedPo) {
                        $currencyConversionRptAmount = \Helper::currencyConversion($input['companySystemID'], $value->purchase_order->supplierTransactionCurrencyID, $value->purchase_order->supplierTransactionCurrencyID, $notRecivedPo->remainingAmount);
                        $committedAmount += $currencyConversionRptAmount['reportingAmount'];
                    }
                }

                $value->committedAmount = $committedAmount;
            }

            $total = array_sum(collect($data)->pluck('committedAmount')->toArray());
        } else if ($input['type'] == 4) {
            $data1 = PurchaseOrderDetails::whereHas('order', function ($q) use ($input,$DLBCPolicy) {
                                            $q->where('companySystemID', $input['companySystemID'])
                                                ->when($DLBCPolicy, function ($q) use($input){
                                                    return $q->where('serviceLineSystemID', $input['serviceLineSystemID']);
                                                })
                                                ->where('approved', 0)
                                                ->where('poCancelledYN', 0)
                                                ->where(function($query) {
                                                    $query->where('projectID', 0)
                                                          ->orWhereNull('projectID');
                                                });
                                        })
                                        ->where('budgetYear', $input['Year'])
                                        ->where('financeGLcodePLSystemID', $input['chartOfAccountID'])
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
                                            $join->on('erp_purchaseorderdetails.financeGLcodePLSystemID', '=', 'tem_gl.chartOfAccountSystemID');
                                        })
                                        ->with(['order'])
                                        ->get();

            $pendingSupplierInvoiceAmount = DirectInvoiceDetails::where('companySystemID', $input['companySystemID'])
                                                            ->when($DLBCPolicy, function($query) use ($input) {
                                                                $query->where('serviceLineSystemID', $input['serviceLineSystemID']);
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
                                                            ->whereHas('supplier_invoice_master', function($query) use ($input) {
                                                                $query->where('approved', 0)
                                                                      ->where('cancelYN', 0)
                                                                      ->where('documentType', 1)
                                                                      ->where('companySystemID', $input['companySystemID']);
                                                             })
                                                            ->with(['supplier_invoice_master'])
                                                            ->get();

            $pendingPvAmount = DirectPaymentDetails::where('companySystemID', $input['companySystemID'])
                                                ->when($DLBCPolicy, function($query) use ($input) {
                                                    $query->where('serviceLineSystemID', $input['serviceLineSystemID']);
                                                })
                                                ->with(['master'])
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
                                                ->whereHas('master', function($query) use ($input) {
                                                    $query->where('approved', 0)
                                                          ->where('cancelYN', 0)
                                                          ->where('invoiceType', 3)
                                                          ->where('companySystemID', $input['companySystemID']);
                                                 })
                                                ->get();


            $data = [];

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

            $total = array_sum(collect($data)->pluck('lineTotal')->toArray());
        } else if ($input['type'] == 5) {
            $data =BudgetConsumedData::with(['purchase_order' => function ($query) {
                                                $query->where('grvRecieved', '!=', 2);
                                    }])
                                    ->where('consumeYN', -1)
                                    ->where('companySystemID', $input['companySystemID'])
                                    ->when($DLBCPolicy, function($query) use ($input){
                                        $query->where('serviceLineSystemID', $input['serviceLineSystemID']);
                                    })
                                    ->where('chartOfAccountID', $input['chartOfAccountID'])
                                    ->where('companyFinanceYearID', $input['companyFinanceYearID'])
                                    ->where('documentSystemID', 2)
                                    ->whereHas('purchase_order', function ($query) {
                                        $query->where('grvRecieved', '!=', 2);
                                    })
                                     ->where(function($query) {
                                        $query->whereNull('projectID')
                                              ->orWhere('projectID', 0);
                                      })
                                    ->get();

            foreach ($data as $key => $value) {
                $committedAmount = 0;
                if (isset($value->purchase_order->grvRecieved) && $value->purchase_order->grvRecieved == 0) {
                    $committedAmount += $value->consumedRptAmount;
                } else {
                    if (isset($value->purchase_order->financeCategory) && $value->purchase_order->financeCategory != 3) {
                        $glColumnName = 'financeGLcodePLSystemID';
                    } else {
                        $glColumnName = 'financeGLcodebBSSystemID';
                    }

                    $notRecivedPo = PurchaseOrderDetails::selectRaw('SUM((GRVcostPerUnitSupTransCur * segment_allocated_items.allocatedQty) - (GRVcostPerUnitSupTransCur * receivedQty)) as remainingAmount, SUM(GRVcostPerUnitSupTransCur * receivedQty) as receivedAmount')
                                                        ->where($glColumnName, $input['chartOfAccountID'])
                                                        ->where('purchaseOrderMasterID', $value->documentSystemCode)
                                                        ->join('segment_allocated_items', 'documentDetailAutoID', '=', 'purchaseOrderDetailsID')
                                                        ->when($DLBCPolicy, function($query) use ($input) {
                                                            $query->where('segment_allocated_items.serviceLineSystemID', $input['serviceLineSystemID']);
                                                        })
                                                        ->where('segment_allocated_items.documentSystemID', $value->documentSystemID)
                                                        ->groupBy('purchaseOrderMasterID')
                                                        ->whereHas('order', function($query) {
                                                            $query->where(function($query) {
                                                                    $query->where('projectID', 0)
                                                                          ->orWhereNull('projectID');
                                                                });
                                                        })
                                                        ->first();

                    if ($notRecivedPo) {
                        $currencyConversionRptAmount = \Helper::currencyConversion($input['companySystemID'], $value->purchase_order->supplierTransactionCurrencyID, $value->purchase_order->supplierTransactionCurrencyID, $notRecivedPo->remainingAmount);
                        $committedAmount += $currencyConversionRptAmount['reportingAmount'];
                    }
                }

                $value->committedAmount = $committedAmount;
            }

            $total = array_sum(collect($data)->pluck('committedAmount')->toArray());
        } else if ($input['type'] == 6) {
            $data = BudgetConsumedData::where('companySystemID', $input['companySystemID'])
                                    ->when($DLBCPolicy, function ($q) use($input){
                                        return $q->where('serviceLineSystemID', $input['serviceLineSystemID']);
                                    })
                                    ->where('companyFinanceYearID', $input['companyFinanceYearID'])
                                    ->where('consumeYN', -1)
                                    ->where(function($query) {
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

            foreach ($data as $key => $value) {
                $actualConsumption = 0;
                if ($value->documentSystemID == 2 && isset($value->purchase_order->grvRecieved) && $value->purchase_order->grvRecieved == 1) {
                    if (isset($value->purchase_order->financeCategory) && $value->purchase_order->financeCategory != 3) {
                        $glColumnName = 'financeGLcodePLSystemID';
                    } else {
                        $glColumnName = 'financeGLcodebBSSystemID';
                    }

                    $notRecivedPo = PurchaseOrderDetails::selectRaw('SUM((GRVcostPerUnitSupTransCur * segment_allocated_items.allocatedQty) - (GRVcostPerUnitSupTransCur * receivedQty)) as remainingAmount, SUM(GRVcostPerUnitSupTransCur * receivedQty) as receivedAmount')
                                                        ->join(DB::raw('(SELECT
                                                                        erp_companyreporttemplatelinks.templateDetailID as templatesDetailsAutoID,
                                                                        erp_companyreporttemplatelinks.templateMasterID,
                                                                        erp_companyreporttemplatelinks.glAutoID as chartOfAccountSystemID,
                                                                        erp_companyreporttemplatelinks.glCode 
                                                                        FROM
                                                                        erp_companyreporttemplatelinks
                                                                        WHERE erp_companyreporttemplatelinks.templateMasterID =' . $input['templatesMasterAutoID'] . ' AND erp_companyreporttemplatelinks.templateDetailID = ' . $input['templateDetailID'] . ' AND erp_companyreporttemplatelinks.glAutoID is not null) as tem_gl'),
                                                        function ($join) use ($glColumnName){
                                                            $join->on('erp_purchaseorderdetails.'.$glColumnName, '=', 'tem_gl.chartOfAccountSystemID');
                                                        })
                                                        ->where('purchaseOrderMasterID', $value->documentSystemCode)
                                                        ->join('segment_allocated_items', 'documentDetailAutoID', '=', 'purchaseOrderDetailsID')
                                                        ->when($DLBCPolicy, function($query) use ($input) {
                                                            $query->where('segment_allocated_items.serviceLineSystemID', $input['serviceLineSystemID']);
                                                        })
                                                        ->where('segment_allocated_items.documentSystemID', $value->documentSystemID)
                                                        ->whereHas('order', function($query) {
                                                            $query->where(function($query) {
                                                                    $query->where('projectID', 0)
                                                                          ->orWhereNull('projectID');
                                                                });
                                                        })
                                                        ->groupBy('purchaseOrderMasterID')
                                                        ->first();

                    if ($notRecivedPo) {
                        $currencyConversionRptAmount = \Helper::currencyConversion($input['companySystemID'], $value->purchase_order->supplierTransactionCurrencyID, $value->purchase_order->supplierTransactionCurrencyID, $notRecivedPo->remainingAmount);
                        $actualConsumption = $value->consumedRptAmount - $currencyConversionRptAmount['reportingAmount'];
                    }
                } else {
                    $actualConsumption = $value->consumedRptAmount;
                }

                $value->actualConsumption = $actualConsumption;
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

        return $this->sendResponse($result, 'details retrieved successfully');
    }


    public function reportBudgetTemplateCategoryWise(Request $request)
    {
        $input = $request->all();


        /** @var BudgetMaster $budgetMaster */
        $budgetMaster = $this->budgetMasterRepository->with(['segment_by', 'template_master', 'finance_year_by'])->findWithoutFail($input['id']);

        if (empty($budgetMaster)) {
            return $this->sendError('Budget Master not found');
        }

        // policy check -> Department wise budget check
        $DLBCPolicy = CompanyPolicyMaster::where('companySystemID', $budgetMaster->companySystemID)
            ->where('companyPolicyCategoryID', 33)
            ->where('isYesNO', 1)
            ->exists();

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

        return $this->sendResponse($data, 'details retrieved successfully');
    }

    public function getGlCodeWiseCommitedBudgetAmount($data, $glIds, $DLBCPolicy)
    {
        $consumedData = BudgetConsumedData::where('companySystemID', $data['companySystemID'])
                                    ->when($DLBCPolicy, function ($q) use($data){
                                        return $q->where('serviceLineSystemID', $data['serviceLineSystemID']);
                                    })
                                    ->with(['purchase_order' => function ($query) {
                                        $query->where('grvRecieved', '!=', 2);
                                    }])
                                    ->where('companyFinanceYearID', $data['companyFinanceYearID'])
                                    ->whereIn('chartOfAccountID', $glIds)
                                    ->where('consumeYN', -1)
                                    ->where('documentSystemID', 2)
                                    ->whereHas('purchase_order', function ($query) {
                                        $query->where('grvRecieved', '!=', 2);
                                    })
                                    ->where(function($query) {
                                        $query->whereNull('projectID')
                                              ->orWhere('projectID', 0);
                                      })
                                    ->get();

        $committedAmount = 0;
        foreach ($consumedData as $key => $value) {
            if (isset($value->purchase_order->grvRecieved) && $value->purchase_order->grvRecieved == 0) {
                $committedAmount += $value->consumedRptAmount;
            } else {
                if (isset($value->purchase_order->financeCategory) && $value->purchase_order->financeCategory != 3) {
                    $glColumnName = 'financeGLcodePLSystemID';
                } else {
                    $glColumnName = 'financeGLcodebBSSystemID';
                }

                $notRecivedPo = PurchaseOrderDetails::selectRaw('SUM((GRVcostPerUnitSupTransCur * segment_allocated_items.allocatedQty) - (GRVcostPerUnitSupTransCur * receivedQty)) as remainingAmount, SUM(GRVcostPerUnitSupTransCur * receivedQty) as receivedAmount')
                                                    ->whereIn($glColumnName, $glIds)
                                                    ->where('purchaseOrderMasterID', $value->documentSystemCode)
                                                    ->join('segment_allocated_items', 'documentDetailAutoID', '=', 'purchaseOrderDetailsID')
                                                    ->when($DLBCPolicy, function($query) use ($data) {
                                                        $query->where('segment_allocated_items.serviceLineSystemID', $data['serviceLineSystemID']);
                                                    })
                                                    ->where('segment_allocated_items.documentSystemID', $value->documentSystemID)
                                                    ->whereHas('order', function($query) {
                                                        $query->where(function($query) {
                                                                $query->where('projectID', 0)
                                                                      ->orWhereNull('projectID');
                                                            });
                                                    })
                                                    ->groupBy('purchaseOrderMasterID')
                                                    ->first();

                if ($notRecivedPo) {
                    $currencyConversionRptAmount = \Helper::currencyConversion($data['companySystemID'], $value->purchase_order->supplierTransactionCurrencyID, $value->purchase_order->supplierTransactionCurrencyID, $notRecivedPo->remainingAmount);
                    $committedAmount += $currencyConversionRptAmount['reportingAmount'];
                }
            }
        }

        return $committedAmount;
    }

    public function getGlCodeWiseActualConsumption($dataParam, $glIds, $DLBCPolicy)
    {
        $data = BudgetConsumedData::where('companySystemID', $dataParam['companySystemID'])
                                    ->when($DLBCPolicy, function ($q) use($dataParam){
                                        return $q->where('serviceLineSystemID', $dataParam['serviceLineSystemID']);
                                    })
                                    ->where(function($query) {
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
                                    })
                                    ->where(function($query) {
                                        $query->whereNull('projectID')
                                              ->orWhere('projectID', 0);
                                      })
                                    ->where('companyFinanceYearID', $dataParam['companyFinanceYearID'])
                                    ->whereIn('chartOfAccountID', $glIds)
                                    ->where('consumeYN', -1)
                                    ->get();


        foreach ($data as $key => $value) {
            $actualConsumption = 0;
            if ($value->documentSystemID == 2 && isset($value->purchase_order->grvRecieved) && $value->purchase_order->grvRecieved == 1) {
                if (isset($value->purchase_order->financeCategory) && $value->purchase_order->financeCategory != 3) {
                    $glColumnName = 'financeGLcodePLSystemID';
                } else {
                    $glColumnName = 'financeGLcodebBSSystemID';
                }

                $notRecivedPo = PurchaseOrderDetails::selectRaw('SUM((GRVcostPerUnitSupTransCur * segment_allocated_items.allocatedQty) - (GRVcostPerUnitSupTransCur * receivedQty)) as remainingAmount, SUM(GRVcostPerUnitSupTransCur * receivedQty) as receivedAmount')
                                                    ->whereIn($glColumnName, $glIds)
                                                    ->where('purchaseOrderMasterID', $value->documentSystemCode)
                                                    ->join('segment_allocated_items', 'documentDetailAutoID', '=', 'purchaseOrderDetailsID')
                                                    ->when($DLBCPolicy, function($query) use ($dataParam) {
                                                        $query->where('segment_allocated_items.serviceLineSystemID', $dataParam['serviceLineSystemID']);
                                                    })
                                                    ->where('segment_allocated_items.documentSystemID', $value->documentSystemID)
                                                     ->whereHas('order', function($query) {
                                                        $query->where(function($query) {
                                                                $query->where('projectID', 0)
                                                                      ->orWhereNull('projectID');
                                                            });
                                                    })
                                                    ->groupBy('purchaseOrderMasterID')
                                                    ->first();

                if ($notRecivedPo) {
                    $currencyConversionRptAmount = \Helper::currencyConversion($dataParam['companySystemID'], $value->purchase_order->supplierTransactionCurrencyID, $value->purchase_order->supplierTransactionCurrencyID, $notRecivedPo->remainingAmount);
                    $actualConsumption = $value->consumedRptAmount - $currencyConversionRptAmount['reportingAmount'];
                }
            } else {
                $actualConsumption = $value->consumedRptAmount;
            }

            $value->actualConsumption = $actualConsumption;
        }

        return array_sum(collect($data)->pluck('actualConsumption')->toArray());
    }

    public function getGlCodeWisePendingDocAmount($dataParam, $glIds, $DLBCPolicy)
    {
        $data1 = PurchaseOrderDetails::whereHas('order', function ($q) use ($dataParam,$DLBCPolicy) {
                                            $q->where('companySystemID', $dataParam['companySystemID'])
                                            ->when($DLBCPolicy, function ($q) use($dataParam){
                                                return $q->where('serviceLineSystemID', $dataParam['serviceLineSystemID']);
                                            })
                                            ->where('approved', 0)
                                            ->where('poCancelledYN', 0)
                                            ->where(function($query) {
                                                $query->where('projectID', 0)
                                                      ->orWhereNull('projectID');
                                            });
                                    })
                                    ->where('budgetYear', $dataParam['Year'])
                                    ->whereIn('financeGLcodePLSystemID', $glIds)
                                    ->whereNotNull('financeGLcodePLSystemID')
                                    ->with(['order'])
                                    ->get();

        $pendingSupplierInvoiceAmount = DirectInvoiceDetails::where('companySystemID', $dataParam['companySystemID'])
                                                        ->when($DLBCPolicy, function($query) use ($dataParam) {
                                                            $query->where('serviceLineSystemID', $dataParam['serviceLineSystemID']);
                                                        })
                                                        ->whereIn('chartOfAccountSystemID', $glIds)
                                                        ->whereHas('supplier_invoice_master', function($query) use ($dataParam) {
                                                            $query->where('approved', 0)
                                                                  ->where('cancelYN', 0)
                                                                  ->where('documentType', 1)
                                                                  ->where('companySystemID', $dataParam['companySystemID']);
                                                         })
                                                        ->with(['supplier_invoice_master'])
                                                        ->get();

        $pendingPvAmount = DirectPaymentDetails::where('companySystemID', $dataParam['companySystemID'])
                                            ->when($DLBCPolicy, function($query) use ($dataParam) {
                                                $query->where('serviceLineSystemID', $dataParam['serviceLineSystemID']);
                                            })
                                            ->with(['master'])
                                            ->whereIn('chartOfAccountSystemID', $glIds)
                                            ->whereHas('master', function($query) use ($dataParam) {
                                                $query->where('approved', 0)
                                                      ->where('cancelYN', 0)
                                                      ->where('invoiceType', 3)
                                                      ->where('companySystemID', $dataParam['companySystemID']);
                                             })
                                            ->get();


        $data = [];

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

        if (isset($input['companyFinanceYearID']) && !is_null($input['companyFinanceYearID']) && $input['companyFinanceYearID'] != 'null' && isset($input['serviceLineSystemID']) && !is_null($input['serviceLineSystemID']) && $input['serviceLineSystemID'] != 'null') {

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
                                                 ->where('reportID', '!=', 3)
                                                 ->get();
            }
        }


        $currencyData = \Helper::companyCurrency($companyId);

        $output = array(
            'reportTemplates' => $reportTemplates,
            'currencyData' => $currencyData,
            'yesNoSelection' => $yesNoSelection,
            'yesNoSelectionForMinus' => $yesNoSelectionForMinus,
            'month' => $month,
            'years' => $years,
            'companyFinanceYear' => $companyFinanceYear,
            'segments' => $segments,
            'masterTemplates' => $masterTemplates,
            'financeYear' => $financeYear
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    public function getBudgetAudit(Request $request)
    {

        $id  = $request->get('id');
        /** @var BudgetMaster $budgetMaster */
        $budgetMaster = $this->budgetMasterRepository->getAudit($id);

        if (empty($budgetMaster)) {
            return $this->sendError('Budget Master not found');
        }

        return $this->sendResponse($budgetMaster->toArray(), 'Budget Master retrieved successfully');
    }


    public function budgetReopen(Request $request)
    {
        $input = $request->all();

        $id = $input['budgetmasterID'];
        $budget = $this->budgetMasterRepository->findWithoutFail($id);
        $emails = array();
        if (empty($budget)) {
            return $this->sendError('Budget not found');
        }

        if ($budget->approvedYN == -1) {
            return $this->sendError('You cannot reopen this Budget it is already fully approved');
        }

        if ($budget->RollLevForApp_curr > 1) {
            return $this->sendError('You cannot reopen this Budget it is already partially approved');
        }

        if ($budget->confirmedYN == 0) {
            return $this->sendError('You cannot reopen this Budget, it is not confirmed');
        }

        $updateInput = ['confirmedYN' => 0, 'confirmedByEmpSystemID' => null, 'confirmedByEmpID' => null,
            'confirmedByEmpName' => null, 'confirmedDate' => null, 'RollLevForApp_curr' => 1];

        $this->budgetMasterRepository->update($updateInput, $id);

        $employee = \Helper::getEmployeeInfo();

        $document = DocumentMaster::where('documentSystemID', $budget->documentSystemID)->first();

        $cancelDocNameBody = $document->documentDescription . ' <b>' . $budget->budgetmasterID . '</b>';
        $cancelDocNameSubject = $document->documentDescription . ' ' . $budget->budgetmasterID;

        $subject = $cancelDocNameSubject . ' is reopened';

        $body = '<p>' . $cancelDocNameBody . ' is reopened by ' . $employee->empID . ' - ' . $employee->empFullName . '</p><p>Comment : ' . $input['reopenComments'] . '</p>';

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

        return $this->sendResponse($budget->toArray(), 'Budget reopened successfully');
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
                      ->orWhere('templateDescription', 'like', "%{$search}%");
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
            return $this->sendError('Selected financial year is not found.', 500);
        }

        $result = CarbonPeriod::create($companyFinanceYear->bigginingDate, '1 month', $companyFinanceYear->endingDate);
        $monthArray = [];
        foreach ($result as $dt) {
            $temp['year'] = $dt->format("Y");
            $temp['monthID'] = floatval($dt->format("m"));
            $temp['monthName'] = (Months::find(floatval($dt->format("m")))) ? Months::find(floatval($dt->format("m")))->monthDes : "";

            $monthArray[] = $temp;
        }

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

        return $this->sendResponse(['checkBudgetPolicy' => $checkBudgetPolicy], 'Budget policy retrieved successfully');
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
                                           ->where('companySystemID', $input['companySystemID'])
                                           ->where('cancelledYN', 0)
                                           ->where('approved', 0)
                                           ->where('budgetBlockYN', -1);
        
        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $purchaseRequests = $purchaseRequests->where(function ($query) use ($search) {
                $query->where('purchaseRequestCode', 'LIKE', "%{$search}%")
                    ->orWhere('comments', 'LIKE', "%{$search}%");
            });
        }

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
            $purchaseOrders = $purchaseOrders->where(function ($query) use ($search) {
                $query->where('purchaseOrderCode', 'LIKE', "%{$search}%")
                    ->orWhere('narration', 'LIKE', "%{$search}%")
                    ->orWhere('referenceNumber', 'LIKE', "%{$search}%")
                    ->orWhere('supplierPrimaryCode', 'LIKE', "%{$search}%")
                    ->orWhere('supplierName', 'LIKE', "%{$search}%");
            });
        }


        $mergeResult = collect($purchaseOrders->get())->merge($purchaseRequests->get());

        $mergeAll = $mergeResult->all();

        return \DataTables::of($mergeAll)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        // $query->orderBy('documentSystemCode', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);

    }

    public function budgetReferBack(Request $request)
    {
        $input = $request->all();

        $budgetMasterID = $input['budgetMasterID'];

        $budgetMaster = BudgetMaster::find($budgetMasterID);
        if (empty($budgetMaster)) {
            return $this->sendError('Budget not found');
        }

        if ($budgetMaster->refferedBackYN != -1) {
            return $this->sendError('You cannot refer Back this Budget');
        }

        $budgetMasterArray = $budgetMaster->toArray();

        $storePOMasterHistory = BudgetMasterRefferedHistory::insert($budgetMasterArray);

        $fetchBudgetDetail = Budjetdetails::where('budgetmasterID', $budgetMasterID)
            ->get();


        if (!empty($fetchBudgetDetail)) {
            foreach ($fetchBudgetDetail as $poDetail) {
                $poDetail['timesReferred'] = $budgetMaster->timesReferred;
            }
        }

        $budgetDetailArray = $fetchBudgetDetail->toArray();

        $storePODetailHistory = BudgetDetailsRefferedHistory::insert($budgetDetailArray);

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

        return $this->sendResponse($budgetMaster->toArray(), 'Budget Amend successfully');
    }
}
