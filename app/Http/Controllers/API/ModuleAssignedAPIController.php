<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateModuleAssignedAPIRequest;
use App\Http\Requests\API\UpdateModuleAssignedAPIRequest;
use App\Models\ModuleAssigned;
use App\Repositories\ModuleAssignedRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ModuleAssignedController
 * @package App\Http\Controllers\API
 */

class ModuleAssignedAPIController extends AppBaseController
{
    /** @var  ModuleAssignedRepository */
    private $moduleAssignedRepository;

    public function __construct(ModuleAssignedRepository $moduleAssignedRepo)
    {
        $this->moduleAssignedRepository = $moduleAssignedRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/moduleAssigneds",
     *      summary="Get a listing of the ModuleAssigneds.",
     *      tags={"ModuleAssigned"},
     *      description="Get all ModuleAssigneds",
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
     *                  @SWG\Items(ref="#/definitions/ModuleAssigned")
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
        $this->moduleAssignedRepository->pushCriteria(new RequestCriteria($request));
        $this->moduleAssignedRepository->pushCriteria(new LimitOffsetCriteria($request));
        $moduleAssigneds = $this->moduleAssignedRepository->all();

        return $this->sendResponse($moduleAssigneds->toArray(), trans('custom.module_assigneds_retrieved_successfully'));
    }

    /**
     * @param CreateModuleAssignedAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/moduleAssigneds",
     *      summary="Store a newly created ModuleAssigned in storage",
     *      tags={"ModuleAssigned"},
     *      description="Store ModuleAssigned",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ModuleAssigned that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ModuleAssigned")
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
     *                  ref="#/definitions/ModuleAssigned"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateModuleAssignedAPIRequest $request)
    {
        $input = $request->all();

        $moduleAssigned = $this->moduleAssignedRepository->create($input);

        return $this->sendResponse($moduleAssigned->toArray(), trans('custom.module_assigned_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/moduleAssigneds/{id}",
     *      summary="Display the specified ModuleAssigned",
     *      tags={"ModuleAssigned"},
     *      description="Get ModuleAssigned",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ModuleAssigned",
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
     *                  ref="#/definitions/ModuleAssigned"
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
        /** @var ModuleAssigned $moduleAssigned */
        $moduleAssigned = $this->moduleAssignedRepository->findWithoutFail($id);

        if (empty($moduleAssigned)) {
            return $this->sendError(trans('custom.module_assigned_not_found'));
        }

        return $this->sendResponse($moduleAssigned->toArray(), trans('custom.module_assigned_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateModuleAssignedAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/moduleAssigneds/{id}",
     *      summary="Update the specified ModuleAssigned in storage",
     *      tags={"ModuleAssigned"},
     *      description="Update ModuleAssigned",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ModuleAssigned",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ModuleAssigned that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ModuleAssigned")
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
     *                  ref="#/definitions/ModuleAssigned"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateModuleAssignedAPIRequest $request)
    {
        $input = $request->all();

        /** @var ModuleAssigned $moduleAssigned */
        $moduleAssigned = $this->moduleAssignedRepository->findWithoutFail($id);

        if (empty($moduleAssigned)) {
            return $this->sendError(trans('custom.module_assigned_not_found'));
        }

        $moduleAssigned = $this->moduleAssignedRepository->update($input, $id);

        return $this->sendResponse($moduleAssigned->toArray(), trans('custom.moduleassigned_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/moduleAssigneds/{id}",
     *      summary="Remove the specified ModuleAssigned from storage",
     *      tags={"ModuleAssigned"},
     *      description="Delete ModuleAssigned",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ModuleAssigned",
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
        /** @var ModuleAssigned $moduleAssigned */
        $moduleAssigned = $this->moduleAssignedRepository->findWithoutFail($id);

        if (empty($moduleAssigned)) {
            return $this->sendError(trans('custom.module_assigned_not_found'));
        }

        $moduleAssigned->delete();

        return $this->sendSuccess('Module Assigned deleted successfully');
    }
}
