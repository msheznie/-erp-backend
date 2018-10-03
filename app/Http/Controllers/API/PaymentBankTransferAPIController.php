<?php
/**
 * =============================================
 * -- File Name : PaymentBankTransferAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Payment Bank Transfer
 * -- Author : Mohamed Fayas
 * -- Create date : 03 - October 2018
 * -- Description : This file contains the all CRUD for Payment Bank Transfer
 * -- REVISION HISTORY
 * -- Date: 03 - October 2018 By: Fayas Description: Added new functions named as getCheckBeforeCreate()
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePaymentBankTransferAPIRequest;
use App\Http\Requests\API\UpdatePaymentBankTransferAPIRequest;
use App\Models\BankAccount;
use App\Models\Company;
use App\Models\PaymentBankTransfer;
use App\Repositories\PaymentBankTransferRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PaymentBankTransferController
 * @package App\Http\Controllers\API
 */

class PaymentBankTransferAPIController extends AppBaseController
{
    /** @var  PaymentBankTransferRepository */
    private $paymentBankTransferRepository;

    public function __construct(PaymentBankTransferRepository $paymentBankTransferRepo)
    {
        $this->paymentBankTransferRepository = $paymentBankTransferRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/paymentBankTransfers",
     *      summary="Get a listing of the PaymentBankTransfers.",
     *      tags={"PaymentBankTransfer"},
     *      description="Get all PaymentBankTransfers",
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
     *                  @SWG\Items(ref="#/definitions/PaymentBankTransfer")
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
        $this->paymentBankTransferRepository->pushCriteria(new RequestCriteria($request));
        $this->paymentBankTransferRepository->pushCriteria(new LimitOffsetCriteria($request));
        $paymentBankTransfers = $this->paymentBankTransferRepository->all();

        return $this->sendResponse($paymentBankTransfers->toArray(), 'Payment Bank Transfers retrieved successfully');
    }

    /**
     * @param CreatePaymentBankTransferAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/paymentBankTransfers",
     *      summary="Store a newly created PaymentBankTransfer in storage",
     *      tags={"PaymentBankTransfer"},
     *      description="Store PaymentBankTransfer",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PaymentBankTransfer that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PaymentBankTransfer")
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
     *                  ref="#/definitions/PaymentBankTransfer"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePaymentBankTransferAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);

        $employee = \Helper::getEmployeeInfo();
        $input['createdPcID'] = gethostname();
        $input['createdUserID'] = $employee->empID;
        $input['createdUserSystemID'] = $employee->employeeSystemID;

        $validator = \Validator::make($input, [
            'description' => 'required',
            'documentDate' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }


        $input['documentDate'] = new Carbon($input['documentDate']);


        $end = (new Carbon())->endOfMonth();
        if ($input['documentDate'] > $end) {
            return $this->sendError('You cannot select a date greater than the current month last day', 500);
        }

        $input['documentSystemID'] = 64;
        $input['documentID'] = 'PBT';

        $bankAccount = BankAccount::find($input['bankAccountAutoID']);

        if (!empty($bankAccount)) {
            $input['bankMasterID'] = $bankAccount->bankmasterAutoID;
            $input['companySystemID'] = $bankAccount->companySystemID;
        } else {
            return $this->sendError('bank Account not found.!', 500);
        }


        $checkPending = PaymentBankTransfer::where('bankAccountAutoID', $input['bankAccountAutoID'])
                                            ->where('approvedYN', 0)
                                            ->first();


        if (!empty($checkPending)) {
            return $this->sendError("There is a bank transfer (" . $checkPending->bankRecPrimaryCode . ") pending for approval for the bank transfer you are trying to add. Please check again.", 500);
        }

        $maxAsOfDate = PaymentBankTransfer::where('bankAccountAutoID', $input['bankAccountAutoID'])
            ->max('documentDate');

        if ($maxAsOfDate >= $input['documentDate']) {
            return $this->sendError('You cannot create bank transfer, Please select the as of date after ' . (new Carbon($maxAsOfDate))->format('d/m/Y'), 500);
        }

        $company = Company::where('companySystemID', $input['companySystemID'])->first();
        if ($company) {
            $input['companyID'] = $company->CompanyID;
        }

        $lastSerial = PaymentBankTransfer::where('companySystemID', $input['companySystemID'])
            ->orderBy('paymentBankTransferID', 'desc')
            ->first();

        $lastSerialNumber = 1;
        if ($lastSerial) {
            $lastSerialNumber = intval($lastSerial->serialNo) + 1;
        }
        $input['serialNo'] = $lastSerialNumber;


        $code = ($input['companyID'] . '\\' . $input['documentID'] . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
        $input['bankTransferDocumentCode'] = $code;

        $paymentBankTransfers = $this->paymentBankTransferRepository->create($input);

        return $this->sendResponse($paymentBankTransfers->toArray(), 'Payment Bank Transfer saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/paymentBankTransfers/{id}",
     *      summary="Display the specified PaymentBankTransfer",
     *      tags={"PaymentBankTransfer"},
     *      description="Get PaymentBankTransfer",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PaymentBankTransfer",
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
     *                  ref="#/definitions/PaymentBankTransfer"
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
        /** @var PaymentBankTransfer $paymentBankTransfer */
        $paymentBankTransfer = $this->paymentBankTransferRepository->findWithoutFail($id);

