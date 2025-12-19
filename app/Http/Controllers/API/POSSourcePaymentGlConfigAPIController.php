<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePOSSourcePaymentGlConfigAPIRequest;
use App\Http\Requests\API\UpdatePOSSourcePaymentGlConfigAPIRequest;
use App\Models\POSSourcePaymentGlConfig;
use App\Repositories\POSSourcePaymentGlConfigRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class POSSourcePaymentGlConfigController
 * @package App\Http\Controllers\API
 */

class POSSourcePaymentGlConfigAPIController extends AppBaseController
{
    /** @var  POSSourcePaymentGlConfigRepository */
    private $pOSSourcePaymentGlConfigRepository;

    public function __construct(POSSourcePaymentGlConfigRepository $pOSSourcePaymentGlConfigRepo)
    {
        $this->pOSSourcePaymentGlConfigRepository = $pOSSourcePaymentGlConfigRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSSourcePaymentGlConfigs",
     *      summary="Get a listing of the POSSourcePaymentGlConfigs.",
     *      tags={"POSSourcePaymentGlConfig"},
     *      description="Get all POSSourcePaymentGlConfigs",
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
     *                  @SWG\Items(ref="#/definitions/POSSourcePaymentGlConfig")
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
        $this->pOSSourcePaymentGlConfigRepository->pushCriteria(new RequestCriteria($request));
        $this->pOSSourcePaymentGlConfigRepository->pushCriteria(new LimitOffsetCriteria($request));
        $pOSSourcePaymentGlConfigs = $this->pOSSourcePaymentGlConfigRepository->all();

        return $this->sendResponse($pOSSourcePaymentGlConfigs->toArray(), trans('custom.p_o_s_source_payment_gl_configs_retrieved_successf'));
    }

    /**
     * @param CreatePOSSourcePaymentGlConfigAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/pOSSourcePaymentGlConfigs",
     *      summary="Store a newly created POSSourcePaymentGlConfig in storage",
     *      tags={"POSSourcePaymentGlConfig"},
     *      description="Store POSSourcePaymentGlConfig",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSSourcePaymentGlConfig that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSSourcePaymentGlConfig")
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
     *                  ref="#/definitions/POSSourcePaymentGlConfig"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePOSSourcePaymentGlConfigAPIRequest $request)
    {
        $input = $request->all();

        $pOSSourcePaymentGlConfig = $this->pOSSourcePaymentGlConfigRepository->create($input);

        return $this->sendResponse($pOSSourcePaymentGlConfig->toArray(), trans('custom.p_o_s_source_payment_gl_config_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSSourcePaymentGlConfigs/{id}",
     *      summary="Display the specified POSSourcePaymentGlConfig",
     *      tags={"POSSourcePaymentGlConfig"},
     *      description="Get POSSourcePaymentGlConfig",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSourcePaymentGlConfig",
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
     *                  ref="#/definitions/POSSourcePaymentGlConfig"
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
        /** @var POSSourcePaymentGlConfig $pOSSourcePaymentGlConfig */
        $pOSSourcePaymentGlConfig = $this->pOSSourcePaymentGlConfigRepository->findWithoutFail($id);

        if (empty($pOSSourcePaymentGlConfig)) {
            return $this->sendError(trans('custom.p_o_s_source_payment_gl_config_not_found'));
        }

        return $this->sendResponse($pOSSourcePaymentGlConfig->toArray(), trans('custom.p_o_s_source_payment_gl_config_retrieved_successfu'));
    }

    /**
     * @param int $id
     * @param UpdatePOSSourcePaymentGlConfigAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/pOSSourcePaymentGlConfigs/{id}",
     *      summary="Update the specified POSSourcePaymentGlConfig in storage",
     *      tags={"POSSourcePaymentGlConfig"},
     *      description="Update POSSourcePaymentGlConfig",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSourcePaymentGlConfig",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSSourcePaymentGlConfig that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSSourcePaymentGlConfig")
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
     *                  ref="#/definitions/POSSourcePaymentGlConfig"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePOSSourcePaymentGlConfigAPIRequest $request)
    {
        $input = $request->all();

        /** @var POSSourcePaymentGlConfig $pOSSourcePaymentGlConfig */
        $pOSSourcePaymentGlConfig = $this->pOSSourcePaymentGlConfigRepository->findWithoutFail($id);

        if (empty($pOSSourcePaymentGlConfig)) {
            return $this->sendError(trans('custom.p_o_s_source_payment_gl_config_not_found'));
        }

        $pOSSourcePaymentGlConfig = $this->pOSSourcePaymentGlConfigRepository->update($input, $id);

        return $this->sendResponse($pOSSourcePaymentGlConfig->toArray(), trans('custom.possourcepaymentglconfig_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/pOSSourcePaymentGlConfigs/{id}",
     *      summary="Remove the specified POSSourcePaymentGlConfig from storage",
     *      tags={"POSSourcePaymentGlConfig"},
     *      description="Delete POSSourcePaymentGlConfig",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSourcePaymentGlConfig",
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
        /** @var POSSourcePaymentGlConfig $pOSSourcePaymentGlConfig */
        $pOSSourcePaymentGlConfig = $this->pOSSourcePaymentGlConfigRepository->findWithoutFail($id);

        if (empty($pOSSourcePaymentGlConfig)) {
            return $this->sendError(trans('custom.p_o_s_source_payment_gl_config_not_found'));
        }

        $pOSSourcePaymentGlConfig->delete();

        return $this->sendSuccess('P O S Source Payment Gl Config deleted successfully');
    }
}
