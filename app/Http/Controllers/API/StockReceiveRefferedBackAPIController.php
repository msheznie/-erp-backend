<?php
/**
 * =============================================
 * -- File Name : StockReceiveRefferedBackAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Stock Receive Reffered Back
 * -- Author : Mohamed Fayas
 * -- Create date : 29 - November 2018
 * -- Description : This file contains the all CRUD for Stock Receive Reffered Back
 * -- REVISION HISTORY
 * -- Date: 29-November 2018 By: Fayas Description: Added new functions named as getReferBackHistoryByStockReceive()
 *
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateStockReceiveRefferedBackAPIRequest;
use App\Http\Requests\API\UpdateStockReceiveRefferedBackAPIRequest;
use App\Models\StockReceiveRefferedBack;
use App\Repositories\StockReceiveRefferedBackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class StockReceiveRefferedBackController
 * @package App\Http\Controllers\API
 */

class StockReceiveRefferedBackAPIController extends AppBaseController
{
    /** @var  StockReceiveRefferedBackRepository */
    private $stockReceiveRefferedBackRepository;

    public function __construct(StockReceiveRefferedBackRepository $stockReceiveRefferedBackRepo)
    {
        $this->stockReceiveRefferedBackRepository = $stockReceiveRefferedBackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/stockReceiveRefferedBacks",
     *      summary="Get a listing of the StockReceiveRefferedBacks.",
     *      tags={"StockReceiveRefferedBack"},
     *      description="Get all StockReceiveRefferedBacks",
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
     *                  @SWG\Items(ref="#/definitions/StockReceiveRefferedBack")
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
        $this->stockReceiveRefferedBackRepository->pushCriteria(new RequestCriteria($request));
        $this->stockReceiveRefferedBackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $stockReceiveRefferedBacks = $this->stockReceiveRefferedBackRepository->all();

        return $this->sendResponse($stockReceiveRefferedBacks->toArray(), trans('custom.stock_receive_reffered_backs_retrieved_successfull'));
    }

    /**
     * @param CreateStockReceiveRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/stockReceiveRefferedBacks",
     *      summary="Store a newly created StockReceiveRefferedBack in storage",
     *      tags={"StockReceiveRefferedBack"},
     *      description="Store StockReceiveRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="StockReceiveRefferedBack that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/StockReceiveRefferedBack")
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
     *                  ref="#/definitions/StockReceiveRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateStockReceiveRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        $stockReceiveRefferedBacks = $this->stockReceiveRefferedBackRepository->create($input);

        return $this->sendResponse($stockReceiveRefferedBacks->toArray(), trans('custom.stock_receive_reffered_back_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/stockReceiveRefferedBacks/{id}",
     *      summary="Display the specified StockReceiveRefferedBack",
     *      tags={"StockReceiveRefferedBack"},
     *      description="Get StockReceiveRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StockReceiveRefferedBack",
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
     *                  ref="#/definitions/StockReceiveRefferedBack"
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
        /** @var StockReceiveRefferedBack $stockReceiveRefferedBack */
        $stockReceiveRefferedBack = $this->stockReceiveRefferedBackRepository->with(['confirmed_by', 'segment_by','finance_period_by'=> function($query){
            $query->selectRaw("CONCAT(DATE_FORMAT(dateFrom,'%d/%m/%Y'),' | ',DATE_FORMAT(dateTo,'%d/%m/%Y')) as financePeriod,companyFinancePeriodID");
        },'finance_year_by'=> function($query){
            $query->selectRaw("CONCAT(DATE_FORMAT(bigginingDate,'%d/%m/%Y'),' | ',DATE_FORMAT(endingDate,'%d/%m/%Y')) as financeYear,companyFinanceYearID");
        }])->findWithoutFail($id);

        if (empty($stockReceiveRefferedBack)) {
            return $this->sendError(trans('custom.stock_receive_reffered_back_not_found'));
        }

