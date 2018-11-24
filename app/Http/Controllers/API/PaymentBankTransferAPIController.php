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
 * -- Date: 03 - October 2018 By: Fayas Description: Added new functions named as getCheckBeforeCreate(),getAllBankTransferByBankAccount(),
 *    getBankTransferApprovalByUser,getBankTransferApprovedByUser
 * -- Date: 04 - October 2018 By: Fayas Description: Added new functions named as exportPaymentBankTransfer()
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePaymentBankTransferAPIRequest;
use App\Http\Requests\API\UpdatePaymentBankTransferAPIRequest;
use App\Models\BankAccount;
use App\Models\BankLedger;
use App\Models\Company;
use App\Models\PaymentBankTransfer;
use App\Repositories\BankLedgerRepository;
use App\Repositories\PaymentBankTransferRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Maatwebsite\Excel\Facades\Excel;
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
    private $bankLedgerRepository;

    public function __construct(PaymentBankTransferRepository $paymentBankTransferRepo, BankLedgerRepository $bankLedgerRepo)
    {
        $this->paymentBankTransferRepository = $paymentBankTransferRepo;
        $this->bankLedgerRepository = $bankLedgerRepo;
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
            'narration' => 'required',
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
            return $this->sendError("There is a bank transfer (" . $checkPending->bankTransferDocumentCode . ") pending for approval for the bank transfer you are trying to add. Please check again.", 500);
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
            $lastSerialNumber = intval($lastSerial->serialNumber) + 1;
        }
        $input['serialNumber'] = $lastSerialNumber;


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
        $paymentBankTransfer = $this->paymentBankTransferRepository->with(['bank_account.currency', 'confirmed_by'])->findWithoutFail($id);

        if (empty($paymentBankTransfer)) {
            return $this->sendError('Payment Bank Transfer not found');
        }

        if (!empty($paymentBankTransfer)) {
            $confirmed = $paymentBankTransfer->confirmedYN;
        }

        $totalPaymentAmount = BankLedger::where('companySystemID', $paymentBankTransfer->companySystemID)
            ->where('payAmountBank', '>', 0)
            ->where("bankAccountID", $paymentBankTransfer->bankAccountAutoID)
            ->where("trsClearedYN", -1)
            ->where("bankClearedYN", 0)
            ->where(function ($q) use ($paymentBankTransfer, $confirmed) {
                $q->where(function ($q1) use ($paymentBankTransfer) {
                    $q1->where('paymentBankTransferID', $paymentBankTransfer->paymentBankTransferID)
                        ->where("pulledToBankTransferYN", -1);
                })->when($confirmed == 0, function ($q2) {
                    $q2->orWhere("pulledToBankTransferYN", 0);
                });
            })->sum('payAmountBank');


        $totalPaymentClearedAmount = BankLedger::where('companySystemID', $paymentBankTransfer->companySystemID)
            ->where('payAmountBank', '>', 0)
            ->where("bankAccountID", $paymentBankTransfer->bankAccountAutoID)
            ->where("trsClearedYN", -1)
            ->where("bankClearedYN", 0)
            ->where(function ($q) use ($paymentBankTransfer) {
                $q->where(function ($q1) use ($paymentBankTransfer) {
                    $q1->where('paymentBankTransferID', $paymentBankTransfer->paymentBankTransferID)
                        ->where("pulledToBankTransferYN", -1);
                });
            })->sum('payAmountBank');

        $paymentBankTransfer->totalPaymentAmount = $totalPaymentAmount;
        $paymentBankTransfer->totalPaymentClearedAmount = $totalPaymentClearedAmount;

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
        $input = array_except($input, ['created_by', 'confirmedByName', 'confirmedByEmpID', 'confirmedDate',
            'confirmed_by', 'confirmedByEmpSystemID']);

        /** @var PaymentBankTransfer $paymentBankTransfer */
        $paymentBankTransfer = $this->paymentBankTransferRepository->findWithoutFail($id);

        if (empty($paymentBankTransfer)) {
            return $this->sendError('Payment Bank Transfer not found');
        }

        if ($paymentBankTransfer->confirmedYN == 1) {
            return $this->sendError('This document already confirmed.', 500);
        }

        if ($paymentBankTransfer->confirmedYN == 0 && $input['confirmedYN'] == 1) {


            $checkItems = BankLedger::where('paymentBankTransferID', $id)
                ->count();
            if ($checkItems == 0) {
                return $this->sendError('Every bank transfer should have at least one cleared item', 500);
            }

            $input['RollLevForApp_curr'] = 1;
            $params = array('autoID' => $id,
                'company' => $paymentBankTransfer->companySystemID,
                'document' => $paymentBankTransfer->documentSystemID,
                'segment' => 0,
                'category' => 0,
                'amount' => 0
            );

            $confirm = \Helper::confirmDocument($params);
            if (!$confirm["success"]) {
                return $this->sendError($confirm["message"], 500);
            }
        }

        //  $paymentBankTransfer = $this->paymentBankTransferRepository->update($input, $id);

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

        $payments = BankLedger::where('paymentBankTransferID', $paymentBankTransfer->paymentBankTransferID)
            ->where('companySystemID', $paymentBankTransfer->companySystemID)
            ->where('bankAccountID', $paymentBankTransfer->bankAccountAutoID)
            ->where('pulledToBankTransferYN', -1)
            ->get();

        foreach ($payments as $data) {
            $updateArray = ['pulledToBankTransferYN' => 0, 'paymentBankTransferID' => null];
            $this->bankLedgerRepository->update($updateArray, $data['bankLedgerAutoID']);
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

    public function getAllBankTransferByBankAccount(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('month', 'year'));

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

        $bankTransfer = PaymentBankTransfer::whereIn('companySystemID', $subCompanies)
            ->where("bankAccountAutoID", $input['bankAccountAutoID'])
            ->with(['created_by', 'bank_account']);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $bankTransfer = $bankTransfer->where(function ($query) use ($search) {
                $query->where('bankTransferDocumentCode', 'LIKE', "%{$search}%")
                    ->orWhere('narration', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($bankTransfer)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('documentDate', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getBankTransferApprovalByUser(Request $request)
    {

        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array());

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $input['companyId'];
        $empID = \Helper::getEmployeeSystemID();

        $search = $request->input('search.value');
        $bankTransfer = DB::table('erp_documentapproved')
            ->select(
                'erp_paymentbanktransfer.*',
                'employees.empName As created_emp',
                'erp_bankaccount.AccountNo As AccountNo',
                'erp_documentapproved.documentApprovedID',
                'rollLevelOrder',
                'approvalLevelID',
                'documentSystemCode')
            ->join('employeesdepartments', function ($query) use ($companyId, $empID) {
                $query->on('erp_documentapproved.approvalGroupID', '=', 'employeesdepartments.employeeGroupID')
                    ->on('erp_documentapproved.documentSystemID', '=', 'employeesdepartments.documentSystemID')
                    ->on('erp_documentapproved.companySystemID', '=', 'employeesdepartments.companySystemID');

                $query->whereIn('employeesdepartments.documentSystemID', [64])
                    ->where('employeesdepartments.companySystemID', $companyId)
                    ->where('employeesdepartments.employeeSystemID', $empID);
            })
            ->join('erp_paymentbanktransfer', function ($query) use ($companyId, $search) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'paymentBankTransferID')
                    ->on('erp_documentapproved.rollLevelOrder', '=', 'RollLevForApp_curr')
                    ->where('erp_paymentbanktransfer.companySystemID', $companyId)
                    ->where('erp_paymentbanktransfer.approvedYN', 0)
                    ->where('erp_paymentbanktransfer.confirmedYN', 1);
            })
            ->where('erp_documentapproved.approvedYN', 0)
            ->leftJoin('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->leftJoin('erp_bankaccount', 'erp_paymentbanktransfer.bankAccountAutoID', 'erp_bankaccount.bankAccountAutoID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->whereIn('erp_documentapproved.documentSystemID', [64])
            ->where('erp_documentapproved.companySystemID', $companyId);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $bankTransfer = $bankTransfer->where(function ($query) use ($search) {
                $query->where('bankTransferDocumentCode', 'LIKE', "%{$search}%")
                    ->orWhere('narration', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::of($bankTransfer)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('paymentBankTransferID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getBankTransferApprovedByUser(Request $request)
    {

        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array());

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $input['companyId'];
        $empID = \Helper::getEmployeeSystemID();

        $search = $request->input('search.value');
        $bankTransfer = DB::table('erp_documentapproved')
            ->select(
                'erp_paymentbanktransfer.*',
                'employees.empName As created_emp',
                'erp_bankaccount.AccountNo As AccountNo',
                'erp_documentapproved.documentApprovedID',
                'rollLevelOrder',
                'approvalLevelID',
                'documentSystemCode')
            ->join('erp_paymentbanktransfer', function ($query) use ($companyId, $search) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'paymentBankTransferID')
                    ->where('erp_paymentbanktransfer.companySystemID', $companyId)
                    ->where('erp_paymentbanktransfer.confirmedYN', 1);
            })
            ->where('erp_documentapproved.approvedYN', -1)
            ->leftJoin('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->leftJoin('erp_bankaccount', 'erp_paymentbanktransfer.bankAccountAutoID', 'erp_bankaccount.bankAccountAutoID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->whereIn('erp_documentapproved.documentSystemID', [64])
            ->where('erp_documentapproved.companySystemID', $companyId)
            ->where('erp_documentapproved.employeeSystemID', $empID);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $bankTransfer = $bankTransfer->where(function ($query) use ($search) {
                $query->where('bankTransferDocumentCode', 'LIKE', "%{$search}%")
                    ->orWhere('narration', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::of($bankTransfer)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('paymentBankTransferID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function exportPaymentBankTransferPreCheck(Request $request)
    {

        $input = $request->all();
        $paymentBankTransfer = PaymentBankTransfer::with(['bank_account'])->find($input['paymentBankTransferID']);

        if (empty($paymentBankTransfer)) {
            return $this->sendError('Payment Bank Transfer not found', 500);
        }

        if ($paymentBankTransfer->exportedYN == 1) {
            return $this->sendError('This document is already exported.', 500);
        }

        if ($paymentBankTransfer->approvedYN != -1) {
            return $this->sendError("This document is not approved. You cannot export. Please check again.", 500);
        }

        $updateArray = ['exportedYN' => -1, 'exportedUserSystemID' => Auth::id(), 'exportedDate' => now()];

        $this->paymentBankTransferRepository->update($updateArray,$input['paymentBankTransferID']);

        return $this->sendResponse([], 'Payment Bank Transfer export to CSV successfully');
    }

    public function exportPaymentBankTransfer(Request $request)
    {

        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('month', 'year'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $decimalPlaces = 3;

        $paymentBankTransfer = PaymentBankTransfer::with(['bank_account'])->find($input['paymentBankTransferID']);

        if (empty($paymentBankTransfer)) {
            return $this->sendError('Payment Bank Transfer not found', 500);
        }

        if ($paymentBankTransfer->exportedYN == 1) {
            return $this->sendError('This document is already exported.', 500);
        }

        if ($paymentBankTransfer->approvedYN != -1) {
            return $this->sendError("This document is not approved. You cannot export. Please check again.", 500);
        }

        $confirmed = $paymentBankTransfer->confirmedYN;

        if ($paymentBankTransfer && $paymentBankTransfer->bank_account) {
            if ($paymentBankTransfer->bank_account->currency) {
                $decimalPlaces = $paymentBankTransfer->bank_account->currency->DecimalPlaces;
            }
        }

        $bankId = 0;
        if ($paymentBankTransfer->bank_account) {
            $bankId = $paymentBankTransfer->bank_account->accountCurrencyID;
        }

        $selectedCompanyId = $paymentBankTransfer->companySystemID;
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);
        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $bankLedger = BankLedger::whereIn('companySystemID', $subCompanies)
            ->where('payAmountBank', '>', 0)
            ->where("bankAccountID", $paymentBankTransfer->bankAccountAutoID)
            ->where("trsClearedYN", -1)
            ->where("bankClearedYN", 0)
            ->where("bankCurrency", $bankId)
            ->whereIn('invoiceType', [2, 3, 5])
            ->where(function ($q) use ($input, $confirmed) {
                $q->where(function ($q1) use ($input) {
                    $q1->where('paymentBankTransferID', $input['paymentBankTransferID'])
                        ->where("pulledToBankTransferYN", -1);
                })->when($confirmed == 0, function ($q2) {
                    $q2->orWhere("pulledToBankTransferYN", 0);
                });
            })
            ->with(['supplier_by' => function ($q3) use ($bankId) {
                $q3->with(['supplierCurrency' => function ($q4) use ($bankId) {
                    $q4->where('currencyID', $bankId)
                        ->with(['bankMemo_by']);
                }]);
            }]);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $bankLedger = $bankLedger->where(function ($query) use ($search) {
                //$query->where('documentCode', 'LIKE', "%{$search}%")
                //   ->orWhere('documentNarration', 'LIKE', "%{$search}%");
            });
        }
        $bankLedger = $bankLedger->orderBy('bankLedgerAutoID', 'desc')->get();
        $data = array();
        $x = 0;
        foreach ($bankLedger as $val) {
            $x++;
            $accountNo13 = '';
            $narration135 = '';
            if ($val['supplier_by']) {
                if ($val['supplier_by']['supplierCurrency']) {
                    if ($val['supplier_by']['supplierCurrency'][0]['bankMemo_by']) {
                        $memos = $val['supplier_by']['supplierCurrency'][0]['bankMemo_by'];
                        foreach ($memos as $memo) {
                            if ($memo->bankMemoTypeID == 4) {
                                $accountNo13 =  preg_replace("/[^0-9]/", "", $memo->memoDetail);
                            } else if ($memo->bankMemoTypeID == 1) {
                                $narration135 = $memo->memoDetail;
                            }
                        }
                    }
                }
            }
            $data[$x]['Account No(13)'] = $accountNo13;
            $data[$x]['Amount(15)'] = number_format($val->payAmountBank, $decimalPlaces);
            $data[$x]['Reference No (16)'] = $val->documentCode;
            $data[$x]['Narration1 (35)'] = $narration135;
            $data[$x]['Narration2 (35)'] = $val->documentNarration;
            if ($val['supplier_by']) {
                $data[$x]['Mobile No'] = $val['supplier_by']['telephone'];
                $data[$x]['EmailID'] = $val['supplier_by']['supEmail'];
            } else {
                $data[$x]['Mobile No'] = '';
                $data[$x]['EmailID'] = '';
            }
        }

        $updateArray = ['exportedYN' => 1];
        $this->paymentBankTransferRepository->update($updateArray,$input['paymentBankTransferID']);

        $time = strtotime("now");
        $fileName = 'payment_bank_transfer_' . $input['paymentBankTransferID'] . '_' . $time . '.pdf';

        $csv = Excel::create($fileName, function ($excel) use ($data) {
            $excel->sheet('Firstsheet', function ($sheet) use ($data) {
                $sheet->setColumnFormat(array(
                    'A' => '0',
                    'B' => '0',
                ));
                $sheet->fromArray($data, null, 'A1', true);
                // $sheet->setAutoSize(true);
                //$sheet->getStyle('A')->getAlignment()->setWrapText(true);
                $sheet->setAutoSize(true);
                //$sheet->setWidth('A', 50);
            });
            //$lastrow = $excel->getActiveSheet()->getHighestRow();
            //$excel->getActiveSheet()->getStyle('A1:J' . $lastrow)->getAlignment()->setWrapText(true);
        })->download('xls');

        return $this->sendResponse([], 'Payment Bank Transfer export to CSV successfully');
    }
}
