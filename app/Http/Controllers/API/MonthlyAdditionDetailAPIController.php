<?php
/**
 * =============================================
 * -- File Name : MonthlyAdditionDetailAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Monthly Addition Detail
 * -- Author : Mohamed Fayas
 * -- Create date : 07 - November 2018
 * -- Description : This file contains the all CRUD for Monthly Addition Detail
 * -- REVISION HISTORY
 * -- Date: 08-November 2018 By: Fayas Description: Added new functions named as getItemsByMonthlyAddition(),checkPullFromExpenseClaim(),
 *                                                              getECForMonthlyAddition(),getECDetailsForMonthlyAddition(),
 *        addMonthlyAdditionDetails(),deleteAllMonthlyAdditionDetails()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateMonthlyAdditionDetailAPIRequest;
use App\Http\Requests\API\UpdateMonthlyAdditionDetailAPIRequest;
use App\Models\Employee;
use App\Models\ExpenseClaim;
use App\Models\ExpenseClaimDetails;
use App\Models\ExpenseClaimDetailsMaster;
use App\Models\ExpenseClaimMaster;
use App\Models\HRMSChartOfAccounts;
use App\Models\MonthlyAdditionDetail;
use App\Repositories\ExpenseClaimRepository;
use App\Repositories\MonthlyAdditionDetailRepository;
use App\Repositories\MonthlyAdditionsMasterRepository;
use App\Repositories\PaySupplierInvoiceMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\F;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\Models\PaySupplierInvoiceMaster;
use App\Models\SMECompany;
/**
 * Class MonthlyAdditionDetailController
 * @package App\Http\Controllers\API
 */
class MonthlyAdditionDetailAPIController extends AppBaseController
{
    /** @var  MonthlyAdditionDetailRepository */
    private $monthlyAdditionDetailRepository;
    private $monthlyAdditionsMasterRepository;
    private $expenseClaimRepository;
    private $paySupplierInvoiceMasterRepository;

    public function __construct(MonthlyAdditionDetailRepository $monthlyAdditionDetailRepo,
                                MonthlyAdditionsMasterRepository $monthlyAdditionsMasterRepo,
                                ExpenseClaimRepository $expenseClaimRepo,
                                PaySupplierInvoiceMasterRepository $paySupplierInvoiceMasterRepo)
    {
        $this->monthlyAdditionDetailRepository = $monthlyAdditionDetailRepo;
        $this->monthlyAdditionsMasterRepository = $monthlyAdditionsMasterRepo;
        $this->expenseClaimRepository = $expenseClaimRepo;
        $this->paySupplierInvoiceMasterRepository = $paySupplierInvoiceMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/monthlyAdditionDetails",
     *      summary="Get a listing of the MonthlyAdditionDetails.",
     *      tags={"MonthlyAdditionDetail"},
     *      description="Get all MonthlyAdditionDetails",
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
     *                  @SWG\Items(ref="#/definitions/MonthlyAdditionDetail")
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
        $this->monthlyAdditionDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->monthlyAdditionDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $monthlyAdditionDetails = $this->monthlyAdditionDetailRepository->all();

        return $this->sendResponse($monthlyAdditionDetails->toArray(), 'Monthly Addition Details retrieved successfully');
    }

    /**
     * @param CreateMonthlyAdditionDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/monthlyAdditionDetails",
     *      summary="Store a newly created MonthlyAdditionDetail in storage",
     *      tags={"MonthlyAdditionDetail"},
     *      description="Store MonthlyAdditionDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="MonthlyAdditionDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/MonthlyAdditionDetail")
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
     *                  ref="#/definitions/MonthlyAdditionDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateMonthlyAdditionDetailAPIRequest $request)
    {
        $input = $request->all();

        $monthlyAdditionDetails = $this->monthlyAdditionDetailRepository->create($input);

        return $this->sendResponse($monthlyAdditionDetails->toArray(), 'Monthly Addition Detail saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/monthlyAdditionDetails/{id}",
     *      summary="Display the specified MonthlyAdditionDetail",
     *      tags={"MonthlyAdditionDetail"},
     *      description="Get MonthlyAdditionDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MonthlyAdditionDetail",
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
     *                  ref="#/definitions/MonthlyAdditionDetail"
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
        /** @var MonthlyAdditionDetail $monthlyAdditionDetail */
        $monthlyAdditionDetail = $this->monthlyAdditionDetailRepository->findWithoutFail($id);

