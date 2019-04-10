<?php
/**
 * =============================================
 * -- File Name : ChartOfAccountsAssignedAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Chart Of Account
 * -- Author : Mohamed Shahmy
 * -- Create date : 13 - September 2018
 * -- Description : Reciept Voucher - Direct voucher CRUD
 * -- REVISION HISTORY
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDirectReceiptDetailAPIRequest;
use App\Http\Requests\API\UpdateDirectReceiptDetailAPIRequest;
use App\Models\DirectReceiptDetail;
use App\Models\CustomerReceivePayment;
use App\Models\CustomerReceivePaymentDetail;
use App\Models\BankAccount;
use App\Models\Contract;
use App\Models\SegmentMaster;
use App\Models\Company;
use App\Models\chartOfAccount;
use App\Repositories\DirectReceiptDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\DB;
use Response;

/**
 * Class DirectReceiptDetailController
 * @package App\Http\Controllers\API
 */
class DirectReceiptDetailAPIController extends AppBaseController
{
    /** @var  DirectReceiptDetailRepository */
    private $directReceiptDetailRepository;

    public function __construct(DirectReceiptDetailRepository $directReceiptDetailRepo)
    {
        $this->directReceiptDetailRepository = $directReceiptDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/directReceiptDetails",
     *      summary="Get a listing of the DirectReceiptDetails.",
     *      tags={"DirectReceiptDetail"},
     *      description="Get all DirectReceiptDetails",
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
     *                  @SWG\Items(ref="#/definitions/DirectReceiptDetail")
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
        $this->directReceiptDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->directReceiptDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $directReceiptDetails = $this->directReceiptDetailRepository->all();

        return $this->sendResponse($directReceiptDetails->toArray(), 'Direct Receipt Details retrieved successfully');
    }

    /**
     * @param CreateDirectReceiptDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/directReceiptDetails",
     *      summary="Store a newly created DirectReceiptDetail in storage",
     *      tags={"DirectReceiptDetail"},
     *      description="Store DirectReceiptDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DirectReceiptDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DirectReceiptDetail")
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
     *                  ref="#/definitions/DirectReceiptDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateDirectReceiptDetailAPIRequest $request)
    {
        $input = $request->all();

        $directReceiptDetails = $this->directReceiptDetailRepository->create($input);

        return $this->sendResponse($directReceiptDetails->toArray(), 'Direct Receipt Detail saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/directReceiptDetails/{id}",
     *      summary="Display the specified DirectReceiptDetail",
     *      tags={"DirectReceiptDetail"},
     *      description="Get DirectReceiptDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DirectReceiptDetail",
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
     *                  ref="#/definitions/DirectReceiptDetail"
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
        /** @var DirectReceiptDetail $directReceiptDetail */
        $directReceiptDetail = $this->directReceiptDetailRepository->findWithoutFail($id);

        if (empty($directReceiptDetail)) {
            return $this->sendError('Direct Receipt Detail not found');
        }

