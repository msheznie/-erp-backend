<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateLptPermissionAPIRequest;
use App\Http\Requests\API\UpdateLptPermissionAPIRequest;
use App\Models\LptPermission;
use App\Repositories\LptPermissionRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class LptPermissionController
 * @package App\Http\Controllers\API
 */

class LptPermissionAPIController extends AppBaseController
{
    /** @var  LptPermissionRepository */
    private $lptPermissionRepository;

    public function __construct(LptPermissionRepository $lptPermissionRepo)
    {
        $this->lptPermissionRepository = $lptPermissionRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/lptPermissions",
     *      summary="Get a listing of the LptPermissions.",
     *      tags={"LptPermission"},
     *      description="Get all LptPermissions",
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
     *                  @SWG\Items(ref="#/definitions/LptPermission")
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
        $this->lptPermissionRepository->pushCriteria(new RequestCriteria($request));
        $this->lptPermissionRepository->pushCriteria(new LimitOffsetCriteria($request));
        $lptPermissions = $this->lptPermissionRepository->all();

        return $this->sendResponse($lptPermissions->toArray(), trans('custom.lpt_permissions_retrieved_successfully'));
    }

    /**
     * @param CreateLptPermissionAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/lptPermissions",
     *      summary="Store a newly created LptPermission in storage",
     *      tags={"LptPermission"},
     *      description="Store LptPermission",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="LptPermission that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/LptPermission")
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
     *                  ref="#/definitions/LptPermission"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateLptPermissionAPIRequest $request)
    {
        $input = $request->all();

        $lptPermission = $this->lptPermissionRepository->create($input);

        return $this->sendResponse($lptPermission->toArray(), trans('custom.lpt_permission_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/lptPermissions/{id}",
     *      summary="Display the specified LptPermission",
     *      tags={"LptPermission"},
     *      description="Get LptPermission",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of LptPermission",
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
     *                  ref="#/definitions/LptPermission"
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
        /** @var LptPermission $lptPermission */
        $lptPermission = $this->lptPermissionRepository->findWithoutFail($id);

        if (empty($lptPermission)) {
            return $this->sendError(trans('custom.lpt_permission_not_found'));
        }

        return $this->sendResponse($lptPermission->toArray(), trans('custom.lpt_permission_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateLptPermissionAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/lptPermissions/{id}",
     *      summary="Update the specified LptPermission in storage",
     *      tags={"LptPermission"},
     *      description="Update LptPermission",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of LptPermission",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="LptPermission that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/LptPermission")
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
     *                  ref="#/definitions/LptPermission"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateLptPermissionAPIRequest $request)
    {
        $input = $request->all();

        /** @var LptPermission $lptPermission */
        $lptPermission = $this->lptPermissionRepository->findWithoutFail($id);

        if (empty($lptPermission)) {
            return $this->sendError(trans('custom.lpt_permission_not_found'));
        }

        $lptPermission = $this->lptPermissionRepository->update($input, $id);

        return $this->sendResponse($lptPermission->toArray(), trans('custom.lptpermission_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/lptPermissions/{id}",
     *      summary="Remove the specified LptPermission from storage",
     *      tags={"LptPermission"},
     *      description="Delete LptPermission",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of LptPermission",
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
        /** @var LptPermission $lptPermission */
        $lptPermission = $this->lptPermissionRepository->findWithoutFail($id);

        if (empty($lptPermission)) {
            return $this->sendError(trans('custom.lpt_permission_not_found'));
        }

        $lptPermission->delete();

        return $this->sendResponse($id, trans('custom.lpt_permission_deleted_successfully'));
    }
}
