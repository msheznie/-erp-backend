<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateEmploymentTypeAPIRequest;
use App\Http\Requests\API\UpdateEmploymentTypeAPIRequest;
use App\Models\EmploymentType;
use App\Repositories\EmploymentTypeRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class EmploymentTypeController
 * @package App\Http\Controllers\API
 */

class EmploymentTypeAPIController extends AppBaseController
{
    /** @var  EmploymentTypeRepository */
    private $employmentTypeRepository;

    public function __construct(EmploymentTypeRepository $employmentTypeRepo)
    {
        $this->employmentTypeRepository = $employmentTypeRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/employmentTypes",
     *      summary="Get a listing of the EmploymentTypes.",
     *      tags={"EmploymentType"},
     *      description="Get all EmploymentTypes",
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
     *                  @SWG\Items(ref="#/definitions/EmploymentType")
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
        $this->employmentTypeRepository->pushCriteria(new RequestCriteria($request));
        $this->employmentTypeRepository->pushCriteria(new LimitOffsetCriteria($request));
        $employmentTypes = $this->employmentTypeRepository->all();

        return $this->sendResponse($employmentTypes->toArray(), trans('custom.employment_types_retrieved_successfully'));
    }

    /**
     * @param CreateEmploymentTypeAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/employmentTypes",
     *      summary="Store a newly created EmploymentType in storage",
     *      tags={"EmploymentType"},
     *      description="Store EmploymentType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="EmploymentType that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/EmploymentType")
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
     *                  ref="#/definitions/EmploymentType"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateEmploymentTypeAPIRequest $request)
    {
        $input = $request->all();

        $employmentTypes = $this->employmentTypeRepository->create($input);

        return $this->sendResponse($employmentTypes->toArray(), trans('custom.employment_type_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/employmentTypes/{id}",
     *      summary="Display the specified EmploymentType",
     *      tags={"EmploymentType"},
     *      description="Get EmploymentType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of EmploymentType",
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
     *                  ref="#/definitions/EmploymentType"
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
        /** @var EmploymentType $employmentType */
        $employmentType = $this->employmentTypeRepository->findWithoutFail($id);

        if (empty($employmentType)) {
            return $this->sendError(trans('custom.employment_type_not_found'));
        }

        return $this->sendResponse($employmentType->toArray(), trans('custom.employment_type_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateEmploymentTypeAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/employmentTypes/{id}",
     *      summary="Update the specified EmploymentType in storage",
     *      tags={"EmploymentType"},
     *      description="Update EmploymentType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of EmploymentType",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="EmploymentType that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/EmploymentType")
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
     *                  ref="#/definitions/EmploymentType"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateEmploymentTypeAPIRequest $request)
    {
        $input = $request->all();

        /** @var EmploymentType $employmentType */
        $employmentType = $this->employmentTypeRepository->findWithoutFail($id);

        if (empty($employmentType)) {
            return $this->sendError(trans('custom.employment_type_not_found'));
        }

        $employmentType = $this->employmentTypeRepository->update($input, $id);

        return $this->sendResponse($employmentType->toArray(), trans('custom.employmenttype_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/employmentTypes/{id}",
     *      summary="Remove the specified EmploymentType from storage",
     *      tags={"EmploymentType"},
     *      description="Delete EmploymentType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of EmploymentType",
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
        /** @var EmploymentType $employmentType */
        $employmentType = $this->employmentTypeRepository->findWithoutFail($id);

        if (empty($employmentType)) {
            return $this->sendError(trans('custom.employment_type_not_found'));
        }

        $employmentType->delete();

        return $this->sendResponse($id, trans('custom.employment_type_deleted_successfully'));
    }
}
