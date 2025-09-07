<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSubModuleMasterAPIRequest;
use App\Http\Requests\API\UpdateSubModuleMasterAPIRequest;
use App\Models\SubModuleMaster;
use App\Repositories\SubModuleMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SubModuleMasterController
 * @package App\Http\Controllers\API
 */

class SubModuleMasterAPIController extends AppBaseController
{
    /** @var  SubModuleMasterRepository */
    private $subModuleMasterRepository;

    public function __construct(SubModuleMasterRepository $subModuleMasterRepo)
    {
        $this->subModuleMasterRepository = $subModuleMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/subModuleMasters",
     *      summary="Get a listing of the SubModuleMasters.",
     *      tags={"SubModuleMaster"},
     *      description="Get all SubModuleMasters",
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
     *                  @SWG\Items(ref="#/definitions/SubModuleMaster")
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
        $this->subModuleMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->subModuleMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $subModuleMasters = $this->subModuleMasterRepository->all();

        return $this->sendResponse($subModuleMasters->toArray(), trans('custom.sub_module_masters_retrieved_successfully'));
    }

    /**
     * @param CreateSubModuleMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/subModuleMasters",
     *      summary="Store a newly created SubModuleMaster in storage",
     *      tags={"SubModuleMaster"},
     *      description="Store SubModuleMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SubModuleMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SubModuleMaster")
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
     *                  ref="#/definitions/SubModuleMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSubModuleMasterAPIRequest $request)
    {
        $input = $request->all();

        $subModuleMaster = $this->subModuleMasterRepository->create($input);

        return $this->sendResponse($subModuleMaster->toArray(), trans('custom.sub_module_master_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/subModuleMasters/{id}",
     *      summary="Display the specified SubModuleMaster",
     *      tags={"SubModuleMaster"},
     *      description="Get SubModuleMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SubModuleMaster",
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
     *                  ref="#/definitions/SubModuleMaster"
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
        /** @var SubModuleMaster $subModuleMaster */
        $subModuleMaster = $this->subModuleMasterRepository->findWithoutFail($id);

        if (empty($subModuleMaster)) {
            return $this->sendError(trans('custom.sub_module_master_not_found'));
        }

        return $this->sendResponse($subModuleMaster->toArray(), trans('custom.sub_module_master_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateSubModuleMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/subModuleMasters/{id}",
     *      summary="Update the specified SubModuleMaster in storage",
     *      tags={"SubModuleMaster"},
     *      description="Update SubModuleMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SubModuleMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SubModuleMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SubModuleMaster")
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
     *                  ref="#/definitions/SubModuleMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSubModuleMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var SubModuleMaster $subModuleMaster */
        $subModuleMaster = $this->subModuleMasterRepository->findWithoutFail($id);

        if (empty($subModuleMaster)) {
            return $this->sendError(trans('custom.sub_module_master_not_found'));
        }

        $subModuleMaster = $this->subModuleMasterRepository->update($input, $id);

        return $this->sendResponse($subModuleMaster->toArray(), trans('custom.submodulemaster_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/subModuleMasters/{id}",
     *      summary="Remove the specified SubModuleMaster from storage",
     *      tags={"SubModuleMaster"},
     *      description="Delete SubModuleMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SubModuleMaster",
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
        /** @var SubModuleMaster $subModuleMaster */
        $subModuleMaster = $this->subModuleMasterRepository->findWithoutFail($id);

        if (empty($subModuleMaster)) {
            return $this->sendError(trans('custom.sub_module_master_not_found'));
        }

        $subModuleMaster->delete();

        return $this->sendSuccess('Sub Module Master deleted successfully');
    }
}
