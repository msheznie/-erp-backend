<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePOSSTAGInvoiceAPIRequest;
use App\Http\Requests\API\UpdatePOSSTAGInvoiceAPIRequest;
use App\Models\POSSTAGInvoice;
use App\Repositories\POSSTAGInvoiceRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class POSSTAGInvoiceController
 * @package App\Http\Controllers\API
 */

class POSSTAGInvoiceAPIController extends AppBaseController
{
    /** @var  POSSTAGInvoiceRepository */
    private $pOSSTAGInvoiceRepository;

    public function __construct(POSSTAGInvoiceRepository $pOSSTAGInvoiceRepo)
    {
        $this->pOSSTAGInvoiceRepository = $pOSSTAGInvoiceRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSSTAGInvoices",
     *      summary="Get a listing of the POSSTAGInvoices.",
     *      tags={"POSSTAGInvoice"},
     *      description="Get all POSSTAGInvoices",
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
     *                  @SWG\Items(ref="#/definitions/POSSTAGInvoice")
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
        $this->pOSSTAGInvoiceRepository->pushCriteria(new RequestCriteria($request));
        $this->pOSSTAGInvoiceRepository->pushCriteria(new LimitOffsetCriteria($request));
        $pOSSTAGInvoices = $this->pOSSTAGInvoiceRepository->all();

        return $this->sendResponse($pOSSTAGInvoices->toArray(), trans('custom.p_o_s_s_t_a_g_invoices_retrieved_successfully'));
    }

    /**
     * @param CreatePOSSTAGInvoiceAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/pOSSTAGInvoices",
     *      summary="Store a newly created POSSTAGInvoice in storage",
     *      tags={"POSSTAGInvoice"},
     *      description="Store POSSTAGInvoice",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSSTAGInvoice that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSSTAGInvoice")
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
     *                  ref="#/definitions/POSSTAGInvoice"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePOSSTAGInvoiceAPIRequest $request)
    {
        $input = $request->all();

        $pOSSTAGInvoice = $this->pOSSTAGInvoiceRepository->create($input);

        return $this->sendResponse($pOSSTAGInvoice->toArray(), trans('custom.p_o_s_s_t_a_g_invoice_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSSTAGInvoices/{id}",
     *      summary="Display the specified POSSTAGInvoice",
     *      tags={"POSSTAGInvoice"},
     *      description="Get POSSTAGInvoice",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSTAGInvoice",
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
     *                  ref="#/definitions/POSSTAGInvoice"
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
        /** @var POSSTAGInvoice $pOSSTAGInvoice */
        $pOSSTAGInvoice = $this->pOSSTAGInvoiceRepository->findWithoutFail($id);

        if (empty($pOSSTAGInvoice)) {
            return $this->sendError(trans('custom.p_o_s_s_t_a_g_invoice_not_found'));
        }

        return $this->sendResponse($pOSSTAGInvoice->toArray(), trans('custom.p_o_s_s_t_a_g_invoice_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdatePOSSTAGInvoiceAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/pOSSTAGInvoices/{id}",
     *      summary="Update the specified POSSTAGInvoice in storage",
     *      tags={"POSSTAGInvoice"},
     *      description="Update POSSTAGInvoice",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSTAGInvoice",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSSTAGInvoice that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSSTAGInvoice")
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
     *                  ref="#/definitions/POSSTAGInvoice"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePOSSTAGInvoiceAPIRequest $request)
    {
        $input = $request->all();

        /** @var POSSTAGInvoice $pOSSTAGInvoice */
        $pOSSTAGInvoice = $this->pOSSTAGInvoiceRepository->findWithoutFail($id);

        if (empty($pOSSTAGInvoice)) {
            return $this->sendError(trans('custom.p_o_s_s_t_a_g_invoice_not_found'));
        }

        $pOSSTAGInvoice = $this->pOSSTAGInvoiceRepository->update($input, $id);

        return $this->sendResponse($pOSSTAGInvoice->toArray(), trans('custom.posstaginvoice_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/pOSSTAGInvoices/{id}",
     *      summary="Remove the specified POSSTAGInvoice from storage",
     *      tags={"POSSTAGInvoice"},
     *      description="Delete POSSTAGInvoice",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSTAGInvoice",
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
        /** @var POSSTAGInvoice $pOSSTAGInvoice */
        $pOSSTAGInvoice = $this->pOSSTAGInvoiceRepository->findWithoutFail($id);

        if (empty($pOSSTAGInvoice)) {
            return $this->sendError(trans('custom.p_o_s_s_t_a_g_invoice_not_found'));
        }

        $pOSSTAGInvoice->delete();

        return $this->sendSuccess('P O S S T A G Invoice deleted successfully');
    }
}
