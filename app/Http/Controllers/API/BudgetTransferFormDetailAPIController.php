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

use App\helper\CurrencyValidation;
use App\helper\Helper;
use App\Http\Requests\API\CreateBudgetTransferFormDetailAPIRequest;
use App\Http\Requests\API\UpdateBudgetTransferFormDetailAPIRequest;
use App\Models\BudgetTransferFormDetail;
use App\Models\Budjetdetails;
use App\Models\ChartOfAccountsAssigned;
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
        $input = $this->convertArrayToValue($input);
        $validator = \Validator::make($input, [
            'budgetTransferFormAutoID' => 'required',
            'fromServiceLineSystemID' => 'required|numeric|min:1',
            'isFromContingency' => 'required|numeric',
            'toTemplateDetailID' => 'required|numeric|min:1',
            'toServiceLineSystemID' => 'required|numeric|min:1',
            'toChartOfAccountSystemID' => 'required|numeric|min:1',
            'remarks' => 'required',
            'adjustmentAmountRpt' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }
        $budgetTransferMaster = $this->budgetTransferFormRepository->find($input['budgetTransferFormAutoID']);

        if (empty($budgetTransferMaster)) {
            return $this->sendError('Budget Transfer not found');
        }

        $masterValidate = \Validator::make($budgetTransferMaster->toArray(), [
            'year' => 'required',
            'templatesMasterAutoID' => 'required',
        ]);

        if ($masterValidate->fails()) {
            return $this->sendError($masterValidate->messages(), 422);
        }

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

        $input['year'] = $budgetTransferMaster->year;

        $fromDepartment = SegmentMaster::where('companySystemID', $budgetTransferMaster->companySystemID)
            ->where('serviceLineSystemID', $input['fromServiceLineSystemID'])
            ->first();

        if (empty($fromDepartment)) {
            throw new \Exception("From Department not found", 500);
        }

        if ($fromDepartment->isActive == 0) {
            throw new \Exception("Please select an active from department", 500);
        }

        $input['fromServiceLineCode'] = $fromDepartment->ServiceLineCode;


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

        $currency = \Helper::currencyConversion($budgetTransferMaster->companySystemID, 2, 2, $input['adjustmentAmountRpt']);
        $input['adjustmentAmountLocal'] = $currency['localAmount'];

        try {
            if( $input['isFromContingency'] == 1){
                $this->from_contingency($input, $budgetTransferMaster);
            }
            else{
                $this->general_transfer($input, $budgetTransferMaster);
            }

            return $this->sendResponse([], 'Budget Transfer Form Detail saved successfully');
        }
        catch (\Exception $ex){
            $error_code = ($ex->getCode() == 422)? 422: 500;
            $msg = ($error_code == 422)? json_decode( $ex->getMessage() ): $ex->getMessage();
            $more = Helper::exception_to_error($ex);

            return $this->sendError($msg, $error_code, $more);
        }
    }

    function from_contingency($input, $budgetTransferMaster){
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


        $utilized = BudgetTransferFormDetail::where('isFromContingency', 1)
            ->where('contingencyBudgetID', $input['contingencyBudgetID'])
            ->sum('adjustmentAmountRpt');


        $balance = $contingencyBudgetPlan->contigencyAmount - $utilized;
        if ($input['adjustmentAmountRpt'] > $balance) {

            $balance = CurrencyValidation::convertToLocalCurrencyDecimal($budgetTransferMaster->companySystemID, $balance);

            $msg = "You cannot transfer more than the balance amount, Balance amount is {$balance}";
            throw new \Exception($msg, 500);
        }

        $budgetTransferFormDetails = $this->budgetTransferFormDetailRepository->create($input);

        return ['status'=> true, 'data'=> $budgetTransferFormDetails];

    }

    public function general_transfer($input, $budgetTransferMaster)
    {
        $validator = \Validator::make($input, [
            'fromTemplateDetailID' => 'required|numeric|min:1',
            'fromChartOfAccountSystemID' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            throw new \Exception($validator->messages(), 422);
        }

        if ($input['fromTemplateDetailID'] == $input['toTemplateDetailID']
            && $input['fromChartOfAccountSystemID'] == $input['toChartOfAccountSystemID']
            && $input['fromServiceLineSystemID'] == $input['toServiceLineSystemID']
        ) {
            throw new \Exception("You cannot transfer to the same account, Please select a different account", 500);
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
            })->orWhere(function ($q1)use($input) {
                $q1->where(function ($q2) use($input){
                    $q2->where('fromTemplateDetailID', $input['toTemplateDetailID'])
                        ->where('fromChartOfAccountSystemID', $input['toChartOfAccountSystemID']);
                })->orWhere(function ($q2)use($input) {
                    $q2->where('toTemplateDetailID', $input['toTemplateDetailID'])
                        ->where('toChartOfAccountSystemID', $input['toChartOfAccountSystemID']);
                });
            });

            //( (((A = C) && (B = D )) || ((A1 = C) && (B1 = C))) || ( ((A = C1) && (B = D1 )) || ((A1 = C1) && (B1 = C1)) )   ) && H = I
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
            })->orWhere(function ($q1)use($input) {
                $q1->where(function ($q2) use($input){
                    $q2->where('fromTemplateDetailID', $input['toTemplateDetailID'])
                        ->where('fromChartOfAccountSystemID', $input['toChartOfAccountSystemID']);
                })->orWhere(function ($q2)use($input) {
                    $q2->where('toTemplateDetailID', $input['toTemplateDetailID'])
                        ->where('toChartOfAccountSystemID', $input['toChartOfAccountSystemID']);
                });
            });

            //( (((A = C) && (B = D )) || ((A1 = C) && (B1 = C))) || ( ((A = C1) && (B = D1 )) || ((A1 = C1) && (B1 = C1)) )   ) && H = I
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

        if (!empty($checkBalance)) {
            if ($input['adjustmentAmountRpt'] > ($checkBalance->balance)) {
                $msg = "You cannot transfer more than the balance amount, Balance amount is {$checkBalance->balance}";
                throw new \Exception($msg, 500);
            }
        }



        $fromChartOfAccount = ChartOfAccountsAssigned::where('companySystemID', $budgetTransferMaster->companySystemID)
            ->where('chartOfAccountSystemID', $input['fromChartOfAccountSystemID'])
            ->first();

        if (empty($fromChartOfAccount)) {
            throw new \Exception("From Account Code not found", 500);
        }

        $input['FromGLCode'] = $fromChartOfAccount->AccountCode;
        $input['FromGLCodeDescription'] = $fromChartOfAccount->AccountDescription;


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
            ->count();


        if($fromDataBudgetCheck == 0){
            return $this->sendError('Selected account code is not available in the budget. Please allocate and try again.', 500);
        }

        return $this->sendResponse($fromDataBudgetCheck, 'successfully');
    }
}
