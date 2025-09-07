<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateStockCountRefferedBackAPIRequest;
use App\Http\Requests\API\UpdateStockCountRefferedBackAPIRequest;
use App\Models\StockCountRefferedBack;
use App\Repositories\StockCountRefferedBackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class StockCountRefferedBackController
 * @package App\Http\Controllers\API
 */

class StockCountRefferedBackAPIController extends AppBaseController
{
    /** @var  StockCountRefferedBackRepository */
    private $stockCountRefferedBackRepository;

    public function __construct(StockCountRefferedBackRepository $stockCountRefferedBackRepo)
    {
        $this->stockCountRefferedBackRepository = $stockCountRefferedBackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/stockCountRefferedBacks",
     *      summary="Get a listing of the StockCountRefferedBacks.",
     *      tags={"StockCountRefferedBack"},
     *      description="Get all StockCountRefferedBacks",
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
     *                  @SWG\Items(ref="#/definitions/StockCountRefferedBack")
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
        $this->stockCountRefferedBackRepository->pushCriteria(new RequestCriteria($request));
        $this->stockCountRefferedBackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $stockCountRefferedBacks = $this->stockCountRefferedBackRepository->all();

        return $this->sendResponse($stockCountRefferedBacks->toArray(), trans('custom.stock_count_reffered_backs_retrieved_successfully'));
    }

    /**
     * @param CreateStockCountRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/stockCountRefferedBacks",
     *      summary="Store a newly created StockCountRefferedBack in storage",
     *      tags={"StockCountRefferedBack"},
     *      description="Store StockCountRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="StockCountRefferedBack that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/StockCountRefferedBack")
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
     *                  ref="#/definitions/StockCountRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateStockCountRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        $stockCountRefferedBack = $this->stockCountRefferedBackRepository->create($input);

        return $this->sendResponse($stockCountRefferedBack->toArray(), trans('custom.stock_count_reffered_back_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/stockCountRefferedBacks/{id}",
     *      summary="Display the specified StockCountRefferedBack",
     *      tags={"StockCountRefferedBack"},
     *      description="Get StockCountRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StockCountRefferedBack",
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
     *                  ref="#/definitions/StockCountRefferedBack"
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
        /** @var StockCountRefferedBack $stockCountRefferedBack */
        $stockCountRefferedBack = $this->stockCountRefferedBackRepository->with(['confirmed_by', 'created_by', 'finance_period_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(dateFrom,'%d/%m/%Y'),' | ',DATE_FORMAT(dateTo,'%d/%m/%Y')) as financePeriod,companyFinancePeriodID");
        }, 'finance_year_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(bigginingDate,'%d/%m/%Y'),' | ',DATE_FORMAT(endingDate,'%d/%m/%Y')) as financeYear,companyFinanceYearID");
        },'segment_by','warehouse_by'])->findWithoutFail($id);

        if (empty($stockCountRefferedBack)) {
            return $this->sendError(trans('custom.stock_count_reffered_back_not_found'));
        }

        return $this->sendResponse($stockCountRefferedBack->toArray(), trans('custom.stock_count_reffered_back_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateStockCountRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/stockCountRefferedBacks/{id}",
     *      summary="Update the specified StockCountRefferedBack in storage",
     *      tags={"StockCountRefferedBack"},
     *      description="Update StockCountRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StockCountRefferedBack",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="StockCountRefferedBack that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/StockCountRefferedBack")
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
     *                  ref="#/definitions/StockCountRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateStockCountRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        /** @var StockCountRefferedBack $stockCountRefferedBack */
        $stockCountRefferedBack = $this->stockCountRefferedBackRepository->findWithoutFail($id);

        if (empty($stockCountRefferedBack)) {
            return $this->sendError(trans('custom.stock_count_reffered_back_not_found'));
        }

        $stockCountRefferedBack = $this->stockCountRefferedBackRepository->update($input, $id);

        return $this->sendResponse($stockCountRefferedBack->toArray(), trans('custom.stockcountrefferedback_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/stockCountRefferedBacks/{id}",
     *      summary="Remove the specified StockCountRefferedBack from storage",
     *      tags={"StockCountRefferedBack"},
     *      description="Delete StockCountRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StockCountRefferedBack",
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
        /** @var StockCountRefferedBack $stockCountRefferedBack */
        $stockCountRefferedBack = $this->stockCountRefferedBackRepository->findWithoutFail($id);

        if (empty($stockCountRefferedBack)) {
            return $this->sendError(trans('custom.stock_count_reffered_back_not_found'));
        }

        $stockCountRefferedBack->delete();

        return $this->sendSuccess('Stock Count Reffered Back deleted successfully');
    }


    public function getReferBackHistoryByStockCounts(Request $request)
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

        $stockCount = StockCountRefferedBack::whereIn('companySystemID', $subCompanies)
            ->where('stockCountAutoID', $id)
            ->with(['created_by', 'warehouse_by', 'segment_by']);


        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $stockCount->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $stockCount->where('approved', $input['approved']);
            }
        }

        if (array_key_exists('serviceLineSystemID', $input)) {
            if ($input['serviceLineSystemID'] && !is_null($input['serviceLineSystemID'])) {
                $stockCount->where('serviceLineSystemID', $input['serviceLineSystemID']);
            }
        }

        if (array_key_exists('location', $input)) {
            if ($input['location'] && !is_null($input['location'])) {
                $stockCount->where('location', $input['location']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $stockCount->whereMonth('stockCountDate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $stockCount->whereYear('stockCountDate', '=', $input['year']);
            }
        }



        $stockCount = $stockCount->select(
            ['stockCountAutoID',
                'stockCountAutoRefferedbackID',
                'stockCountCode',
                'comment',
                'stockCountDate',
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
            $stockCount = $stockCount->where(function ($query) use ($search) {
                $query->where('stockCountCode', 'LIKE', "%{$search}%")
                    ->orWhere('comment', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($stockCount)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('stockCountAutoRefferedbackID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }
}
