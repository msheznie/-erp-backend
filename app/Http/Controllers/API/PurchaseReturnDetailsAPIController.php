<?php
/**
 * =============================================
 * -- File Name : PurchaseReturnDetailsAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Purchase Return Details
 * -- Author : Mohamed Fayas
 * -- Create date : 31 - July 2018
 * -- Description : This file contains the all CRUD for Purchase Return
 * -- REVISION HISTORY
 * -- Date: 10-August 2018 By: Fayas Description: Added new functions named as getPurchaseRequestByDocumentType()
 * -- Date: 10-August 2018 By: Fayas Description: Added new functions named as getItemsByPurchaseReturnMaster(),storePurchaseReturnDetailsFromGRV()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePurchaseReturnDetailsAPIRequest;
use App\Http\Requests\API\UpdatePurchaseReturnDetailsAPIRequest;
use App\Models\GRVMaster;
use App\Models\PurchaseReturnDetails;
use App\Repositories\PurchaseReturnDetailsRepository;
use App\Repositories\PurchaseReturnRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PurchaseReturnDetailsController
 * @package App\Http\Controllers\API
 */
class PurchaseReturnDetailsAPIController extends AppBaseController
{
    /** @var  PurchaseReturnDetailsRepository */
    private $purchaseReturnDetailsRepository;
    private $purchaseReturnRepository;

    public function __construct(PurchaseReturnDetailsRepository $purchaseReturnDetailsRepo, PurchaseReturnRepository $purchaseReturnRepository)
    {
        $this->purchaseReturnDetailsRepository = $purchaseReturnDetailsRepo;
        $this->purchaseReturnRepository = $purchaseReturnRepository;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/purchaseReturnDetails",
     *      summary="Get a listing of the PurchaseReturnDetails.",
     *      tags={"PurchaseReturnDetails"},
     *      description="Get all PurchaseReturnDetails",
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
     *                  @SWG\Items(ref="#/definitions/PurchaseReturnDetails")
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
        $this->purchaseReturnDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->purchaseReturnDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $purchaseReturnDetails = $this->purchaseReturnDetailsRepository->all();

        return $this->sendResponse($purchaseReturnDetails->toArray(), 'Purchase Return Details retrieved successfully');
    }

    /**
     * @param CreatePurchaseReturnDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/purchaseReturnDetails",
     *      summary="Store a newly created PurchaseReturnDetails in storage",
     *      tags={"PurchaseReturnDetails"},
     *      description="Store PurchaseReturnDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PurchaseReturnDetails that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PurchaseReturnDetails")
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
     *                  ref="#/definitions/PurchaseReturnDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePurchaseReturnDetailsAPIRequest $request)
    {
        $input = $request->all();

        $purchaseReturnDetails = $this->purchaseReturnDetailsRepository->create($input);

        return $this->sendResponse($purchaseReturnDetails->toArray(), 'Purchase Return Details saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/purchaseReturnDetails/{id}",
     *      summary="Display the specified PurchaseReturnDetails",
     *      tags={"PurchaseReturnDetails"},
     *      description="Get PurchaseReturnDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PurchaseReturnDetails",
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
     *                  ref="#/definitions/PurchaseReturnDetails"
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
        /** @var PurchaseReturnDetails $purchaseReturnDetails */
        $purchaseReturnDetails = $this->purchaseReturnDetailsRepository->findWithoutFail($id);

        if (empty($purchaseReturnDetails)) {
            return $this->sendError('Purchase Return Details not found');
        }

