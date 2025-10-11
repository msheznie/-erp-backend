<?php
/**
 * =============================================
 * -- File Name : PaymentBankTransferRefferedBackAPIController.php
 * -- Project Name : ERP
 * -- Module Name : Payment Bank Transfer Referred Back
 * -- Author : Mohamed Fayas
 * -- Create date : 11 - December 2018
 * -- Description : This file contains the all CRUD for Payment Bank Transfer Referred Back
 * -- REVISION HISTORY
 * -- Date: 11 - December 2018 By: Fayas Description: Added new functions named as getReferBackHistoryByBankTransfer
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePaymentBankTransferRefferedBackAPIRequest;
use App\Http\Requests\API\UpdatePaymentBankTransferRefferedBackAPIRequest;
use App\Models\PaymentBankTransferDetailRefferedBack;
use App\Models\PaymentBankTransferRefferedBack;
use App\Repositories\PaymentBankTransferRefferedBackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PaymentBankTransferRefferedBackController
 * @package App\Http\Controllers\API
 */

class PaymentBankTransferRefferedBackAPIController extends AppBaseController
{
    /** @var  PaymentBankTransferRefferedBackRepository */
    private $paymentBankTransferRefferedBackRepository;

    public function __construct(PaymentBankTransferRefferedBackRepository $paymentBankTransferRefferedBackRepo)
    {
        $this->paymentBankTransferRefferedBackRepository = $paymentBankTransferRefferedBackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/paymentBankTransferRefferedBacks",
     *      summary="Get a listing of the PaymentBankTransferRefferedBacks.",
     *      tags={"PaymentBankTransferRefferedBack"},
     *      description="Get all PaymentBankTransferRefferedBacks",
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
     *                  @SWG\Items(ref="#/definitions/PaymentBankTransferRefferedBack")
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
        $this->paymentBankTransferRefferedBackRepository->pushCriteria(new RequestCriteria($request));
        $this->paymentBankTransferRefferedBackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $paymentBankTransferRefferedBacks = $this->paymentBankTransferRefferedBackRepository->all();

        return $this->sendResponse($paymentBankTransferRefferedBacks->toArray(), trans('custom.payment_bank_transfer_reffered_backs_retrieved_suc'));
    }

    /**
     * @param CreatePaymentBankTransferRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/paymentBankTransferRefferedBacks",
     *      summary="Store a newly created PaymentBankTransferRefferedBack in storage",
     *      tags={"PaymentBankTransferRefferedBack"},
     *      description="Store PaymentBankTransferRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PaymentBankTransferRefferedBack that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PaymentBankTransferRefferedBack")
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
     *                  ref="#/definitions/PaymentBankTransferRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePaymentBankTransferRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        $paymentBankTransferRefferedBacks = $this->paymentBankTransferRefferedBackRepository->create($input);

        return $this->sendResponse($paymentBankTransferRefferedBacks->toArray(), trans('custom.payment_bank_transfer_reffered_back_saved_successf'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/paymentBankTransferRefferedBacks/{id}",
     *      summary="Display the specified PaymentBankTransferRefferedBack",
     *      tags={"PaymentBankTransferRefferedBack"},
     *      description="Get PaymentBankTransferRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PaymentBankTransferRefferedBack",
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
     *                  ref="#/definitions/PaymentBankTransferRefferedBack"
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
        /** @var PaymentBankTransferRefferedBack $paymentBankTransferRefferedBack */
        $paymentBankTransfer = $this->paymentBankTransferRefferedBackRepository->with(['bank_account.currency', 'confirmed_by'])->findWithoutFail($id);

        if (empty($paymentBankTransfer)) {
            return $this->sendError(trans('custom.payment_bank_transfer_reffered_back_not_found'));
        }

        if (!empty($paymentBankTransfer)) {
            $confirmed = $paymentBankTransfer->confirmedYN;
        }

        $totalPaymentAmount = PaymentBankTransferDetailRefferedBack::where('companySystemID', $paymentBankTransfer->companySystemID)
            ->where('payAmountBank', '>', 0)
            ->where("bankAccountID", $paymentBankTransfer->bankAccountAutoID)
            ->where("timesReferred", $paymentBankTransfer->timesReferred)
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


        $totalPaymentClearedAmount = PaymentBankTransferDetailRefferedBack::where('companySystemID', $paymentBankTransfer->companySystemID)
            ->where('payAmountBank', '>', 0)
            ->where("bankAccountID", $paymentBankTransfer->bankAccountAutoID)
            ->where("timesReferred", $paymentBankTransfer->timesReferred)
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

        return $this->sendResponse($paymentBankTransfer->toArray(), trans('custom.payment_bank_transfer_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdatePaymentBankTransferRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/paymentBankTransferRefferedBacks/{id}",
     *      summary="Update the specified PaymentBankTransferRefferedBack in storage",
     *      tags={"PaymentBankTransferRefferedBack"},
     *      description="Update PaymentBankTransferRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PaymentBankTransferRefferedBack",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PaymentBankTransferRefferedBack that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PaymentBankTransferRefferedBack")
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
     *                  ref="#/definitions/PaymentBankTransferRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePaymentBankTransferRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        /** @var PaymentBankTransferRefferedBack $paymentBankTransferRefferedBack */
        $paymentBankTransferRefferedBack = $this->paymentBankTransferRefferedBackRepository->findWithoutFail($id);

        if (empty($paymentBankTransferRefferedBack)) {
            return $this->sendError(trans('custom.payment_bank_transfer_reffered_back_not_found'));
        }

        $paymentBankTransferRefferedBack = $this->paymentBankTransferRefferedBackRepository->update($input, $id);

        return $this->sendResponse($paymentBankTransferRefferedBack->toArray(), trans('custom.paymentbanktransferrefferedback_updated_successful'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/paymentBankTransferRefferedBacks/{id}",
     *      summary="Remove the specified PaymentBankTransferRefferedBack from storage",
     *      tags={"PaymentBankTransferRefferedBack"},
     *      description="Delete PaymentBankTransferRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PaymentBankTransferRefferedBack",
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
        /** @var PaymentBankTransferRefferedBack $paymentBankTransferRefferedBack */
        $paymentBankTransferRefferedBack = $this->paymentBankTransferRefferedBackRepository->findWithoutFail($id);

        if (empty($paymentBankTransferRefferedBack)) {
            return $this->sendError(trans('custom.payment_bank_transfer_reffered_back_not_found'));
        }

        $paymentBankTransferRefferedBack->delete();

        return $this->sendResponse($id, trans('custom.payment_bank_transfer_reffered_back_deleted_succes'));
    }


    public function getReferBackHistoryByBankTransfer(Request $request)
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

        $bankTransfer = PaymentBankTransferRefferedBack::whereIn('companySystemID', $subCompanies)
            ->where("paymentBankTransferID", $input['paymentBankTransferID'])
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
                        $query->orderBy('paymentBankTransferRefferedBackID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }
}
