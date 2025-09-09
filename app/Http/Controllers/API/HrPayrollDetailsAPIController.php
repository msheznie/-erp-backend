<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateHrPayrollDetailsAPIRequest;
use App\Http\Requests\API\UpdateHrPayrollDetailsAPIRequest;
use App\Models\HrPayrollDetails;
use App\Repositories\HrPayrollDetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class HrPayrollDetailsController
 * @package App\Http\Controllers\API
 */

class HrPayrollDetailsAPIController extends AppBaseController
{
    /** @var  HrPayrollDetailsRepository */
    private $hrPayrollDetailsRepository;

    public function __construct(HrPayrollDetailsRepository $hrPayrollDetailsRepo)
    {
        $this->hrPayrollDetailsRepository = $hrPayrollDetailsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/hrPayrollDetails",
     *      summary="Get a listing of the HrPayrollDetails.",
     *      tags={"HrPayrollDetails"},
     *      description="Get all HrPayrollDetails",
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
     *                  @SWG\Items(ref="#/definitions/HrPayrollDetails")
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
        $this->hrPayrollDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->hrPayrollDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $hrPayrollDetails = $this->hrPayrollDetailsRepository->all();

        return $this->sendResponse($hrPayrollDetails->toArray(), trans('custom.hr_payroll_details_retrieved_successfully'));
    }

    /**
     * @param CreateHrPayrollDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/hrPayrollDetails",
     *      summary="Store a newly created HrPayrollDetails in storage",
     *      tags={"HrPayrollDetails"},
     *      description="Store HrPayrollDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="HrPayrollDetails that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/HrPayrollDetails")
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
     *                  ref="#/definitions/HrPayrollDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateHrPayrollDetailsAPIRequest $request)
    {
        $input = $request->all();

        $hrPayrollDetails = $this->hrPayrollDetailsRepository->create($input);

        return $this->sendResponse($hrPayrollDetails->toArray(), trans('custom.hr_payroll_details_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/hrPayrollDetails/{id}",
     *      summary="Display the specified HrPayrollDetails",
     *      tags={"HrPayrollDetails"},
     *      description="Get HrPayrollDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of HrPayrollDetails",
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
     *                  ref="#/definitions/HrPayrollDetails"
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
        /** @var HrPayrollDetails $hrPayrollDetails */
        $hrPayrollDetails = $this->hrPayrollDetailsRepository->findWithoutFail($id);

        if (empty($hrPayrollDetails)) {
            return $this->sendError(trans('custom.hr_payroll_details_not_found'));
        }

        return $this->sendResponse($hrPayrollDetails->toArray(), trans('custom.hr_payroll_details_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateHrPayrollDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/hrPayrollDetails/{id}",
     *      summary="Update the specified HrPayrollDetails in storage",
     *      tags={"HrPayrollDetails"},
     *      description="Update HrPayrollDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of HrPayrollDetails",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="HrPayrollDetails that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/HrPayrollDetails")
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
     *                  ref="#/definitions/HrPayrollDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateHrPayrollDetailsAPIRequest $request)
    {
        $input = $request->all();

        /** @var HrPayrollDetails $hrPayrollDetails */
        $hrPayrollDetails = $this->hrPayrollDetailsRepository->findWithoutFail($id);

        if (empty($hrPayrollDetails)) {
            return $this->sendError(trans('custom.hr_payroll_details_not_found'));
        }

        $hrPayrollDetails = $this->hrPayrollDetailsRepository->update($input, $id);

        return $this->sendResponse($hrPayrollDetails->toArray(), trans('custom.hrpayrolldetails_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/hrPayrollDetails/{id}",
     *      summary="Remove the specified HrPayrollDetails from storage",
     *      tags={"HrPayrollDetails"},
     *      description="Delete HrPayrollDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of HrPayrollDetails",
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
        /** @var HrPayrollDetails $hrPayrollDetails */
        $hrPayrollDetails = $this->hrPayrollDetailsRepository->findWithoutFail($id);

        if (empty($hrPayrollDetails)) {
            return $this->sendError(trans('custom.hr_payroll_details_not_found'));
        }

        $hrPayrollDetails->delete();

        return $this->sendSuccess('Hr Payroll Details deleted successfully');
    }
}
