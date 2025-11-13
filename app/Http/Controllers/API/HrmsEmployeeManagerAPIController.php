<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateHrmsEmployeeManagerAPIRequest;
use App\Http\Requests\API\UpdateHrmsEmployeeManagerAPIRequest;
use App\Models\HrmsEmployeeManager;
use App\Repositories\HrmsEmployeeManagerRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class HrmsEmployeeManagerController
 * @package App\Http\Controllers\API
 */

class HrmsEmployeeManagerAPIController extends AppBaseController
{
    /** @var  HrmsEmployeeManagerRepository */
    private $hrmsEmployeeManagerRepository;

    public function __construct(HrmsEmployeeManagerRepository $hrmsEmployeeManagerRepo)
    {
        $this->hrmsEmployeeManagerRepository = $hrmsEmployeeManagerRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/hrmsEmployeeManagers",
     *      summary="Get a listing of the HrmsEmployeeManagers.",
     *      tags={"HrmsEmployeeManager"},
     *      description="Get all HrmsEmployeeManagers",
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
     *                  @SWG\Items(ref="#/definitions/HrmsEmployeeManager")
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
        $this->hrmsEmployeeManagerRepository->pushCriteria(new RequestCriteria($request));
        $this->hrmsEmployeeManagerRepository->pushCriteria(new LimitOffsetCriteria($request));
        $hrmsEmployeeManagers = $this->hrmsEmployeeManagerRepository->all();

        return $this->sendResponse($hrmsEmployeeManagers->toArray(), trans('custom.hrms_employee_managers_retrieved_successfully'));
    }

    /**
     * @param CreateHrmsEmployeeManagerAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/hrmsEmployeeManagers",
     *      summary="Store a newly created HrmsEmployeeManager in storage",
     *      tags={"HrmsEmployeeManager"},
     *      description="Store HrmsEmployeeManager",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="HrmsEmployeeManager that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/HrmsEmployeeManager")
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
     *                  ref="#/definitions/HrmsEmployeeManager"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateHrmsEmployeeManagerAPIRequest $request)
    {
        $input = $request->all();

        $hrmsEmployeeManager = $this->hrmsEmployeeManagerRepository->create($input);

        return $this->sendResponse($hrmsEmployeeManager->toArray(), trans('custom.hrms_employee_manager_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/hrmsEmployeeManagers/{id}",
     *      summary="Display the specified HrmsEmployeeManager",
     *      tags={"HrmsEmployeeManager"},
     *      description="Get HrmsEmployeeManager",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of HrmsEmployeeManager",
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
     *                  ref="#/definitions/HrmsEmployeeManager"
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
        /** @var HrmsEmployeeManager $hrmsEmployeeManager */
        $hrmsEmployeeManager = $this->hrmsEmployeeManagerRepository->findWithoutFail($id);

        if (empty($hrmsEmployeeManager)) {
            return $this->sendError(trans('custom.hrms_employee_manager_not_found'));
        }

        return $this->sendResponse($hrmsEmployeeManager->toArray(), trans('custom.hrms_employee_manager_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateHrmsEmployeeManagerAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/hrmsEmployeeManagers/{id}",
     *      summary="Update the specified HrmsEmployeeManager in storage",
     *      tags={"HrmsEmployeeManager"},
     *      description="Update HrmsEmployeeManager",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of HrmsEmployeeManager",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="HrmsEmployeeManager that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/HrmsEmployeeManager")
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
     *                  ref="#/definitions/HrmsEmployeeManager"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateHrmsEmployeeManagerAPIRequest $request)
    {
        $input = $request->all();

        /** @var HrmsEmployeeManager $hrmsEmployeeManager */
        $hrmsEmployeeManager = $this->hrmsEmployeeManagerRepository->findWithoutFail($id);

        if (empty($hrmsEmployeeManager)) {
            return $this->sendError(trans('custom.hrms_employee_manager_not_found'));
        }

        $hrmsEmployeeManager = $this->hrmsEmployeeManagerRepository->update($input, $id);

        return $this->sendResponse($hrmsEmployeeManager->toArray(), trans('custom.hrmsemployeemanager_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/hrmsEmployeeManagers/{id}",
     *      summary="Remove the specified HrmsEmployeeManager from storage",
     *      tags={"HrmsEmployeeManager"},
     *      description="Delete HrmsEmployeeManager",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of HrmsEmployeeManager",
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
        /** @var HrmsEmployeeManager $hrmsEmployeeManager */
        $hrmsEmployeeManager = $this->hrmsEmployeeManagerRepository->findWithoutFail($id);

        if (empty($hrmsEmployeeManager)) {
            return $this->sendError(trans('custom.hrms_employee_manager_not_found'));
        }

        $hrmsEmployeeManager->delete();

        return $this->sendSuccess('Hrms Employee Manager deleted successfully');
    }
}
