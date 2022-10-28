<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateRouteAPIRequest;
use App\Http\Requests\API\UpdateRouteAPIRequest;
use App\Models\Route;
use App\Repositories\RouteRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class RouteController
 * @package App\Http\Controllers\API
 */

class RouteAPIController extends AppBaseController
{
    /** @var  RouteRepository */
    private $routeRepository;

    public function __construct(RouteRepository $routeRepo)
    {
        $this->routeRepository = $routeRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/routes",
     *      summary="getRouteList",
     *      tags={"Route"},
     *      description="Get all Routes",
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
     *                  @OA\Items(ref="#/definitions/Route")
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
        $this->routeRepository->pushCriteria(new RequestCriteria($request));
        $this->routeRepository->pushCriteria(new LimitOffsetCriteria($request));
        $routes = $this->routeRepository->all();

        return $this->sendResponse($routes->toArray(), 'Routes retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/routes",
     *      summary="createRoute",
     *      tags={"Route"},
     *      description="Create Route",
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
     *                  ref="#/definitions/Route"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateRouteAPIRequest $request)
    {
        $input = $request->all();

        $route = $this->routeRepository->create($input);

        return $this->sendResponse($route->toArray(), 'Route saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/routes/{id}",
     *      summary="getRouteItem",
     *      tags={"Route"},
     *      description="Get Route",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of Route",
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
     *                  ref="#/definitions/Route"
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
        /** @var Route $route */
        $route = $this->routeRepository->findWithoutFail($id);

        if (empty($route)) {
            return $this->sendError('Route not found');
        }

        return $this->sendResponse($route->toArray(), 'Route retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/routes/{id}",
     *      summary="updateRoute",
     *      tags={"Route"},
     *      description="Update Route",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of Route",
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
     *                  ref="#/definitions/Route"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateRouteAPIRequest $request)
    {
        $input = $request->all();

        /** @var Route $route */
        $route = $this->routeRepository->findWithoutFail($id);

        if (empty($route)) {
            return $this->sendError('Route not found');
        }

        $route = $this->routeRepository->update($input, $id);

        return $this->sendResponse($route->toArray(), 'Route updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/routes/{id}",
     *      summary="deleteRoute",
     *      tags={"Route"},
     *      description="Delete Route",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of Route",
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
        /** @var Route $route */
        $route = $this->routeRepository->findWithoutFail($id);

        if (empty($route)) {
            return $this->sendError('Route not found');
        }

        $route->delete();

        return $this->sendSuccess('Route deleted successfully');
    }
}
