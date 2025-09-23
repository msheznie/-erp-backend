<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateChartOfAccountAllocationDetailAPIRequest;
use App\Http\Requests\API\UpdateChartOfAccountAllocationDetailAPIRequest;
use App\Models\ChartOfAccountAllocationDetail;
use App\Models\Company;
use App\Models\SegmentMaster;
use App\Repositories\ChartOfAccountAllocationDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ChartOfAccountAllocationDetailController
 * @package App\Http\Controllers\API
 */

class ChartOfAccountAllocationDetailAPIController extends AppBaseController
{
    /** @var  ChartOfAccountAllocationDetailRepository */
    private $chartOfAccountAllocationDetailRepository;

    public function __construct(ChartOfAccountAllocationDetailRepository $chartOfAccountAllocationDetailRepo)
    {
        $this->chartOfAccountAllocationDetailRepository = $chartOfAccountAllocationDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/chartOfAccountAllocationDetails",
     *      summary="Get a listing of the ChartOfAccountAllocationDetails.",
     *      tags={"ChartOfAccountAllocationDetail"},
     *      description="Get all ChartOfAccountAllocationDetails",
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
     *                  @SWG\Items(ref="#/definitions/ChartOfAccountAllocationDetail")
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
        $this->chartOfAccountAllocationDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->chartOfAccountAllocationDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $chartOfAccountAllocationDetails = $this->chartOfAccountAllocationDetailRepository->all();

        return $this->sendResponse($chartOfAccountAllocationDetails->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.chart_of_account_allocation_details')]));
    }

    /**
     * @param CreateChartOfAccountAllocationDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/chartOfAccountAllocationDetails",
     *      summary="Store a newly created ChartOfAccountAllocationDetail in storage",
     *      tags={"ChartOfAccountAllocationDetail"},
     *      description="Store ChartOfAccountAllocationDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ChartOfAccountAllocationDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ChartOfAccountAllocationDetail")
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
     *                  ref="#/definitions/ChartOfAccountAllocationDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateChartOfAccountAllocationDetailAPIRequest $request)
    {
        $input = $request->all();
        $messages = [
            'allocationmaid.required' => trans('custom.validation_allocation_master_id_required')
        ];
        $validator = \Validator::make($input, [
            'chartOfAccountAllocationMasterID' => 'required|numeric|min:1',
            'companySystemID' => 'required|numeric|min:1',
            'allocationmaid' => 'required|numeric|min:1',
            'productLineID' => 'required|numeric|min:1',
            'percentage' => 'required'
        ], $messages);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }
        $input = $this->convertArrayToValue($input);

        if (isset($input['companySystemID'])) {

            $companyMaster = Company::where('companySystemID', $input['companySystemID'])->first();

            if ($companyMaster) {
                $input['companyid'] = $companyMaster->CompanyID;
            }
        }

        if (isset($input['productLineID'])) {

            $segmentMaster = SegmentMaster::where('serviceLineSystemID', $input['productLineID'])->first();

            if ($segmentMaster) {
                $input['productLineCode'] = $segmentMaster->ServiceLineCode;
            }
        }
        $input['timestamp'] = now();

        $chartOfAccountAllocationDetail = $this->chartOfAccountAllocationDetailRepository->create($input);

        return $this->sendResponse($chartOfAccountAllocationDetail->toArray(), trans('custom.save', ['attribute' => trans('custom.chart_of_account_allocation_details')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/chartOfAccountAllocationDetails/{id}",
     *      summary="Display the specified ChartOfAccountAllocationDetail",
     *      tags={"ChartOfAccountAllocationDetail"},
     *      description="Get ChartOfAccountAllocationDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ChartOfAccountAllocationDetail",
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
     *                  ref="#/definitions/ChartOfAccountAllocationDetail"
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
        /** @var ChartOfAccountAllocationDetail $chartOfAccountAllocationDetail */
        $chartOfAccountAllocationDetail = $this->chartOfAccountAllocationDetailRepository->findWithoutFail($id);

        if (empty($chartOfAccountAllocationDetail)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.chart_of_account_allocation_details')]));
        }

        return $this->sendResponse($chartOfAccountAllocationDetail->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.chart_of_account_allocation_details')]));
    }

    /**
     * @param int $id
     * @param UpdateChartOfAccountAllocationDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/chartOfAccountAllocationDetails/{id}",
     *      summary="Update the specified ChartOfAccountAllocationDetail in storage",
     *      tags={"ChartOfAccountAllocationDetail"},
     *      description="Update ChartOfAccountAllocationDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ChartOfAccountAllocationDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ChartOfAccountAllocationDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ChartOfAccountAllocationDetail")
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
     *                  ref="#/definitions/ChartOfAccountAllocationDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateChartOfAccountAllocationDetailAPIRequest $request)
    {
        $input = $request->all();
        $input = array_except($input,'segment');
        /** @var ChartOfAccountAllocationDetail $chartOfAccountAllocationDetail */
        $chartOfAccountAllocationDetail = $this->chartOfAccountAllocationDetailRepository->findWithoutFail($id);

        if (empty($chartOfAccountAllocationDetail)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.chart_of_account_allocation_details')]));
        }

        $checkAllocationPercentage = ChartOfAccountAllocationDetail::where('chartOfAccountAllocationMasterID', $chartOfAccountAllocationDetail->chartOfAccountAllocationMasterID)
                                                                    ->where('chartOfAccountAllocationDetailID', '!=', $id)
                                                                   ->sum('percentage');

        $totalAllocationPercentage = $input['percentage'] + (($checkAllocationPercentage) ? $checkAllocationPercentage : 0);

        if ($totalAllocationPercentage > 100) {
            return $this->sendError("Total allocation percentage cannot be greater than 100",500);
        }

        $chartOfAccountAllocationDetail = $this->chartOfAccountAllocationDetailRepository->update($input, $id);

        return $this->sendResponse($chartOfAccountAllocationDetail->toArray(), trans('custom.update', ['attribute' => trans('custom.chart_of_account_allocation_details')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/chartOfAccountAllocationDetails/{id}",
     *      summary="Remove the specified ChartOfAccountAllocationDetail from storage",
     *      tags={"ChartOfAccountAllocationDetail"},
     *      description="Delete ChartOfAccountAllocationDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ChartOfAccountAllocationDetail",
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
        /** @var ChartOfAccountAllocationDetail $chartOfAccountAllocationDetail */
        $chartOfAccountAllocationDetail = $this->chartOfAccountAllocationDetailRepository->findWithoutFail($id);

        if (empty($chartOfAccountAllocationDetail)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.chart_of_account_allocation_details')]));
        }

        $chartOfAccountAllocationDetail->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.chart_of_account_allocation_details')]));
    }
}
