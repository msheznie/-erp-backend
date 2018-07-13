<?php
/**
 * =============================================
 * -- File Name : StockTransferAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Stock Transfer
 * -- Author : Mohamed Nazir
 * -- Create date : 13-July 2018
 * -- Description : This file contains the all CRUD for Stock Transfer
 * -- REVISION HISTORY
 * -- Date: 13-July 2018 By: Nazir Description: Added new functions named as getStockTransferMasterView() For load Master View
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateStockTransferAPIRequest;
use App\Http\Requests\API\UpdateStockTransferAPIRequest;
use App\Models\CompanyFinanceYear;
use App\Models\Months;
use App\Models\SegmentMaster;
use App\Models\StockTransfer;
use App\Models\WarehouseMaster;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Repositories\StockTransferRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Response;

/**
 * Class StockTransferController
 * @package App\Http\Controllers\API
 */

class StockTransferAPIController extends AppBaseController
{
    /** @var  StockTransferRepository */
    private $stockTransferRepository;
    private $userRepository;

    public function __construct(StockTransferRepository $stockTransferRepo, UserRepository $userRepo)
    {
        $this->stockTransferRepository = $stockTransferRepo;
        $this->userRepository = $userRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/stockTransfers",
     *      summary="Get a listing of the StockTransfers.",
     *      tags={"StockTransfer"},
     *      description="Get all StockTransfers",
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
     *                  @SWG\Items(ref="#/definitions/StockTransfer")
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
        $this->stockTransferRepository->pushCriteria(new RequestCriteria($request));
        $this->stockTransferRepository->pushCriteria(new LimitOffsetCriteria($request));
        $stockTransfers = $this->stockTransferRepository->all();

        return $this->sendResponse($stockTransfers->toArray(), 'Stock Transfers retrieved successfully');
    }

    /**
     * @param CreateStockTransferAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/stockTransfers",
     *      summary="Store a newly created StockTransfer in storage",
     *      tags={"StockTransfer"},
     *      description="Store StockTransfer",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="StockTransfer that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/StockTransfer")
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
     *                  ref="#/definitions/StockTransfer"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateStockTransferAPIRequest $request)
    {
        $input = $request->all();

        $stockTransfers = $this->stockTransferRepository->create($input);

        return $this->sendResponse($stockTransfers->toArray(), 'Stock Transfer saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/stockTransfers/{id}",
     *      summary="Display the specified StockTransfer",
     *      tags={"StockTransfer"},
     *      description="Get StockTransfer",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StockTransfer",
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
     *                  ref="#/definitions/StockTransfer"
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
        /** @var StockTransfer $stockTransfer */
        $stockTransfer = $this->stockTransferRepository->with(['created_by', 'confirmed_by', 'segment_by', 'location_by'])->findWithoutFail($id);

        if (empty($stockTransfer)) {
            return $this->sendError('Stock Transfer not found');
        }

