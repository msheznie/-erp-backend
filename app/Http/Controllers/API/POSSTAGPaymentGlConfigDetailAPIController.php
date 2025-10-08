<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePOSSTAGPaymentGlConfigDetailAPIRequest;
use App\Http\Requests\API\UpdatePOSSTAGPaymentGlConfigDetailAPIRequest;
use App\Models\POSSTAGPaymentGlConfigDetail;
use App\Repositories\POSSTAGPaymentGlConfigDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class POSSTAGPaymentGlConfigDetailController
 * @package App\Http\Controllers\API
 */

class POSSTAGPaymentGlConfigDetailAPIController extends AppBaseController
{
    /** @var  POSSTAGPaymentGlConfigDetailRepository */
    private $pOSSTAGPaymentGlConfigDetailRepository;

    public function __construct(POSSTAGPaymentGlConfigDetailRepository $pOSSTAGPaymentGlConfigDetailRepo)
    {
        $this->pOSSTAGPaymentGlConfigDetailRepository = $pOSSTAGPaymentGlConfigDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSSTAGPaymentGlConfigDetails",
     *      summary="Get a listing of the POSSTAGPaymentGlConfigDetails.",
     *      tags={"POSSTAGPaymentGlConfigDetail"},
     *      description="Get all POSSTAGPaymentGlConfigDetails",
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
     *                  @SWG\Items(ref="#/definitions/POSSTAGPaymentGlConfigDetail")
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
        $this->pOSSTAGPaymentGlConfigDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->pOSSTAGPaymentGlConfigDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $pOSSTAGPaymentGlConfigDetails = $this->pOSSTAGPaymentGlConfigDetailRepository->all();

        return $this->sendResponse($pOSSTAGPaymentGlConfigDetails->toArray(), trans('custom.p_o_s_s_t_a_g_payment_gl_config_details_retrieved_'));
    }

    /**
     * @param CreatePOSSTAGPaymentGlConfigDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/pOSSTAGPaymentGlConfigDetails",
     *      summary="Store a newly created POSSTAGPaymentGlConfigDetail in storage",
     *      tags={"POSSTAGPaymentGlConfigDetail"},
     *      description="Store POSSTAGPaymentGlConfigDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSSTAGPaymentGlConfigDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSSTAGPaymentGlConfigDetail")
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
     *                  ref="#/definitions/POSSTAGPaymentGlConfigDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePOSSTAGPaymentGlConfigDetailAPIRequest $request)
    {
        $input = $request->all();

        $pOSSTAGPaymentGlConfigDetail = $this->pOSSTAGPaymentGlConfigDetailRepository->create($input);

        return $this->sendResponse($pOSSTAGPaymentGlConfigDetail->toArray(), trans('custom.p_o_s_s_t_a_g_payment_gl_config_detail_saved_succe'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSSTAGPaymentGlConfigDetails/{id}",
     *      summary="Display the specified POSSTAGPaymentGlConfigDetail",
     *      tags={"POSSTAGPaymentGlConfigDetail"},
     *      description="Get POSSTAGPaymentGlConfigDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSTAGPaymentGlConfigDetail",
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
     *                  ref="#/definitions/POSSTAGPaymentGlConfigDetail"
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
        /** @var POSSTAGPaymentGlConfigDetail $pOSSTAGPaymentGlConfigDetail */
        $pOSSTAGPaymentGlConfigDetail = $this->pOSSTAGPaymentGlConfigDetailRepository->findWithoutFail($id);

        if (empty($pOSSTAGPaymentGlConfigDetail)) {
            return $this->sendError(trans('custom.p_o_s_s_t_a_g_payment_gl_config_detail_not_found'));
        }

        return $this->sendResponse($pOSSTAGPaymentGlConfigDetail->toArray(), trans('custom.p_o_s_s_t_a_g_payment_gl_config_detail_retrieved_s'));
    }

    /**
     * @param int $id
     * @param UpdatePOSSTAGPaymentGlConfigDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/pOSSTAGPaymentGlConfigDetails/{id}",
     *      summary="Update the specified POSSTAGPaymentGlConfigDetail in storage",
     *      tags={"POSSTAGPaymentGlConfigDetail"},
     *      description="Update POSSTAGPaymentGlConfigDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSTAGPaymentGlConfigDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSSTAGPaymentGlConfigDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSSTAGPaymentGlConfigDetail")
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
     *                  ref="#/definitions/POSSTAGPaymentGlConfigDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePOSSTAGPaymentGlConfigDetailAPIRequest $request)
    {
        $input = $request->all();

        /** @var POSSTAGPaymentGlConfigDetail $pOSSTAGPaymentGlConfigDetail */
        $pOSSTAGPaymentGlConfigDetail = $this->pOSSTAGPaymentGlConfigDetailRepository->findWithoutFail($id);

        if (empty($pOSSTAGPaymentGlConfigDetail)) {
            return $this->sendError(trans('custom.p_o_s_s_t_a_g_payment_gl_config_detail_not_found'));
        }

        $pOSSTAGPaymentGlConfigDetail = $this->pOSSTAGPaymentGlConfigDetailRepository->update($input, $id);

        return $this->sendResponse($pOSSTAGPaymentGlConfigDetail->toArray(), trans('custom.posstagpaymentglconfigdetail_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/pOSSTAGPaymentGlConfigDetails/{id}",
     *      summary="Remove the specified POSSTAGPaymentGlConfigDetail from storage",
     *      tags={"POSSTAGPaymentGlConfigDetail"},
     *      description="Delete POSSTAGPaymentGlConfigDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSTAGPaymentGlConfigDetail",
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
        /** @var POSSTAGPaymentGlConfigDetail $pOSSTAGPaymentGlConfigDetail */
        $pOSSTAGPaymentGlConfigDetail = $this->pOSSTAGPaymentGlConfigDetailRepository->findWithoutFail($id);

        if (empty($pOSSTAGPaymentGlConfigDetail)) {
            return $this->sendError(trans('custom.p_o_s_s_t_a_g_payment_gl_config_detail_not_found'));
        }

        $pOSSTAGPaymentGlConfigDetail->delete();

        return $this->sendSuccess('P O S S T A G Payment Gl Config Detail deleted successfully');
    }
}
