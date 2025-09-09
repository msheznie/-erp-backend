<?php
/**
 * =============================================
 * -- File Name : StockAdjustmentRefferedBackAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Stock Adjustment Reffered Back
 * -- Author : Mohamed Fayas
 * -- Create date : 6 - February 2019
 * -- Description : This file contains the all CRUD for Stock Adjustment Reffered Back
 * -- REVISION HISTORY
 * -- Date: 06 - February 2019 By: Fayas Description: Added new functions named as getReferBackHistoryByStockAdjustments()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateStockAdjustmentRefferedBackAPIRequest;
use App\Http\Requests\API\UpdateStockAdjustmentRefferedBackAPIRequest;
use App\Models\StockAdjustmentRefferedBack;
use App\Repositories\StockAdjustmentRefferedBackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class StockAdjustmentRefferedBackController
 * @package App\Http\Controllers\API
 */

class StockAdjustmentRefferedBackAPIController extends AppBaseController
{
    /** @var  StockAdjustmentRefferedBackRepository */
    private $stockAdjustmentRefferedBackRepository;

    public function __construct(StockAdjustmentRefferedBackRepository $stockAdjustmentRefferedBackRepo)
    {
        $this->stockAdjustmentRefferedBackRepository = $stockAdjustmentRefferedBackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/stockAdjustmentRefferedBacks",
     *      summary="Get a listing of the StockAdjustmentRefferedBacks.",
     *      tags={"StockAdjustmentRefferedBack"},
     *      description="Get all StockAdjustmentRefferedBacks",
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
     *                  @SWG\Items(ref="#/definitions/StockAdjustmentRefferedBack")
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
        $this->stockAdjustmentRefferedBackRepository->pushCriteria(new RequestCriteria($request));
        $this->stockAdjustmentRefferedBackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $stockAdjustmentRefferedBacks = $this->stockAdjustmentRefferedBackRepository->all();

        return $this->sendResponse($stockAdjustmentRefferedBacks->toArray(), trans('custom.stock_adjustment_reffered_backs_retrieved_successf'));
    }