        return $this->sendResponse($purchaseReturnDetails->toArray(), 'Purchase Return Details retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdatePurchaseReturnDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/purchaseReturnDetails/{id}",
     *      summary="Update the specified PurchaseReturnDetails in storage",
     *      tags={"PurchaseReturnDetails"},
     *      description="Update PurchaseReturnDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PurchaseReturnDetails",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PurchaseReturnDetails that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PurchaseReturnDetails")
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
     *                  ref="#/definitions/PurchaseReturnDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePurchaseReturnDetailsAPIRequest $request)
    {
        $input = $request->all();

        /** @var PurchaseReturnDetails $purchaseReturnDetails */
        $purchaseReturnDetails = $this->purchaseReturnDetailsRepository->findWithoutFail($id);

        if (empty($purchaseReturnDetails)) {
            return $this->sendError('Purchase Return Details not found');
        }

        $purchaseReturnDetails = $this->purchaseReturnDetailsRepository->update($input, $id);

        return $this->sendResponse($purchaseReturnDetails->toArray(), 'PurchaseReturnDetails updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/purchaseReturnDetails/{id}",
     *      summary="Remove the specified PurchaseReturnDetails from storage",
     *      tags={"PurchaseReturnDetails"},
     *      description="Delete PurchaseReturnDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PurchaseReturnDetails",
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
        /** @var PurchaseReturnDetails $purchaseReturnDetails */
        $purchaseReturnDetails = $this->purchaseReturnDetailsRepository->findWithoutFail($id);

        if (empty($purchaseReturnDetails)) {
            return $this->sendError('Purchase Return Details not found');
        }

        $purchaseReturnDetails->delete();

        return $this->sendResponse($id, 'Purchase Return Details deleted successfully');
    }

    public function getItemsByPurchaseReturnMaster(Request $request)
    {

        $input = $request->all();
        /** @var PurchaseReturn $purchaseReturn */
        $purchaseReturn = $this->purchaseReturnRepository->findWithoutFail($input['id']);

        if (empty($purchaseReturn)) {
            return $this->sendError('Purchase Return  not found');
        }

        $purchaseReturnDetails = PurchaseReturnDetails::where('purhaseReturnAutoID', $input['id'])->with(['unit', 'grv_master'])->get();

        return $this->sendResponse($purchaseReturnDetails, 'Purchase Return Details retrieved successfully');
    }

    public function storePurchaseReturnDetailsFromGRV(Request $request)
    {

        $input = $request->all();

        $employee = \Helper::getEmployeeInfo();


        foreach ($input['detailTable'] as $newValidation) {
            if ($newValidation['isChecked']) {
                if ($newValidation['rnoQty'] <= 0) {
                    return $this->sendError("Return Qty required", 500);
                }
                if ($newValidation['rnoQty'] > $newValidation['noQty']) {
                    return $this->sendError("Return qty cannot be greater than GRV qty", 500);
                }
            }
        }

        /** @var PurchaseReturn $purchaseReturn */
        $purchaseReturn = $this->purchaseReturnRepository->findWithoutFail($input['purhaseReturnAutoID']);

        if (empty($purchaseReturn)) {
            return $this->sendError('Purchase Return  not found');
        }

        $grv = GRVMaster::find($input['grvAutoID']);

        if (empty($grv)) {
            return $this->sendError('GRV not found');
        }

        foreach ($input['detailTable'] as $new) {

            if ($new['isChecked'] && $new['rQty'] > 0) {
                $detailExistSameItem = PurchaseReturnDetails::where('purhaseReturnAutoID', $input['purhaseReturnAutoID'])
                    ->where('itemCode', $new['itemCode'])
                    ->where('grvAutoID', $new['grvAutoID'])
                    ->count();

                if ($detailExistSameItem > 0) {
                    return $this->sendError('Same inventory item cannot be added more than once', 500);
                }

                /*if ($new['unitCost'] == 0 || $new['unitCost'] == 0) {
                    return $this->sendError("Cost is 0. You cannot return", 500);
                }

                if ($new['unitCostLocal'] < 0 || $new['unitCostRpt'] < 0) {
                    return $this->sendError("Cost is negative. You cannot return", 500);
                }*/
                $item = array();

                $item['createdPCID'] = gethostname();
                $item['createdUserID'] = $employee->empID;
                $item['createdUserSystemID'] = $employee->employeeSystemID;

                $item['purhaseReturnAutoID'] = $input['purhaseReturnAutoID'];
                $item['companyID'] = 'string';
                $item['grvAutoID'] = $new['grvAutoID'];
                $item['grvDetailsID'] = $new['grvDetailsID'];
                $item['itemCode'] = $new['itemCode'];
                $item['itemPrimaryCode'] = $new['itemPrimaryCode'];
                $item['itemDescription'] = $new['itemDescription'];
                $item['supplierPartNumber'] = $new['supplierPartNumber'];
                $item['unitOfMeasure'] = $new['unitOfMeasure'];
                $item['GRVQty'] = $new['noQty'];
                $item['comment'] = $new['comment'];
                $item['noQty'] = $new['rnoQty'];
                $item['supplierDefaultCurrencyID'] = $new['supplierDefaultCurrencyID'];
                $item['supplierDefaultER'] = $new['supplierDefaultER'];

                $item['supplierTransactionCurrencyID'] = $new['grvDetailsID'];
                $item['supplierTransactionER'] = $new['grvDetailsID'];

                $item['companyReportingCurrencyID'] = $new['companyReportingCurrencyID'];
                $item['companyReportingER'] = $new['companyReportingER'];
                $item['localCurrencyID'] = $new['localCurrencyID'];
                $item['localCurrencyER'] = $new['localCurrencyER'];
                $item['GRVcostPerUnitLocalCur'] = $new['GRVcostPerUnitLocalCur'];
                $item['GRVcostPerUnitSupDefaultCur'] = $new['GRVcostPerUnitSupDefaultCur '];
                $item['GRVcostPerUnitSupTransCur'] = $new['GRVcostPerUnitSupTransCur'];
                $item['GRVcostPerUnitComRptCur'] = $new['GRVcostPerUnitComRptCur'];
                $item['netAmount'] = $new['netAmount'];

                $item['netAmountLocal'] = $new['grvDetailsID'];
                $item['netAmountRpt'] = $new['grvDetailsID'];

                $prndItem = $this->stockReceiveDetailsRepository->create($item);
            }
        }

        return $this->sendResponse($purchaseReturn, 'Purchase Return Details retrieved successfully');
    }


}
