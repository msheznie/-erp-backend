<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateHrmsDepartmentMasterAPIRequest;
use App\Http\Requests\API\UpdateHrmsDepartmentMasterAPIRequest;
use App\Models\HrmsDepartmentMaster;
use App\Repositories\HrmsDepartmentMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class HrmsDepartmentMasterController
 * @package App\Http\Controllers\API
 */

class HrmsDepartmentMasterAPIController extends AppBaseController
{
    /** @var  HrmsDepartmentMasterRepository */
    private $hrmsDepartmentMasterRepository;

    public function __construct(HrmsDepartmentMasterRepository $hrmsDepartmentMasterRepo)
    {
        $this->hrmsDepartmentMasterRepository = $hrmsDepartmentMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/hrmsDepartmentMasters",
     *      summary="Get a listing of the HrmsDepartmentMasters.",
     *      tags={"HrmsDepartmentMaster"},
     *      description="Get all HrmsDepartmentMasters",
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
     *                  @SWG\Items(ref="#/definitions/HrmsDepartmentMaster")
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
        $this->hrmsDepartmentMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->hrmsDepartmentMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $hrmsDepartmentMasters = $this->hrmsDepartmentMasterRepository->all();

        return $this->sendResponse($hrmsDepartmentMasters->toArray(), trans('custom.hrms_department_masters_retrieved_successfully'));
    }

    /**
     * @param CreateHrmsDepartmentMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/hrmsDepartmentMasters",
     *      summary="Store a newly created HrmsDepartmentMaster in storage",
     *      tags={"HrmsDepartmentMaster"},
     *      description="Store HrmsDepartmentMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="HrmsDepartmentMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/HrmsDepartmentMaster")
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
     *                  ref="#/definitions/HrmsDepartmentMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateHrmsDepartmentMasterAPIRequest $request)
    {
        $input = $request->all();

        $hrmsDepartmentMaster = $this->hrmsDepartmentMasterRepository->create($input);

        return $this->sendResponse($hrmsDepartmentMaster->toArray(), trans('custom.hrms_department_master_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/hrmsDepartmentMasters/{id}",
     *      summary="Display the specified HrmsDepartmentMaster",
     *      tags={"HrmsDepartmentMaster"},
     *      description="Get HrmsDepartmentMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of HrmsDepartmentMaster",
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
     *                  ref="#/definitions/HrmsDepartmentMaster"
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
        /** @var HrmsDepartmentMaster $hrmsDepartmentMaster */
        $hrmsDepartmentMaster = $this->hrmsDepartmentMasterRepository->findWithoutFail($id);

        if (empty($hrmsDepartmentMaster)) {
            return $this->sendError(trans('custom.hrms_department_master_not_found'));
        }

        return $this->sendResponse($hrmsDepartmentMaster->toArray(), trans('custom.hrms_department_master_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateHrmsDepartmentMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/hrmsDepartmentMasters/{id}",
     *      summary="Update the specified HrmsDepartmentMaster in storage",
     *      tags={"HrmsDepartmentMaster"},
     *      description="Update HrmsDepartmentMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of HrmsDepartmentMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="HrmsDepartmentMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/HrmsDepartmentMaster")
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
     *                  ref="#/definitions/HrmsDepartmentMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateHrmsDepartmentMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var HrmsDepartmentMaster $hrmsDepartmentMaster */
        $hrmsDepartmentMaster = $this->hrmsDepartmentMasterRepository->findWithoutFail($id);

        if (empty($hrmsDepartmentMaster)) {
            return $this->sendError(trans('custom.hrms_department_master_not_found'));
        }

        $hrmsDepartmentMaster = $this->hrmsDepartmentMasterRepository->update($input, $id);

        return $this->sendResponse($hrmsDepartmentMaster->toArray(), trans('custom.hrmsdepartmentmaster_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/hrmsDepartmentMasters/{id}",
     *      summary="Remove the specified HrmsDepartmentMaster from storage",
     *      tags={"HrmsDepartmentMaster"},
     *      description="Delete HrmsDepartmentMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of HrmsDepartmentMaster",
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
        /** @var HrmsDepartmentMaster $hrmsDepartmentMaster */
        $hrmsDepartmentMaster = $this->hrmsDepartmentMasterRepository->findWithoutFail($id);

        if (empty($hrmsDepartmentMaster)) {
            return $this->sendError(trans('custom.hrms_department_master_not_found'));
        }

        $hrmsDepartmentMaster->delete();

        return $this->sendResponse($id, trans('custom.hrms_department_master_deleted_successfully'));
    }
}
