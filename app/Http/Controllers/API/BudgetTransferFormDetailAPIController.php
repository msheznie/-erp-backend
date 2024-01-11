<?php
/**
 * =============================================
 * -- File Name : BudgetTransferFormDetailAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Budget Transfer
 * -- Author : Mohamed Fayas
 * -- Create date : 18 - August 2018
 * -- Description : This file contains the all CRUD for Budget Transfer Form Detail
 * -- REVISION HISTORY
 * -- Date: 08-August 2018 By: Nazir Description: Added new function getDetailsByBudgetTransfer()
 */


namespace App\Http\Controllers\API;

use App\helper\BudgetConsumptionService;
use App\helper\CurrencyValidation;
use App\helper\Helper;
use App\Http\Requests\API\CreateBudgetTransferFormDetailAPIRequest;
use App\Http\Requests\API\UpdateBudgetTransferFormDetailAPIRequest;
use App\Models\BudgetTransferFormDetail;
use App\Models\Budjetdetails;
use App\Models\Company;
use App\Models\BudgetMaster;
use App\Models\ChartOfAccountsAssigned;
use App\Models\CompanyPolicyMaster;
use App\Models\ContingencyBudgetPlan;
use App\Models\SegmentMaster;
use App\Repositories\BudgetTransferFormDetailRepository;
use App\Repositories\BudgetTransferFormRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class BudgetTransferFormDetailController
 * @package App\Http\Controllers\API
 */
class BudgetTransferFormDetailAPIController extends AppBaseController
{
    /** @var  BudgetTransferFormDetailRepository */
    private $budgetTransferFormDetailRepository;
    private $budgetTransferFormRepository;

    public function __construct(BudgetTransferFormDetailRepository $budgetTransferFormDetailRepo, BudgetTransferFormRepository $budgetTransferFormRepo)
    {
        $this->budgetTransferFormDetailRepository = $budgetTransferFormDetailRepo;
        $this->budgetTransferFormRepository = $budgetTransferFormRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/budgetTransferFormDetails",
     *      summary="Get a listing of the BudgetTransferFormDetails.",
     *      tags={"BudgetTransferFormDetail"},
     *      description="Get all BudgetTransferFormDetails",
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
     *                  @SWG\Items(ref="#/definitions/BudgetTransferFormDetail")
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
        $this->budgetTransferFormDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->budgetTransferFormDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $budgetTransferFormDetails = $this->budgetTransferFormDetailRepository->all();

        return $this->sendResponse($budgetTransferFormDetails->toArray(), 'Budget Transfer Form Details retrieved successfully');
    }

    /**
     * @param CreateBudgetTransferFormDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/budgetTransferFormDetails",
     *      summary="Store a newly created BudgetTransferFormDetail in storage",
     *      tags={"BudgetTransferFormDetail"},
     *      description="Store BudgetTransferFormDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BudgetTransferFormDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BudgetTransferFormDetail")
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
     *                  ref="#/definitions/BudgetTransferFormDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */

