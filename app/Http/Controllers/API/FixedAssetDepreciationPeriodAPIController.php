<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateFixedAssetDepreciationPeriodAPIRequest;
use App\Http\Requests\API\UpdateFixedAssetDepreciationPeriodAPIRequest;
use App\Models\FixedAssetDepreciationPeriod;
use App\Repositories\FixedAssetDepreciationPeriodRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class FixedAssetDepreciationPeriodController
 * @package App\Http\Controllers\API
 */
class FixedAssetDepreciationPeriodAPIController extends AppBaseController
{
    /** @var  FixedAssetDepreciationPeriodRepository */
    private $fixedAssetDepreciationPeriodRepository;

    public function __construct(FixedAssetDepreciationPeriodRepository $fixedAssetDepreciationPeriodRepo)
    {
        $this->fixedAssetDepreciationPeriodRepository = $fixedAssetDepreciationPeriodRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/fixedAssetDepreciationPeriods",
     *      summary="Get a listing of the FixedAssetDepreciationPeriods.",
     *      tags={"FixedAssetDepreciationPeriod"},
     *      description="Get all FixedAssetDepreciationPeriods",
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
     *                  @SWG\Items(ref="#/definitions/FixedAssetDepreciationPeriod")
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
        $this->fixedAssetDepreciationPeriodRepository->pushCriteria(new RequestCriteria($request));
        $this->fixedAssetDepreciationPeriodRepository->pushCriteria(new LimitOffsetCriteria($request));
        $fixedAssetDepreciationPeriods = $this->fixedAssetDepreciationPeriodRepository->all();

        return $this->sendResponse($fixedAssetDepreciationPeriods->toArray(), trans('custom.fixed_asset_depreciation_periods_retrieved_success'));
    }

    /**
     * @param CreateFixedAssetDepreciationPeriodAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/fixedAssetDepreciationPeriods",
     *      summary="Store a newly created FixedAssetDepreciationPeriod in storage",
     *      tags={"FixedAssetDepreciationPeriod"},
     *      description="Store FixedAssetDepreciationPeriod",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="FixedAssetDepreciationPeriod that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/FixedAssetDepreciationPeriod")
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
     *                  ref="#/definitions/FixedAssetDepreciationPeriod"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateFixedAssetDepreciationPeriodAPIRequest $request)
    {
        $input = $request->all();

        $fixedAssetDepreciationPeriods = $this->fixedAssetDepreciationPeriodRepository->create($input);

        return $this->sendResponse($fixedAssetDepreciationPeriods->toArray(), trans('custom.fixed_asset_depreciation_period_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/fixedAssetDepreciationPeriods/{id}",
     *      summary="Display the specified FixedAssetDepreciationPeriod",
     *      tags={"FixedAssetDepreciationPeriod"},
     *      description="Get FixedAssetDepreciationPeriod",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FixedAssetDepreciationPeriod",
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
     *                  ref="#/definitions/FixedAssetDepreciationPeriod"
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
        /** @var FixedAssetDepreciationPeriod $fixedAssetDepreciationPeriod */
        $fixedAssetDepreciationPeriod = $this->fixedAssetDepreciationPeriodRepository->findWithoutFail($id);

        if (empty($fixedAssetDepreciationPeriod)) {
            return $this->sendError(trans('custom.fixed_asset_depreciation_period_not_found'));
        }

