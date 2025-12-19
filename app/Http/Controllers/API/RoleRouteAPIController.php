<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateRoleRouteAPIRequest;
use App\Http\Requests\API\UpdateRoleRouteAPIRequest;
use App\Models\RoleRoute;
use App\Repositories\RoleRouteRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class RoleRouteController
 * @package App\Http\Controllers\API
 */

class RoleRouteAPIController extends AppBaseController
{
    /** @var  RoleRouteRepository */
    private $roleRouteRepository;

    public function __construct(RoleRouteRepository $roleRouteRepo)
    {
        $this->roleRouteRepository = $roleRouteRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/roleRoutes",
     *      summary="getRoleRouteList",
     *      tags={"RoleRoute"},
     *      description="Get all RoleRoutes",
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
     *                  @OA\Items(ref="#/definitions/RoleRoute")
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
        $this->roleRouteRepository->pushCriteria(new RequestCriteria($request));
        $this->roleRouteRepository->pushCriteria(new LimitOffsetCriteria($request));
        $roleRoutes = $this->roleRouteRepository->all();

        return $this->sendResponse($roleRoutes->toArray(), trans('custom.role_routes_retrieved_successfully'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/roleRoutes",
     *      summary="createRoleRoute",
     *      tags={"RoleRoute"},
     *      description="Create RoleRoute",
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
     *                  ref="#/definitions/RoleRoute"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateRoleRouteAPIRequest $request)
    {
        $input = $request->all();

        $roleRoute = $this->roleRouteRepository->create($input);

        return $this->sendResponse($roleRoute->toArray(), trans('custom.role_route_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/roleRoutes/{id}",
     *      summary="getRoleRouteItem",
     *      tags={"RoleRoute"},
     *      description="Get RoleRoute",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of RoleRoute",
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
     *                  ref="#/definitions/RoleRoute"
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
        /** @var RoleRoute $roleRoute */
        $roleRoute = $this->roleRouteRepository->findWithoutFail($id);

        if (empty($roleRoute)) {
            return $this->sendError(trans('custom.role_route_not_found'));
        }

        return $this->sendResponse($roleRoute->toArray(), trans('custom.role_route_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/roleRoutes/{id}",
     *      summary="updateRoleRoute",
     *      tags={"RoleRoute"},
     *      description="Update RoleRoute",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of RoleRoute",
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
     *                  ref="#/definitions/RoleRoute"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateRoleRouteAPIRequest $request)
    {
        $input = $request->all();

        /** @var RoleRoute $roleRoute */
        $roleRoute = $this->roleRouteRepository->findWithoutFail($id);

        if (empty($roleRoute)) {
            return $this->sendError(trans('custom.role_route_not_found'));
        }

        $roleRoute = $this->roleRouteRepository->update($input, $id);

        return $this->sendResponse($roleRoute->toArray(), trans('custom.roleroute_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/roleRoutes/{id}",
     *      summary="deleteRoleRoute",
     *      tags={"RoleRoute"},
     *      description="Delete RoleRoute",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of RoleRoute",
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
        /** @var RoleRoute $roleRoute */
        $roleRoute = $this->roleRouteRepository->findWithoutFail($id);

        if (empty($roleRoute)) {
            return $this->sendError(trans('custom.role_route_not_found'));
        }

        $roleRoute->delete();

        return $this->sendSuccess('Role Route deleted successfully');
    }
}