    public function store(CreateBudgetTransferFormDetailAPIRequest $request)
    {
        $input = $request->all();
        $budgetTransferToData = isset($input['budgetTransferToData']) ? $input['budgetTransferToData'] : [];

        $input = $this->convertArrayToValue(array_except($input, 'budgetTransferToData'));
        $validator = \Validator::make($input, [
            'budgetTransferFormAutoID' => 'required',
            'fromServiceLineSystemID' => 'required|numeric|min:1',
            'isFromContingency' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        if (count($budgetTransferToData) == 0) {
            return $this->sendError('Budget Transfer To data not found', 500);
        }

        foreach ($budgetTransferToData as $key => $value) {
            $value = $this->convertArrayToValue($value);
            $validator = \Validator::make($value, [
                'toTemplateDetailID' => 'required|numeric|min:1',
                'toServiceLineSystemID' => 'required|numeric|min:1',
                'toChartOfAccountSystemID' => 'required|numeric|min:1',
                'remarks' => 'required',
                'adjustmentAmountRpt' => 'required|numeric'
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->messages(), 422);
            }            
        }

        $budgetTransferMaster = $this->budgetTransferFormRepository->find($input['budgetTransferFormAutoID']);

        if (empty($budgetTransferMaster)) {
            return $this->sendError('Budget Trasnfer is not created for selected segment and financial year');
        }

        $masterValidate = \Validator::make($budgetTransferMaster->toArray(), [
            'year' => 'required',
            'templatesMasterAutoID' => 'required',
        ]);

        if ($masterValidate->fails()) {
            return $this->sendError($masterValidate->messages(), 422);
        }

        $fromDepartment = SegmentMaster::where('companySystemID', $budgetTransferMaster->companySystemID)
            ->where('serviceLineSystemID', $input['fromServiceLineSystemID'])
            ->first();

        if (empty($fromDepartment)) {
            throw new \Exception("From Department not found", 500);
        }

        if ($fromDepartment->isActive == 0) {
            throw new \Exception("Please select an active from department", 500);
        }

        $input['year'] = $budgetTransferMaster->year;
        $input['fromServiceLineCode'] = $fromDepartment->ServiceLineCode;

        $companyData = Company::find($budgetTransferMaster->companySystemID);

        if (empty($companyData)) {
            return $this->sendError('Company not found');
        }


        try {
            if( $input['isFromContingency'] != 1){
                $fromChartOfAccount = ChartOfAccountsAssigned::where('companySystemID', $budgetTransferMaster->companySystemID)
                    ->where('chartOfAccountSystemID', $input['fromChartOfAccountSystemID'])
                    ->first();

                if (empty($fromChartOfAccount)) {
                    return $this->sendError('From Account Code not found');
                }

                $input['FromGLCode'] = $fromChartOfAccount->AccountCode;
                $input['FromGLCodeDescription'] = $fromChartOfAccount->AccountDescription;
                
                $this->validateTransferBalance($input, $budgetTransferMaster, $budgetTransferToData);
            } else {
                $this->validateContingencyBudget($input, $budgetTransferMaster, $budgetTransferToData);
            }

            DB::beginTransaction();
                foreach ($budgetTransferToData as $key => $value) {
                    $value = $this->convertArrayToValue($value);
                    $input['toChartOfAccountSystemID'] = $value['toChartOfAccountSystemID'];
                    $input['toServiceLineSystemID'] = $value['toServiceLineSystemID'];
                    $input['toTemplateDetailID'] = $value['toTemplateDetailID'];
                    $input['remarks'] = $value['remarks'];
                    $input['adjustmentAmountRpt'] = $value['adjustmentAmountRpt'];

                    $toChartOfAccount  = ChartOfAccountsAssigned::where('companySystemID',$budgetTransferMaster->companySystemID)
                        ->where('chartOfAccountSystemID',$input['toChartOfAccountSystemID'])
                        ->first();

                    if(empty($toChartOfAccount)){
                        return $this->sendError('To Account Not Found', 500);
                    }

                    $toDataBudgetCheck = Budjetdetails::where('companySystemID', $budgetTransferMaster->companySystemID)
                        ->where('chartOfAccountID',$input['toChartOfAccountSystemID'])
                        ->where('serviceLineSystemID',$input['toServiceLineSystemID'])
                        ->where('templateDetailID',$input['toTemplateDetailID'])
                        ->where('Year',$budgetTransferMaster->year)
                        ->count();

                    if($toDataBudgetCheck == 0){
                        return $this->sendError('There is no budget allocated for '.$toChartOfAccount->AccountCode, 500);
                    }
            
                    $toDepartment = SegmentMaster::where('companySystemID', $budgetTransferMaster->companySystemID)
                        ->where('serviceLineSystemID', $input['toServiceLineSystemID'])
                        ->first();

                    if (empty($toDepartment)) {
                        return $this->sendError('To Department not found');
                    }

                    if ($toDepartment->isActive == 0) {
                        return $this->sendError('Please select an active to department', 500);
                    }
                    
                    $input['toServiceLineCode'] = $toDepartment->ServiceLineCode;
                    
                    $toChartOfAccount = ChartOfAccountsAssigned::where('companySystemID', $budgetTransferMaster->companySystemID)
                        ->where('chartOfAccountSystemID', $input['toChartOfAccountSystemID'])->first();

                    if (empty($toChartOfAccount)) {
                        return $this->sendError('To Account Code not found');
                    }

                    $input['toGLCode'] = $toChartOfAccount->AccountCode;
                    $input['toGLCodeDescription'] = $toChartOfAccount->AccountDescription;
                    
                    $currency = \Helper::currencyConversion($budgetTransferMaster->companySystemID, $companyData->reportingCurrency, $companyData->reportingCurrency, $input['adjustmentAmountRpt']);
                    $input['adjustmentAmountLocal'] = $currency['localAmount'];
                    
                    if( $input['isFromContingency'] == 1){
                        $this->from_contingency($input, $budgetTransferMaster);
                    }
                    else{
                        $this->general_transfer($input, $budgetTransferMaster);
                    }
                }

            DB::commit();
            return $this->sendResponse([], 'Budget Transfer Form Detail saved successfully');
        }
        catch (\Exception $ex){
            $error_code = ($ex->getCode() == 422)? 422: 500;
            $msg = ($error_code == 422)? json_decode( $ex->getMessage() ): $ex->getMessage();
            $more = Helper::exception_to_error($ex);

            return $this->sendError($msg, $error_code, $more);
        }
    }

    public function validateTransferBalance($input, $budgetTransferMaster, $budgetTransferToData)
    {
         $validator = \Validator::make($input, [
            'fromTemplateDetailID' => 'required|numeric|min:1',
            'fromChartOfAccountSystemID' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            throw new \Exception($validator->messages(), 422);
        }
        
        $fromChartOfAccount  = ChartOfAccountsAssigned::where('companySystemID',$budgetTransferMaster->companySystemID)
            ->where('chartOfAccountSystemID',$input['fromChartOfAccountSystemID'])
            ->first();

        if(empty($fromChartOfAccount)){
            throw new \Exception("From Account Not Found", 500);
        }

        $fromDataBudgetCheck = Budjetdetails::where('companySystemID', $budgetTransferMaster->companySystemID)
            ->where('chartOfAccountID',$input['fromChartOfAccountSystemID'])
            ->where('serviceLineSystemID',$input['fromServiceLineSystemID'])
            ->where('templateDetailID',$input['fromTemplateDetailID'])
            ->where('Year',$budgetTransferMaster->year)
            ->count();


        if($fromDataBudgetCheck == 0){
            throw new \Exception("Selected account code is not available in the budget. 
                            Please allocate and try again.", 500);
        }


        $checkSameEntry = BudgetTransferFormDetail::where(function ($q) use($input){
            $q->where(function ($q1) use($input){
                $q1->where(function ($q2) use($input){
                    $q2->where('fromTemplateDetailID', $input['fromTemplateDetailID'])
                        ->where('fromChartOfAccountSystemID', $input['fromChartOfAccountSystemID']);
                })->orWhere(function ($q2)use($input) {
                    $q2->where('toTemplateDetailID', $input['fromTemplateDetailID'])
                        ->where('toChartOfAccountSystemID', $input['fromChartOfAccountSystemID']);
                });
            });
        })
        ->where('budgetTransferFormAutoID' , $input['budgetTransferFormAutoID'])
        ->count();

        if ($checkSameEntry > 0) {
            throw new \Exception("Selected GL Code is already added. Please check again", 500);
        }


        $checkPendingFromGL = BudgetTransferFormDetail::where(function ($q) use($input){
            $q->where(function ($q1) use($input){
                $q1->where(function ($q2) use($input){
                    $q2->where('fromTemplateDetailID', $input['fromTemplateDetailID'])
                        ->where('fromChartOfAccountSystemID', $input['fromChartOfAccountSystemID']);
                })->orWhere(function ($q2)use($input) {
                    $q2->where('toTemplateDetailID', $input['fromTemplateDetailID'])
                        ->where('toChartOfAccountSystemID', $input['fromChartOfAccountSystemID']);
                });
            });
        })
        ->whereHas('master',function ($q) use ($budgetTransferMaster) {
            $q->where('companySystemID', $budgetTransferMaster->companySystemID)
                ->where('year', $budgetTransferMaster->year)
                ->where('approvedYN', 0);
        })
        ->with(['master'])
        ->first();

        if (!empty($checkPendingFromGL)) {
            $msg = "There is a Budget Transfer (" . $checkPendingFromGL->master->transferVoucherNo . ") pending for 
                    approval for the GL Code you are trying to add. Please check again.";

            throw new \Exception($msg, 500);
        }


        $checkBalance = Budjetdetails::select(DB::raw("
                                       (SUM(budjetAmtLocal) * -1) as totalLocal,
                                       (SUM(budjetAmtRpt) * -1) as totalRpt,
                                       chartofaccounts.AccountCode,chartofaccounts.AccountDescription,
                                       erp_companyreporttemplatedetails.description as templateDetailDescription,
                                       erp_companyreporttemplatedetails.companyReportTemplateID as templatesMasterAutoID,
                                       erp_budjetdetails.*
                                       ,ifnull(ca.consumed_amount,0) as consumed_amount
                                       ,ifnull(ppo.rptAmt,0) as pending_po_amount,
                                       ((SUM(budjetAmtRpt)*-1) - (ifnull(ca.consumed_amount,0) + ifnull(ppo.rptAmt,0))) AS balance
                                       "))
            ->where('erp_budjetdetails.companySystemID', $budgetTransferMaster->companySystemID)
            ->where('erp_budjetdetails.serviceLineSystemID', $input['fromServiceLineSystemID'])
            ->where('erp_budjetdetails.Year', $budgetTransferMaster->year)
            ->where('erp_budjetdetails.templateDetailID', $input['fromTemplateDetailID'])
            ->where('erp_companyreporttemplatedetails.companyReportTemplateID', $budgetTransferMaster->templatesMasterAutoID)
            ->where('erp_budjetdetails.chartOfAccountID', $input['fromChartOfAccountSystemID'])
            ->leftJoin('chartofaccounts', 'chartOfAccountID', '=', 'chartOfAccountSystemID')
            ->leftJoin('erp_companyreporttemplatedetails', 'templateDetailID', '=', 'detID')
            ->leftJoin(DB::raw('(SELECT erp_budgetconsumeddata.companySystemID, erp_budgetconsumeddata.serviceLineSystemID,
                                                erp_budgetconsumeddata.chartOfAccountID, erp_budgetconsumeddata.Year, 
                                                Sum(erp_budgetconsumeddata.consumedRptAmount) AS consumed_amount FROM
                                                erp_budgetconsumeddata WHERE erp_budgetconsumeddata.consumeYN = -1 
                                                GROUP BY erp_budgetconsumeddata.companySystemID, erp_budgetconsumeddata.serviceLineSystemID, 
                                                erp_budgetconsumeddata.chartOfAccountID, erp_budgetconsumeddata.Year) as ca'),
                function ($join) {
                    $join->on('erp_budjetdetails.companySystemID', '=', 'ca.companySystemID')
                        ->on('erp_budjetdetails.serviceLineSystemID', '=', 'ca.serviceLineSystemID')
                        ->on('erp_budjetdetails.Year', '=', 'ca.Year')
                        ->on('erp_budjetdetails.chartOfAccountID', '=', 'ca.chartOfAccountID');
                })
            ->leftJoin(DB::raw('(SELECT erp_purchaseordermaster.companySystemID, erp_purchaseordermaster.serviceLineSystemID, 
                               erp_purchaseorderdetails.financeGLcodePLSystemID, Sum(GRVcostPerUnitLocalCur * noQty) AS localAmt, 
                               Sum(GRVcostPerUnitComRptCur * noQty) AS rptAmt, erp_purchaseorderdetails.budgetYear FROM 
                               erp_purchaseordermaster INNER JOIN erp_purchaseorderdetails ON erp_purchaseordermaster.purchaseOrderID = erp_purchaseorderdetails.purchaseOrderMasterID WHERE (((erp_purchaseordermaster.approved)=0) 
                               AND ((erp_purchaseordermaster.poCancelledYN)=0))GROUP BY erp_purchaseordermaster.companySystemID, erp_purchaseordermaster.serviceLineSystemID, erp_purchaseorderdetails.financeGLcodePL, erp_purchaseorderdetails.budgetYear HAVING 
                               (((erp_purchaseorderdetails.financeGLcodePLSystemID) Is Not Null))) as ppo'),
                function ($join) {
                    $join->on('erp_budjetdetails.companySystemID', '=', 'ppo.companySystemID')
                        ->on('erp_budjetdetails.serviceLineSystemID', '=', 'ppo.serviceLineSystemID')
                        ->on('erp_budjetdetails.Year', '=', 'ppo.budgetYear')
                        ->on('erp_budjetdetails.chartOfAccountID', '=', 'ppo.financeGLcodePLSystemID');
                })
            ->groupBy(['erp_budjetdetails.companySystemID', 'erp_budjetdetails.serviceLineSystemID',
                'erp_budjetdetails.chartOfAccountID', 'erp_budjetdetails.Year'])
            ->first();

        $transferAmount = collect($budgetTransferToData)->sum('adjustmentAmountRpt');

        if (!empty($checkBalance)) {
            if ($checkBalance->balance <= 0) {
                $msg = "You cannot transfer from a negative balance amount or a zero balance amount";
                throw new \Exception($msg, 500);
            }
            if ($transferAmount > abs($checkBalance->balance) && $checkBalance->balance > 0) {
                $balanceShow = abs($checkBalance->balance);
                $msg = "You cannot transfer more than the balance amount, Balance amount is {$balanceShow}";
                throw new \Exception($msg, 500);
            }
        }
    }

    public function validateContingencyBudget($input, $budgetTransferMaster, $budgetTransferToData)
    {
        $validator = \Validator::make($input, [
            'contingencyBudgetID' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            throw new \Exception($validator->messages(), 422);
        }

        $contingencyBudgetID = $input['contingencyBudgetID'];
        $contingencyBudgetPlan = ContingencyBudgetPlan::find( $contingencyBudgetID );

        if( empty($contingencyBudgetPlan) ){
            throw new \Exception("Contingency Budget details not found", 500);
        }

        $utilized = BudgetTransferFormDetail::where('isFromContingency', 1)
            ->where('contingencyBudgetID', $input['contingencyBudgetID'])
            ->sum('adjustmentAmountRpt');


        $balance = $contingencyBudgetPlan->contigencyAmount - $utilized;
        $transferAmount = collect($budgetTransferToData)->sum('adjustmentAmountRpt');
        if ($transferAmount > $balance) {

            $balance = CurrencyValidation::convertToRptCurrencyDecimal($budgetTransferMaster->companySystemID, $balance);

            $msg = "You cannot transfer more than the balance amount, Balance amount is {$balance}";
            throw new \Exception($msg, 500);
        }
    }

    function from_contingency($input, $budgetTransferMaster){
       
        $checkSameEntry = BudgetTransferFormDetail::where('contingencyBudgetID' , $input['contingencyBudgetID'])
            ->where('toTemplateDetailID' , $input['toTemplateDetailID'])
            ->where('toChartOfAccountSystemID' , $input['toChartOfAccountSystemID'])
            ->where('budgetTransferFormAutoID' , $input['budgetTransferFormAutoID'])
            ->count();

        if ($checkSameEntry > 0) {
            throw new \Exception("Selected GL Code is already added. Please check again", 500);
        }


        $checkPendingFromGL = BudgetTransferFormDetail::where(function ($q) use($input){
            $q->where(function ($q2) use($input){
                $q2->where('fromTemplateDetailID', $input['toTemplateDetailID'])
                    ->where('fromChartOfAccountSystemID', $input['toChartOfAccountSystemID']);
            })->orWhere(function ($q2)use($input) {
                $q2->where('toTemplateDetailID', $input['toTemplateDetailID'])
                    ->where('toChartOfAccountSystemID', $input['toChartOfAccountSystemID']);
            });
        })
        ->whereHas('master',function ($q) use ($budgetTransferMaster) {
            $q->where('companySystemID', $budgetTransferMaster->companySystemID)
                ->where('year', $budgetTransferMaster->year)
                ->where('approvedYN', 0);
        })
        ->with(['master'])
        ->first();

        if (!empty($checkPendingFromGL)) {
            throw new \Exception("There is a Budget Transfer (" . $checkPendingFromGL->master->transferVoucherNo . ") 
                        pending for approval for the GL Code you are trying to add. Please check again.", 500);
        }
       
        $budgetTransferFormDetails = $this->budgetTransferFormDetailRepository->create($input);

        return ['status'=> true, 'data'=> $budgetTransferFormDetails];

    }

    public function general_transfer($input, $budgetTransferMaster)
    {
        if ($input['fromTemplateDetailID'] == $input['toTemplateDetailID']
            && $input['fromChartOfAccountSystemID'] == $input['toChartOfAccountSystemID']
            && $input['fromServiceLineSystemID'] == $input['toServiceLineSystemID']
        ) {
            throw new \Exception("You cannot transfer to the same account, Please select a different account", 500);
        }

        $checkSameEntry = BudgetTransferFormDetail::where(function ($q) use($input){
            $q->where(function ($q1)use($input) {
                $q1->where(function ($q2) use($input){
                    $q2->where('fromTemplateDetailID', $input['toTemplateDetailID'])
                        ->where('fromChartOfAccountSystemID', $input['toChartOfAccountSystemID']);
                })->orWhere(function ($q2)use($input) {
                    $q2->where('toTemplateDetailID', $input['toTemplateDetailID'])
                        ->where('toChartOfAccountSystemID', $input['toChartOfAccountSystemID']);
                });
            });
        })
        ->where('budgetTransferFormAutoID' , $input['budgetTransferFormAutoID'])
        ->count();

        if ($checkSameEntry > 0) {
            throw new \Exception("Selected GL Code is already added. Please check again", 500);
        }



        $checkPendingFromGL = BudgetTransferFormDetail::where(function ($q) use($input){
            $q->where(function ($q1)use($input) {
                $q1->where(function ($q2) use($input){
                    $q2->where('fromTemplateDetailID', $input['toTemplateDetailID'])
                        ->where('fromChartOfAccountSystemID', $input['toChartOfAccountSystemID']);
                })->orWhere(function ($q2)use($input) {
                    $q2->where('toTemplateDetailID', $input['toTemplateDetailID'])
                        ->where('toChartOfAccountSystemID', $input['toChartOfAccountSystemID']);
                });
            });
        })
        ->whereHas('master',function ($q) use ($budgetTransferMaster) {
            $q->where('companySystemID', $budgetTransferMaster->companySystemID)
                ->where('year', $budgetTransferMaster->year)
                ->where('approvedYN', 0);
        })
        ->with(['master'])
        ->first();

        if (!empty($checkPendingFromGL)) {
            $msg = "There is a Budget Transfer (" . $checkPendingFromGL->master->transferVoucherNo . ") pending for 
                    approval for the GL Code you are trying to add. Please check again.";

            throw new \Exception($msg, 500);
        }

        $budgetTransferFormDetails = $this->budgetTransferFormDetailRepository->create($input);

        return ['status'=> true, 'data'=> $budgetTransferFormDetails];
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/budgetTransferFormDetails/{id}",
     *      summary="Display the specified BudgetTransferFormDetail",
     *      tags={"BudgetTransferFormDetail"},
     *      description="Get BudgetTransferFormDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BudgetTransferFormDetail",
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
     *                  ref="#/definitions/BudgetTransferFormDetail"
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
        /** @var BudgetTransferFormDetail $budgetTransferFormDetail */
        $budgetTransferFormDetail = $this->budgetTransferFormDetailRepository->findWithoutFail($id);

        if (empty($budgetTransferFormDetail)) {
            return $this->sendError('Budget Transfer Form Detail not found');
        }

        return $this->sendResponse($budgetTransferFormDetail->toArray(), 'Budget Transfer Form Detail retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateBudgetTransferFormDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/budgetTransferFormDetails/{id}",
     *      summary="Update the specified BudgetTransferFormDetail in storage",
     *      tags={"BudgetTransferFormDetail"},
     *      description="Update BudgetTransferFormDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BudgetTransferFormDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BudgetTransferFormDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BudgetTransferFormDetail")
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
     *                  ref="#/definitions/BudgetTransferFormDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateBudgetTransferFormDetailAPIRequest $request)
    {
        $input = $request->all();

        /** @var BudgetTransferFormDetail $budgetTransferFormDetail */
        $budgetTransferFormDetail = $this->budgetTransferFormDetailRepository->findWithoutFail($id);

        if (empty($budgetTransferFormDetail)) {
            return $this->sendError('Budget Transfer Form Detail not found');
        }

        $budgetTransferFormDetail = $this->budgetTransferFormDetailRepository->update($input, $id);

        return $this->sendResponse($budgetTransferFormDetail->toArray(), 'BudgetTransferFormDetail updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/budgetTransferFormDetails/{id}",
     *      summary="Remove the specified BudgetTransferFormDetail from storage",
     *      tags={"BudgetTransferFormDetail"},
     *      description="Delete BudgetTransferFormDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BudgetTransferFormDetail",
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
        /** @var BudgetTransferFormDetail $budgetTransferFormDetail */
        $budgetTransferFormDetail = $this->budgetTransferFormDetailRepository->findWithoutFail($id);

        if (empty($budgetTransferFormDetail)) {
            return $this->sendError('Budget Transfer Form Detail not found');
        }

        $budgetTransferFormDetail->delete();

        return $this->sendResponse($id, 'Budget Transfer Form Detail deleted successfully');
    }

    public function getDetailsByBudgetTransfer(Request $request)
    {
        $input = $request->all();
        $id = $input['budgetTransferFormAutoID'];

        $items = BudgetTransferFormDetail::where('budgetTransferFormAutoID', $id)
            ->with([
                'from_segment', 'to_segment', 'from_template', 'to_template',
                'contingency:ID,contingencyBudgetNo,comments'
            ])
            ->get();

        return $this->sendResponse($items->toArray(), 'Budget Transfer Form Detail retrieved successfully');
    }


    public function checkBudgetAllocation(Request $request){

        $input = $request->all();
        $input = $this->convertArrayToValue($input);
        $validator = \Validator::make($input, [
            'companySystemID' => 'required|numeric',
            'fromTemplateDetailID' => 'required|numeric|min:1',
            'fromServiceLineSystemID' => 'required|numeric|min:1',
            'fromChartOfAccountSystemID' => 'required|numeric|min:1',
            'year' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $fromDataBudgetCheck = Budjetdetails::where('companySystemID', $input['companySystemID'])
            ->where('chartOfAccountID',$input['fromChartOfAccountSystemID'])
            ->where('serviceLineSystemID',$input['fromServiceLineSystemID'])
            ->where('templateDetailID',$input['fromTemplateDetailID'])
            ->where('Year',$input['year'])
            ->whereHas('budget_master', function ($query) use ($input){
                $query->where('templateMasterID', $input['templatesMasterAutoID']);
            })
            ->count();


        if($fromDataBudgetCheck == 0){
            $budgetAmount = 0;
            $total = array();
            $total['totalLocal'] = 0;
            $total['totalRpt'] = 0;
            $total['committedAmount'] = 0;
            $total['actuallConsumptionAmount'] = 0;
            $total['pendingDocumentAmount'] = 0;
            $total['balance'] = 0;


            $companyData = Company::with(['reportingcurrency'])->find($input['companySystemID']);
            return $this->sendResponse(['budgetAmount' => abs($budgetAmount),'total' => $total, 'companyData' => $companyData], 'successfully');
        }

        $checkBalance = Budjetdetails::select(DB::raw("
                                       (SUM(budjetAmtLocal) * -1) as totalLocal,
                                       (SUM(budjetAmtRpt) * -1) as totalRpt,
                                       chartofaccounts.AccountCode,chartofaccounts.AccountDescription,
                                       erp_companyreporttemplatedetails.description as templateDetailDescription,
                                       erp_companyreporttemplatedetails.companyReportTemplateID as templatesMasterAutoID,
                                       erp_budjetdetails.*
                                       ,ifnull(ca.consumed_amount,0) as consumed_amount
                                       ,ifnull(ppo.rptAmt,0) as pending_po_amount,
                                       ((SUM(budjetAmtRpt)*-1) - (ifnull(ca.consumed_amount,0) + ifnull(ppo.rptAmt,0))) AS balance
                                       "))
            ->where('erp_budjetdetails.companySystemID', $input['companySystemID'])
            ->where('erp_budjetdetails.serviceLineSystemID', $input['fromServiceLineSystemID'])
            ->where('erp_budjetdetails.Year', $input['year'])
            ->where('erp_budjetdetails.templateDetailID', $input['fromTemplateDetailID'])
            ->where('erp_companyreporttemplatedetails.companyReportTemplateID', $input['templatesMasterAutoID'])
            ->where('erp_budjetdetails.chartOfAccountID', $input['fromChartOfAccountSystemID'])
            ->leftJoin('chartofaccounts', 'chartOfAccountID', '=', 'chartOfAccountSystemID')
            ->leftJoin('erp_companyreporttemplatedetails', 'templateDetailID', '=', 'detID')
            ->leftJoin(DB::raw('(SELECT erp_budgetconsumeddata.companySystemID, erp_budgetconsumeddata.serviceLineSystemID,
                                                erp_budgetconsumeddata.chartOfAccountID, erp_budgetconsumeddata.Year, 
                                                Sum(erp_budgetconsumeddata.consumedRptAmount) AS consumed_amount FROM
                                                erp_budgetconsumeddata WHERE erp_budgetconsumeddata.consumeYN = -1 
                                                GROUP BY erp_budgetconsumeddata.companySystemID, erp_budgetconsumeddata.serviceLineSystemID, 
                                                erp_budgetconsumeddata.chartOfAccountID, erp_budgetconsumeddata.Year) as ca'),
                function ($join) {
                    $join->on('erp_budjetdetails.companySystemID', '=', 'ca.companySystemID')
                        ->on('erp_budjetdetails.serviceLineSystemID', '=', 'ca.serviceLineSystemID')
                        ->on('erp_budjetdetails.Year', '=', 'ca.Year')
                        ->on('erp_budjetdetails.chartOfAccountID', '=', 'ca.chartOfAccountID');
                })
            ->leftJoin(DB::raw('(SELECT erp_purchaseordermaster.companySystemID, erp_purchaseordermaster.serviceLineSystemID, 
                               erp_purchaseorderdetails.financeGLcodePLSystemID, Sum(GRVcostPerUnitLocalCur * noQty) AS localAmt, 
                               Sum(GRVcostPerUnitComRptCur * noQty) AS rptAmt, erp_purchaseorderdetails.budgetYear FROM 
                               erp_purchaseordermaster INNER JOIN erp_purchaseorderdetails ON erp_purchaseordermaster.purchaseOrderID = erp_purchaseorderdetails.purchaseOrderMasterID WHERE (((erp_purchaseordermaster.approved)=0) 
                               AND ((erp_purchaseordermaster.poCancelledYN)=0))GROUP BY erp_purchaseordermaster.companySystemID, erp_purchaseordermaster.serviceLineSystemID, erp_purchaseorderdetails.financeGLcodePL, erp_purchaseorderdetails.budgetYear HAVING 
                               (((erp_purchaseorderdetails.financeGLcodePLSystemID) Is Not Null))) as ppo'),
                function ($join) {
                    $join->on('erp_budjetdetails.companySystemID', '=', 'ppo.companySystemID')
                        ->on('erp_budjetdetails.serviceLineSystemID', '=', 'ppo.serviceLineSystemID')
                        ->on('erp_budjetdetails.Year', '=', 'ppo.budgetYear')
                        ->on('erp_budjetdetails.chartOfAccountID', '=', 'ppo.financeGLcodePLSystemID');
                })
            ->groupBy(['erp_budjetdetails.companySystemID', 'erp_budjetdetails.serviceLineSystemID',
                'erp_budjetdetails.chartOfAccountID', 'erp_budjetdetails.Year'])
            ->first();

        $budgetAmount = (!empty($checkBalance)) ? $checkBalance->balance : 0;

        // policy check -> Department wise budget check
        $DLBCPolicy = CompanyPolicyMaster::where('companySystemID', $input['companySystemID'])
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
            ->where('erp_budjetdetails.companySystemID', $input['companySystemID'])
            ->where('erp_budjetdetails.serviceLineSystemID', $input['fromServiceLineSystemID'])
            ->where('erp_budjetdetails.Year', $input['year'])
            ->where('erp_budjetdetails.templateDetailID', $input['fromTemplateDetailID'])
            ->where('erp_companyreporttemplatedetails.companyReportTemplateID', $input['templatesMasterAutoID'])
            ->where('erp_budjetdetails.chartOfAccountID', $input['fromChartOfAccountSystemID'])
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
                        ->on('erp_budjetdetails.Year', '=', 'ca.Year')
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
                        ->on('erp_budjetdetails.Year', '=', 'ca.Year')
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


        $companyData = Company::with(['reportingcurrency'])->find($input['companySystemID']);
        return $this->sendResponse(['budgetAmount' => abs($budgetAmount),'total' => $total, 'companyData' => $companyData], 'successfully');
    }
}
