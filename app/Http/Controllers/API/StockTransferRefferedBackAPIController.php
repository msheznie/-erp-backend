<?php
/**
 * =============================================
 * -- File Name : StockTransferRefferedBackAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Stock Transfer Referred Back
 * -- Author : Mohamed Fayas
 * -- Create date : 29 - November 2018
 * -- Description : This file contains the all CRUD for Stock Transfer Referred Back
 * -- REVISION HISTORY
 * -- Date: 29-November 2018 By: Fayas Description: Added new functions named as getReferBackHistoryByStockTransfer()
 *
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateStockTransferRefferedBackAPIRequest;
use App\Http\Requests\API\UpdateStockTransferRefferedBackAPIRequest;
use App\Models\StockTransferRefferedBack;
use App\Repositories\StockTransferRefferedBackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class StockTransferRefferedBackController
 * @package App\Http\Controllers\API
 */

class StockTransferRefferedBackAPIController extends AppBaseController
{
    /** @var  StockTransferRefferedBackRepository */
    private $stockTransferRefferedBackRepository;

    public function __construct(StockTransferRefferedBackRepository $stockTransferRefferedBackRepo)
    {
        $this->stockTransferRefferedBackRepository = $stockTransferRefferedBackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/stockTransferRefferedBacks",
     *      summary="Get a listing of the StockTransferRefferedBacks.",
     *      tags={"StockTransferRefferedBack"},
     *      description="Get all StockTransferRefferedBacks",
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
     *                  @SWG\Items(ref="#/definitions/StockTransferRefferedBack")
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
        $this->stockTransferRefferedBackRepository->pushCriteria(new RequestCriteria($request));
        $this->stockTransferRefferedBackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $stockTransferRefferedBacks = $this->stockTransferRefferedBackRepository->all();

        return $this->sendResponse($stockTransferRefferedBacks->toArray(), trans('custom.stock_transfer_reffered_backs_retrieved_successful'));
    }

