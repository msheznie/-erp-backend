<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateHrDepartmentMasterAPIRequest;
use App\Http\Requests\API\UpdateHrDepartmentMasterAPIRequest;
use App\Models\HrDepartmentMaster;
use App\Repositories\HrDepartmentMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class HrDepartmentMasterController
 * @package App\Http\Controllers\API
 */

class HrDepartmentMasterAPIController extends AppBaseController
{
    /** @var  HrDepartmentMasterRepository */
    private $hrDepartmentMasterRepository;

    public function __construct(HrDepartmentMasterRepository $hrDepartmentMasterRepo)
    {
        $this->hrDepartmentMasterRepository = $hrDepartmentMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/hrDepartmentMasters",
     *      summary="getHrDepartmentMasterList",
     *      tags={"HrDepartmentMaster"},
     *      description="Get all HrDepartmentMasters",
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
     *                  @OA\Items(ref="#/definitions/HrDepartmentMaster")
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
        $this->hrDepartmentMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->hrDepartmentMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $hrDepartmentMasters = $this->hrDepartmentMasterRepository->all();

        return $this->sendResponse($hrDepartmentMasters->toArray(), trans('custom.hr_department_masters_retrieved_successfully'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/hrDepartmentMasters",
     *      summary="createHrDepartmentMaster",
     *      tags={"HrDepartmentMaster"},
     *      description="Create HrDepartmentMaster",
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
     *                  ref="#/definitions/HrDepartmentMaster"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateHrDepartmentMasterAPIRequest $request)
    {
        $input = $request->all();

        $hrDepartmentMaster = $this->hrDepartmentMasterRepository->create($input);

        return $this->sendResponse($hrDepartmentMaster->toArray(), trans('custom.hr_department_master_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/hrDepartmentMasters/{id}",
     *      summary="getHrDepartmentMasterItem",
     *      tags={"HrDepartmentMaster"},
     *      description="Get HrDepartmentMaster",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of HrDepartmentMaster",
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
     *                  ref="#/definitions/HrDepartmentMaster"
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
        /** @var HrDepartmentMaster $hrDepartmentMaster */
        $hrDepartmentMaster = $this->hrDepartmentMasterRepository->findWithoutFail($id);

        if (empty($hrDepartmentMaster)) {
            return $this->sendError(trans('custom.hr_department_master_not_found'));
        }

        return $this->sendResponse($hrDepartmentMaster->toArray(), trans('custom.hr_department_master_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/hrDepartmentMasters/{id}",
     *      summary="updateHrDepartmentMaster",
     *      tags={"HrDepartmentMaster"},
     *      description="Update HrDepartmentMaster",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of HrDepartmentMaster",
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
     *                  ref="#/definitions/HrDepartmentMaster"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateHrDepartmentMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var HrDepartmentMaster $hrDepartmentMaster */
        $hrDepartmentMaster = $this->hrDepartmentMasterRepository->findWithoutFail($id);

        if (empty($hrDepartmentMaster)) {
            return $this->sendError(trans('custom.hr_department_master_not_found'));
        }

        $hrDepartmentMaster = $this->hrDepartmentMasterRepository->update($input, $id);

        return $this->sendResponse($hrDepartmentMaster->toArray(), trans('custom.hrdepartmentmaster_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/hrDepartmentMasters/{id}",
     *      summary="deleteHrDepartmentMaster",
     *      tags={"HrDepartmentMaster"},
     *      description="Delete HrDepartmentMaster",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of HrDepartmentMaster",
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
        /** @var HrDepartmentMaster $hrDepartmentMaster */
        $hrDepartmentMaster = $this->hrDepartmentMasterRepository->findWithoutFail($id);

        if (empty($hrDepartmentMaster)) {
            return $this->sendError(trans('custom.hr_department_master_not_found'));
        }

        $hrDepartmentMaster->delete();

        return $this->sendSuccess('Hr Department Master deleted successfully');
    }
}
