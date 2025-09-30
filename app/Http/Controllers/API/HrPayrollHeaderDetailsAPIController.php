<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateHrPayrollHeaderDetailsAPIRequest;
use App\Http\Requests\API\UpdateHrPayrollHeaderDetailsAPIRequest;
use App\Models\HrPayrollHeaderDetails;
use App\Repositories\HrPayrollHeaderDetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class HrPayrollHeaderDetailsController
 * @package App\Http\Controllers\API
 */

class HrPayrollHeaderDetailsAPIController extends AppBaseController
{
    /** @var  HrPayrollHeaderDetailsRepository */
    private $hrPayrollHeaderDetailsRepository;

    public function __construct(HrPayrollHeaderDetailsRepository $hrPayrollHeaderDetailsRepo)
    {
        $this->hrPayrollHeaderDetailsRepository = $hrPayrollHeaderDetailsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/hrPayrollHeaderDetails",
     *      summary="Get a listing of the HrPayrollHeaderDetails.",
     *      tags={"HrPayrollHeaderDetails"},
     *      description="Get all HrPayrollHeaderDetails",
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
     *                  @SWG\Items(ref="#/definitions/HrPayrollHeaderDetails")
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
        $this->hrPayrollHeaderDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->hrPayrollHeaderDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $hrPayrollHeaderDetails = $this->hrPayrollHeaderDetailsRepository->all();

        return $this->sendResponse($hrPayrollHeaderDetails->toArray(), trans('custom.hr_payroll_header_details_retrieved_successfully'));
    }

    /**
     * @param CreateHrPayrollHeaderDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/hrPayrollHeaderDetails",
     *      summary="Store a newly created HrPayrollHeaderDetails in storage",
     *      tags={"HrPayrollHeaderDetails"},
     *      description="Store HrPayrollHeaderDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="HrPayrollHeaderDetails that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/HrPayrollHeaderDetails")
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
     *                  ref="#/definitions/HrPayrollHeaderDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateHrPayrollHeaderDetailsAPIRequest $request)
    {
        $input = $request->all();

        $hrPayrollHeaderDetails = $this->hrPayrollHeaderDetailsRepository->create($input);

        return $this->sendResponse($hrPayrollHeaderDetails->toArray(), trans('custom.hr_payroll_header_details_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/hrPayrollHeaderDetails/{id}",
     *      summary="Display the specified HrPayrollHeaderDetails",
     *      tags={"HrPayrollHeaderDetails"},
     *      description="Get HrPayrollHeaderDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of HrPayrollHeaderDetails",
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
     *                  ref="#/definitions/HrPayrollHeaderDetails"
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
        /** @var HrPayrollHeaderDetails $hrPayrollHeaderDetails */
        $hrPayrollHeaderDetails = $this->hrPayrollHeaderDetailsRepository->findWithoutFail($id);

        if (empty($hrPayrollHeaderDetails)) {
            return $this->sendError(trans('custom.hr_payroll_header_details_not_found'));
        }

        return $this->sendResponse($hrPayrollHeaderDetails->toArray(), trans('custom.hr_payroll_header_details_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateHrPayrollHeaderDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/hrPayrollHeaderDetails/{id}",
     *      summary="Update the specified HrPayrollHeaderDetails in storage",
     *      tags={"HrPayrollHeaderDetails"},
     *      description="Update HrPayrollHeaderDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of HrPayrollHeaderDetails",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="HrPayrollHeaderDetails that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/HrPayrollHeaderDetails")
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
     *                  ref="#/definitions/HrPayrollHeaderDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateHrPayrollHeaderDetailsAPIRequest $request)
    {
        $input = $request->all();

        /** @var HrPayrollHeaderDetails $hrPayrollHeaderDetails */
        $hrPayrollHeaderDetails = $this->hrPayrollHeaderDetailsRepository->findWithoutFail($id);

        if (empty($hrPayrollHeaderDetails)) {
            return $this->sendError(trans('custom.hr_payroll_header_details_not_found'));
        }

        $hrPayrollHeaderDetails = $this->hrPayrollHeaderDetailsRepository->update($input, $id);

        return $this->sendResponse($hrPayrollHeaderDetails->toArray(), trans('custom.hrpayrollheaderdetails_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/hrPayrollHeaderDetails/{id}",
     *      summary="Remove the specified HrPayrollHeaderDetails from storage",
     *      tags={"HrPayrollHeaderDetails"},
     *      description="Delete HrPayrollHeaderDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of HrPayrollHeaderDetails",
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
        /** @var HrPayrollHeaderDetails $hrPayrollHeaderDetails */
        $hrPayrollHeaderDetails = $this->hrPayrollHeaderDetailsRepository->findWithoutFail($id);

        if (empty($hrPayrollHeaderDetails)) {
            return $this->sendError(trans('custom.hr_payroll_header_details_not_found'));
        }

        $hrPayrollHeaderDetails->delete();

        return $this->sendSuccess('Hr Payroll Header Details deleted successfully');
    }
}