        return $this->sendResponse($fixedAssetDepreciationPeriod->toArray(), trans('custom.fixed_asset_depreciation_period_retrieved_successf'));
    }

    /**
     * @param int $id
     * @param UpdateFixedAssetDepreciationPeriodAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/fixedAssetDepreciationPeriods/{id}",
     *      summary="Update the specified FixedAssetDepreciationPeriod in storage",
     *      tags={"FixedAssetDepreciationPeriod"},
     *      description="Update FixedAssetDepreciationPeriod",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FixedAssetDepreciationPeriod",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="FixedAssetDepreciationPeriod that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/FixedAssetDepreciationPeriod")
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
     *                  ref="#/definitions/FixedAssetDepreciationPeriod"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateFixedAssetDepreciationPeriodAPIRequest $request)
    {
        $input = $request->all();

        /** @var FixedAssetDepreciationPeriod $fixedAssetDepreciationPeriod */
        $fixedAssetDepreciationPeriod = $this->fixedAssetDepreciationPeriodRepository->findWithoutFail($id);

        if (empty($fixedAssetDepreciationPeriod)) {
            return $this->sendError(trans('custom.fixed_asset_depreciation_period_not_found'));
        }

        $fixedAssetDepreciationPeriod = $this->fixedAssetDepreciationPeriodRepository->update($input, $id);

        return $this->sendResponse($fixedAssetDepreciationPeriod->toArray(), trans('custom.fixedassetdepreciationperiod_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/fixedAssetDepreciationPeriods/{id}",
     *      summary="Remove the specified FixedAssetDepreciationPeriod from storage",
     *      tags={"FixedAssetDepreciationPeriod"},
     *      description="Delete FixedAssetDepreciationPeriod",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FixedAssetDepreciationPeriod",
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
        /** @var FixedAssetDepreciationPeriod $fixedAssetDepreciationPeriod */
        $fixedAssetDepreciationPeriod = $this->fixedAssetDepreciationPeriodRepository->findWithoutFail($id);

        if (empty($fixedAssetDepreciationPeriod)) {
            return $this->sendError(trans('custom.fixed_asset_depreciation_period_not_found'));
        }

        $fixedAssetDepreciationPeriod->delete();

        return $this->sendResponse($id, trans('custom.fixed_asset_depreciation_period_deleted_successful'));
    }


    public function getAssetDepPeriodsByID(Request $request)
    {
        ini_set('max_execution_time', 5000);
        ini_set('memory_limit', '1024M');
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $assetDepPeriod = FixedAssetDepreciationPeriod::with(['maincategory_by', 'financecategory_by', 'serviceline_by'])->ofDepreciation($input['depMasterAutoID']);

        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $assetDepPeriod = $assetDepPeriod->where(function ($query) use ($search) {
                $query->where('faCode', 'LIKE', "%{$search}%");
                $query->orWhere('assetDescription', 'LIKE', "%{$search}%");
            });
        }

        $outputSUM = $assetDepPeriod->get();

        $depAmountLocal = collect($outputSUM)->pluck('depAmountLocal')->toArray();
        $depAmountLocal = array_sum($depAmountLocal);

        $depAmountRpt = collect($outputSUM)->pluck('depAmountRpt')->toArray();
        $depAmountRpt = array_sum($depAmountRpt);

        return \DataTables::eloquent($assetDepPeriod)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('DepreciationPeriodsID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->with('totalAmount', [
                'depAmountLocal' => $depAmountLocal,
                'depAmountRpt' => $depAmountRpt,
            ])
            ->make(true);
    }

    function exportAMDepreciation(Request $request)
    {
        $type = $request->type;
        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        //$input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'confirmedYN', 'approved', 'wareHouseFrom', 'month', 'year'));
        $companyCurrency = \Helper::companyCurrency($input['companyID']);

        $assetDepPeriod = FixedAssetDepreciationPeriod::with(['maincategory_by', 'financecategory_by', 'serviceline_by'])->ofDepreciation($input['depMasterAutoID']);

        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $assetDepPeriod = $assetDepPeriod->where(function ($query) use ($search) {
                $query->where('faCode', 'LIKE', "%{$search}%");
                $query->orWhere('assetDescription', 'LIKE', "%{$search}%");
            });
        }

        $outputSUM = $assetDepPeriod->get();
        if ($outputSUM) {
            $x = 0;
            foreach ($outputSUM as $val) {
                $data[$x]['FA Code'] = $val->faCode;
                $data[$x]['Asset Description'] = $val->assetDescription;
                $data[$x]['Department'] = $val->serviceline_by? $val->serviceline_by->ServiceLineDes : '';
                $data[$x]['Finance Category'] = $val->financecategory_by? $val->financecategory_by->financeCatDescription : '';
                $data[$x]['Category'] = $val->maincategory_by? $val->maincategory_by->catDescription : '';
                $data[$x]['Dep Percent'] = $val->depPercent;
                $data[$x]['Cost Unit'] = number_format($val->COSTUNIT, $val->localcurrency? $val->localcurrency->DecimalPlaces : 2);
                $data[$x]['Cost Unit Rpt'] = number_format($val->costUnitRpt, $val->reportingcurrency? $val->reportingcurrency->DecimalPlaces : 2);
                $data[$x]['Dep Amount Local'] = number_format($val->depAmountLocal, $val->localcurrency? $val->localcurrency->DecimalPlaces : 2);
                $data[$x]['Dep Amount Rpt'] = number_format($val->depAmountRpt, $val->reportingcurrency? $val->reportingcurrency->DecimalPlaces : 2);
                $x++;
            }
        } else {
            $data = array();
        }
         \Excel::create('asset_depreciation', function ($excel) use ($data) {
            $excel->sheet('sheet name', function ($sheet) use ($data) {
                $sheet->fromArray($data, null, 'A1', true);
                $sheet->setAutoSize(true);
                $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
            });
            $lastrow = $excel->getActiveSheet()->getHighestRow();
            $excel->getActiveSheet()->getStyle('A1:J' . $lastrow)->getAlignment()->setWrapText(true);
        })->download($type);

        return $this->sendResponse(array(), trans('custom.success_export'));
    }
}
