<?php
/**
 * =============================================
 * -- File Name : DirectPaymentReferbackAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Direct Payment Referback
 * -- Author : Mohamed Nazir
 * -- Create date : 21 - November 2018
 * -- Description : This file contains the all CRUD for Direct Payment Referback
 * -- REVISION HISTORY
 * -- Date: 21-November 2018 By: Nazir Description: Added new function getDirectPaymentDetailsHistoryByID(),
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDirectPaymentReferbackAPIRequest;
use App\Http\Requests\API\UpdateDirectPaymentReferbackAPIRequest;
use App\Models\CurrencyConversion;
use App\Models\DirectPaymentReferback;
use App\Repositories\DirectPaymentReferbackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class DirectPaymentReferbackController
 * @package App\Http\Controllers\API
 */
class DirectPaymentReferbackAPIController extends AppBaseController
{
    /** @var  DirectPaymentReferbackRepository */
    private $directPaymentReferbackRepository;

    public function __construct(DirectPaymentReferbackRepository $directPaymentReferbackRepo)
    {
        $this->directPaymentReferbackRepository = $directPaymentReferbackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/directPaymentReferbacks",
     *      summary="Get a listing of the DirectPaymentReferbacks.",
     *      tags={"DirectPaymentReferback"},
     *      description="Get all DirectPaymentReferbacks",
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
     *                  @SWG\Items(ref="#/definitions/DirectPaymentReferback")
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
        $this->directPaymentReferbackRepository->pushCriteria(new RequestCriteria($request));
        $this->directPaymentReferbackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $directPaymentReferbacks = $this->directPaymentReferbackRepository->all();

        return $this->sendResponse($directPaymentReferbacks->toArray(), trans('custom.direct_payment_referbacks_retrieved_successfully'));
    }

    /**
     * @param CreateDirectPaymentReferbackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/directPaymentReferbacks",
     *      summary="Store a newly created DirectPaymentReferback in storage",
     *      tags={"DirectPaymentReferback"},
     *      description="Store DirectPaymentReferback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DirectPaymentReferback that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DirectPaymentReferback")
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
     *                  ref="#/definitions/DirectPaymentReferback"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateDirectPaymentReferbackAPIRequest $request)
    {
        $input = $request->all();

        $directPaymentReferbacks = $this->directPaymentReferbackRepository->create($input);

        return $this->sendResponse($directPaymentReferbacks->toArray(), trans('custom.direct_payment_referback_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/directPaymentReferbacks/{id}",
     *      summary="Display the specified DirectPaymentReferback",
     *      tags={"DirectPaymentReferback"},
     *      description="Get DirectPaymentReferback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DirectPaymentReferback",
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
     *                  ref="#/definitions/DirectPaymentReferback"
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
        /** @var DirectPaymentReferback $directPaymentReferback */
        $directPaymentReferback = $this->directPaymentReferbackRepository->findWithoutFail($id);

        if (empty($directPaymentReferback)) {
            return $this->sendError(trans('custom.direct_payment_referback_not_found'));
        }

        return $this->sendResponse($directPaymentReferback->toArray(), trans('custom.direct_payment_referback_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateDirectPaymentReferbackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/directPaymentReferbacks/{id}",
     *      summary="Update the specified DirectPaymentReferback in storage",
     *      tags={"DirectPaymentReferback"},
     *      description="Update DirectPaymentReferback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DirectPaymentReferback",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DirectPaymentReferback that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DirectPaymentReferback")
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
     *                  ref="#/definitions/DirectPaymentReferback"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateDirectPaymentReferbackAPIRequest $request)
    {
        $input = $request->all();

        /** @var DirectPaymentReferback $directPaymentReferback */
        $directPaymentReferback = $this->directPaymentReferbackRepository->findWithoutFail($id);

        if (empty($directPaymentReferback)) {
            return $this->sendError(trans('custom.direct_payment_referback_not_found'));
        }

        $directPaymentReferback = $this->directPaymentReferbackRepository->update($input, $id);

        return $this->sendResponse($directPaymentReferback->toArray(), trans('custom.directpaymentreferback_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/directPaymentReferbacks/{id}",
     *      summary="Remove the specified DirectPaymentReferback from storage",
     *      tags={"DirectPaymentReferback"},
     *      description="Delete DirectPaymentReferback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DirectPaymentReferback",
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
        /** @var DirectPaymentReferback $directPaymentReferback */
        $directPaymentReferback = $this->directPaymentReferbackRepository->findWithoutFail($id);

        if (empty($directPaymentReferback)) {
            return $this->sendError(trans('custom.direct_payment_referback_not_found'));
        }

        $directPaymentReferback->delete();

        return $this->sendResponse($id, trans('custom.direct_payment_referback_deleted_successfully'));
    }

    public function getDirectPaymentHistoryDetails(Request $request)
    {
        $id = $request->PayMasterAutoId;

        $directPaymentDetails = $this->directPaymentReferbackRepository->with(['segment', 'chartofaccount'])->findWhere(['directPaymentAutoID' => $id, 'timesReferred' => $request->timesReferred]);

        return $this->sendResponse($directPaymentDetails, trans('custom.details_retrieved_successfully'));
    }

    public function getDPHistoryExchangeRateAmount(Request $request)
    {
        $directPaymentDetails = DirectPaymentReferback::where('directPaymentDetailsID', $request->directPaymentDetailsID)->where('timesReferred', $request->timesReferred)->first();

        if (empty($directPaymentDetails)) {
            return $this->sendError(trans('custom.direct_payment_details_not_found'));
        }

        if ($request->toBankCurrencyID) {

            $conversion = CurrencyConversion::where('masterCurrencyID', $directPaymentDetails->bankCurrencyID)->where('subCurrencyID', $request->toBankCurrencyID)->first();
            $conversion = $conversion->conversion;

            $bankAmount = 0;
            if ($request->toBankCurrencyID == $directPaymentDetails->bankCurrencyID) {
                $bankAmount = $directPaymentDetails->DPAmount;
            } else {
                if ($conversion > $directPaymentDetails->DPAmountCurrencyER) {
                    if ($conversion > 1) {
                        $bankAmount = $directPaymentDetails->DPAmount / $conversion;
                    } else {
                        $bankAmount = $directPaymentDetails->DPAmount * $conversion;
                    }
                } else {
                    If ($conversion > 1) {
                        $bankAmount = $directPaymentDetails->DPAmount * $conversion;
                    } else {
                        $bankAmount = $directPaymentDetails->DPAmount / $conversion;
                    }
                }
            }

            $output = ['toBankCurrencyER' => $conversion, 'toBankAmount' => $bankAmount];
            return $this->sendResponse($output, trans('custom.successfully_data_retrieved'));
        } else {
            $output = ['toBankCurrencyER' => 0, 'toBankAmount' => 0];
            return $this->sendResponse($output, trans('custom.successfully_data_retrieved'));
        }
    }

    public function getDirectPaymentDetailsHistoryByID(Request $request)
    {
        $directPaymentDetails = DirectPaymentReferback::where('directPaymentDetailsID', $request->directPaymentDetailsID)->where('timesReferred', $request->timesReferred)->first();

        if (empty($directPaymentDetails)) {
            return $this->sendError(trans('custom.direct_payment_details_not_found'));
        }

        return $this->sendResponse($directPaymentDetails->toArray(), trans('custom.direct_payment_details_retrieved_successfully'));
    }
}
