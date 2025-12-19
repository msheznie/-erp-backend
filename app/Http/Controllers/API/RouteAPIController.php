<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateRouteAPIRequest;
use App\Http\Requests\API\UpdateRouteAPIRequest;
use App\Jobs\UpdateRoleRouteJob;
use App\Models\Route;
use App\Models\UserGroup;
use App\Repositories\RouteRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\helper\CommonJobService;

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

        return $this->sendResponse($routes->toArray(), trans('custom.routes_retrieved_successfully'));
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

        return $this->sendResponse($route->toArray(), trans('custom.route_saved_successfully'));
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
            return $this->sendError(trans('custom.route_not_found'));
        }

        return $this->sendResponse($route->toArray(), trans('custom.route_retrieved_successfully'));
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
            return $this->sendError(trans('custom.route_not_found'));
        }

        $route = $this->routeRepository->update($input, $id);

        return $this->sendResponse($route->toArray(), trans('custom.route_updated_successfully'));
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
            return $this->sendError(trans('custom.route_not_found'));
        }

        $route->delete();

        return $this->sendSuccess('Route deleted successfully');
    }


     public function updateRoutes(Request $request)
    {
        $app = app();
        
        $routes = $app->routes->getRoutes();


        if (env('IS_MULTI_TENANCY', false)) {
            $tenants = CommonJobService::tenant_list();
            if(count($tenants) == 0){
                return  "tenant list is empty";
            }


            foreach ($tenants as $tenant){
                $tenantDb = $tenant->database;

                CommonJobService::db_switch($tenantDb);

                Route::where('application', 0)->delete();

                foreach ($routes as $key => $value) {
                    $temp = [];
                    if (!is_null($value->getName()) && $value->getName() != "api.") {
                        $temp['name'] = $value->getName();
                        $temp['method'] = $value->methods()[0];
                        $temp['uri'] = $value->uri();
                        $temp['action'] = $value->getActionName();
                        $temp['application'] = 0;
                        
                        Route::create($temp);
                    }
                }
            }
        } else {
            Route::where('application', 0)->delete();

            foreach ($routes as $key => $value) {
                $temp = [];
                if (!is_null($value->getName()) && $value->getName() != "api.") {
                    $temp['name'] = $value->getName();
                    $temp['method'] = $value->methods()[0];
                    $temp['uri'] = $value->uri();
                    $temp['action'] = $value->getActionName();
                    $temp['application'] = 0;
                    
                    Route::create($temp);
                }
            }
        }

        return 'route table updated successfully';
    }


    public function updateRoleRoutes(Request $request)
    {

        if (env('IS_MULTI_TENANCY', false)) {
            $tenants = CommonJobService::tenant_list();
            if(count($tenants) == 0){
                return  "tenant list is empty";
            }


            foreach ($tenants as $tenant){
                $tenantDb = $tenant->database;

                CommonJobService::db_switch($tenantDb);

                $userGroups = UserGroup::where('isDeleted', 0)->get();

                foreach ($userGroups as $key => $value) {
                    UpdateRoleRouteJob::dispatch($tenantDb, $value->userGroupID);
                }

            }
        } else {
            $userGroups = UserGroup::where('isDeleted', 0)->get();
            foreach ($userGroups as $key => $value) {
                UpdateRoleRouteJob::dispatch("", $value->userGroupID);
            }
        }

        return 'role route table updated successfully';
    }
}