        return $this->sendResponse($stockReceiveRefferedBack->toArray(), trans('custom.stock_receive_reffered_back_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateStockReceiveRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/stockReceiveRefferedBacks/{id}",
     *      summary="Update the specified StockReceiveRefferedBack in storage",
     *      tags={"StockReceiveRefferedBack"},
     *      description="Update StockReceiveRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StockReceiveRefferedBack",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="StockReceiveRefferedBack that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/StockReceiveRefferedBack")
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
     *                  ref="#/definitions/StockReceiveRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateStockReceiveRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        /** @var StockReceiveRefferedBack $stockReceiveRefferedBack */
        $stockReceiveRefferedBack = $this->stockReceiveRefferedBackRepository->findWithoutFail($id);

        if (empty($stockReceiveRefferedBack)) {
            return $this->sendError(trans('custom.stock_receive_reffered_back_not_found'));
        }

        $stockReceiveRefferedBack = $this->stockReceiveRefferedBackRepository->update($input, $id);

        return $this->sendResponse($stockReceiveRefferedBack->toArray(), trans('custom.stockreceiverefferedback_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/stockReceiveRefferedBacks/{id}",
     *      summary="Remove the specified StockReceiveRefferedBack from storage",
     *      tags={"StockReceiveRefferedBack"},
     *      description="Delete StockReceiveRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StockReceiveRefferedBack",
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
        /** @var StockReceiveRefferedBack $stockReceiveRefferedBack */
        $stockReceiveRefferedBack = $this->stockReceiveRefferedBackRepository->findWithoutFail($id);

        if (empty($stockReceiveRefferedBack)) {
            return $this->sendError(trans('custom.stock_receive_reffered_back_not_found'));
        }

        $stockReceiveRefferedBack->delete();

        return $this->sendResponse($id, trans('custom.stock_receive_reffered_back_deleted_successfully'));
    }

    public function getReferBackHistoryByStockReceive(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'locationFrom', 'locationTo', 'confirmedYN', 'approved',
            'grvRecieved', 'month', 'year', 'invoicedBooked'));
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $stockReceive = StockReceiveRefferedBack::where('companySystemID', $input['companyId'])
            ->where('documentSystemID', $input['documentId'])
            ->where('stockReceiveAutoID', $input['id'])
            ->with(['created_by', 'segment_by']);

        if (array_key_exists('serviceLineSystemID', $input)) {
            if ($input['serviceLineSystemID'] && !is_null($input['serviceLineSystemID'])) {
                $stockReceive->where('serviceLineSystemID', $input['serviceLineSystemID']);
            }
        }

        if (array_key_exists('locationFrom', $input)) {
            if ($input['locationFrom'] && !is_null($input['locationFrom'])) {
                $stockReceive->where('locationFrom', $input['locationFrom']);
            }
        }

        if (array_key_exists('locationTo', $input)) {
            if ($input['locationTo'] && !is_null($input['locationTo'])) {
                $stockReceive->where('locationTo', $input['locationTo']);
            }
        }

        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $stockReceive->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $stockReceive->where('approved', $input['approved']);
            }
        }

        if (array_key_exists('interCompanyTransferYN', $input)) {
            if (($input['interCompanyTransferYN'] == 0 || $input['interCompanyTransferYN'] == -1) && !is_null($input['interCompanyTransferYN'])) {
                $stockReceive->where('interCompanyTransferYN', $input['interCompanyTransferYN']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $stockReceive->whereMonth('receivedDate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $stockReceive->whereYear('receivedDate', '=', $input['year']);
            }
        }

        $stockReceive = $stockReceive->select(
            ['stockReceiveRefferedID',
              'stockReceiveAutoID',
                'stockReceiveCode',
                'documentSystemID',
                'refNo',
                'createdDateTime',
                'createdUserSystemID',
                'comment',
                'receivedDate',
                'serviceLineSystemID',
                'confirmedDate',
                'approvedDate',
                'timesReferred',
                'confirmedYN',
                'approved',
                'approvedDate',
                'refferedBackYN'
            ]);

        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $stockReceive = $stockReceive->where(function ($query) use ($search) {
                $query->where('stockReceiveCode', 'LIKE', "%{$search}%")
                    ->orWhere('comment', 'LIKE', "%{$search}%")
                    ->orWhere('refNo', 'LIKE', "%{$search}%");
            });
        }

        $policy = 0;

        return \DataTables::eloquent($stockReceive)
            ->addColumn('Actions', $policy)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('stockReceiveRefferedID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

}
