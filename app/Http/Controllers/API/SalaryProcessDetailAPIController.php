<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSalaryProcessDetailAPIRequest;
use App\Http\Requests\API\UpdateSalaryProcessDetailAPIRequest;
use App\Models\SalaryProcessDetail;
use App\Repositories\SalaryProcessDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SalaryProcessDetailController
 * @package App\Http\Controllers\API
 */

class SalaryProcessDetailAPIController extends AppBaseController
{
    /** @var  SalaryProcessDetailRepository */
    private $salaryProcessDetailRepository;

    public function __construct(SalaryProcessDetailRepository $salaryProcessDetailRepo)
    {
        $this->salaryProcessDetailRepository = $salaryProcessDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/salaryProcessDetails",
     *      summary="Get a listing of the SalaryProcessDetails.",
     *      tags={"SalaryProcessDetail"},
     *      description="Get all SalaryProcessDetails",
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
     *                  @SWG\Items(ref="#/definitions/SalaryProcessDetail")
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
        $this->salaryProcessDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->salaryProcessDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $salaryProcessDetails = $this->salaryProcessDetailRepository->all();

        return $this->sendResponse($salaryProcessDetails->toArray(), trans('custom.salary_process_details_retrieved_successfully'));
    }

    /**
     * @param CreateSalaryProcessDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/salaryProcessDetails",
     *      summary="Store a newly created SalaryProcessDetail in storage",
     *      tags={"SalaryProcessDetail"},
     *      description="Store SalaryProcessDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SalaryProcessDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SalaryProcessDetail")
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
     *                  ref="#/definitions/SalaryProcessDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSalaryProcessDetailAPIRequest $request)
    {
        $input = $request->all();

        $salaryProcessDetail = $this->salaryProcessDetailRepository->create($input);

        return $this->sendResponse($salaryProcessDetail->toArray(), trans('custom.salary_process_detail_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/salaryProcessDetails/{id}",
     *      summary="Display the specified SalaryProcessDetail",
     *      tags={"SalaryProcessDetail"},
     *      description="Get SalaryProcessDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SalaryProcessDetail",
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
     *                  ref="#/definitions/SalaryProcessDetail"
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
        /** @var SalaryProcessDetail $salaryProcessDetail */
        $salaryProcessDetail = $this->salaryProcessDetailRepository->findWithoutFail($id);

        if (empty($salaryProcessDetail)) {
            return $this->sendError(trans('custom.salary_process_detail_not_found'));
        }

        return $this->sendResponse($salaryProcessDetail->toArray(), trans('custom.salary_process_detail_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateSalaryProcessDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/salaryProcessDetails/{id}",
     *      summary="Update the specified SalaryProcessDetail in storage",
     *      tags={"SalaryProcessDetail"},
     *      description="Update SalaryProcessDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SalaryProcessDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SalaryProcessDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SalaryProcessDetail")
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
     *                  ref="#/definitions/SalaryProcessDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSalaryProcessDetailAPIRequest $request)
    {
        $input = $request->all();

        /** @var SalaryProcessDetail $salaryProcessDetail */
        $salaryProcessDetail = $this->salaryProcessDetailRepository->findWithoutFail($id);

        if (empty($salaryProcessDetail)) {
            return $this->sendError(trans('custom.salary_process_detail_not_found'));
        }

        $salaryProcessDetail = $this->salaryProcessDetailRepository->update($input, $id);

        return $this->sendResponse($salaryProcessDetail->toArray(), trans('custom.salaryprocessdetail_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/salaryProcessDetails/{id}",
     *      summary="Remove the specified SalaryProcessDetail from storage",
     *      tags={"SalaryProcessDetail"},
     *      description="Delete SalaryProcessDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SalaryProcessDetail",
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
        /** @var SalaryProcessDetail $salaryProcessDetail */
        $salaryProcessDetail = $this->salaryProcessDetailRepository->findWithoutFail($id);

        if (empty($salaryProcessDetail)) {
            return $this->sendError(trans('custom.salary_process_detail_not_found'));
        }

        $salaryProcessDetail->delete();

        return $this->sendResponse($id, trans('custom.salary_process_detail_deleted_successfully'));
    }
}
