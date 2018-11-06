<?php
/**
 * =============================================
 * -- File Name : BankLedgerAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Bank Ledger
 * -- Author : Mohamed Fayas
 * -- Create date : 18 - September 2018
 * -- Description : This file contains the all CRUD for Bank Ledger
 * -- REVISION HISTORY
 * -- Date: 19-September 2018 By: Fayas Description: Added new functions named as getBankReconciliationsByType()
 * -- Date: 27-September 2018 By: Fayas Description: Added new functions named as getBankAccountPaymentReceiptByType()
 * -- Date: 03-October 2018 By: Fayas Description: Added new functions named as getPaymentsByBankTransfer()
 * -- Date: 30-October 2018 By: Fayas Description: Added new functions named as getChequePrintingItems()
 * -- Date: 31-October 2018 By: Fayas Description: Added new functions named as getChequePrintingFormData(),printChequeItems()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBankLedgerAPIRequest;
use App\Http\Requests\API\UpdateBankLedgerAPIRequest;
use App\Models\BankAccount;
use App\Models\BankLedger;
use App\Models\BankMaster;
use App\Models\BankReconciliation;
use App\Models\GeneralLedger;
use App\Models\PaymentBankTransfer;
use App\Models\PaySupplierInvoiceMaster;
use App\Repositories\BankLedgerRepository;
use App\Repositories\BankReconciliationRepository;
use App\Repositories\PaymentBankTransferRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class BankLedgerController
 * @package App\Http\Controllers\API
 */
class BankLedgerAPIController extends AppBaseController
{
    /** @var  BankLedgerRepository */
    private $bankLedgerRepository;
    private $bankReconciliationRepository;
    private $paymentBankTransferRepository;

    public function __construct(BankLedgerRepository $bankLedgerRepo, BankReconciliationRepository $bankReconciliationRepo,
                                PaymentBankTransferRepository $paymentBankTransferRepo)
    {
        $this->bankLedgerRepository = $bankLedgerRepo;
        $this->bankReconciliationRepository = $bankReconciliationRepo;
        $this->paymentBankTransferRepository = $paymentBankTransferRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/bankLedgers",
     *      summary="Get a listing of the BankLedgers.",
     *      tags={"BankLedger"},
     *      description="Get all BankLedgers",
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
     *                  @SWG\Items(ref="#/definitions/BankLedger")
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
        $this->bankLedgerRepository->pushCriteria(new RequestCriteria($request));
        $this->bankLedgerRepository->pushCriteria(new LimitOffsetCriteria($request));
        $bankLedgers = $this->bankLedgerRepository->all();

        return $this->sendResponse($bankLedgers->toArray(), 'Bank Ledgers retrieved successfully');
    }

    /**
     * @param CreateBankLedgerAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/bankLedgers",
     *      summary="Store a newly created BankLedger in storage",
     *      tags={"BankLedger"},
     *      description="Store BankLedger",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BankLedger that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BankLedger")
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
     *                  ref="#/definitions/BankLedger"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateBankLedgerAPIRequest $request)
    {
        $input = $request->all();

        $bankLedgers = $this->bankLedgerRepository->create($input);

        return $this->sendResponse($bankLedgers->toArray(), 'Bank Ledger saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/bankLedgers/{id}",
     *      summary="Display the specified BankLedger",
     *      tags={"BankLedger"},
     *      description="Get BankLedger",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BankLedger",
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
     *                  ref="#/definitions/BankLedger"
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
        /** @var BankLedger $bankLedger */
        $bankLedger = $this->bankLedgerRepository->findWithoutFail($id);

        if (empty($bankLedger)) {
            return $this->sendError('Bank Ledger not found');
        }