        return $this->sendResponse($stockTransfer->toArray(), 'Stock Transfer retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateStockTransferAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/stockTransfers/{id}",
     *      summary="Update the specified StockTransfer in storage",
     *      tags={"StockTransfer"},
     *      description="Update StockTransfer",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StockTransfer",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="StockTransfer that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/StockTransfer")
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
     *                  ref="#/definitions/StockTransfer"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateStockTransferAPIRequest $request)
    {
        $input = $request->all();

        /** @var StockTransfer $stockTransfer */
        $stockTransfer = $this->stockTransferRepository->findWithoutFail($id);

        if (empty($stockTransfer)) {
            return $this->sendError('Stock Transfer not found');
        }

        $stockTransfer = $this->stockTransferRepository->update($input, $id);

        return $this->sendResponse($stockTransfer->toArray(), 'StockTransfer updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/stockTransfers/{id}",
     *      summary="Remove the specified StockTransfer from storage",
     *      tags={"StockTransfer"},
     *      description="Delete StockTransfer",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StockTransfer",
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
        /** @var StockTransfer $stockTransfer */
        $stockTransfer = $this->stockTransferRepository->findWithoutFail($id);

        if (empty($stockTransfer)) {
            return $this->sendError('Stock Transfer not found');
        }

        $stockTransfer->delete();

        return $this->sendResponse($id, 'Stock Transfer deleted successfully');
    }

    public function getStockTransferMasterView(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'grvLocation', 'poCancelledYN', 'poConfirmedYN', 'approved', 'grvRecieved', 'month', 'year', 'invoicedBooked'));
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $stockTransferMaster = StockTransfer::where('companySystemID', $input['companyId']);
        $stockTransferMaster->where('documentSystemID', $input['documentId']);
        $stockTransferMaster->with(['created_by' => function ($query) {
        }, 'segment_by' => function ($query) {
        }]);

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

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $stockTransferMaster->whereMonth('tranferDate+', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $stockTransferMaster->whereYear('tranferDate', '=', $input['year']);
            }
        }

        $stockTransferMaster = $stockTransferMaster->select(
            ['erp_stocktransfer.stockTransferAutoID',
                'erp_stocktransfer.stockTransferCode',
                'erp_stocktransfer.documentSystemID',
                'erp_stocktransfer.refNo',
                'erp_stocktransfer.createdDateTime',
                'erp_stocktransfer.createdUserSystemID',
                'erp_stocktransfer.comment',
                'erp_stocktransfer.tranferDate',
                'erp_stocktransfer.serviceLineSystemID',
                'erp_stocktransfer.confirmedDate',
                'erp_stocktransfer.approvedDate',
                'erp_stocktransfer.timesReferred',
                'erp_stocktransfer.confirmedYN',
                'erp_stocktransfer.approved',
                'erp_stocktransfer.approvedDate'
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
                        $query->orderBy('stockTransferAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getStockTransferFormData(Request $request)
    {
        $companyId = $request['companyId'];

        $segments = SegmentMaster::where("companySystemID", $companyId);
        if (isset($request['type']) && $request['type'] != 'filter') {
            $segments = $segments->where('isActive', 1);
        }
        $segments = $segments->get();

        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();

        /** all Units*/
        $yesNoSelectionForMinus = YesNoSelectionForMinus::all();

        $month = Months::all();

        $years = StockTransfer::select(DB::raw("YEAR(createdDateTime) as year"))
            ->whereNotNull('createdDateTime')
            ->groupby('year')
            ->orderby('year', 'desc')
            ->get();


        $wareHouseLocation = WarehouseMaster::where("companySystemID", $companyId);
        if (isset($request['type']) && $request['type'] != 'filter') {
            $wareHouseLocation = $wareHouseLocation->where('isActive', 1);
        }
        $wareHouseLocation = $wareHouseLocation->get();


        $financialYears = array(array('value' => intval(date("Y")), 'label' => date("Y")),
            array('value' => intval(date("Y", strtotime("-1 year"))), 'label' => date("Y", strtotime("-1 year"))));

        $companyFinanceYear = CompanyFinanceYear::select(DB::raw("companyFinanceYearID,isCurrent,CONCAT(DATE_FORMAT(bigginingDate, '%d/%m/%Y'), ' | ' ,DATE_FORMAT(endingDate, '%d/%m/%Y')) as financeYear"));
        $companyFinanceYear = $companyFinanceYear->where('companySystemID', $companyId);
        if (isset($request['type']) && $request['type'] == 'add') {
            $companyFinanceYear = $companyFinanceYear->where('isActive', -1);
        }
        $companyFinanceYear = $companyFinanceYear->get();


        $output = array('segments' => $segments,
            'yesNoSelection' => $yesNoSelection,
            'yesNoSelectionForMinus' => $yesNoSelectionForMinus,
            'month' => $month,
            'years' => $years,
            'wareHouseLocation' => $wareHouseLocation
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

}
