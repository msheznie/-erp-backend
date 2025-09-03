<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateHRFeatureFlagsAPIRequest;
use App\Http\Requests\API\UpdateHRFeatureFlagsAPIRequest;
use App\Models\HRFeatureFlags;
use App\Repositories\HRFeatureFlagsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class HRFeatureFlagsController
 * @package App\Http\Controllers\API
 */

class HRFeatureFlagsAPIController extends AppBaseController
{
    /** @var  HRFeatureFlagsRepository */
    private $hRFeatureFlagsRepository;

    public function __construct(HRFeatureFlagsRepository $hRFeatureFlagsRepo)
    {
        $this->hRFeatureFlagsRepository = $hRFeatureFlagsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/hRFeatureFlags",
     *      summary="getHRFeatureFlagsList",
     *      tags={"HRFeatureFlags"},
     *      description="Get all HRFeatureFlags",
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(ref="#/definitions/HRFeatureFlags")
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->hRFeatureFlagsRepository->pushCriteria(new RequestCriteria($request));
        $this->hRFeatureFlagsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $hRFeatureFlags = $this->hRFeatureFlagsRepository->all();

        return $this->sendResponse($hRFeatureFlags->toArray(), 'H R Feature Flags retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/hRFeatureFlags",
     *      summary="createHRFeatureFlags",
     *      tags={"HRFeatureFlags"},
     *      description="Create HRFeatureFlags",
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *            @OA\Schema(
     *                type="object",
     *                required={""},
     *                @OA\Property(
     *                    property="name",
     *                    description="desc",
     *                    type="string"
     *                )
     *            )
     *        )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/HRFeatureFlags"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateHRFeatureFlagsAPIRequest $request)
    {
        $input = $request->all();

        $hRFeatureFlags = $this->hRFeatureFlagsRepository->create($input);

        return $this->sendResponse($hRFeatureFlags->toArray(), 'H R Feature Flags saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/hRFeatureFlags/{id}",
     *      summary="getHRFeatureFlagsItem",
     *      tags={"HRFeatureFlags"},
     *      description="Get HRFeatureFlags",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of HRFeatureFlags",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/HRFeatureFlags"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var HRFeatureFlags $hRFeatureFlags */
        $hRFeatureFlags = $this->hRFeatureFlagsRepository->findWithoutFail($id);

        if (empty($hRFeatureFlags)) {
            return $this->sendError('H R Feature Flags not found');
        }

        return $this->sendResponse($hRFeatureFlags->toArray(), 'H R Feature Flags retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/hRFeatureFlags/{id}",
     *      summary="updateHRFeatureFlags",
     *      tags={"HRFeatureFlags"},
     *      description="Update HRFeatureFlags",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of HRFeatureFlags",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *            @OA\Schema(
     *                type="object",
     *                required={""},
     *                @OA\Property(
     *                    property="name",
     *                    description="desc",
     *                    type="string"
     *                )
     *            )
     *        )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/HRFeatureFlags"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateHRFeatureFlagsAPIRequest $request)
    {
        $input = $request->all();

        /** @var HRFeatureFlags $hRFeatureFlags */
        $hRFeatureFlags = $this->hRFeatureFlagsRepository->findWithoutFail($id);

        if (empty($hRFeatureFlags)) {
            return $this->sendError('H R Feature Flags not found');
        }

        $hRFeatureFlags = $this->hRFeatureFlagsRepository->update($input, $id);

        return $this->sendResponse($hRFeatureFlags->toArray(), 'HRFeatureFlags updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/hRFeatureFlags/{id}",
     *      summary="deleteHRFeatureFlags",
     *      tags={"HRFeatureFlags"},
     *      description="Delete HRFeatureFlags",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of HRFeatureFlags",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var HRFeatureFlags $hRFeatureFlags */
        $hRFeatureFlags = $this->hRFeatureFlagsRepository->findWithoutFail($id);

        if (empty($hRFeatureFlags)) {
            return $this->sendError('H R Feature Flags not found');
        }

        $hRFeatureFlags->delete();

        return $this->sendSuccess('H R Feature Flags deleted successfully');
    }
}
