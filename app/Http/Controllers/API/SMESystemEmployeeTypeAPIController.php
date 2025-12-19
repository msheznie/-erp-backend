<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSMESystemEmployeeTypeAPIRequest;
use App\Http\Requests\API\UpdateSMESystemEmployeeTypeAPIRequest;
use App\Models\SMESystemEmployeeType;
use App\Repositories\SMESystemEmployeeTypeRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SMESystemEmployeeTypeController
 * @package App\Http\Controllers\API
 */

class SMESystemEmployeeTypeAPIController extends AppBaseController
{
    /** @var  SMESystemEmployeeTypeRepository */
    private $sMESystemEmployeeTypeRepository;

    public function __construct(SMESystemEmployeeTypeRepository $sMESystemEmployeeTypeRepo)
    {
        $this->sMESystemEmployeeTypeRepository = $sMESystemEmployeeTypeRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/sMESystemEmployeeTypes",
     *      summary="Get a listing of the SMESystemEmployeeTypes.",
     *      tags={"SMESystemEmployeeType"},
     *      description="Get all SMESystemEmployeeTypes",
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
     *                  @SWG\Items(ref="#/definitions/SMESystemEmployeeType")
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
        $this->sMESystemEmployeeTypeRepository->pushCriteria(new RequestCriteria($request));
        $this->sMESystemEmployeeTypeRepository->pushCriteria(new LimitOffsetCriteria($request));
        $sMESystemEmployeeTypes = $this->sMESystemEmployeeTypeRepository->all();

        return $this->sendResponse($sMESystemEmployeeTypes->toArray(), trans('custom.s_m_e_system_employee_types_retrieved_successfully'));
    }

    /**
     * @param CreateSMESystemEmployeeTypeAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/sMESystemEmployeeTypes",
     *      summary="Store a newly created SMESystemEmployeeType in storage",
     *      tags={"SMESystemEmployeeType"},
     *      description="Store SMESystemEmployeeType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SMESystemEmployeeType that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SMESystemEmployeeType")
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
     *                  ref="#/definitions/SMESystemEmployeeType"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSMESystemEmployeeTypeAPIRequest $request)
    {
        $input = $request->all();

        $sMESystemEmployeeType = $this->sMESystemEmployeeTypeRepository->create($input);

        return $this->sendResponse($sMESystemEmployeeType->toArray(), trans('custom.s_m_e_system_employee_type_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/sMESystemEmployeeTypes/{id}",
     *      summary="Display the specified SMESystemEmployeeType",
     *      tags={"SMESystemEmployeeType"},
     *      description="Get SMESystemEmployeeType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SMESystemEmployeeType",
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
     *                  ref="#/definitions/SMESystemEmployeeType"
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
        /** @var SMESystemEmployeeType $sMESystemEmployeeType */
        $sMESystemEmployeeType = $this->sMESystemEmployeeTypeRepository->findWithoutFail($id);

        if (empty($sMESystemEmployeeType)) {
            return $this->sendError(trans('custom.s_m_e_system_employee_type_not_found'));
        }

        return $this->sendResponse($sMESystemEmployeeType->toArray(), trans('custom.s_m_e_system_employee_type_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateSMESystemEmployeeTypeAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/sMESystemEmployeeTypes/{id}",
     *      summary="Update the specified SMESystemEmployeeType in storage",
     *      tags={"SMESystemEmployeeType"},
     *      description="Update SMESystemEmployeeType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SMESystemEmployeeType",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SMESystemEmployeeType that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SMESystemEmployeeType")
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
     *                  ref="#/definitions/SMESystemEmployeeType"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSMESystemEmployeeTypeAPIRequest $request)
    {
        $input = $request->all();

        /** @var SMESystemEmployeeType $sMESystemEmployeeType */
        $sMESystemEmployeeType = $this->sMESystemEmployeeTypeRepository->findWithoutFail($id);

        if (empty($sMESystemEmployeeType)) {
            return $this->sendError(trans('custom.s_m_e_system_employee_type_not_found'));
        }

        $sMESystemEmployeeType = $this->sMESystemEmployeeTypeRepository->update($input, $id);

        return $this->sendResponse($sMESystemEmployeeType->toArray(), trans('custom.smesystememployeetype_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/sMESystemEmployeeTypes/{id}",
     *      summary="Remove the specified SMESystemEmployeeType from storage",
     *      tags={"SMESystemEmployeeType"},
     *      description="Delete SMESystemEmployeeType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SMESystemEmployeeType",
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
        /** @var SMESystemEmployeeType $sMESystemEmployeeType */
        $sMESystemEmployeeType = $this->sMESystemEmployeeTypeRepository->findWithoutFail($id);

        if (empty($sMESystemEmployeeType)) {
            return $this->sendError(trans('custom.s_m_e_system_employee_type_not_found'));
        }

        $sMESystemEmployeeType->delete();

        return $this->sendSuccess('S M E System Employee Type deleted successfully');
    }
}