        return $this->sendResponse($directReceiptDetail->toArray(), 'Direct Receipt Detail retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateDirectReceiptDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/directReceiptDetails/{id}",
     *      summary="Update the specified DirectReceiptDetail in storage",
     *      tags={"DirectReceiptDetail"},
     *      description="Update DirectReceiptDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DirectReceiptDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DirectReceiptDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DirectReceiptDetail")
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
     *                  ref="#/definitions/DirectReceiptDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateDirectReceiptDetailAPIRequest $request)
    {
        $input = $request->all();

        /** @var DirectReceiptDetail $directReceiptDetail */
        $directReceiptDetail = $this->directReceiptDetailRepository->findWithoutFail($id);

        if (empty($directReceiptDetail)) {
            return $this->sendError('Direct Receipt Detail not found');
        }

        $directReceiptDetail = $this->directReceiptDetailRepository->update($input, $id);

        return $this->sendResponse($directReceiptDetail->toArray(), 'DirectReceiptDetail updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/directReceiptDetails/{id}",
     *      summary="Remove the specified DirectReceiptDetail from storage",
     *      tags={"DirectReceiptDetail"},
     *      description="Delete DirectReceiptDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DirectReceiptDetail",
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
        /** @var DirectReceiptDetail $directReceiptDetail */
        $directReceiptDetail = $this->directReceiptDetailRepository->findWithoutFail($id);

        if (empty($directReceiptDetail)) {
            return $this->sendError('Direct Receipt Detail not found');
        }
        $masterID = $directReceiptDetail->directReceiptAutoID;

        $directReceiptDetail->delete();
        $details = DirectReceiptDetail::select(DB::raw("IFNULL(SUM(DRAmount),0) as receivedAmount"), DB::raw("IFNULL(SUM(localAmount),0) as localAmount"), DB::raw("IFNULL(SUM(DRAmount),0) as bankAmount"), DB::raw("IFNULL(SUM(comRptAmount),0) as companyRptAmount"))->where('directReceiptAutoID', $id)->first()->toArray();

        CustomerReceivePayment::where('custReceivePaymentAutoID', $masterID)->update($details);


        return $this->sendResponse($id, 'Direct Receipt Detail deleted successfully');
    }

    public function directRecieptDetailsRecords(Request $request)
    {
        $input = $request->all();
        $id = $input['id'];
        $detail['detail'] = DirectReceiptDetail::where('directReceiptAutoID', $id)->get();

        $detail['custreceiptVocuherDetail'] = CustomerReceivePaymentDetail::where('custReceivePaymentAutoID', $id)
            ->where('matchingDocID', 0)
            ->get();

        return $this->sendResponse($detail, 'Direct Receipt Detail deleted successfully');
    }

    public function directReceiptContractDropDown(request $request)
    {
        $input = $request->all();
        $detailID = $input['detailID'];
        $detail = DirectReceiptDetail::where('directReceiptDetailsID', $detailID)->first();
        $master = CustomerReceivePayment::where('custReceivePaymentAutoID', $detail->directReceiptAutoID)->first();

        if($master->customerID != '' || $master->customerID != 0){
            $qry = "SELECT contractUID, ContractNumber FROM contractmaster WHERE companySystemID = $master->companySystemID AND clientID = $master->customerID;";
        }else{
            $qry = "SELECT contractUID, ContractNumber FROM contractmaster WHERE companySystemID = $master->companySystemID";
        }

        $contract = DB::select($qry);

        return $this->sendResponse($contract, 'Contract deleted successfully');
    }

    public function customerDirectVoucherDetails(request $request)
    {
        $input = $request->all();


        $input = $request->all();
        $messages = [
            'companySystemID.required' => 'Company is required.',
            'directReceiptAutoID.required' => 'ID is required.',
            'glCode.required' => 'GL Account is required.'
        ];

        $validator = \Validator::make($request->all(), [
            'companySystemID' => 'required|numeric|min:1',
            'directReceiptAutoID' => 'required|numeric|min:1',
            'glCode' => 'required|numeric|min:1'
        ], $messages);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $companySystemID = $input['companySystemID'];
        $directReceiptAutoID = $input['directReceiptAutoID'];
        $glCode = $input['glCode'];;


        /*get master*/
        $master = CustomerReceivePayment::where('custReceivePaymentAutoID', $directReceiptAutoID)->first();
        if ($master->custChequeDate == '') {
            return $this->sendError('Cheque date field is required.', 500);
        }
        $bankGL = BankAccount::select('chartOfAccountSystemID')->where('bankAccountAutoID', $master->bankAccount)->first();
        $company = Company::where('companySystemID', $companySystemID)->first();


        $chartOfAccount = chartOfAccount::select('AccountCode', 'AccountDescription', 'catogaryBLorPL', 'chartOfAccountSystemID', 'controlAccounts')->where('chartOfAccountSystemID', $glCode)->first();

        if ($bankGL->chartOfAccountSystemID == $chartOfAccount->chartOfAccountSystemID) {
            return $this->sendError('Cannot add. You are trying to select the same account.', 500);
        }


        $inputData['directReceiptAutoID'] = $directReceiptAutoID;
        $inputData['companyID'] = $company->CompanyID;
        $inputData['companySystemID'] = $companySystemID;

        $inputData['chartOfAccountSystemID'] = $chartOfAccount->chartOfAccountSystemID;
        $inputData['glCode'] = $chartOfAccount->AccountCode;
        $inputData['glCodeDes'] = $chartOfAccount->AccountDescription;

        $inputData['comments'] = $master->narration;
        $inputData['DRAmountCurrency'] = $master->custTransactionCurrencyID;
        $inputData['DDRAmountCurrencyER'] = $master->custTransactionCurrencyER;
        $inputData['DRAmount'] = 0;
        $inputData['localCurrency'] = $master->localCurrencyID;
        $inputData['localCurrencyER'] = $master->localCurrencyER;
        $inputData['localAmount'] = 0;
        $inputData['comRptCurrency'] = $master->companyRptCurrencyID;
        $inputData['comRptCurrencyER'] = $master->companyRptCurrencyER;
        $inputData['comRptAmount'] = 0;


        DB::beginTransaction();

        try {

            DirectReceiptDetail::create($inputData);

            $details = DirectReceiptDetail::select(DB::raw("SUM(DRAmount) as receivedAmount"), DB::raw("SUM(localAmount) as localAmount"), DB::raw("SUM(DRAmount) as bankAmount"), DB::raw("SUM(comRptAmount) as companyRptAmount"))->where('directReceiptAutoID', $directReceiptAutoID)->first()->toArray();

            CustomerReceivePayment::where('custReceivePaymentAutoID', $directReceiptAutoID)->update($details);


            DB::commit();
            return $this->sendResponse('s', 'successfully created');
        } catch (\Exception $exception) {
            DB::rollback();
            return $this->sendError($exception->getMessage());
        }

    }

    public function updateDirectReceiptVoucher(request $request)
    {

        $input = $request->all();
        $input = $this->convertArrayToValue($input);
        $id = $input['directReceiptDetailsID'];
        array_except($input, 'directReceiptDetailsID');

        $detail = DirectReceiptDetail::where('directReceiptDetailsID', $id)->first();


        if (empty($detail)) {
            return $this->sendError('Receipt voucher detail not found', 500);
        }
        $master = CustomerReceivePayment::where('custReceivePaymentAutoID', $detail->directReceiptAutoID)->first();


        if ($input['contractUID'] != $detail->contractUID) {
            $input['contractID'] = NULL;
            $contract = Contract::select('ContractNumber', 'isRequiredStamp', 'paymentInDaysForJob')->where('CompanyID', $detail->companyID)->where('contractUID', $input['contractUID'])->first();
            $input['contractID'] = $contract->ContractNumber;


        }

        if ($input['serviceLineSystemID'] != $detail->serviceLineSystemID) {

            $serviceLine = SegmentMaster::select('serviceLineSystemID', 'ServiceLineCode')->where('serviceLineSystemID', $input['serviceLineSystemID'])->first();
            $input['serviceLineSystemID'] = $serviceLine->serviceLineSystemID;
            $input['serviceLineCode'] = $serviceLine->ServiceLineCode;
            $input['contractID'] = NULL;
            $input['contractUID'] = NULL;
        }

        if($input['serviceLineSystemID'] == 0){
            $input['serviceLineSystemID'] = null;
            $input['serviceLineCode'] = null;
        }

        if ($input['DRAmount'] != $detail->DRAmount) {
            $myCurr = $master->custTransactionCurrencyID;               /*currencyID*/
            $decimal = \Helper::getCurrencyDecimalPlace($myCurr);

            $input['DRAmountCurrency'] = $master->custTransactionCurrencyID;
            $input['DDRAmountCurrencyER'] = $master->custTransactionCurrencyER;
            $totalAmount = $input['DRAmount'];
            $input['DRAmount'] = round($input['DRAmount'], $decimal);
            /**/
            $currency = \Helper::convertAmountToLocalRpt($master->documentSystemID, $detail->directReceiptAutoID, $totalAmount);
            $input["comRptAmount"] = \Helper::roundValue($currency['reportingAmount']);
            $input["localAmount"] = \Helper::roundValue($currency['localAmount']);

        }

        DB::beginTransaction();

        try {

            $x = DirectReceiptDetail::where('directReceiptDetailsID', $id)->update($input);
            $details = DirectReceiptDetail::select(DB::raw("IFNULL(SUM(DRAmount),0) as receivedAmount"), DB::raw("IFNULL(SUM(localAmount),0) as localAmount"), DB::raw("IFNULL(SUM(DRAmount),0) as bankAmount"), DB::raw("IFNULL(SUM(comRptAmount),0) as companyRptAmount"))->where('directReceiptAutoID', $detail->directReceiptAutoID)->first()->toArray();

            CustomerReceivePayment::where('custReceivePaymentAutoID', $detail->directReceiptAutoID)->update($details);


            DB::commit();
            return $this->sendResponse('s', 'successfully updated');
        } catch (\Exception $exception) {
            DB::rollback();
            return $this->sendError($exception);
        }


    }
}
