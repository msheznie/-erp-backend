<?php
/**
 * =============================================
 * -- File Name : DirectInvoiceDetailsRefferedBackAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Direct Invoice Details Reffered Back
 * -- Author : Mohamed Nazir
 * -- Create date : 01 - October 2018
 * -- Description : This file contains the all CRUD for Direct Invoice Details Reffered Back
 * -- REVISION HISTORY
 * -- Date: 01-October 2018 By: Nazir Description: Added new function getSIDetailDirectAmendHistory(),
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDirectInvoiceDetailsRefferedBackAPIRequest;
use App\Http\Requests\API\UpdateDirectInvoiceDetailsRefferedBackAPIRequest;
use App\Models\DirectInvoiceDetailsRefferedBack;
use App\Repositories\DirectInvoiceDetailsRefferedBackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class DirectInvoiceDetailsRefferedBackController
 * @package App\Http\Controllers\API
 */
class DirectInvoiceDetailsRefferedBackAPIController extends AppBaseController
{
    /** @var  DirectInvoiceDetailsRefferedBackRepository */
    private $directInvoiceDetailsRefferedBackRepository;

    public function __construct(DirectInvoiceDetailsRefferedBackRepository $directInvoiceDetailsRefferedBackRepo)
    {
        $this->directInvoiceDetailsRefferedBackRepository = $directInvoiceDetailsRefferedBackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/directInvoiceDetailsRefferedBacks",
     *      summary="Get a listing of the DirectInvoiceDetailsRefferedBacks.",
     *      tags={"DirectInvoiceDetailsRefferedBack"},
     *      description="Get all DirectInvoiceDetailsRefferedBacks",
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
     *                  @SWG\Items(ref="#/definitions/DirectInvoiceDetailsRefferedBack")
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
        $this->directInvoiceDetailsRefferedBackRepository->pushCriteria(new RequestCriteria($request));
        $this->directInvoiceDetailsRefferedBackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $directInvoiceDetailsRefferedBacks = $this->directInvoiceDetailsRefferedBackRepository->all();

        return $this->sendResponse($directInvoiceDetailsRefferedBacks->toArray(), trans('custom.direct_invoice_details_reffered_backs_retrieved_su'));
    }

    /**
     * @param CreateDirectInvoiceDetailsRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/directInvoiceDetailsRefferedBacks",
     *      summary="Store a newly created DirectInvoiceDetailsRefferedBack in storage",
     *      tags={"DirectInvoiceDetailsRefferedBack"},
     *      description="Store DirectInvoiceDetailsRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DirectInvoiceDetailsRefferedBack that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DirectInvoiceDetailsRefferedBack")
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
     *                  ref="#/definitions/DirectInvoiceDetailsRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateDirectInvoiceDetailsRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        $directInvoiceDetailsRefferedBacks = $this->directInvoiceDetailsRefferedBackRepository->create($input);

        return $this->sendResponse($directInvoiceDetailsRefferedBacks->toArray(), trans('custom.direct_invoice_details_reffered_back_saved_success'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/directInvoiceDetailsRefferedBacks/{id}",
     *      summary="Display the specified DirectInvoiceDetailsRefferedBack",
     *      tags={"DirectInvoiceDetailsRefferedBack"},
     *      description="Get DirectInvoiceDetailsRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DirectInvoiceDetailsRefferedBack",
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
     *                  ref="#/definitions/DirectInvoiceDetailsRefferedBack"
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
        /** @var DirectInvoiceDetailsRefferedBack $directInvoiceDetailsRefferedBack */
        $directInvoiceDetailsRefferedBack = $this->directInvoiceDetailsRefferedBackRepository->findWithoutFail($id);

        if (empty($directInvoiceDetailsRefferedBack)) {
            return $this->sendError(trans('custom.direct_invoice_details_reffered_back_not_found'));
        }

        return $this->sendResponse($directInvoiceDetailsRefferedBack->toArray(), trans('custom.direct_invoice_details_reffered_back_retrieved_suc'));
    }

    /**
     * @param int $id
     * @param UpdateDirectInvoiceDetailsRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/directInvoiceDetailsRefferedBacks/{id}",
     *      summary="Update the specified DirectInvoiceDetailsRefferedBack in storage",
     *      tags={"DirectInvoiceDetailsRefferedBack"},
     *      description="Update DirectInvoiceDetailsRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DirectInvoiceDetailsRefferedBack",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DirectInvoiceDetailsRefferedBack that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DirectInvoiceDetailsRefferedBack")
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
     *                  ref="#/definitions/DirectInvoiceDetailsRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateDirectInvoiceDetailsRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        /** @var DirectInvoiceDetailsRefferedBack $directInvoiceDetailsRefferedBack */
        $directInvoiceDetailsRefferedBack = $this->directInvoiceDetailsRefferedBackRepository->findWithoutFail($id);

        if (empty($directInvoiceDetailsRefferedBack)) {
            return $this->sendError(trans('custom.direct_invoice_details_reffered_back_not_found'));
        }

        $directInvoiceDetailsRefferedBack = $this->directInvoiceDetailsRefferedBackRepository->update($input, $id);

        return $this->sendResponse($directInvoiceDetailsRefferedBack->toArray(), trans('custom.directinvoicedetailsrefferedback_updated_successfu'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/directInvoiceDetailsRefferedBacks/{id}",
     *      summary="Remove the specified DirectInvoiceDetailsRefferedBack from storage",
     *      tags={"DirectInvoiceDetailsRefferedBack"},
     *      description="Delete DirectInvoiceDetailsRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DirectInvoiceDetailsRefferedBack",
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
        /** @var DirectInvoiceDetailsRefferedBack $directInvoiceDetailsRefferedBack */
        $directInvoiceDetailsRefferedBack = $this->directInvoiceDetailsRefferedBackRepository->findWithoutFail($id);

        if (empty($directInvoiceDetailsRefferedBack)) {
            return $this->sendError(trans('custom.direct_invoice_details_reffered_back_not_found'));
        }

        $directInvoiceDetailsRefferedBack->delete();

        return $this->sendResponse($id, trans('custom.direct_invoice_details_reffered_back_deleted_succe'));
    }

    public function getSIDetailDirectAmendHistory(Request $request)
    {
        $input = $request->all();
        $directInvoiceAutoID = $input['directInvoiceAutoID'];
        $timesReferred = $input['timesReferred'];

        $items = DirectInvoiceDetailsRefferedBack::where('directInvoiceAutoID', $directInvoiceAutoID)
            ->where('timesReferred', $timesReferred)
            ->with(['segment', 'chartofaccount'])
            ->get();

        return $this->sendResponse($items->toArray(), trans('custom.purchase_order_details_reffered_history_retrieved_'));
    }
}
