<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSrpEmployeeDetailsAPIRequest;
use App\Http\Requests\API\UpdateSrpEmployeeDetailsAPIRequest;
use App\Models\SrpEmployeeDetails;
use App\Repositories\SrpEmployeeDetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SrpEmployeeDetailsController
 * @package App\Http\Controllers\API
 */

class SrpEmployeeDetailsAPIController extends AppBaseController
{
    /** @var  SrpEmployeeDetailsRepository */
    private $srpEmployeeDetailsRepository;

    public function __construct(SrpEmployeeDetailsRepository $srpEmployeeDetailsRepo)
    {
        $this->srpEmployeeDetailsRepository = $srpEmployeeDetailsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/srpEmployeeDetails",
     *      summary="Get a listing of the SrpEmployeeDetails.",
     *      tags={"SrpEmployeeDetails"},
     *      description="Get all SrpEmployeeDetails",
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
     *                  @SWG\Items(ref="#/definitions/SrpEmployeeDetails")
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
        $this->srpEmployeeDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->srpEmployeeDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $srpEmployeeDetails = $this->srpEmployeeDetailsRepository->all();

        return $this->sendResponse($srpEmployeeDetails->toArray(), trans('custom.srp_employee_details_retrieved_successfully'));
    }

    /**
     * @param CreateSrpEmployeeDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/srpEmployeeDetails",
     *      summary="Store a newly created SrpEmployeeDetails in storage",
     *      tags={"SrpEmployeeDetails"},
     *      description="Store SrpEmployeeDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SrpEmployeeDetails that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SrpEmployeeDetails")
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
     *                  ref="#/definitions/SrpEmployeeDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSrpEmployeeDetailsAPIRequest $request)
    {
        $input = $request->all();

        $srpEmployeeDetails = $this->srpEmployeeDetailsRepository->create($input);

        return $this->sendResponse($srpEmployeeDetails->toArray(), trans('custom.srp_employee_details_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/srpEmployeeDetails/{id}",
     *      summary="Display the specified SrpEmployeeDetails",
     *      tags={"SrpEmployeeDetails"},
     *      description="Get SrpEmployeeDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SrpEmployeeDetails",
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
     *                  ref="#/definitions/SrpEmployeeDetails"
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
        /** @var SrpEmployeeDetails $srpEmployeeDetails */
        $srpEmployeeDetails = $this->srpEmployeeDetailsRepository->findWithoutFail($id);

        if (empty($srpEmployeeDetails)) {
            return $this->sendError(trans('custom.srp_employee_details_not_found'));
        }

        return $this->sendResponse($srpEmployeeDetails->toArray(), trans('custom.srp_employee_details_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateSrpEmployeeDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/srpEmployeeDetails/{id}",
     *      summary="Update the specified SrpEmployeeDetails in storage",
     *      tags={"SrpEmployeeDetails"},
     *      description="Update SrpEmployeeDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SrpEmployeeDetails",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SrpEmployeeDetails that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SrpEmployeeDetails")
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
     *                  ref="#/definitions/SrpEmployeeDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSrpEmployeeDetailsAPIRequest $request)
    {
        $input = $request->all();

        /** @var SrpEmployeeDetails $srpEmployeeDetails */
        $srpEmployeeDetails = $this->srpEmployeeDetailsRepository->findWithoutFail($id);

        if (empty($srpEmployeeDetails)) {
            return $this->sendError(trans('custom.srp_employee_details_not_found'));
        }

        $srpEmployeeDetails = $this->srpEmployeeDetailsRepository->update($input, $id);

        return $this->sendResponse($srpEmployeeDetails->toArray(), trans('custom.srpemployeedetails_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/srpEmployeeDetails/{id}",
     *      summary="Remove the specified SrpEmployeeDetails from storage",
     *      tags={"SrpEmployeeDetails"},
     *      description="Delete SrpEmployeeDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SrpEmployeeDetails",
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
        /** @var SrpEmployeeDetails $srpEmployeeDetails */
        $srpEmployeeDetails = $this->srpEmployeeDetailsRepository->findWithoutFail($id);

        if (empty($srpEmployeeDetails)) {
            return $this->sendError(trans('custom.srp_employee_details_not_found'));
        }

        $srpEmployeeDetails->delete();

        return $this->sendSuccess('Srp Employee Details deleted successfully');
    }
}
