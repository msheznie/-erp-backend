<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateThirdPartyIntegrationKeysAPIRequest;
use App\Http\Requests\API\UpdateThirdPartyIntegrationKeysAPIRequest;
use App\Models\ThirdPartyIntegrationKeys;
use App\Repositories\ThirdPartyIntegrationKeysRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ThirdPartyIntegrationKeysController
 * @package App\Http\Controllers\API
 */

class ThirdPartyIntegrationKeysAPIController extends AppBaseController
{
    /** @var  ThirdPartyIntegrationKeysRepository */
    private $thirdPartyIntegrationKeysRepository;

    public function __construct(ThirdPartyIntegrationKeysRepository $thirdPartyIntegrationKeysRepo)
    {
        $this->thirdPartyIntegrationKeysRepository = $thirdPartyIntegrationKeysRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/thirdPartyIntegrationKeys",
     *      summary="Get a listing of the ThirdPartyIntegrationKeys.",
     *      tags={"ThirdPartyIntegrationKeys"},
     *      description="Get all ThirdPartyIntegrationKeys",
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
     *                  @SWG\Items(ref="#/definitions/ThirdPartyIntegrationKeys")
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
        $this->thirdPartyIntegrationKeysRepository->pushCriteria(new RequestCriteria($request));
        $this->thirdPartyIntegrationKeysRepository->pushCriteria(new LimitOffsetCriteria($request));
        $thirdPartyIntegrationKeys = $this->thirdPartyIntegrationKeysRepository->all();

        return $this->sendResponse($thirdPartyIntegrationKeys->toArray(), trans('custom.third_party_integration_keys_retrieved_successfull'));
    }

    /**
     * @param CreateThirdPartyIntegrationKeysAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/thirdPartyIntegrationKeys",
     *      summary="Store a newly created ThirdPartyIntegrationKeys in storage",
     *      tags={"ThirdPartyIntegrationKeys"},
     *      description="Store ThirdPartyIntegrationKeys",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ThirdPartyIntegrationKeys that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ThirdPartyIntegrationKeys")
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
     *                  ref="#/definitions/ThirdPartyIntegrationKeys"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateThirdPartyIntegrationKeysAPIRequest $request)
    {
        $input = $request->all();

        $thirdPartyIntegrationKeys = $this->thirdPartyIntegrationKeysRepository->create($input);

        return $this->sendResponse($thirdPartyIntegrationKeys->toArray(), trans('custom.third_party_integration_keys_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/thirdPartyIntegrationKeys/{id}",
     *      summary="Display the specified ThirdPartyIntegrationKeys",
     *      tags={"ThirdPartyIntegrationKeys"},
     *      description="Get ThirdPartyIntegrationKeys",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ThirdPartyIntegrationKeys",
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
     *                  ref="#/definitions/ThirdPartyIntegrationKeys"
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
        /** @var ThirdPartyIntegrationKeys $thirdPartyIntegrationKeys */
        $thirdPartyIntegrationKeys = $this->thirdPartyIntegrationKeysRepository->findWithoutFail($id);

        if (empty($thirdPartyIntegrationKeys)) {
            return $this->sendError(trans('custom.third_party_integration_keys_not_found'));
        }

        return $this->sendResponse($thirdPartyIntegrationKeys->toArray(), trans('custom.third_party_integration_keys_retrieved_successfull'));
    }

    /**
     * @param int $id
     * @param UpdateThirdPartyIntegrationKeysAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/thirdPartyIntegrationKeys/{id}",
     *      summary="Update the specified ThirdPartyIntegrationKeys in storage",
     *      tags={"ThirdPartyIntegrationKeys"},
     *      description="Update ThirdPartyIntegrationKeys",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ThirdPartyIntegrationKeys",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ThirdPartyIntegrationKeys that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ThirdPartyIntegrationKeys")
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
     *                  ref="#/definitions/ThirdPartyIntegrationKeys"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateThirdPartyIntegrationKeysAPIRequest $request)
    {
        $input = $request->all();

        /** @var ThirdPartyIntegrationKeys $thirdPartyIntegrationKeys */
        $thirdPartyIntegrationKeys = $this->thirdPartyIntegrationKeysRepository->findWithoutFail($id);

        if (empty($thirdPartyIntegrationKeys)) {
            return $this->sendError(trans('custom.third_party_integration_keys_not_found'));
        }

        $thirdPartyIntegrationKeys = $this->thirdPartyIntegrationKeysRepository->update($input, $id);

        return $this->sendResponse($thirdPartyIntegrationKeys->toArray(), trans('custom.thirdpartyintegrationkeys_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/thirdPartyIntegrationKeys/{id}",
     *      summary="Remove the specified ThirdPartyIntegrationKeys from storage",
     *      tags={"ThirdPartyIntegrationKeys"},
     *      description="Delete ThirdPartyIntegrationKeys",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ThirdPartyIntegrationKeys",
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
        /** @var ThirdPartyIntegrationKeys $thirdPartyIntegrationKeys */
        $thirdPartyIntegrationKeys = $this->thirdPartyIntegrationKeysRepository->findWithoutFail($id);

        if (empty($thirdPartyIntegrationKeys)) {
            return $this->sendError(trans('custom.third_party_integration_keys_not_found'));
        }

        $thirdPartyIntegrationKeys->delete();

        return $this->sendSuccess('Third Party Integration Keys deleted successfully');
    }
}
