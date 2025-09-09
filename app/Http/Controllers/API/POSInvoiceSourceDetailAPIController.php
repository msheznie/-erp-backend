<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePOSInvoiceSourceDetailAPIRequest;
use App\Http\Requests\API\UpdatePOSInvoiceSourceDetailAPIRequest;
use App\Models\POSInvoiceSourceDetail;
use App\Repositories\POSInvoiceSourceDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class POSInvoiceSourceDetailController
 * @package App\Http\Controllers\API
 */

class POSInvoiceSourceDetailAPIController extends AppBaseController
{
    /** @var  POSInvoiceSourceDetailRepository */
    private $pOSInvoiceSourceDetailRepository;

    public function __construct(POSInvoiceSourceDetailRepository $pOSInvoiceSourceDetailRepo)
    {
        $this->pOSInvoiceSourceDetailRepository = $pOSInvoiceSourceDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSInvoiceSourceDetails",
     *      summary="Get a listing of the POSInvoiceSourceDetails.",
     *      tags={"POSInvoiceSourceDetail"},
     *      description="Get all POSInvoiceSourceDetails",
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
     *                  @SWG\Items(ref="#/definitions/POSInvoiceSourceDetail")
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
        $this->pOSInvoiceSourceDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->pOSInvoiceSourceDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $pOSInvoiceSourceDetails = $this->pOSInvoiceSourceDetailRepository->all();

        return $this->sendResponse($pOSInvoiceSourceDetails->toArray(), trans('custom.p_o_s_invoice_source_details_retrieved_successfull'));
    }

    /**
     * @param CreatePOSInvoiceSourceDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/pOSInvoiceSourceDetails",
     *      summary="Store a newly created POSInvoiceSourceDetail in storage",
     *      tags={"POSInvoiceSourceDetail"},
     *      description="Store POSInvoiceSourceDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSInvoiceSourceDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSInvoiceSourceDetail")
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
     *                  ref="#/definitions/POSInvoiceSourceDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePOSInvoiceSourceDetailAPIRequest $request)
    {
        $input = $request->all();

        $pOSInvoiceSourceDetail = $this->pOSInvoiceSourceDetailRepository->create($input);

        return $this->sendResponse($pOSInvoiceSourceDetail->toArray(), trans('custom.p_o_s_invoice_source_detail_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSInvoiceSourceDetails/{id}",
     *      summary="Display the specified POSInvoiceSourceDetail",
     *      tags={"POSInvoiceSourceDetail"},
     *      description="Get POSInvoiceSourceDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSInvoiceSourceDetail",
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
     *                  ref="#/definitions/POSInvoiceSourceDetail"
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
        /** @var POSInvoiceSourceDetail $pOSInvoiceSourceDetail */
        $pOSInvoiceSourceDetail = $this->pOSInvoiceSourceDetailRepository->findWithoutFail($id);

        if (empty($pOSInvoiceSourceDetail)) {
            return $this->sendError(trans('custom.p_o_s_invoice_source_detail_not_found'));
        }

        return $this->sendResponse($pOSInvoiceSourceDetail->toArray(), trans('custom.p_o_s_invoice_source_detail_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdatePOSInvoiceSourceDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/pOSInvoiceSourceDetails/{id}",
     *      summary="Update the specified POSInvoiceSourceDetail in storage",
     *      tags={"POSInvoiceSourceDetail"},
     *      description="Update POSInvoiceSourceDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSInvoiceSourceDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSInvoiceSourceDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSInvoiceSourceDetail")
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
     *                  ref="#/definitions/POSInvoiceSourceDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePOSInvoiceSourceDetailAPIRequest $request)
    {
        $input = $request->all();

        /** @var POSInvoiceSourceDetail $pOSInvoiceSourceDetail */
        $pOSInvoiceSourceDetail = $this->pOSInvoiceSourceDetailRepository->findWithoutFail($id);

        if (empty($pOSInvoiceSourceDetail)) {
            return $this->sendError(trans('custom.p_o_s_invoice_source_detail_not_found'));
        }

        $pOSInvoiceSourceDetail = $this->pOSInvoiceSourceDetailRepository->update($input, $id);

        return $this->sendResponse($pOSInvoiceSourceDetail->toArray(), trans('custom.posinvoicesourcedetail_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/pOSInvoiceSourceDetails/{id}",
     *      summary="Remove the specified POSInvoiceSourceDetail from storage",
     *      tags={"POSInvoiceSourceDetail"},
     *      description="Delete POSInvoiceSourceDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSInvoiceSourceDetail",
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
        /** @var POSInvoiceSourceDetail $pOSInvoiceSourceDetail */
        $pOSInvoiceSourceDetail = $this->pOSInvoiceSourceDetailRepository->findWithoutFail($id);

        if (empty($pOSInvoiceSourceDetail)) {
            return $this->sendError(trans('custom.p_o_s_invoice_source_detail_not_found'));
        }

        $pOSInvoiceSourceDetail->delete();

        return $this->sendSuccess('P O S Invoice Source Detail deleted successfully');
    }
}
