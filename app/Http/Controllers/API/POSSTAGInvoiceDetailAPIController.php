<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePOSSTAGInvoiceDetailAPIRequest;
use App\Http\Requests\API\UpdatePOSSTAGInvoiceDetailAPIRequest;
use App\Models\POSSTAGInvoiceDetail;
use App\Repositories\POSSTAGInvoiceDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class POSSTAGInvoiceDetailController
 * @package App\Http\Controllers\API
 */

class POSSTAGInvoiceDetailAPIController extends AppBaseController
{
    /** @var  POSSTAGInvoiceDetailRepository */
    private $pOSSTAGInvoiceDetailRepository;

    public function __construct(POSSTAGInvoiceDetailRepository $pOSSTAGInvoiceDetailRepo)
    {
        $this->pOSSTAGInvoiceDetailRepository = $pOSSTAGInvoiceDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSSTAGInvoiceDetails",
     *      summary="Get a listing of the POSSTAGInvoiceDetails.",
     *      tags={"POSSTAGInvoiceDetail"},
     *      description="Get all POSSTAGInvoiceDetails",
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
     *                  @SWG\Items(ref="#/definitions/POSSTAGInvoiceDetail")
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
        $this->pOSSTAGInvoiceDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->pOSSTAGInvoiceDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $pOSSTAGInvoiceDetails = $this->pOSSTAGInvoiceDetailRepository->all();

        return $this->sendResponse($pOSSTAGInvoiceDetails->toArray(), trans('custom.p_o_s_s_t_a_g_invoice_details_retrieved_successful'));
    }

    /**
     * @param CreatePOSSTAGInvoiceDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/pOSSTAGInvoiceDetails",
     *      summary="Store a newly created POSSTAGInvoiceDetail in storage",
     *      tags={"POSSTAGInvoiceDetail"},
     *      description="Store POSSTAGInvoiceDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSSTAGInvoiceDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSSTAGInvoiceDetail")
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
     *                  ref="#/definitions/POSSTAGInvoiceDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePOSSTAGInvoiceDetailAPIRequest $request)
    {
        $input = $request->all();

        $pOSSTAGInvoiceDetail = $this->pOSSTAGInvoiceDetailRepository->create($input);

        return $this->sendResponse($pOSSTAGInvoiceDetail->toArray(), trans('custom.p_o_s_s_t_a_g_invoice_detail_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSSTAGInvoiceDetails/{id}",
     *      summary="Display the specified POSSTAGInvoiceDetail",
     *      tags={"POSSTAGInvoiceDetail"},
     *      description="Get POSSTAGInvoiceDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSTAGInvoiceDetail",
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
     *                  ref="#/definitions/POSSTAGInvoiceDetail"
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
        /** @var POSSTAGInvoiceDetail $pOSSTAGInvoiceDetail */
        $pOSSTAGInvoiceDetail = $this->pOSSTAGInvoiceDetailRepository->findWithoutFail($id);

        if (empty($pOSSTAGInvoiceDetail)) {
            return $this->sendError(trans('custom.p_o_s_s_t_a_g_invoice_detail_not_found'));
        }

        return $this->sendResponse($pOSSTAGInvoiceDetail->toArray(), trans('custom.p_o_s_s_t_a_g_invoice_detail_retrieved_successfull'));
    }

    /**
     * @param int $id
     * @param UpdatePOSSTAGInvoiceDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/pOSSTAGInvoiceDetails/{id}",
     *      summary="Update the specified POSSTAGInvoiceDetail in storage",
     *      tags={"POSSTAGInvoiceDetail"},
     *      description="Update POSSTAGInvoiceDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSTAGInvoiceDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSSTAGInvoiceDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSSTAGInvoiceDetail")
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
     *                  ref="#/definitions/POSSTAGInvoiceDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePOSSTAGInvoiceDetailAPIRequest $request)
    {
        $input = $request->all();

        /** @var POSSTAGInvoiceDetail $pOSSTAGInvoiceDetail */
        $pOSSTAGInvoiceDetail = $this->pOSSTAGInvoiceDetailRepository->findWithoutFail($id);

        if (empty($pOSSTAGInvoiceDetail)) {
            return $this->sendError(trans('custom.p_o_s_s_t_a_g_invoice_detail_not_found'));
        }

        $pOSSTAGInvoiceDetail = $this->pOSSTAGInvoiceDetailRepository->update($input, $id);

        return $this->sendResponse($pOSSTAGInvoiceDetail->toArray(), trans('custom.posstaginvoicedetail_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/pOSSTAGInvoiceDetails/{id}",
     *      summary="Remove the specified POSSTAGInvoiceDetail from storage",
     *      tags={"POSSTAGInvoiceDetail"},
     *      description="Delete POSSTAGInvoiceDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSTAGInvoiceDetail",
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
        /** @var POSSTAGInvoiceDetail $pOSSTAGInvoiceDetail */
        $pOSSTAGInvoiceDetail = $this->pOSSTAGInvoiceDetailRepository->findWithoutFail($id);

        if (empty($pOSSTAGInvoiceDetail)) {
            return $this->sendError(trans('custom.p_o_s_s_t_a_g_invoice_detail_not_found'));
        }

        $pOSSTAGInvoiceDetail->delete();

        return $this->sendSuccess('P O S S T A G Invoice Detail deleted successfully');
    }
}
