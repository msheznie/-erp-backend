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
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBankLedgerAPIRequest;
use App\Http\Requests\API\UpdateBankLedgerAPIRequest;
use App\Models\BankLedger;
use App\Models\BankReconciliation;
use App\Repositories\BankLedgerRepository;
use App\Repositories\BankReconciliationRepository;
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

    public function __construct(BankLedgerRepository $bankLedgerRepo, BankReconciliationRepository $bankReconciliationRepo)
    {
        $this->bankLedgerRepository = $bankLedgerRepo;
        $this->bankReconciliationRepository = $bankReconciliationRepo;
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

        $bankReconciliation = $this->bankReconciliationRepository->findWithoutFail($input['bankRecAutoID']);

        if (empty($bankReconciliation)) {
            return $this->sendError('Bank Reconciliation not found');
        }


        if ($bankReconciliation->confirmedYN == 1) {
            return $this->sendError('You cannot edit, This document already confirmed.', 500);
        }

        $employee = \Helper::getEmployeeInfo();
        $updateArray = array();

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
        } else {
            $updateArray['bankClearedAmount'] = 0;
            $updateArray['bankClearedByEmpName'] = null;
            $updateArray['bankClearedByEmpID'] = null;
            $updateArray['bankClearedByEmpSystemID'] = null;
            $updateArray['bankClearedDate'] = null;
            $updateArray['bankRecAutoID'] = null;
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
        $bankReconciliationNew = $this->bankReconciliationRepository->update($inputNew, $input['bankRecAutoID']);


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

        if (array_key_exists('invoiceType', $input) && ($input['invoiceType'] == 1 || $input['invoiceType'] == 2)) {

            if ($input['invoiceType'] == 1) {
                $type = '<';
            } else if ($input['invoiceType'] == 2) {
                $type = '>';
            }
        }

        $bankRecAsOf = '';
        $bankReconciliation = BankReconciliation::find($input['bankRecAutoID']);

        if (!empty($bankReconciliation)) {
            $bankRecAsOf = new Carbon($bankReconciliation->bankRecAsOf);
        }

        $logistics = BankLedger::whereIn('companySystemID', $subCompanies)
                                ->where('payAmountBank', $type, 0)
                                ->where("bankAccountID", $input['bankAccountAutoID'])
                                ->where("trsClearedYN", -1)
                                ->when($bankRecAsOf != '', function ($q) use ($bankRecAsOf) {
                                    $q->where("documentDate", '<=', $bankRecAsOf);
                                })
                                ->where(function ($q) use ($input) {
                                    $q->where(function ($q1) use ($input) {
                                        $q1->where('bankRecAutoID', $input['bankRecAutoID'])
                                            ->where("bankClearedYN", -1);
                                    })->orWhere("bankClearedYN", 0);
                                });

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $logistics = $logistics->where(function ($query) use ($search) {
                $query->where('documentCode', 'LIKE', "%{$search}%")
                    ->orWhere('documentNarration', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($logistics)
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

}