    /**
     * @param CreateStockTransferRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/stockTransferRefferedBacks",
     *      summary="Store a newly created StockTransferRefferedBack in storage",
     *      tags={"StockTransferRefferedBack"},
     *      description="Store StockTransferRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="StockTransferRefferedBack that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/StockTransferRefferedBack")
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
     *                  ref="#/definitions/StockTransferRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateStockTransferRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        $stockTransferRefferedBacks = $this->stockTransferRefferedBackRepository->create($input);

        return $this->sendResponse($stockTransferRefferedBacks->toArray(), trans('custom.stock_transfer_reffered_back_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/stockTransferRefferedBacks/{id}",
     *      summary="Display the specified StockTransferRefferedBack",
     *      tags={"StockTransferRefferedBack"},
     *      description="Get StockTransferRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StockTransferRefferedBack",
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
     *                  ref="#/definitions/StockTransferRefferedBack"
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
        /** @var StockTransferRefferedBack $stockTransferRefferedBack */
        $stockTransferRefferedBack = $this->stockTransferRefferedBackRepository->with(['created_by', 'confirmed_by', 'segment_by', 'finance_period_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(dateFrom,'%d/%m/%Y'),' | ',DATE_FORMAT(dateTo,'%d/%m/%Y')) as financePeriod,companyFinancePeriodID");
        }, 'finance_year_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(bigginingDate,'%d/%m/%Y'),' | ',DATE_FORMAT(endingDate,'%d/%m/%Y')) as financeYear,companyFinanceYearID");
        }])->findWithoutFail($id);

        if (empty($stockTransferRefferedBack)) {
            return $this->sendError(trans('custom.stock_transfer_referred_back_not_found'));
        }

        return $this->sendResponse($stockTransferRefferedBack->toArray(), trans('custom.stock_transfer_referred_back_retrieved_successfull'));
    }

    /**
     * @param int $id
     * @param UpdateStockTransferRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/stockTransferRefferedBacks/{id}",
     *      summary="Update the specified StockTransferRefferedBack in storage",
     *      tags={"StockTransferRefferedBack"},
     *      description="Update StockTransferRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StockTransferRefferedBack",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="StockTransferRefferedBack that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/StockTransferRefferedBack")
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
     *                  ref="#/definitions/StockTransferRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateStockTransferRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        /** @var StockTransferRefferedBack $stockTransferRefferedBack */
        $stockTransferRefferedBack = $this->stockTransferRefferedBackRepository->findWithoutFail($id);

        if (empty($stockTransferRefferedBack)) {
            return $this->sendError(trans('custom.stock_transfer_reffered_back_not_found'));
        }

        $stockTransferRefferedBack = $this->stockTransferRefferedBackRepository->update($input, $id);

        return $this->sendResponse($stockTransferRefferedBack->toArray(), trans('custom.stocktransferrefferedback_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/stockTransferRefferedBacks/{id}",
     *      summary="Remove the specified StockTransferRefferedBack from storage",
     *      tags={"StockTransferRefferedBack"},
     *      description="Delete StockTransferRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StockTransferRefferedBack",
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
        /** @var StockTransferRefferedBack $stockTransferRefferedBack */
        $stockTransferRefferedBack = $this->stockTransferRefferedBackRepository->findWithoutFail($id);

        if (empty($stockTransferRefferedBack)) {
            return $this->sendError(trans('custom.stock_transfer_reffered_back_not_found'));
        }

        $stockTransferRefferedBack->delete();

        return $this->sendResponse($id, trans('custom.stock_transfer_reffered_back_deleted_successfully'));
    }

    public function getReferBackHistoryByStockTransfer(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'locationFrom', 'confirmedYN', 'approved', 'month', 'year', 'interCompanyTransferYN'));
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $stockTransferMaster = StockTransferRefferedBack::where('companySystemID', $input['companyId'])
                                                            ->where('stockTransferAutoID', $input['id'])
                                                            ->where('documentSystemID', $input['documentId'])
                                                            ->with(['created_by', 'segment_by']);

        if (array_key_exists('serviceLineSystemID', $input)) {
            if ($input['serviceLineSystemID'] && !is_null($input['serviceLineSystemID'])) {
                $stockTransferMaster->where('serviceLineSystemID', $input['serviceLineSystemID']);
            }
        }

        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $stockTransferMaster->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $stockTransferMaster->where('approved', $input['approved']);
            }
        }

        if (array_key_exists('interCompanyTransferYN', $input)) {
            if (($input['interCompanyTransferYN'] == 0 || $input['interCompanyTransferYN'] == -1) && !is_null($input['interCompanyTransferYN'])) {
                $stockTransferMaster->where('interCompanyTransferYN', $input['interCompanyTransferYN']);
            }
        }

        if (array_key_exists('locationFrom', $input)) {
            if ($input['locationFrom'] && !is_null($input['locationFrom'])) {
                $stockTransferMaster->where('locationFrom', '=', $input['locationFrom']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $stockTransferMaster->whereMonth('tranferDate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $stockTransferMaster->whereYear('tranferDate', '=', $input['year']);
            }
        }

        $stockTransferMaster = $stockTransferMaster->select(
            ['erp_stocktransferrefferedback.stockTransferAutoID',
                'erp_stocktransferrefferedback.stockTransferRefferedID',
                'erp_stocktransferrefferedback.stockTransferCode',
                'erp_stocktransferrefferedback.documentSystemID',
                'erp_stocktransferrefferedback.refNo',
                'erp_stocktransferrefferedback.createdDateTime',
                'erp_stocktransferrefferedback.createdUserSystemID',
                'erp_stocktransferrefferedback.comment',
                'erp_stocktransferrefferedback.tranferDate',
                'erp_stocktransferrefferedback.serviceLineSystemID',
                'erp_stocktransferrefferedback.confirmedDate',
                'erp_stocktransferrefferedback.approvedDate',
                'erp_stocktransferrefferedback.timesReferred',
                'erp_stocktransferrefferedback.confirmedYN',
                'erp_stocktransferrefferedback.approved',
                'erp_stocktransferrefferedback.approvedDate',
                'erp_stocktransferrefferedback.fullyReceived',
                'erp_stocktransferrefferedback.refferedBackYN'
            ]);

        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $stockTransferMaster = $stockTransferMaster->where(function ($query) use ($search) {
                $query->where('stockTransferCode', 'LIKE', "%{$search}%")
                    ->orWhere('comment', 'LIKE', "%{$search}%")
                    ->orWhere('refNo', 'LIKE', "%{$search}%");
            });
        }

        $policy = 0;

        return \DataTables::eloquent($stockTransferMaster)
            ->addColumn('Actions', $policy)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('stockTransferRefferedID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

}
