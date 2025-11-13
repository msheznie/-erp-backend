<?php
/**
 * =============================================
 * -- File Name : GposInvoicePaymentsAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  General pos invoice payments
 * -- Author : Mohamed Fayas
 * -- Create date : 22 - January 2019
 * -- Description : This file contains the all CRUD for  General pos invoice payments
 * -- REVISION HISTORY
 * -- Date: 22 - January 2019 By: Fayas Description: Added new function
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateGposInvoicePaymentsAPIRequest;
use App\Http\Requests\API\UpdateGposInvoicePaymentsAPIRequest;
use App\Models\GposInvoicePayments;
use App\Repositories\GposInvoicePaymentsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class GposInvoicePaymentsController
 * @package App\Http\Controllers\API
 */

class GposInvoicePaymentsAPIController extends AppBaseController
{
    /** @var  GposInvoicePaymentsRepository */
    private $gposInvoicePaymentsRepository;

    public function __construct(GposInvoicePaymentsRepository $gposInvoicePaymentsRepo)
    {
        $this->gposInvoicePaymentsRepository = $gposInvoicePaymentsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/gposInvoicePayments",
     *      summary="Get a listing of the GposInvoicePayments.",
     *      tags={"GposInvoicePayments"},
     *      description="Get all GposInvoicePayments",
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
     *                  @SWG\Items(ref="#/definitions/GposInvoicePayments")
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
        $this->gposInvoicePaymentsRepository->pushCriteria(new RequestCriteria($request));
        $this->gposInvoicePaymentsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $gposInvoicePayments = $this->gposInvoicePaymentsRepository->all();

        return $this->sendResponse($gposInvoicePayments->toArray(), trans('custom.gpos_invoice_payments_retrieved_successfully'));
    }

    /**
     * @param CreateGposInvoicePaymentsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/gposInvoicePayments",
     *      summary="Store a newly created GposInvoicePayments in storage",
     *      tags={"GposInvoicePayments"},
     *      description="Store GposInvoicePayments",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="GposInvoicePayments that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/GposInvoicePayments")
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
     *                  ref="#/definitions/GposInvoicePayments"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateGposInvoicePaymentsAPIRequest $request)
    {
        $input = $request->all();

        $gposInvoicePayments = $this->gposInvoicePaymentsRepository->create($input);

        return $this->sendResponse($gposInvoicePayments->toArray(), trans('custom.gpos_invoice_payments_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/gposInvoicePayments/{id}",
     *      summary="Display the specified GposInvoicePayments",
     *      tags={"GposInvoicePayments"},
     *      description="Get GposInvoicePayments",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of GposInvoicePayments",
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
     *                  ref="#/definitions/GposInvoicePayments"
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
        /** @var GposInvoicePayments $gposInvoicePayments */
        $gposInvoicePayments = $this->gposInvoicePaymentsRepository->findWithoutFail($id);

        if (empty($gposInvoicePayments)) {
            return $this->sendError(trans('custom.gpos_invoice_payments_not_found'));
        }

        return $this->sendResponse($gposInvoicePayments->toArray(), trans('custom.gpos_invoice_payments_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateGposInvoicePaymentsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/gposInvoicePayments/{id}",
     *      summary="Update the specified GposInvoicePayments in storage",
     *      tags={"GposInvoicePayments"},
     *      description="Update GposInvoicePayments",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of GposInvoicePayments",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="GposInvoicePayments that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/GposInvoicePayments")
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
     *                  ref="#/definitions/GposInvoicePayments"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateGposInvoicePaymentsAPIRequest $request)
    {
        $input = $request->all();

        /** @var GposInvoicePayments $gposInvoicePayments */
        $gposInvoicePayments = $this->gposInvoicePaymentsRepository->findWithoutFail($id);

        if (empty($gposInvoicePayments)) {
            return $this->sendError(trans('custom.gpos_invoice_payments_not_found'));
        }

        $gposInvoicePayments = $this->gposInvoicePaymentsRepository->update($input, $id);

        return $this->sendResponse($gposInvoicePayments->toArray(), trans('custom.gposinvoicepayments_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/gposInvoicePayments/{id}",
     *      summary="Remove the specified GposInvoicePayments from storage",
     *      tags={"GposInvoicePayments"},
     *      description="Delete GposInvoicePayments",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of GposInvoicePayments",
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
        /** @var GposInvoicePayments $gposInvoicePayments */
        $gposInvoicePayments = $this->gposInvoicePaymentsRepository->findWithoutFail($id);

        if (empty($gposInvoicePayments)) {
            return $this->sendError(trans('custom.gpos_invoice_payments_not_found'));
        }

        $gposInvoicePayments->delete();

        return $this->sendResponse($id, trans('custom.gpos_invoice_payments_deleted_successfully'));
    }
}
