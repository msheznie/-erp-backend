<?php
/**
 * =============================================
 * -- File Name : PoPaymentTermsRefferedbackAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Po Payment Terms Reffered back
 * -- Author : Mohamed Nazir
 * -- Create date : 30 - July 2018
 * -- Description : This file contains the all CRUD for Po Payment Terms Reffered back
 * -- REVISION HISTORY
 * -- Date: 30-July 2018 By: Nazir Description: Added new function getPoPaymentTermsForAmendHistory(),
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePoPaymentTermsRefferedbackAPIRequest;
use App\Http\Requests\API\UpdatePoPaymentTermsRefferedbackAPIRequest;
use App\Models\PoPaymentTermsRefferedback;
use App\Repositories\PoPaymentTermsRefferedbackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\DB;
use Response;

/**
 * Class PoPaymentTermsRefferedbackController
 * @package App\Http\Controllers\API
 */

class PoPaymentTermsRefferedbackAPIController extends AppBaseController
{
    /** @var  PoPaymentTermsRefferedbackRepository */
    private $poPaymentTermsRefferedbackRepository;

    public function __construct(PoPaymentTermsRefferedbackRepository $poPaymentTermsRefferedbackRepo)
    {
        $this->poPaymentTermsRefferedbackRepository = $poPaymentTermsRefferedbackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/poPaymentTermsRefferedbacks",
     *      summary="Get a listing of the PoPaymentTermsRefferedbacks.",
     *      tags={"PoPaymentTermsRefferedback"},
     *      description="Get all PoPaymentTermsRefferedbacks",
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
     *                  @SWG\Items(ref="#/definitions/PoPaymentTermsRefferedback")
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
        $this->poPaymentTermsRefferedbackRepository->pushCriteria(new RequestCriteria($request));
        $this->poPaymentTermsRefferedbackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $poPaymentTermsRefferedbacks = $this->poPaymentTermsRefferedbackRepository->all();

        return $this->sendResponse($poPaymentTermsRefferedbacks->toArray(), trans('custom.po_payment_terms_refferedbacks_retrieved_successfu'));
    }

    /**
     * @param CreatePoPaymentTermsRefferedbackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/poPaymentTermsRefferedbacks",
     *      summary="Store a newly created PoPaymentTermsRefferedback in storage",
     *      tags={"PoPaymentTermsRefferedback"},
     *      description="Store PoPaymentTermsRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PoPaymentTermsRefferedback that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PoPaymentTermsRefferedback")
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
     *                  ref="#/definitions/PoPaymentTermsRefferedback"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePoPaymentTermsRefferedbackAPIRequest $request)
    {
        $input = $request->all();

        $poPaymentTermsRefferedbacks = $this->poPaymentTermsRefferedbackRepository->create($input);

        return $this->sendResponse($poPaymentTermsRefferedbacks->toArray(), trans('custom.po_payment_terms_refferedback_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/poPaymentTermsRefferedbacks/{id}",
     *      summary="Display the specified PoPaymentTermsRefferedback",
     *      tags={"PoPaymentTermsRefferedback"},
     *      description="Get PoPaymentTermsRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PoPaymentTermsRefferedback",
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
     *                  ref="#/definitions/PoPaymentTermsRefferedback"
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
        /** @var PoPaymentTermsRefferedback $poPaymentTermsRefferedback */
        $poPaymentTermsRefferedback = $this->poPaymentTermsRefferedbackRepository->findWithoutFail($id);

        if (empty($poPaymentTermsRefferedback)) {
            return $this->sendError(trans('custom.po_payment_terms_refferedback_not_found'));
        }

        return $this->sendResponse($poPaymentTermsRefferedback->toArray(), trans('custom.po_payment_terms_refferedback_retrieved_successful'));
    }

    /**
     * @param int $id
     * @param UpdatePoPaymentTermsRefferedbackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/poPaymentTermsRefferedbacks/{id}",
     *      summary="Update the specified PoPaymentTermsRefferedback in storage",
     *      tags={"PoPaymentTermsRefferedback"},
     *      description="Update PoPaymentTermsRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PoPaymentTermsRefferedback",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PoPaymentTermsRefferedback that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PoPaymentTermsRefferedback")
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
     *                  ref="#/definitions/PoPaymentTermsRefferedback"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePoPaymentTermsRefferedbackAPIRequest $request)
    {
        $input = $request->all();

        /** @var PoPaymentTermsRefferedback $poPaymentTermsRefferedback */
        $poPaymentTermsRefferedback = $this->poPaymentTermsRefferedbackRepository->findWithoutFail($id);

        if (empty($poPaymentTermsRefferedback)) {
            return $this->sendError(trans('custom.po_payment_terms_refferedback_not_found'));
        }

        $poPaymentTermsRefferedback = $this->poPaymentTermsRefferedbackRepository->update($input, $id);

        return $this->sendResponse($poPaymentTermsRefferedback->toArray(), trans('custom.popaymenttermsrefferedback_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/poPaymentTermsRefferedbacks/{id}",
     *      summary="Remove the specified PoPaymentTermsRefferedback from storage",
     *      tags={"PoPaymentTermsRefferedback"},
     *      description="Delete PoPaymentTermsRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PoPaymentTermsRefferedback",
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
        /** @var PoPaymentTermsRefferedback $poPaymentTermsRefferedback */
        $poPaymentTermsRefferedback = $this->poPaymentTermsRefferedbackRepository->findWithoutFail($id);

        if (empty($poPaymentTermsRefferedback)) {
            return $this->sendError(trans('custom.po_payment_terms_refferedback_not_found'));
        }

        $poPaymentTermsRefferedback->delete();

        return $this->sendResponse($id, trans('custom.po_payment_terms_refferedback_deleted_successfully'));
    }

    public function getPoPaymentTermsForAmendHistory(Request $request)
    {
        $input = $request->all();
        $timesReferred = $input['timesReferred'];

        $poAdvancePaymentType = PoPaymentTermsRefferedback::select(DB::raw('*, DATE_FORMAT(comDate, "%d/%m/%Y") as comDate'))
            ->where('poID', $input['purchaseOrderID'])
            ->where('timesReferred', $timesReferred)
            ->orderBy('paymentTermID', 'ASC')
            ->get();

        return $this->sendResponse($poAdvancePaymentType->toArray(), trans('custom.data_retrieved_successfully'));
    }
}