        if (empty($monthlyAdditionDetail)) {
            return $this->sendError('Monthly Addition Detail not found');
        }

        return $this->sendResponse($monthlyAdditionDetail->toArray(), 'Monthly Addition Detail retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateMonthlyAdditionDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/monthlyAdditionDetails/{id}",
     *      summary="Update the specified MonthlyAdditionDetail in storage",
     *      tags={"MonthlyAdditionDetail"},
     *      description="Update MonthlyAdditionDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MonthlyAdditionDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="MonthlyAdditionDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/MonthlyAdditionDetail")
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
     *                  ref="#/definitions/MonthlyAdditionDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateMonthlyAdditionDetailAPIRequest $request)
    {
        $input = $request->all();

        /** @var MonthlyAdditionDetail $monthlyAdditionDetail */
        $monthlyAdditionDetail = $this->monthlyAdditionDetailRepository->findWithoutFail($id);

        if (empty($monthlyAdditionDetail)) {
            return $this->sendError('Monthly Addition Detail not found');
        }

        $monthlyAdditionDetail = $this->monthlyAdditionDetailRepository->update($input, $id);

        return $this->sendResponse($monthlyAdditionDetail->toArray(), 'MonthlyAdditionDetail updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/monthlyAdditionDetails/{id}",
     *      summary="Remove the specified MonthlyAdditionDetail from storage",
     *      tags={"MonthlyAdditionDetail"},
     *      description="Delete MonthlyAdditionDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MonthlyAdditionDetail",
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
        /** @var MonthlyAdditionDetail $monthlyAdditionDetail */
        $monthlyAdditionDetail = $this->monthlyAdditionDetailRepository->findWithoutFail($id);

        if (empty($monthlyAdditionDetail)) {
            return $this->sendError('Monthly Addition Detail not found');
        }

        $monthlyAdditionDetail->delete();

        return $this->sendResponse($id, 'Monthly Addition Detail deleted successfully');
    }

    public function getItemsByMonthlyAddition(Request $request)
    {
        $input = $request->all();
        $rId = $input['monthlyAdditionsMasterID'];

        $items = MonthlyAdditionDetail::where('monthlyAdditionsMasterID', $rId)
            ->with(['employee', 'department', 'currency_ma', 'expense_claim', 'chart_of_account'])
            ->get();

        return $this->sendResponse($items->toArray(), 'Monthly Addition Details retrieved successfully');
    }

    public function checkPullFromExpenseClaim(Request $request)
    {
        $input = $request->all();
        $id = $input['id'];

        $monthlyAddition = $this->monthlyAdditionsMasterRepository->findWithoutFail($id);

        if (empty($monthlyAddition)) {
            return $this->sendError('Monthly Addition not found');
        }

        if ($monthlyAddition->confirmedYN == 1) {
            return $this->sendError('You cannot add items as the document is already confirmed.', 500);
        }

        $validator = \Validator::make($monthlyAddition->toArray(), [
            'companySystemID' => 'required',
            'currency' => 'required|numeric|min:1',
            'empType' => 'required|numeric|min:1',
            'processPeriod' => 'required|numeric|min:1'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        return $this->sendResponse($monthlyAddition->toArray(), 'Monthly Addition retrieved successfully');
    }

    public function getECForMonthlyAddition(Request $request)
    {
        $input = $request->all();
        $id = $input['id'];
        $isFromPV = isset($input['isFromPV'])?$input['isFromPV']:0;

        if(array_key_exists('type',$input)) {
            $companySystemID = 0;
            $claimedUserID=0;
            if($input['type'] == 1 && $isFromPV == 0) {
                $monthlyAddition = $this->monthlyAdditionsMasterRepository->findWithoutFail($id);

                if (empty($monthlyAddition)) {
                    return $this->sendError('Monthly Addition not found');
                }

                if ($monthlyAddition->confirmedYN == 1) {
                    return $this->sendError('You cannot add items as the document is already confirmed.', 500);
                }

                $validator = \Validator::make($monthlyAddition->toArray(), [
                    'companySystemID' => 'required',
                    'currency' => 'required|numeric|min:1',
                    'empType' => 'required|numeric|min:1',
                    'processPeriod' => 'required|numeric|min:1'
                ]);

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }

                $companySystemID =  $monthlyAddition->companySystemID;
            }else if($input['type'] == 2 || ($input['type'] == 1 && $isFromPV == 1)){
                $paySupplierInvoice = $this->paySupplierInvoiceMasterRepository->find($id);

                if (empty($paySupplierInvoice)) {
                    return $this->sendError('Pay Supplier Invoice not found');
                }

                if ($paySupplierInvoice->confirmedYN == 1) {
                    return $this->sendError('You cannot add items as the document is already confirmed.', 500);
                }

                $companySystemID =  $paySupplierInvoice->companySystemID;
                $claimedUserID =  $paySupplierInvoice->directPaymentPayeeEmpID;
            }

            $expenseClaims = ExpenseClaimMaster::where('companyID', $companySystemID)
                ->where('approvedYN', 1)
                ->where('addedToSalary', 0)
                ->where('addedForPayment', 0);
                /*->whereHas('details', function ($q) use ($monthlyAddition) {
                    $q->where('currencyID', $monthlyAddition->currency);
                })*/
                if($isFromPV && $input['type']==1){
                    $expenseClaims = $expenseClaims->where('claimedByEmpID',$claimedUserID);
                }

            $expenseClaims = $expenseClaims->orderBy('expenseClaimDate', 'desc')
                ->get();

            return $this->sendResponse($expenseClaims, 'Monthly Addition retrieved successfully');
        }else{
            return $this->sendError('Error', 500);
        }
    }

    public function getECDetailsForMonthlyAddition(Request $request)
    {
        $input = $request->all();
        $id = $input['id'];
        $voucher_id = $input['voucher_id'];
        $expenseClaim = ExpenseClaimMaster::find($id);

        $voucher = PaySupplierInvoiceMaster::select('PayMasterAutoId','supplierTransCurrencyID')->find($voucher_id);

        if (empty($expenseClaim)) {
            return $this->sendError('Expense Claim not found');
        }

        $data = SMECompany::where('company_id', $expenseClaim->companyID)->select('company_default_decimal')->first();

        $expenseClaimDetails = ExpenseClaimDetailsMaster::where('companyID', $expenseClaim->companyID)
            ->where('expenseClaimMasterAutoID', $id)
            //->where('currencyID', $monthlyAddition->currency)
            ->with(['currency','local_currency','category'])
            ->get();

            foreach($expenseClaimDetails as &$det)
            {
                $currencyConversion = \Helper::currencyConversion($expenseClaim->companyID, $det->transactionCurrencyID, $voucher->supplierTransCurrencyID, $det->transactionAmount);

                $det['expence_claim_amount'] = round($currencyConversion['localAmount'],$data->company_default_decimal);
            }

        return $this->sendResponse($expenseClaimDetails, 'Expense Claim Details retrieved successfully');
    }

    public function addMonthlyAdditionDetails(Request $request)
    {
        $input = $request->all();
        $id = $input['expenseClaimId'];
        $monthlyAdditionId = $input['monthlyAdditionId'];

        $expenseClaim = $this->expenseClaimRepository->find($id);

        if (empty($expenseClaim)) {
            return $this->sendError('Expense Claim not found');
        }

        $monthlyAddition = $this->monthlyAdditionsMasterRepository->findWithoutFail($monthlyAdditionId);

        if (empty($monthlyAddition)) {
            return $this->sendError('Monthly Addition not found');
        }

        $expenseClaimDetails = ExpenseClaimDetails::where('companySystemID', $expenseClaim->companySystemID)
            ->where('expenseClaimMasterAutoID', $id)
            //->where('currencyID', $monthlyAddition->currency)
            ->with(['currency'])
            ->get();

        foreach ($expenseClaimDetails as $detail) {

            $emp = Employee::with(['details'])->find($expenseClaim->clamiedByNameSystemID);

            $empID = '';
            $empDepartment = 0;

            if(!empty($emp)){
                $empID = $emp->empID;
                if($emp->details){
                    $empDepartment = $emp->details->departmentID;
                }
            }

            $chartAccountId = 0;

            $hrms_account = HRMSChartOfAccounts::where('AccountCode',$detail['glCode'])->first();

            if(!empty($hrms_account)){
                $chartAccountId = $hrms_account->charofAccAutoID;
            }

            $temData = array('monthlyAdditionsMasterID' => $monthlyAddition->monthlyAdditionsMasterID,
                'expenseClaimMasterAutoID' => $expenseClaim->expenseClaimMasterAutoID,
                'empSystemID' => $expenseClaim->clamiedByNameSystemID,
                'empID' => $empID,
                'empdepartment' => $empDepartment,
                'description' => $detail['description'],
                'declareCurrency' => $detail['localCurrency'],
                'declareAmount' => $detail['localAmount'],
                'amountMA' => $detail['localAmount'],
                'currencyMAID' => $detail['localCurrency'],
                'glCode' => $chartAccountId, //$detail['chartOfAccountSystemID'],
                'localCurrencyID' => $detail['localCurrency'],
                'localCurrencyER' => $detail['localCurrencyER'],
                'localAmount' => $detail['localAmount'],
                'rptCurrencyID' => $detail['comRptCurrency'],
                'rptCurrencyER' => $detail['comRptCurrencyER'],
                'rptAmount' => $detail['comRptAmount'],
                'IsSSO' => 0,
                'IsTax' => 0,
                'createdpc' => gethostname());
            $this->monthlyAdditionDetailRepository->create($temData);

        }

        if (count($expenseClaimDetails) > 0) {
            $this->expenseClaimRepository->update(['addedForPayment' => -1, 'addedToSalary' => -1], $id);
        }

        return $this->sendResponse($expenseClaimDetails, 'Monthly Addition Details added successfully');
    }

    public function deleteAllMonthlyAdditionDetails(Request $request)
    {
        $input = $request->all();
        $id = $input['id'];

        $monthlyAddition = $this->monthlyAdditionsMasterRepository->findWithoutFail($id);

        if (empty($monthlyAddition)) {
            return $this->sendError('Monthly Addition not found');
        }

        if ($monthlyAddition->confirmedYN == 1) {
            return $this->sendError('This document already confirmed you cannot delete items.', 500);
        }

        $monthlyAdditionDetails = $this->monthlyAdditionDetailRepository->findWhere(['monthlyAdditionsMasterID' => $id]);

        foreach ($monthlyAdditionDetails as $detail) {
            $monthlyAdditionDetail = $this->monthlyAdditionDetailRepository->findWithoutFail($detail['monthlyAdditionDetailID']);

            if (!empty($monthlyAdditionDetail)) {
                $expenseClaim = $this->expenseClaimRepository->find($detail['expenseClaimMasterAutoID']);
                if (!empty($expenseClaim)) {
                    $this->expenseClaimRepository->update(['addedForPayment' => 0, 'addedToSalary' => 0], $detail['expenseClaimMasterAutoID']);
                }
                $monthlyAdditionDetail->delete();
            }
        }

        return $this->sendResponse($monthlyAdditionDetails, 'Monthly Addition Details deleted successfully');
    }

}
