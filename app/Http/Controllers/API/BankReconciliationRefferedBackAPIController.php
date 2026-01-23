<?php
/**
 * =============================================
 * -- File Name : BankReconciliationRefferedBackAPIController.php
 * -- Project Name : ERP
 * -- Module Name : Bank Reconciliation Reffered Back
 * -- Author : Mohamed Fayas
 * -- Create date : 12 - December 2018
 * -- Description : This file contains the all CRUD for  Bank Reconciliation Reffered Back
 * -- REVISION HISTORY
 * -- Date: 12-December 2018 By: Fayas Description: Added new functions named as getReferBackHistoryByBankRec()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBankReconciliationRefferedBackAPIRequest;
use App\Http\Requests\API\UpdateBankReconciliationRefferedBackAPIRequest;
use App\Models\BankReconciliationRefferedBack;
use App\Models\PaymentBankTransferDetailRefferedBack;
use App\Repositories\BankReconciliationRefferedBackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\helper\Helper;

/**
 * Class BankReconciliationRefferedBackController
 * @package App\Http\Controllers\API
 */

class BankReconciliationRefferedBackAPIController extends AppBaseController
{
    /** @var  BankReconciliationRefferedBackRepository */
    private $bankReconciliationRefferedBackRepository;

    public function __construct(BankReconciliationRefferedBackRepository $bankReconciliationRefferedBackRepo)
    {
        $this->bankReconciliationRefferedBackRepository = $bankReconciliationRefferedBackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/bankReconciliationRefferedBacks",
     *      summary="Get a listing of the BankReconciliationRefferedBacks.",
     *      tags={"BankReconciliationRefferedBack"},
     *      description="Get all BankReconciliationRefferedBacks",
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
     *                  @SWG\Items(ref="#/definitions/BankReconciliationRefferedBack")
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
        $this->bankReconciliationRefferedBackRepository->pushCriteria(new RequestCriteria($request));
        $this->bankReconciliationRefferedBackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $bankReconciliationRefferedBacks = $this->bankReconciliationRefferedBackRepository->all();

        return $this->sendResponse($bankReconciliationRefferedBacks->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.bank_reconciliation_reffered_backs')]));
    }

    /**
     * @param CreateBankReconciliationRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/bankReconciliationRefferedBacks",
     *      summary="Store a newly created BankReconciliationRefferedBack in storage",
     *      tags={"BankReconciliationRefferedBack"},
     *      description="Store BankReconciliationRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BankReconciliationRefferedBack that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BankReconciliationRefferedBack")
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
     *                  ref="#/definitions/BankReconciliationRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateBankReconciliationRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        $bankReconciliationRefferedBacks = $this->bankReconciliationRefferedBackRepository->create($input);

        return $this->sendResponse($bankReconciliationRefferedBacks->toArray(), trans('custom.save', ['attribute' => trans('custom.bank_reconciliation_reffered_backs')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/bankReconciliationRefferedBacks/{id}",
     *      summary="Display the specified BankReconciliationRefferedBack",
     *      tags={"BankReconciliationRefferedBack"},
     *      description="Get BankReconciliationRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BankReconciliationRefferedBack",
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
     *                  ref="#/definitions/BankReconciliationRefferedBack"
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
        /** @var BankReconciliationRefferedBack $bankReconciliationRefferedBack */
        $bankReconciliation = $this->bankReconciliationRefferedBackRepository->with(['bank_account.currency', 'confirmed_by'])->findWithoutFail($id);

        if (empty($bankReconciliation)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.bank_reconciliation')]));
        }

        if (!empty($bankReconciliation)) {
            $confirmed = $bankReconciliation->confirmedYN;
        }

        $totalReceiptAmount = PaymentBankTransferDetailRefferedBack::where('companySystemID', $bankReconciliation->companySystemID)
            ->where('payAmountBank', '<', 0)
            ->where("bankAccountID", $bankReconciliation->bankAccountAutoID)
            ->where("timesReferred", $bankReconciliation->timesReferred)
            ->where("trsClearedYN", -1)
            ->whereDate("postedDate", '<=', $bankReconciliation->bankRecAsOf)
            ->where(function ($q) use ($bankReconciliation, $confirmed) {
                $q->where(function ($q1) use ($bankReconciliation) {
                    $q1->where('bankRecAutoID', $bankReconciliation->bankRecAutoID)
                        ->where("bankClearedYN", -1);
                })->when($confirmed == 0, function ($q2) {
                    $q2->orWhere("bankClearedYN", 0);
                });
            })->sum('payAmountBank');

        $totalPaymentAmount = PaymentBankTransferDetailRefferedBack::where('companySystemID', $bankReconciliation->companySystemID)
            ->where('payAmountBank', '>', 0)
            ->where("bankAccountID", $bankReconciliation->bankAccountAutoID)
            ->where("timesReferred", $bankReconciliation->timesReferred)
            ->where("trsClearedYN", -1)
            ->whereDate("postedDate", '<=', $bankReconciliation->bankRecAsOf)
            ->where(function ($q) use ($bankReconciliation, $confirmed) {
                $q->where(function ($q1) use ($bankReconciliation) {
                    $q1->where('bankRecAutoID', $bankReconciliation->bankRecAutoID)
                        ->where("bankClearedYN", -1);
                })->when($confirmed == 0, function ($q2) {
                    $q2->orWhere("bankClearedYN", 0);
                });
            })->sum('payAmountBank');

        $totalReceiptClearedAmount = PaymentBankTransferDetailRefferedBack::where('companySystemID', $bankReconciliation->companySystemID)
            ->where('payAmountBank', '<', 0)
            ->where("bankAccountID", $bankReconciliation->bankAccountAutoID)
            ->where("timesReferred", $bankReconciliation->timesReferred)
            ->where("trsClearedYN", -1)
            ->whereDate("postedDate", '<=', $bankReconciliation->bankRecAsOf)
            ->where(function ($q) use ($bankReconciliation) {
                $q->where(function ($q1) use ($bankReconciliation) {
                    $q1->where('bankRecAutoID', $bankReconciliation->bankRecAutoID)
                        ->where("bankClearedYN", -1);
                });
            })->sum('bankClearedAmount');

        $totalPaymentClearedAmount = PaymentBankTransferDetailRefferedBack::where('companySystemID', $bankReconciliation->companySystemID)
            ->where('payAmountBank', '>', 0)
            ->where("bankAccountID", $bankReconciliation->bankAccountAutoID)
            ->where("timesReferred", $bankReconciliation->timesReferred)
            ->where("trsClearedYN", -1)
            ->whereDate("postedDate", '<=', $bankReconciliation->bankRecAsOf)
            ->where(function ($q) use ($bankReconciliation) {
                $q->where(function ($q1) use ($bankReconciliation) {
                    $q1->where('bankRecAutoID', $bankReconciliation->bankRecAutoID)
                        ->where("bankClearedYN", -1);
                });
            })->sum('bankClearedAmount');

        $bankReconciliation->totalReceiptAmount = $totalReceiptAmount * -1;
        $bankReconciliation->totalReceiptClearedAmount = $totalReceiptClearedAmount * -1;
        $bankReconciliation->totalPaymentAmount = $totalPaymentAmount;
        $bankReconciliation->totalPaymentClearedAmount = $totalPaymentClearedAmount;

        return $this->sendResponse($bankReconciliation->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.bank_reconciliation_reffered_backs')]));
    }

    /**
     * @param int $id
     * @param UpdateBankReconciliationRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/bankReconciliationRefferedBacks/{id}",
     *      summary="Update the specified BankReconciliationRefferedBack in storage",
     *      tags={"BankReconciliationRefferedBack"},
     *      description="Update BankReconciliationRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BankReconciliationRefferedBack",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BankReconciliationRefferedBack that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BankReconciliationRefferedBack")
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
     *                  ref="#/definitions/BankReconciliationRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateBankReconciliationRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        /** @var BankReconciliationRefferedBack $bankReconciliationRefferedBack */
        $bankReconciliationRefferedBack = $this->bankReconciliationRefferedBackRepository->findWithoutFail($id);

        if (empty($bankReconciliationRefferedBack)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.bank_reconciliation_reffered_backs')]));
        }

        $bankReconciliationRefferedBack = $this->bankReconciliationRefferedBackRepository->update($input, $id);

        return $this->sendResponse($bankReconciliationRefferedBack->toArray(), trans('custom.update', ['attribute' => trans('custom.bank_reconciliation_reffered_backs')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/bankReconciliationRefferedBacks/{id}",
     *      summary="Remove the specified BankReconciliationRefferedBack from storage",
     *      tags={"BankReconciliationRefferedBack"},
     *      description="Delete BankReconciliationRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BankReconciliationRefferedBack",
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
        /** @var BankReconciliationRefferedBack $bankReconciliationRefferedBack */
        $bankReconciliationRefferedBack = $this->bankReconciliationRefferedBackRepository->findWithoutFail($id);

        if (empty($bankReconciliationRefferedBack)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.bank_reconciliation_reffered_backs')]));
        }

        $bankReconciliationRefferedBack->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.bank_reconciliation_reffered_backs')]));
    }

    public function getReferBackHistoryByBankRec(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('month', 'year'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $selectedCompanyId = $request['companyId'];
        $isGroup = Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $bankReconciliation = BankReconciliationRefferedBack::whereIn('companySystemID', $subCompanies)
            ->where("bankRecAutoID", $input['bankRecAutoID'])
            ->with(['month', 'created_by', 'bank_account']);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $bankReconciliation = $bankReconciliation->where(function ($query) use ($search) {
                $query->where('bankRecPrimaryCode', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($bankReconciliation)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('bankRecRefferedBackAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

}