        if (empty($paymentBankTransfer)) {
            return $this->sendError('Payment Bank Transfer not found');
        }

        return $this->sendResponse($paymentBankTransfer->toArray(), 'Payment Bank Transfer retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdatePaymentBankTransferAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/paymentBankTransfers/{id}",
     *      summary="Update the specified PaymentBankTransfer in storage",
     *      tags={"PaymentBankTransfer"},
     *      description="Update PaymentBankTransfer",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PaymentBankTransfer",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PaymentBankTransfer that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PaymentBankTransfer")
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
     *                  ref="#/definitions/PaymentBankTransfer"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePaymentBankTransferAPIRequest $request)
    {
        $input = $request->all();

        /** @var PaymentBankTransfer $paymentBankTransfer */
        $paymentBankTransfer = $this->paymentBankTransferRepository->findWithoutFail($id);

        if (empty($paymentBankTransfer)) {
            return $this->sendError('Payment Bank Transfer not found');
        }

        $paymentBankTransfer = $this->paymentBankTransferRepository->update($input, $id);

        return $this->sendResponse($paymentBankTransfer->toArray(), 'PaymentBankTransfer updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/paymentBankTransfers/{id}",
     *      summary="Remove the specified PaymentBankTransfer from storage",
     *      tags={"PaymentBankTransfer"},
     *      description="Delete PaymentBankTransfer",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PaymentBankTransfer",
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
        /** @var PaymentBankTransfer $paymentBankTransfer */
        $paymentBankTransfer = $this->paymentBankTransferRepository->findWithoutFail($id);

        if (empty($paymentBankTransfer)) {
            return $this->sendError('Payment Bank Transfer not found');
        }

        $paymentBankTransfer->delete();

        return $this->sendResponse($id, 'Payment Bank Transfer deleted successfully');
    }

    public function getCheckBeforeCreate(Request $request)
    {
        $input = $request->all();
        $bankAccount = BankAccount::find($input['bankAccountAutoID']);

        if (empty($bankAccount)) {
            return $this->sendError('Bank Account not found');
        }

        $checkPending = PaymentBankTransfer::where('bankAccountAutoID', $input['bankAccountAutoID'])
                                            ->where('companySystemID', $bankAccount->companySystemID)
                                            ->where('approvedYN', 0)
                                            ->first();

        if (!empty($checkPending)) {
            return $this->sendError("There is a bank transfer (" . $checkPending->bankTransferDocumentCode . ") pending for approval for the bank transfer you are trying to add. Please check again.", 500);
        }

        return $this->sendResponse($bankAccount->toArray(), 'Successfully');
    }
}
