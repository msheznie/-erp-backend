<?php
/**
 * =============================================
 * -- File Name : StockTransferDetailsAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Stock Transfer Details
 * -- Author : Mohamed Nazir
 * -- Create date : 16-July 2018
 * -- Description : This file contains the all CRUD for Stock Transfer
 * -- REVISION HISTORY
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateStockTransferDetailsAPIRequest;
use App\Http\Requests\API\UpdateStockTransferDetailsAPIRequest;
use App\Models\ErpItemLedger;
use App\Models\FinanceItemcategorySubAssigned;
use App\Models\ItemAssigned;
use App\Models\StockTransferDetails;
use App\Models\StockTransfer;
use App\Models\Company;
use App\Repositories\StockTransferDetailsRepository;
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
 * Class StockTransferDetailsController
 * @package App\Http\Controllers\API
 */
class StockTransferDetailsAPIController extends AppBaseController
{
    /** @var  StockTransferDetailsRepository */
    private $stockTransferDetailsRepository;
    private $userRepository;

    public function __construct(StockTransferDetailsRepository $stockTransferDetailsRepo, UserRepository $userRepo)
    {
        $this->stockTransferDetailsRepository = $stockTransferDetailsRepo;
        $this->userRepository = $userRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/stockTransferDetails",
     *      summary="Get a listing of the StockTransferDetails.",
     *      tags={"StockTransferDetails"},
     *      description="Get all StockTransferDetails",
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
     *                  @SWG\Items(ref="#/definitions/StockTransferDetails")
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
        $this->stockTransferDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->stockTransferDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $stockTransferDetails = $this->stockTransferDetailsRepository->all();

        return $this->sendResponse($stockTransferDetails->toArray(), 'Stock Transfer Details retrieved successfully');
    }

    /**
     * @param CreateStockTransferDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/stockTransferDetails",
     *      summary="Store a newly created StockTransferDetails in storage",
     *      tags={"StockTransferDetails"},
     *      description="Store StockTransferDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="StockTransferDetails that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/StockTransferDetails")
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
     *                  ref="#/definitions/StockTransferDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateStockTransferDetailsAPIRequest $request)
    {
        $input = $request->all();

        $id = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);

        $input = array_except($request->all(), 'unit_by');
        $input = $this->convertArrayToValue($input);

        $companySystemID = $input['companySystemID'];

        $item = ItemAssigned::where('itemCodeSystem', $input['itemCode'])
            ->where('companySystemID', $companySystemID)
            ->first();

        $itemExist = StockTransferDetails::where('itemCodeSystem', $input['itemCode'])
            ->where('stockTransferAutoID', $input['stockTransferAutoID'])
            ->first();

        if (!empty($itemExist)) {
            return $this->sendError('Added Item All Ready Exist');
        }

        if (empty($item)) {
            return $this->sendError('Item not found');
        }

        $stockTransferMaster = StockTransfer::where('stockTransferAutoID', $input['stockTransferAutoID'])
            ->first();

        if (empty($stockTransferMaster)) {
            return $this->sendError('Stock Transfer not found');
        }

        $input['stockTransferCode'] = $stockTransferMaster->stockTransferCode;
        $input['itemCodeSystem'] = $item->itemCodeSystem;
        $input['itemPrimaryCode'] = $item->itemPrimaryCode;
        $input['itemDescription'] = $item->itemDescription;
        $input['unitOfMeasure'] = $item->itemUnitOfMeasure;
        $input['unitCostLocal'] = $item->wacValueLocal;
        $input['unitCostRpt'] = $item->wacValueReporting;
        $input['itemFinanceCategoryID'] = $item->financeCategoryMaster;
        $input['itemFinanceCategorySubID'] = $item->financeCategorySub;

        $financeItemCategorySubAssigned = FinanceItemcategorySubAssigned::where('companySystemID', $companySystemID)
            ->where('mainItemCategoryID', $input['itemFinanceCategoryID'])
            ->where('itemCategorySubID', $input['itemFinanceCategorySubID'])
            ->first();

        $input['financeGLcodebBS'] = $financeItemCategorySubAssigned->financeGLcodebBS;

        $currentStockQty = ErpItemLedger::where('itemSystemCode', $input['itemCode'])
            ->where('companySystemID', $companySystemID)
            ->groupBy('itemSystemCode')
            ->sum('inOutQty');

        $currentWareHouseStockQty = ErpItemLedger::where('itemSystemCode', $input['itemCode'])
            ->where('companySystemID', $companySystemID)
            ->where('wareHouseSystemCode', $stockTransferMaster->locationFrom)
            ->groupBy('itemSystemCode')
            ->sum('inOutQty');

        $input['currentStockQty'] = $currentStockQty;
        $input['warehouseStockQty'] = $currentWareHouseStockQty;

        $company = Company::where('companySystemID', $input['companySystemID'])->first();
        if ($company) {
            $input['localCurrencyID'] = $company->localCurrencyID;
            $input['reportingCurrencyID'] = $company->reportingCurrency;
        }

        $input['createdPcID'] = gethostname();
        $input['createdUserID'] = $user->employee['empID'];
        $input['createdUserSystemID'] = $user->employee['employeeSystemID'];

        $stockTransferDetails = $this->stockTransferDetailsRepository->create($input);

        return $this->sendResponse($stockTransferDetails->toArray(), 'Stock Transfer Details saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/stockTransferDetails/{id}",
     *      summary="Display the specified StockTransferDetails",
     *      tags={"StockTransferDetails"},
     *      description="Get StockTransferDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StockTransferDetails",
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
     *                  ref="#/definitions/StockTransferDetails"
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
        /** @var StockTransferDetails $stockTransferDetails */
        $stockTransferDetails = $this->stockTransferDetailsRepository->findWithoutFail($id);

