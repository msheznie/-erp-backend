<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateHrmsDesignationAPIRequest;
use App\Http\Requests\API\UpdateHrmsDesignationAPIRequest;
use App\Models\HrmsDesignation;
use App\Repositories\HrmsDesignationRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class HrmsDesignationController
 * @package App\Http\Controllers\API
 */

class HrmsDesignationAPIController extends AppBaseController
{
    /** @var  HrmsDesignationRepository */
    private $hrmsDesignationRepository;

    public function __construct(HrmsDesignationRepository $hrmsDesignationRepo)
    {
        $this->hrmsDesignationRepository = $hrmsDesignationRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/hrmsDesignations",
     *      summary="Get a listing of the HrmsDesignations.",
     *      tags={"HrmsDesignation"},
     *      description="Get all HrmsDesignations",
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
     *                  @SWG\Items(ref="#/definitions/HrmsDesignation")
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
        $this->hrmsDesignationRepository->pushCriteria(new RequestCriteria($request));
        $this->hrmsDesignationRepository->pushCriteria(new LimitOffsetCriteria($request));
        $hrmsDesignations = $this->hrmsDesignationRepository->all();

        return $this->sendResponse($hrmsDesignations->toArray(), trans('custom.hrms_designations_retrieved_successfully'));
    }

    /**
     * @param CreateHrmsDesignationAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/hrmsDesignations",
     *      summary="Store a newly created HrmsDesignation in storage",
     *      tags={"HrmsDesignation"},
     *      description="Store HrmsDesignation",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="HrmsDesignation that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/HrmsDesignation")
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
     *                  ref="#/definitions/HrmsDesignation"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateHrmsDesignationAPIRequest $request)
    {
        $input = $request->all();

        $hrmsDesignation = $this->hrmsDesignationRepository->create($input);

        return $this->sendResponse($hrmsDesignation->toArray(), trans('custom.hrms_designation_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/hrmsDesignations/{id}",
     *      summary="Display the specified HrmsDesignation",
     *      tags={"HrmsDesignation"},
     *      description="Get HrmsDesignation",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of HrmsDesignation",
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
     *                  ref="#/definitions/HrmsDesignation"
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
        /** @var HrmsDesignation $hrmsDesignation */
        $hrmsDesignation = $this->hrmsDesignationRepository->findWithoutFail($id);

        if (empty($hrmsDesignation)) {
            return $this->sendError(trans('custom.hrms_designation_not_found'));
        }

        return $this->sendResponse($hrmsDesignation->toArray(), trans('custom.hrms_designation_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateHrmsDesignationAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/hrmsDesignations/{id}",
     *      summary="Update the specified HrmsDesignation in storage",
     *      tags={"HrmsDesignation"},
     *      description="Update HrmsDesignation",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of HrmsDesignation",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="HrmsDesignation that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/HrmsDesignation")
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
     *                  ref="#/definitions/HrmsDesignation"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateHrmsDesignationAPIRequest $request)
    {
        $input = $request->all();

        /** @var HrmsDesignation $hrmsDesignation */
        $hrmsDesignation = $this->hrmsDesignationRepository->findWithoutFail($id);

        if (empty($hrmsDesignation)) {
            return $this->sendError(trans('custom.hrms_designation_not_found'));
        }

        $hrmsDesignation = $this->hrmsDesignationRepository->update($input, $id);

        return $this->sendResponse($hrmsDesignation->toArray(), trans('custom.hrmsdesignation_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/hrmsDesignations/{id}",
     *      summary="Remove the specified HrmsDesignation from storage",
     *      tags={"HrmsDesignation"},
     *      description="Delete HrmsDesignation",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of HrmsDesignation",
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
        /** @var HrmsDesignation $hrmsDesignation */
        $hrmsDesignation = $this->hrmsDesignationRepository->findWithoutFail($id);

        if (empty($hrmsDesignation)) {
            return $this->sendError(trans('custom.hrms_designation_not_found'));
        }

        $hrmsDesignation->delete();

        return $this->sendSuccess('Hrms Designation deleted successfully');
    }
}
