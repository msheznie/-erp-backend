<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSMELaveTypeAPIRequest;
use App\Http\Requests\API\UpdateSMELaveTypeAPIRequest;
use App\Models\SMELaveType;
use App\Repositories\SMELaveTypeRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SMELaveTypeController
 * @package App\Http\Controllers\API
 */

class SMELaveTypeAPIController extends AppBaseController
{
    /** @var  SMELaveTypeRepository */
    private $sMELaveTypeRepository;

    public function __construct(SMELaveTypeRepository $sMELaveTypeRepo)
    {
        $this->sMELaveTypeRepository = $sMELaveTypeRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/sMELaveTypes",
     *      summary="Get a listing of the SMELaveTypes.",
     *      tags={"SMELaveType"},
     *      description="Get all SMELaveTypes",
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
     *                  @SWG\Items(ref="#/definitions/SMELaveType")
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
        $this->sMELaveTypeRepository->pushCriteria(new RequestCriteria($request));
        $this->sMELaveTypeRepository->pushCriteria(new LimitOffsetCriteria($request));
        $sMELaveTypes = $this->sMELaveTypeRepository->all();

        return $this->sendResponse($sMELaveTypes->toArray(), trans('custom.s_m_e_lave_types_retrieved_successfully'));
    }

    /**
     * @param CreateSMELaveTypeAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/sMELaveTypes",
     *      summary="Store a newly created SMELaveType in storage",
     *      tags={"SMELaveType"},
     *      description="Store SMELaveType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SMELaveType that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SMELaveType")
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
     *                  ref="#/definitions/SMELaveType"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSMELaveTypeAPIRequest $request)
    {
        $input = $request->all();

        $sMELaveType = $this->sMELaveTypeRepository->create($input);

        return $this->sendResponse($sMELaveType->toArray(), trans('custom.s_m_e_lave_type_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/sMELaveTypes/{id}",
     *      summary="Display the specified SMELaveType",
     *      tags={"SMELaveType"},
     *      description="Get SMELaveType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SMELaveType",
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
     *                  ref="#/definitions/SMELaveType"
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
        /** @var SMELaveType $sMELaveType */
        $sMELaveType = $this->sMELaveTypeRepository->findWithoutFail($id);

        if (empty($sMELaveType)) {
            return $this->sendError(trans('custom.s_m_e_lave_type_not_found'));
        }

        return $this->sendResponse($sMELaveType->toArray(), trans('custom.s_m_e_lave_type_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateSMELaveTypeAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/sMELaveTypes/{id}",
     *      summary="Update the specified SMELaveType in storage",
     *      tags={"SMELaveType"},
     *      description="Update SMELaveType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SMELaveType",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SMELaveType that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SMELaveType")
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
     *                  ref="#/definitions/SMELaveType"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSMELaveTypeAPIRequest $request)
    {
        $input = $request->all();

        /** @var SMELaveType $sMELaveType */
        $sMELaveType = $this->sMELaveTypeRepository->findWithoutFail($id);

        if (empty($sMELaveType)) {
            return $this->sendError(trans('custom.s_m_e_lave_type_not_found'));
        }

        $sMELaveType = $this->sMELaveTypeRepository->update($input, $id);

        return $this->sendResponse($sMELaveType->toArray(), trans('custom.smelavetype_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/sMELaveTypes/{id}",
     *      summary="Remove the specified SMELaveType from storage",
     *      tags={"SMELaveType"},
     *      description="Delete SMELaveType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SMELaveType",
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
        /** @var SMELaveType $sMELaveType */
        $sMELaveType = $this->sMELaveTypeRepository->findWithoutFail($id);

        if (empty($sMELaveType)) {
            return $this->sendError(trans('custom.s_m_e_lave_type_not_found'));
        }

        $sMELaveType->delete();

        return $this->sendSuccess('S M E Lave Type deleted successfully');
    }
}
