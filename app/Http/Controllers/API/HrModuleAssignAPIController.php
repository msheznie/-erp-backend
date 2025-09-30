<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateHrModuleAssignAPIRequest;
use App\Http\Requests\API\UpdateHrModuleAssignAPIRequest;
use App\Models\HrModuleAssign;
use App\Repositories\HrModuleAssignRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class HrModuleAssignController
 * @package App\Http\Controllers\API
 */

class HrModuleAssignAPIController extends AppBaseController
{
    /** @var  HrModuleAssignRepository */
    private $hrModuleAssignRepository;

    public function __construct(HrModuleAssignRepository $hrModuleAssignRepo)
    {
        $this->hrModuleAssignRepository = $hrModuleAssignRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/hrModuleAssigns",
     *      summary="getHrModuleAssignList",
     *      tags={"HrModuleAssign"},
     *      description="Get all HrModuleAssigns",
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
     *                  @OA\Items(ref="#/definitions/HrModuleAssign")
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
        $this->hrModuleAssignRepository->pushCriteria(new RequestCriteria($request));
        $this->hrModuleAssignRepository->pushCriteria(new LimitOffsetCriteria($request));
        $hrModuleAssigns = $this->hrModuleAssignRepository->all();

        return $this->sendResponse($hrModuleAssigns->toArray(), trans('custom.hr_module_assigns_retrieved_successfully'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/hrModuleAssigns",
     *      summary="createHrModuleAssign",
     *      tags={"HrModuleAssign"},
     *      description="Create HrModuleAssign",
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
     *                  ref="#/definitions/HrModuleAssign"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateHrModuleAssignAPIRequest $request)
    {
        $input = $request->all();

        $hrModuleAssign = $this->hrModuleAssignRepository->create($input);

        return $this->sendResponse($hrModuleAssign->toArray(), trans('custom.hr_module_assign_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/hrModuleAssigns/{id}",
     *      summary="getHrModuleAssignItem",
     *      tags={"HrModuleAssign"},
     *      description="Get HrModuleAssign",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of HrModuleAssign",
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
     *                  ref="#/definitions/HrModuleAssign"
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
        /** @var HrModuleAssign $hrModuleAssign */
        $hrModuleAssign = $this->hrModuleAssignRepository->findWithoutFail($id);

        if (empty($hrModuleAssign)) {
            return $this->sendError(trans('custom.hr_module_assign_not_found'));
        }

        return $this->sendResponse($hrModuleAssign->toArray(), trans('custom.hr_module_assign_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/hrModuleAssigns/{id}",
     *      summary="updateHrModuleAssign",
     *      tags={"HrModuleAssign"},
     *      description="Update HrModuleAssign",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of HrModuleAssign",
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
     *                  ref="#/definitions/HrModuleAssign"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateHrModuleAssignAPIRequest $request)
    {
        $input = $request->all();

        /** @var HrModuleAssign $hrModuleAssign */
        $hrModuleAssign = $this->hrModuleAssignRepository->findWithoutFail($id);

        if (empty($hrModuleAssign)) {
            return $this->sendError(trans('custom.hr_module_assign_not_found'));
        }

        $hrModuleAssign = $this->hrModuleAssignRepository->update($input, $id);

        return $this->sendResponse($hrModuleAssign->toArray(), trans('custom.hrmoduleassign_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/hrModuleAssigns/{id}",
     *      summary="deleteHrModuleAssign",
     *      tags={"HrModuleAssign"},
     *      description="Delete HrModuleAssign",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of HrModuleAssign",
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
        /** @var HrModuleAssign $hrModuleAssign */
        $hrModuleAssign = $this->hrModuleAssignRepository->findWithoutFail($id);

        if (empty($hrModuleAssign)) {
            return $this->sendError(trans('custom.hr_module_assign_not_found'));
        }

        $hrModuleAssign->delete();

        return $this->sendSuccess('Hr Module Assign deleted successfully');
    }
}
