<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePOSStagPaymentGlConfigAPIRequest;
use App\Http\Requests\API\UpdatePOSStagPaymentGlConfigAPIRequest;
use App\Models\POSStagPaymentGlConfig;
use App\Repositories\POSStagPaymentGlConfigRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class POSStagPaymentGlConfigController
 * @package App\Http\Controllers\API
 */

class POSStagPaymentGlConfigAPIController extends AppBaseController
{
    /** @var  POSStagPaymentGlConfigRepository */
    private $pOSStagPaymentGlConfigRepository;

    public function __construct(POSStagPaymentGlConfigRepository $pOSStagPaymentGlConfigRepo)
    {
        $this->pOSStagPaymentGlConfigRepository = $pOSStagPaymentGlConfigRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSStagPaymentGlConfigs",
     *      summary="Get a listing of the POSStagPaymentGlConfigs.",
     *      tags={"POSStagPaymentGlConfig"},
     *      description="Get all POSStagPaymentGlConfigs",
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
     *                  @SWG\Items(ref="#/definitions/POSStagPaymentGlConfig")
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
        $this->pOSStagPaymentGlConfigRepository->pushCriteria(new RequestCriteria($request));
        $this->pOSStagPaymentGlConfigRepository->pushCriteria(new LimitOffsetCriteria($request));
        $pOSStagPaymentGlConfigs = $this->pOSStagPaymentGlConfigRepository->all();

        return $this->sendResponse($pOSStagPaymentGlConfigs->toArray(), trans('custom.p_o_s_stag_payment_gl_configs_retrieved_successful'));
    }

    /**
     * @param CreatePOSStagPaymentGlConfigAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/pOSStagPaymentGlConfigs",
     *      summary="Store a newly created POSStagPaymentGlConfig in storage",
     *      tags={"POSStagPaymentGlConfig"},
     *      description="Store POSStagPaymentGlConfig",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSStagPaymentGlConfig that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSStagPaymentGlConfig")
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
     *                  ref="#/definitions/POSStagPaymentGlConfig"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePOSStagPaymentGlConfigAPIRequest $request)
    {
        $input = $request->all();

        $pOSStagPaymentGlConfig = $this->pOSStagPaymentGlConfigRepository->create($input);

        return $this->sendResponse($pOSStagPaymentGlConfig->toArray(), trans('custom.p_o_s_stag_payment_gl_config_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSStagPaymentGlConfigs/{id}",
     *      summary="Display the specified POSStagPaymentGlConfig",
     *      tags={"POSStagPaymentGlConfig"},
     *      description="Get POSStagPaymentGlConfig",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSStagPaymentGlConfig",
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
     *                  ref="#/definitions/POSStagPaymentGlConfig"
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
        /** @var POSStagPaymentGlConfig $pOSStagPaymentGlConfig */
        $pOSStagPaymentGlConfig = $this->pOSStagPaymentGlConfigRepository->findWithoutFail($id);

        if (empty($pOSStagPaymentGlConfig)) {
            return $this->sendError(trans('custom.p_o_s_stag_payment_gl_config_not_found'));
        }

        return $this->sendResponse($pOSStagPaymentGlConfig->toArray(), trans('custom.p_o_s_stag_payment_gl_config_retrieved_successfull'));
    }

    /**
     * @param int $id
     * @param UpdatePOSStagPaymentGlConfigAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/pOSStagPaymentGlConfigs/{id}",
     *      summary="Update the specified POSStagPaymentGlConfig in storage",
     *      tags={"POSStagPaymentGlConfig"},
     *      description="Update POSStagPaymentGlConfig",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSStagPaymentGlConfig",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSStagPaymentGlConfig that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSStagPaymentGlConfig")
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
     *                  ref="#/definitions/POSStagPaymentGlConfig"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePOSStagPaymentGlConfigAPIRequest $request)
    {
        $input = $request->all();

        /** @var POSStagPaymentGlConfig $pOSStagPaymentGlConfig */
        $pOSStagPaymentGlConfig = $this->pOSStagPaymentGlConfigRepository->findWithoutFail($id);

        if (empty($pOSStagPaymentGlConfig)) {
            return $this->sendError(trans('custom.p_o_s_stag_payment_gl_config_not_found'));
        }

        $pOSStagPaymentGlConfig = $this->pOSStagPaymentGlConfigRepository->update($input, $id);

        return $this->sendResponse($pOSStagPaymentGlConfig->toArray(), trans('custom.posstagpaymentglconfig_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/pOSStagPaymentGlConfigs/{id}",
     *      summary="Remove the specified POSStagPaymentGlConfig from storage",
     *      tags={"POSStagPaymentGlConfig"},
     *      description="Delete POSStagPaymentGlConfig",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSStagPaymentGlConfig",
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
        /** @var POSStagPaymentGlConfig $pOSStagPaymentGlConfig */
        $pOSStagPaymentGlConfig = $this->pOSStagPaymentGlConfigRepository->findWithoutFail($id);

        if (empty($pOSStagPaymentGlConfig)) {
            return $this->sendError(trans('custom.p_o_s_stag_payment_gl_config_not_found'));
        }

        $pOSStagPaymentGlConfig->delete();

        return $this->sendSuccess('P O S Stag Payment Gl Config deleted successfully');
    }
}
