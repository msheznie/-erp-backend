<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateNavigationRouteAPIRequest;
use App\Http\Requests\API\UpdateNavigationRouteAPIRequest;
use App\Models\NavigationRoute;
use App\Repositories\NavigationRouteRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class NavigationRouteController
 * @package App\Http\Controllers\API
 */

class NavigationRouteAPIController extends AppBaseController
{
    /** @var  NavigationRouteRepository */
    private $navigationRouteRepository;

    public function __construct(NavigationRouteRepository $navigationRouteRepo)
    {
        $this->navigationRouteRepository = $navigationRouteRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/navigationRoutes",
     *      summary="getNavigationRouteList",
     *      tags={"NavigationRoute"},
     *      description="Get all NavigationRoutes",
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
     *                  @OA\Items(ref="#/definitions/NavigationRoute")
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
        $this->navigationRouteRepository->pushCriteria(new RequestCriteria($request));
        $this->navigationRouteRepository->pushCriteria(new LimitOffsetCriteria($request));
        $navigationRoutes = $this->navigationRouteRepository->all();

        return $this->sendResponse($navigationRoutes->toArray(), trans('custom.navigation_routes_retrieved_successfully'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/navigationRoutes",
     *      summary="createNavigationRoute",
     *      tags={"NavigationRoute"},
     *      description="Create NavigationRoute",
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
     *                  ref="#/definitions/NavigationRoute"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateNavigationRouteAPIRequest $request)
    {
        $input = $request->all();

        $navigationRoute = $this->navigationRouteRepository->create($input);

        return $this->sendResponse($navigationRoute->toArray(), trans('custom.navigation_route_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/navigationRoutes/{id}",
     *      summary="getNavigationRouteItem",
     *      tags={"NavigationRoute"},
     *      description="Get NavigationRoute",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of NavigationRoute",
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
     *                  ref="#/definitions/NavigationRoute"
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
        /** @var NavigationRoute $navigationRoute */
        $navigationRoute = $this->navigationRouteRepository->findWithoutFail($id);

        if (empty($navigationRoute)) {
            return $this->sendError(trans('custom.navigation_route_not_found'));
        }

        return $this->sendResponse($navigationRoute->toArray(), trans('custom.navigation_route_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/navigationRoutes/{id}",
     *      summary="updateNavigationRoute",
     *      tags={"NavigationRoute"},
     *      description="Update NavigationRoute",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of NavigationRoute",
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
     *                  ref="#/definitions/NavigationRoute"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateNavigationRouteAPIRequest $request)
    {
        $input = $request->all();

        /** @var NavigationRoute $navigationRoute */
        $navigationRoute = $this->navigationRouteRepository->findWithoutFail($id);

        if (empty($navigationRoute)) {
            return $this->sendError(trans('custom.navigation_route_not_found'));
        }

        $navigationRoute = $this->navigationRouteRepository->update($input, $id);

        return $this->sendResponse($navigationRoute->toArray(), trans('custom.navigationroute_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/navigationRoutes/{id}",
     *      summary="deleteNavigationRoute",
     *      tags={"NavigationRoute"},
     *      description="Delete NavigationRoute",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of NavigationRoute",
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
        /** @var NavigationRoute $navigationRoute */
        $navigationRoute = $this->navigationRouteRepository->findWithoutFail($id);

        if (empty($navigationRoute)) {
            return $this->sendError(trans('custom.navigation_route_not_found'));
        }

        $navigationRoute->delete();

        return $this->sendSuccess('Navigation Route deleted successfully');
    }
}
