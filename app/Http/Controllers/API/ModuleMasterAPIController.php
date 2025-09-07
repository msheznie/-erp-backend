<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateModuleMasterAPIRequest;
use App\Http\Requests\API\UpdateModuleMasterAPIRequest;
use App\Models\ModuleMaster;
use App\Repositories\ModuleMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ModuleMasterController
 * @package App\Http\Controllers\API
 */

class ModuleMasterAPIController extends AppBaseController
{
    /** @var  ModuleMasterRepository */
    private $moduleMasterRepository;

    public function __construct(ModuleMasterRepository $moduleMasterRepo)
    {
        $this->moduleMasterRepository = $moduleMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/moduleMasters",
     *      summary="Get a listing of the ModuleMasters.",
     *      tags={"ModuleMaster"},
     *      description="Get all ModuleMasters",
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
     *                  @SWG\Items(ref="#/definitions/ModuleMaster")
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
        $this->moduleMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->moduleMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $moduleMasters = $this->moduleMasterRepository->all();

        return $this->sendResponse($moduleMasters->toArray(), trans('custom.module_masters_retrieved_successfully'));
    }

    /**
     * @param CreateModuleMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/moduleMasters",
     *      summary="Store a newly created ModuleMaster in storage",
     *      tags={"ModuleMaster"},
     *      description="Store ModuleMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ModuleMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ModuleMaster")
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
     *                  ref="#/definitions/ModuleMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateModuleMasterAPIRequest $request)
    {
        $input = $request->all();

        $moduleMaster = $this->moduleMasterRepository->create($input);

        return $this->sendResponse($moduleMaster->toArray(), trans('custom.module_master_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/moduleMasters/{id}",
     *      summary="Display the specified ModuleMaster",
     *      tags={"ModuleMaster"},
     *      description="Get ModuleMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ModuleMaster",
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
     *                  ref="#/definitions/ModuleMaster"
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
        /** @var ModuleMaster $moduleMaster */
        $moduleMaster = $this->moduleMasterRepository->findWithoutFail($id);

        if (empty($moduleMaster)) {
            return $this->sendError(trans('custom.module_master_not_found'));
        }

        return $this->sendResponse($moduleMaster->toArray(), trans('custom.module_master_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateModuleMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/moduleMasters/{id}",
     *      summary="Update the specified ModuleMaster in storage",
     *      tags={"ModuleMaster"},
     *      description="Update ModuleMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ModuleMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ModuleMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ModuleMaster")
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
     *                  ref="#/definitions/ModuleMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateModuleMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var ModuleMaster $moduleMaster */
        $moduleMaster = $this->moduleMasterRepository->findWithoutFail($id);

        if (empty($moduleMaster)) {
            return $this->sendError(trans('custom.module_master_not_found'));
        }

        $moduleMaster = $this->moduleMasterRepository->update($input, $id);

        return $this->sendResponse($moduleMaster->toArray(), trans('custom.modulemaster_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/moduleMasters/{id}",
     *      summary="Remove the specified ModuleMaster from storage",
     *      tags={"ModuleMaster"},
     *      description="Delete ModuleMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ModuleMaster",
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
        /** @var ModuleMaster $moduleMaster */
        $moduleMaster = $this->moduleMasterRepository->findWithoutFail($id);

        if (empty($moduleMaster)) {
            return $this->sendError(trans('custom.module_master_not_found'));
        }

        $moduleMaster->delete();

        return $this->sendSuccess('Module Master deleted successfully');
    }
}