    /**
     * @param CreateStockAdjustmentRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/stockAdjustmentRefferedBacks",
     *      summary="Store a newly created StockAdjustmentRefferedBack in storage",
     *      tags={"StockAdjustmentRefferedBack"},
     *      description="Store StockAdjustmentRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="StockAdjustmentRefferedBack that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/StockAdjustmentRefferedBack")
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
     *                  ref="#/definitions/StockAdjustmentRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateStockAdjustmentRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        $stockAdjustmentRefferedBacks = $this->stockAdjustmentRefferedBackRepository->create($input);

        return $this->sendResponse($stockAdjustmentRefferedBacks->toArray(), trans('custom.stock_adjustment_reffered_back_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/stockAdjustmentRefferedBacks/{id}",
     *      summary="Display the specified StockAdjustmentRefferedBack",
     *      tags={"StockAdjustmentRefferedBack"},
     *      description="Get StockAdjustmentRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StockAdjustmentRefferedBack",
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
     *                  ref="#/definitions/StockAdjustmentRefferedBack"
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
        /** @var StockAdjustmentRefferedBack $stockAdjustmentRefferedBack */
        $stockAdjustmentRefferedBack = $this->stockAdjustmentRefferedBackRepository->with(['confirmed_by', 'created_by', 'finance_period_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(dateFrom,'%d/%m/%Y'),' | ',DATE_FORMAT(dateTo,'%d/%m/%Y')) as financePeriod,companyFinancePeriodID");
        }, 'finance_year_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(bigginingDate,'%d/%m/%Y'),' | ',DATE_FORMAT(endingDate,'%d/%m/%Y')) as financeYear,companyFinanceYearID");
        }])->findWithoutFail($id);

        if (empty($stockAdjustmentRefferedBack)) {
            return $this->sendError(trans('custom.stock_adjustment_not_found'));
        }

        return $this->sendResponse($stockAdjustmentRefferedBack->toArray(), trans('custom.stock_adjustment_referred_back_retrieved_successfu'));
    }

    /**
     * @param int $id
     * @param UpdateStockAdjustmentRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/stockAdjustmentRefferedBacks/{id}",
     *      summary="Update the specified StockAdjustmentRefferedBack in storage",
     *      tags={"StockAdjustmentRefferedBack"},
     *      description="Update StockAdjustmentRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StockAdjustmentRefferedBack",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="StockAdjustmentRefferedBack that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/StockAdjustmentRefferedBack")
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
     *                  ref="#/definitions/StockAdjustmentRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateStockAdjustmentRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        /** @var StockAdjustmentRefferedBack $stockAdjustmentRefferedBack */
        $stockAdjustmentRefferedBack = $this->stockAdjustmentRefferedBackRepository->findWithoutFail($id);

        if (empty($stockAdjustmentRefferedBack)) {
            return $this->sendError(trans('custom.stock_adjustment_reffered_back_not_found'));
        }

        $stockAdjustmentRefferedBack = $this->stockAdjustmentRefferedBackRepository->update($input, $id);

        return $this->sendResponse($stockAdjustmentRefferedBack->toArray(), trans('custom.stockadjustmentrefferedback_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/stockAdjustmentRefferedBacks/{id}",
     *      summary="Remove the specified StockAdjustmentRefferedBack from storage",
     *      tags={"StockAdjustmentRefferedBack"},
     *      description="Delete StockAdjustmentRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StockAdjustmentRefferedBack",
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
        /** @var StockAdjustmentRefferedBack $stockAdjustmentRefferedBack */
        $stockAdjustmentRefferedBack = $this->stockAdjustmentRefferedBackRepository->findWithoutFail($id);

        if (empty($stockAdjustmentRefferedBack)) {
            return $this->sendError(trans('custom.stock_adjustment_reffered_back_not_found'));
        }

        $stockAdjustmentRefferedBack->delete();

        return $this->sendResponse($id, trans('custom.stock_adjustment_reffered_back_deleted_successfull'));
    }

    public function getReferBackHistoryByStockAdjustments(Request $request)
    {

        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'confirmedYN', 'approved', 'location', 'month', 'year'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $selectedCompanyId = $request['companyId'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);
        $id = array_key_exists('id', $input)?$input['id']:0;
        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $stockAdjustments = StockAdjustmentRefferedBack::whereIn('companySystemID', $subCompanies)
            ->where('stockAdjustmentAutoID', $id)
            ->with(['created_by', 'warehouse_by', 'segment_by']);


        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $stockAdjustments->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $stockAdjustments->where('approved', $input['approved']);
            }
        }

        if (array_key_exists('serviceLineSystemID', $input)) {
            if ($input['serviceLineSystemID'] && !is_null($input['serviceLineSystemID'])) {
                $stockAdjustments->where('serviceLineSystemID', $input['serviceLineSystemID']);
            }
        }

        if (array_key_exists('location', $input)) {
            if ($input['location'] && !is_null($input['location'])) {
                $stockAdjustments->where('location', $input['location']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $stockAdjustments->whereMonth('stockAdjustmentDate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $stockAdjustments->whereYear('stockAdjustmentDate', '=', $input['year']);
            }
        }



        $stockAdjustments = $stockAdjustments->select(
            ['stockAdjustmentAutoID',
                'stockAdjustmentAutoRefferedbackID',
                'stockAdjustmentCode',
                'comment',
                'stockAdjustmentDate',
                'confirmedYN',
                'approved',
                'serviceLineSystemID',
                'documentSystemID',
                'confirmedByEmpSystemID',
                'createdUserSystemID',
                'confirmedDate',
                'createdDateTime',
                'refNo',
                'location',
                'timesReferred'
            ]);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $stockAdjustments = $stockAdjustments->where(function ($query) use ($search) {
                $query->where('stockAdjustmentCode', 'LIKE', "%{$search}%")
                    ->orWhere('comment', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($stockAdjustments)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('stockAdjustmentAutoRefferedbackID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }
}
