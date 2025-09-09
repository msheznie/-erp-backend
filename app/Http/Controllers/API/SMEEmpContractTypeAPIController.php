<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSMEEmpContractTypeAPIRequest;
use App\Http\Requests\API\UpdateSMEEmpContractTypeAPIRequest;
use App\Models\SMEEmpContractType;
use App\Repositories\SMEEmpContractTypeRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SMEEmpContractTypeController
 * @package App\Http\Controllers\API
 */

class SMEEmpContractTypeAPIController extends AppBaseController
{
    /** @var  SMEEmpContractTypeRepository */
    private $sMEEmpContractTypeRepository;

    public function __construct(SMEEmpContractTypeRepository $sMEEmpContractTypeRepo)
    {
        $this->sMEEmpContractTypeRepository = $sMEEmpContractTypeRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/sMEEmpContractTypes",
     *      summary="Get a listing of the SMEEmpContractTypes.",
     *      tags={"SMEEmpContractType"},
     *      description="Get all SMEEmpContractTypes",
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
     *                  @SWG\Items(ref="#/definitions/SMEEmpContractType")
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
        $this->sMEEmpContractTypeRepository->pushCriteria(new RequestCriteria($request));
        $this->sMEEmpContractTypeRepository->pushCriteria(new LimitOffsetCriteria($request));
        $sMEEmpContractTypes = $this->sMEEmpContractTypeRepository->all();

        return $this->sendResponse($sMEEmpContractTypes->toArray(), trans('custom.s_m_e_emp_contract_types_retrieved_successfully'));
    }

    /**
     * @param CreateSMEEmpContractTypeAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/sMEEmpContractTypes",
     *      summary="Store a newly created SMEEmpContractType in storage",
     *      tags={"SMEEmpContractType"},
     *      description="Store SMEEmpContractType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SMEEmpContractType that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SMEEmpContractType")
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
     *                  ref="#/definitions/SMEEmpContractType"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSMEEmpContractTypeAPIRequest $request)
    {
        $input = $request->all();

        $sMEEmpContractType = $this->sMEEmpContractTypeRepository->create($input);

        return $this->sendResponse($sMEEmpContractType->toArray(), trans('custom.s_m_e_emp_contract_type_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/sMEEmpContractTypes/{id}",
     *      summary="Display the specified SMEEmpContractType",
     *      tags={"SMEEmpContractType"},
     *      description="Get SMEEmpContractType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SMEEmpContractType",
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
     *                  ref="#/definitions/SMEEmpContractType"
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
        /** @var SMEEmpContractType $sMEEmpContractType */
        $sMEEmpContractType = $this->sMEEmpContractTypeRepository->findWithoutFail($id);

        if (empty($sMEEmpContractType)) {
            return $this->sendError(trans('custom.s_m_e_emp_contract_type_not_found'));
        }

        return $this->sendResponse($sMEEmpContractType->toArray(), trans('custom.s_m_e_emp_contract_type_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateSMEEmpContractTypeAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/sMEEmpContractTypes/{id}",
     *      summary="Update the specified SMEEmpContractType in storage",
     *      tags={"SMEEmpContractType"},
     *      description="Update SMEEmpContractType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SMEEmpContractType",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SMEEmpContractType that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SMEEmpContractType")
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
     *                  ref="#/definitions/SMEEmpContractType"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSMEEmpContractTypeAPIRequest $request)
    {
        $input = $request->all();

        /** @var SMEEmpContractType $sMEEmpContractType */
        $sMEEmpContractType = $this->sMEEmpContractTypeRepository->findWithoutFail($id);

        if (empty($sMEEmpContractType)) {
            return $this->sendError(trans('custom.s_m_e_emp_contract_type_not_found'));
        }

        $sMEEmpContractType = $this->sMEEmpContractTypeRepository->update($input, $id);

        return $this->sendResponse($sMEEmpContractType->toArray(), trans('custom.smeempcontracttype_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/sMEEmpContractTypes/{id}",
     *      summary="Remove the specified SMEEmpContractType from storage",
     *      tags={"SMEEmpContractType"},
     *      description="Delete SMEEmpContractType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SMEEmpContractType",
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
        /** @var SMEEmpContractType $sMEEmpContractType */
        $sMEEmpContractType = $this->sMEEmpContractTypeRepository->findWithoutFail($id);

        if (empty($sMEEmpContractType)) {
            return $this->sendError(trans('custom.s_m_e_emp_contract_type_not_found'));
        }

        $sMEEmpContractType->delete();

        return $this->sendSuccess('S M E Emp Contract Type deleted successfully');
    }
}
