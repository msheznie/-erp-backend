<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDesignationAPIRequest;
use App\Http\Requests\API\UpdateDesignationAPIRequest;
use App\Models\Designation;
use App\Repositories\DesignationRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class DesignationController
 * @package App\Http\Controllers\API
 */

class DesignationAPIController extends AppBaseController
{
    /** @var  DesignationRepository */
    private $designationRepository;

    public function __construct(DesignationRepository $designationRepo)
    {
        $this->designationRepository = $designationRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/designations",
     *      summary="Get a listing of the Designations.",
     *      tags={"Designation"},
     *      description="Get all Designations",
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
     *                  @SWG\Items(ref="#/definitions/Designation")
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
        $this->designationRepository->pushCriteria(new RequestCriteria($request));
        $this->designationRepository->pushCriteria(new LimitOffsetCriteria($request));
        $designations = $this->designationRepository->all();

        return $this->sendResponse($designations->toArray(), trans('custom.designations_retrieved_successfully'));
    }

    /**
     * @param CreateDesignationAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/designations",
     *      summary="Store a newly created Designation in storage",
     *      tags={"Designation"},
     *      description="Store Designation",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Designation that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Designation")
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
     *                  ref="#/definitions/Designation"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateDesignationAPIRequest $request)
    {
        $input = $request->all();

        $designations = $this->designationRepository->create($input);

        return $this->sendResponse($designations->toArray(), trans('custom.designation_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/designations/{id}",
     *      summary="Display the specified Designation",
     *      tags={"Designation"},
     *      description="Get Designation",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Designation",
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
     *                  ref="#/definitions/Designation"
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
        /** @var Designation $designation */
        $designation = $this->designationRepository->findWithoutFail($id);

        if (empty($designation)) {
            return $this->sendError(trans('custom.designation_not_found'));
        }

        return $this->sendResponse($designation->toArray(), trans('custom.designation_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateDesignationAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/designations/{id}",
     *      summary="Update the specified Designation in storage",
     *      tags={"Designation"},
     *      description="Update Designation",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Designation",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Designation that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Designation")
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
     *                  ref="#/definitions/Designation"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateDesignationAPIRequest $request)
    {
        $input = $request->all();

        /** @var Designation $designation */
        $designation = $this->designationRepository->findWithoutFail($id);

        if (empty($designation)) {
            return $this->sendError(trans('custom.designation_not_found'));
        }

        $designation = $this->designationRepository->update($input, $id);

        return $this->sendResponse($designation->toArray(), trans('custom.designation_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/designations/{id}",
     *      summary="Remove the specified Designation from storage",
     *      tags={"Designation"},
     *      description="Delete Designation",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Designation",
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
        /** @var Designation $designation */
        $designation = $this->designationRepository->findWithoutFail($id);

        if (empty($designation)) {
            return $this->sendError(trans('custom.designation_not_found'));
        }

        $designation->delete();

        return $this->sendResponse($id, trans('custom.designation_deleted_successfully'));
    }
}