        return $this->sendResponse($bankLedger->toArray(), 'Bank Ledger retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateBankLedgerAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/bankLedgers/{id}",
     *      summary="Update the specified BankLedger in storage",
     *      tags={"BankLedger"},
     *      description="Update BankLedger",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BankLedger",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BankLedger that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BankLedger")
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
     *                  ref="#/definitions/BankLedger"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateBankLedgerAPIRequest $request)
    {
        $input = $request->all();

        /** @var BankLedger $bankLedger */
        $bankLedger = $this->bankLedgerRepository->findWithoutFail($id);

        if (empty($bankLedger)) {
            return $this->sendError('Bank Ledger not found');
        }

        $employee = \Helper::getEmployeeInfo();
        $updateArray = array();

        if (array_key_exists('editType', $input)) {

            if ($input['editType'] == 1) {

                $bankReconciliation = $this->bankReconciliationRepository->findWithoutFail($input['bankRecAutoID']);

                if (empty($bankReconciliation)) {
                    return $this->sendError('Bank Reconciliation not found');
                }

                if ($bankReconciliation->confirmedYN == 1) {
                    return $this->sendError('You cannot edit, This document already confirmed.', 500);
                }

                if ($input['bankClearedYN']) {
                    $updateArray['bankClearedYN'] = -1;
                } else {
                    $updateArray['bankClearedYN'] = 0;
                }

                if ($updateArray['bankClearedYN']) {
                    $updateArray['bankClearedAmount'] = $bankLedger->payAmountBank;
                    $updateArray['bankClearedByEmpName'] = $employee->empName;
                    $updateArray['bankClearedByEmpID'] = $employee->empID;
                    $updateArray['bankClearedByEmpSystemID'] = $employee->employeeSystemID;
                    $updateArray['bankClearedDate'] = now();
                    $updateArray['bankRecAutoID'] = $input['bankRecAutoID'];
                    $updateArray['bankreconciliationDate'] = $bankReconciliation->bankRecAsOf;
                    $updateArray['bankRecYear'] = $bankReconciliation->year;
                    $updateArray['bankrecMonth'] = $bankReconciliation->month;
                } else {
                    $updateArray['bankClearedAmount'] = 0;
                    $updateArray['bankClearedByEmpName'] = null;
                    $updateArray['bankClearedByEmpID'] = null;
                    $updateArray['bankClearedByEmpSystemID'] = null;
                    $updateArray['bankClearedDate'] = null;
                    $updateArray['bankRecAutoID'] = null;
                    $updateArray['bankreconciliationDate'] = null;
                    $updateArray['bankRecYear'] = null;
                    $updateArray['bankrecMonth'] = null;
                }

                $bankLedger = $this->bankLedgerRepository->update($updateArray, $id);

                $bankRecReceiptAmount = BankLedger::where('bankRecAutoID', $input['bankRecAutoID'])
                    ->where('bankClearedYN', -1)
                    ->where('payAmountBank', '<', 0)
                    ->sum('bankClearedAmount');

                $bankRecPaymentAmount = BankLedger::where('bankRecAutoID', $input['bankRecAutoID'])
                    ->where('bankClearedYN', -1)
                    ->where('payAmountBank', '>', 0)
                    ->sum('bankClearedAmount');

                $closingAmount = $bankReconciliation->openingBalance + ($bankRecReceiptAmount * -1) - $bankRecPaymentAmount;

                $inputNew = array('closingBalance' => $closingAmount);
                $this->bankReconciliationRepository->update($inputNew, $input['bankRecAutoID']);
            } else if ($input['editType'] == 2) {

                if ($bankLedger->bankClearedYN == -1) {
                    return $this->sendError('You cannot edit, This item already added to bank reconciliation.', 500);
                }

                if ($input['trsCollectedYN']) {
                    $updateArray['trsCollectedYN'] = -1;
                } else {
                    $updateArray['trsCollectedYN'] = 0;
                }

                if ($updateArray['trsCollectedYN']) {
                    $updateArray['trsCollectedByEmpName'] = $employee->empName;
                    $updateArray['trsCollectedByEmpID'] = $employee->empID;
                    $updateArray['trsCollectedByEmpSystemID'] = $employee->employeeSystemID;
                    $updateArray['trsCollectedDate'] = now();
                } else {
                    $updateArray['trsCollectedByEmpName'] = null;
                    $updateArray['trsCollectedByEmpID'] = null;
                    $updateArray['trsCollectedByEmpSystemID'] = null;
                    $updateArray['trsCollectedDate'] = null;
                }

                $bankLedger = $this->bankLedgerRepository->update($updateArray, $id);
            } else if ($input['editType'] == 3) {

                if ($bankLedger->pulledToBankTransferYN == -1) {
                    return $this->sendError('You cannot edit, This payment already added to bank transfer.', 500);
                }

                if ($bankLedger->bankClearedYN == -1) {
                    return $this->sendError('You cannot edit, This item already added to bank reconciliation.', 500);
                }

                if ($input['trsClearedYN']) {
                    $updateArray['trsClearedYN'] = -1;
                } else {
                    $updateArray['trsClearedYN'] = 0;
                }

                if ($updateArray['trsClearedYN'] == -1) {


                    $checkGLAmount = GeneralLedger::where('companySystemID', $bankLedger->companySystemID)
                        ->where('documentSystemID', $bankLedger->documentSystemID)
                        ->where('documentSystemCode', $bankLedger->documentSystemCode)
                        ->where('chartOfAccountSystemID', $bankLedger->payeeGLCodeID)
                        ->first();

                    if (!empty($checkGLAmount)) {
                        $glAmount = 0;
                        if ($bankLedger->bankCurrency == $checkGLAmount->documentLocalCurrencyID) {
                            $glAmount = $checkGLAmount->documentLocalAmount;
                        } else if ($bankLedger->bankCurrency == $checkGLAmount->documentRptCurrencyID) {
                            $glAmount = $checkGLAmount->documentRptAmount;
                        }
                        if ($bankLedger->payAmountBank != $glAmount) {
                            return $this->sendError('Bank amount is not matching with GL amount.', 500);
                        }
                    } else {
                        return $this->sendError('GL data cannot be found for this document.', 500);
                    }
                }


                if ($updateArray['trsClearedYN']) {
                    $updateArray['trsClearedAmount'] = $bankLedger->payAmountBank;
                    $updateArray['trsClearedByEmpName'] = $employee->empName;
                    $updateArray['trsClearedByEmpID'] = $employee->empID;
                    $updateArray['trsClearedByEmpSystemID'] = $employee->employeeSystemID;
                    $updateArray['trsClearedDate'] = now();
                } else {
                    $updateArray['trsClearedAmount'] = 0;
                    $updateArray['trsClearedByEmpName'] = null;
                    $updateArray['trsClearedByEmpID'] = null;
                    $updateArray['trsClearedByEmpSystemID'] = null;
                    $updateArray['trsClearedDate'] = null;
                }

                $bankLedger = $this->bankLedgerRepository->update($updateArray, $id);
            } else if ($input['editType'] == 4) {

                $bankTransfer = $this->paymentBankTransferRepository->findWithoutFail($input['paymentBankTransferID']);

                if (empty($bankTransfer)) {
                    return $this->sendError('Bank Transfer not found');
                }

                if ($bankTransfer->confirmedYN == 1) {
                    return $this->sendError('You cannot edit, This document already confirmed.', 500);
                }

                if ($input['pulledToBankTransferYN']) {
                    $updateArray['pulledToBankTransferYN'] = -1;
                } else {
                    $updateArray['pulledToBankTransferYN'] = 0;
                }

                if ($updateArray['pulledToBankTransferYN']) {
                    $updateArray['paymentBankTransferID'] = $input['paymentBankTransferID'];
                } else {
                    $updateArray['paymentBankTransferID'] = null;
                }

                $bankLedger = $this->bankLedgerRepository->update($updateArray, $id);
            }
        }
        $bankLedger = $this->bankLedgerRepository->findWithoutFail($id);
        return $this->sendResponse($bankLedger->toArray(), 'BankLedger updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/bankLedgers/{id}",
     *      summary="Remove the specified BankLedger from storage",
     *      tags={"BankLedger"},
     *      description="Delete BankLedger",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BankLedger",
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
        /** @var BankLedger $bankLedger */
        $bankLedger = $this->bankLedgerRepository->findWithoutFail($id);

        if (empty($bankLedger)) {
            return $this->sendError('Bank Ledger not found');
        }

        $bankLedger->delete();

        return $this->sendResponse($id, 'Bank Ledger deleted successfully');
    }

    public function getBankReconciliationsByType(Request $request)
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

        $type = '<';

        if (array_key_exists('type', $input) && ($input['type'] == 1 || $input['type'] == 2)) {

            if ($input['type'] == 1) {
                $type = '<';
            } else if ($input['type'] == 2) {
                $type = '>';
            }
        }

        $bankReconciliation = BankReconciliation::find($input['bankRecAutoID']);
        $confirmed = 0;
        if (!empty($bankReconciliation)) {
            $confirmed = $bankReconciliation->confirmedYN;
        }

        $bankLedger = BankLedger::whereIn('companySystemID', $subCompanies)
            ->where('payAmountBank', $type, 0)
            ->where("bankAccountID", $input['bankAccountAutoID'])
            ->where("trsClearedYN", -1)
            ->where(function ($q) use ($input, $confirmed) {
                $q->where(function ($q1) use ($input) {
                    $q1->where('bankRecAutoID', $input['bankRecAutoID'])
                        ->where("bankClearedYN", -1);
                })->when($confirmed == 0, function ($q2) {
                    $q2->orWhere("bankClearedYN", 0);
                });

            });

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $bankLedger = $bankLedger->where(function ($query) use ($search) {
                $query->where('documentCode', 'LIKE', "%{$search}%")
                    ->orWhere('documentNarration', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($bankLedger)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('bankLedgerAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getBankAccountPaymentReceiptByType(Request $request)
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

        $type = '<';

        if (array_key_exists('type', $input) && ($input['type'] == 1 || $input['type'] == 2)) {

            if ($input['type'] == 1) {
                $type = '<';
            } else if ($input['type'] == 2) {
                $type = '>';
            }
        }

        $bankLedger = BankLedger::whereIn('companySystemID', $subCompanies)
            ->where('payAmountBank', $type, 0)
            ->where("bankAccountID", $input['bankAccountAutoID'])
            //->where("trsClearedYN", -1)
            ->where("bankClearedYN", 0);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $bankLedger = $bankLedger->where(function ($query) use ($search) {
                $query->where('documentCode', 'LIKE', "%{$search}%")
                    ->orWhere('documentNarration', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($bankLedger)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('bankLedgerAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getPaymentsByBankTransfer(Request $request)
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

        $paymentBankTransfer = PaymentBankTransfer::find($input['paymentBankTransferID']);
        $confirmed = 0;
        if (!empty($paymentBankTransfer)) {
            $confirmed = $paymentBankTransfer->confirmedYN;
        }
        $bankId = 1;
        if ($paymentBankTransfer->bank_account) {
            $bankId = $paymentBankTransfer->bank_account->accountCurrencyID;
        }

        $bankLedger = BankLedger::whereIn('companySystemID', $subCompanies)
            ->where('payAmountBank', '>', 0)
            ->where("bankAccountID", $input['bankAccountAutoID'])
            ->where("trsClearedYN", -1)
            ->where("bankClearedYN", 0)
            ->whereIn('invoiceType', [2, 3, 5])
            ->where("bankCurrency", $bankId)
            ->where(function ($q) use ($input, $confirmed) {
                $q->where(function ($q1) use ($input) {
                    $q1->where('paymentBankTransferID', $input['paymentBankTransferID'])
                        ->where("pulledToBankTransferYN", -1);
                })->when($confirmed == 0, function ($q2) {
                    $q2->orWhere("pulledToBankTransferYN", 0);
                });
            });

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $bankLedger = $bankLedger->where(function ($query) use ($search) {
                $query->where('documentCode', 'LIKE', "%{$search}%")
                    ->orWhere('documentNarration', 'LIKE', "%{$search}%")
                    ->orWhere('payeeName', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($bankLedger)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('bankLedgerAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getChequePrintingItems(Request $request)
    {
        $input = $request->all();
        $search = $request->input('search.value');
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $bankLedger = $this->chequeListQrt($input, $search, 0);

        return \DataTables::eloquent($bankLedger)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('bankLedgerAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function chequeListQrt($request, $search, $chequePrintedYN)
    {
        $input = $request;
        $input = $this->convertArrayToSelectedValue($input, array('bankID', 'bankAccountID', 'invoiceType'));
        $selectedCompanyId = $request['companySystemID'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }


        $validator = \Validator::make($input, [
            'bankID' => 'required',
            'bankAccountID' => 'required',
            'invoiceType' => 'required',
            'option' => 'required'
        ]);

        if (!$validator->fails() && $input['bankID'] > 0 && $input['bankAccountID'] > 0 &&
            in_array($input['invoiceType'], [2, 3, 5])
            && in_array($input['option'], [0, -1])
        ) {
        } else {
            $input['chequePaymentYN'] = -99;
            $input['invoiceType'] = -99;
            $input['bankID'] = -99;
            $input['bankAccountID'] = -99;
            $input['option'] = -99;
        }

        $bankAccount = BankAccount::where('bankmasterAutoID', $input['bankID'])
            ->where('bankAccountAutoID', $input['bankAccountID'])
            ->with(['currency'])
            ->first();

        $bank_currency_id = 2;
        if ($bankAccount && $bankAccount->currency) {
            $bank_currency_id = $bankAccount->currency->currencyID;
        }

        $bankLedger = BankLedger::whereIn('companySystemID', $subCompanies)
            ->where("chequePrintedYN", $chequePrintedYN)
            ->where("chequePaymentYN", $input['option'])
            ->when(request('invoiceType') && in_array($input['invoiceType'], $input), function ($q) use ($input) {
                return $q->where('invoiceType', $input['invoiceType']);
            })
            ->when(request('bankID'), function ($q) use ($input) {
                return $q->where('bankID', $input['bankID']);
            })
            ->when(request('bankAccountID'), function ($q) use ($input) {
                return $q->where('bankAccountID', $input['bankAccountID']);
            })
            ->with(['bank_currency_by', 'company', 'bank_account', 'supplier_by' => function ($q3) use ($bank_currency_id) {
                $q3->with(['supplierCurrency' => function ($q4) use ($bank_currency_id) {
                    $q4->where('currencyID', $bank_currency_id)
                        ->with(['bankMemo_by']);
                }]);
            }]);


        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $bankLedger = $bankLedger->where(function ($query) use ($search) {
                $query->where('documentCode', 'LIKE', "%{$search}%")
                    ->orWhere('documentNarration', 'LIKE', "%{$search}%")
                    ->orWhere('payeeName', 'LIKE', "%{$search}%");
            });
        }

        return $bankLedger;
    }

    public function updatePrintChequeItems(Request $request)
    {
        $input = $request->all();
        $search = $request->input('search.value');
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $employee = \Helper::getEmployeeInfo();
        $bankLedger = $this->chequeListQrt($input, $search, 0)
            ->when($input['chequeNumberRange'], function ($q) use ($input) {
                $q->where('documentChequeNo', '>=', $input['chequeNumberRangeFrom'])
                    ->where('documentChequeNo', '<=', $input['chequeNumberRangeTo']);
            })
            ->orderBy('bankLedgerAutoID', $sort)
            ->get();

        if (count($bankLedger) == 0) {
            return $this->sendError('No any item found for print', 500);
        }

        foreach ($bankLedger as $item) {
            $temArray = array();
            $temArray['chequePrintedYN'] = 1;
            $temArray['chequePrintedDateTime'] = now();
            $temArray['chequePrintedByEmpSystemID'] = $employee->employeeSystemID;
            $temArray['chequePrintedByEmpID'] = $employee->empID;
            $temArray['chequePrintedByEmpName'] = $employee->empName;
            $this->bankLedgerRepository->update($temArray, $item->bankLedgerAutoID);
        }

        return $this->sendResponse($bankLedger->toArray(), 'updated successfully');
    }

    public function printChequeItems(Request $request)
    {
        $input = $request->all();

        /*return $checkAuth = \Helper::getEmployeeInfoByURL($input);
        if (!$checkAuth["success"]) {
            return $this->sendError($checkAuth["message"], 500);
        }
        $employee = $checkAuth["message"];*/
        $search = $request->input('search.value');
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $bankLedger = $this->chequeListQrt($input, $search, 1)
            ->when($input['chequeNumberRange'], function ($q) use ($input) {
                $q->where('documentChequeNo', '>=', $input['chequeNumberRangeFrom'])
                    ->where('documentChequeNo', '<=', $input['chequeNumberRangeTo']);
            })
            ->orderBy('bankLedgerAutoID', $sort)
            ->get();

        if (count($bankLedger) == 0) {
            return $this->sendError('Not found', 500);
        }
        $time = strtotime("now");
        $fileName = 'cheque' . $time . '.pdf';
        $f = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);

        foreach ($bankLedger as $item) {

            $temArray['chequePrintedYN'] = -1;
            //$this->bankLedgerRepository->update($temArray, $item->bankLedgerAutoID);

            $amountSplit = explode(".",$item->payAmountBank);
            $intAmt = 0;
            $floatAmt = 00;

            if(count($amountSplit) == 1){
                $intAmt = $amountSplit[0];
                $floatAmt = 00;
            }else if(count($amountSplit) == 2){
                $intAmt = $amountSplit[0];
                $floatAmt = $amountSplit[1];
            }

            $item['amount_word'] = ucwords($f->format($intAmt));
            if ($item['supplier_by']) {
                if ($item['supplier_by']['supplierCurrency']) {
                    if ($item['supplier_by']['supplierCurrency'][0]['bankMemo_by']) {
                        $memos = $item['supplier_by']['supplierCurrency'][0]['bankMemo_by'];
                       $item->memos = $memos;
                    }
                }
            }
            $item['decimalPlaces'] = 2;
            $item['floatAmt'] = (string)$floatAmt;
            if($item['bank_currency_by']){
                $item['decimalPlaces'] = $item['bank_currency_by']['DecimalPlaces'];
            }

            $temDetails = PaySupplierInvoiceMaster::where('PayMasterAutoId', $item['documentSystemCode'])
                                                  ->first();

            if(!empty($temDetails)){
                if($input['invoiceType'] == 2){
                    $item['details'] = $temDetails->supplierdetail;
                }else if($input['invoiceType'] == 3){
                    $item['details'] = $temDetails->directdetail;
                }else if($input['invoiceType'] == 5){
                    $item['details'] = $temDetails->advancedetail;
                }else{
                    $item['details'] = [];
                }
            }else{
                $item['details'] = [];
            }
        }

        //return $bankLedger;
        $htmlName = '';
        if($input['option']  == -1){
            $htmlName = 'cheque';
        }else if($input['option']  ==  0){
            $htmlName = 'bank_transfer';
        }
        $array = array('entities' => $bankLedger, 'date' => now());
        if($htmlName){
            $html = view('print.'.$htmlName, $array);
            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($html);
            //$materielIssue->docRefNo = \Helper::getCompanyDocRefNo($input['companySystemID'], $materielIssue->documentSystemID);
            //'landscape'
            return $pdf->setPaper('a4', 'portrait')->setWarnings(false)->stream($fileName);
        }else{
            return $this->sendError('Error', 500);
        }
    }


    public function getChequePrintingFormData(Request $request)
    {
        $selectedCompanyId = $request['companyId'];
        $subCompaniesByGroup = [];
        if (\Helper::checkIsCompanyGroup($selectedCompanyId)) {
            $subCompaniesByGroup = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompaniesByGroup = (array)$selectedCompanyId;
        }

        $banks = BankMaster::all();
        $bankIds = $banks->pluck('bankmasterAutoID');
        $accounts = BankAccount::whereIn('companySystemID', $subCompaniesByGroup)
            ->whereIN('bankmasterAutoID', $bankIds)
            ->where('isAccountActive', 1)
            ->get();
        $output = array(
            'banks' => $banks,
            'accounts' => $accounts
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }
}