        if (empty($stockTransferDetails)) {
            return $this->sendError('Stock Transfer Details not found');
        }

        return $this->sendResponse($stockTransferDetails->toArray(), 'Stock Transfer Details retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateStockTransferDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/stockTransferDetails/{id}",
     *      summary="Update the specified StockTransferDetails in storage",
     *      tags={"StockTransferDetails"},
     *      description="Update StockTransferDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StockTransferDetails",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="StockTransferDetails that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/StockTransferDetails")
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
     *                  ref="#/definitions/StockTransferDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateStockTransferDetailsAPIRequest $request)
    {
        $input = $request->all();

        $userId = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($userId);

        $input = array_except($request->all(), 'unit_by');
        $input = $this->convertArrayToValue($input);

        /** @var StockTransferDetails $stockTransferDetails */
        $stockTransferDetails = $this->stockTransferDetailsRepository->findWithoutFail($id);

        if (empty($stockTransferDetails)) {
            return $this->sendError('Stock Transfer Details not found');
        }

        $input['modifiedPc'] = gethostname();
        $input['modifiedUser'] = $user->employee['empID'];

        $stockTransferDetails = $this->stockTransferDetailsRepository->update($input, $id);

        return $this->sendResponse($stockTransferDetails->toArray(), 'Stock Transfer Details updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/stockTransferDetails/{id}",
     *      summary="Remove the specified StockTransferDetails from storage",
     *      tags={"StockTransferDetails"},
     *      description="Delete StockTransferDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StockTransferDetails",
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
        /** @var StockTransferDetails $stockTransferDetails */
        $stockTransferDetails = $this->stockTransferDetailsRepository->findWithoutFail($id);

        if (empty($stockTransferDetails)) {
            return $this->sendError('Stock Transfer Details not found');
        }

        $stockTransferDetails->delete();

        return $this->sendResponse($id, 'Stock Transfer Details deleted successfully');
    }

    public function getStockTransferDetails(Request $request)
    {
        $input = $request->all();
        $stockTransferAutoID = $input['stockTransferAutoID'];

        $items = StockTransferDetails::select(DB::raw('stockTransferDetailsID,"" as totalCost,unitCostRpt,unitOfMeasure,itemCodeSystem,itemPrimaryCode,itemDescription,qty, currentStockQty,warehouseStockQty'))
            ->where('stockTransferAutoID', $stockTransferAutoID)
            ->with(['unit_by' => function ($query) {
            }])
            ->get();

        return $this->sendResponse($items->toArray(), 'Stock Transfer details retrieved successfully');
    }
}
