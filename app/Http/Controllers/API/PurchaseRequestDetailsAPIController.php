<?php
/**
 * =============================================
 * -- File Name : PurchaseRequestDetailsAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Purchase Request Details
 * -- Author : Mohamed Fayas
 * -- Create date : 29 - March 2018
 * -- Description : This file contains the all CRUD for Purchase Request Details
 * -- REVISION HISTORY
 * -- Date: 29-March 2018 By: Fayas Description: Added new functions named as getItemsByPurchaseRequest()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePurchaseRequestDetailsAPIRequest;
use App\Http\Requests\API\UpdatePurchaseRequestDetailsAPIRequest;
use App\Models\CompanyPolicyMaster;
use App\Models\FinanceItemcategorySubAssigned;
use App\Models\GRVDetails;
use App\Models\ItemAssigned;
use App\Models\PurchaseOrderDetails;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestDetails;
use App\Repositories\PurchaseRequestDetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PurchaseRequestDetailsController
 * @package App\Http\Controllers\API
 */
class PurchaseRequestDetailsAPIController extends AppBaseController
{
    /** @var  PurchaseRequestDetailsRepository */
    private $purchaseRequestDetailsRepository;

    public function __construct(PurchaseRequestDetailsRepository $purchaseRequestDetailsRepo)
    {
        $this->purchaseRequestDetailsRepository = $purchaseRequestDetailsRepo;
    }

    /**
     * Display a listing of the PurchaseRequestDetails.
     * GET|HEAD /purchaseRequestDetails
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->purchaseRequestDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->purchaseRequestDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $purchaseRequestDetails = $this->purchaseRequestDetailsRepository->all();

        return $this->sendResponse($purchaseRequestDetails->toArray(), 'Purchase Request Details retrieved successfully');
    }


    /**
     * Display a listing of the items by Purchase Request.
     * GET|HEAD /getItemsByPurchaseRequest
     *
     * @param Request $request
     * @return Response
     */
    public function getItemsByPurchaseRequest(Request $request)
    {
        $input = $request->all();
        $prId = $input['purchaseRequestId'];

        $items = PurchaseRequestDetails::where('purchaseRequestID', $prId)
            ->with(['uom'])
            ->get();

        return $this->sendResponse($items->toArray(), 'Purchase Request Details retrieved successfully');
    }

    /**
     * Store a newly created PurchaseRequestDetails in storage.
     * POST /purchaseRequestDetails
     *
     * @param CreatePurchaseRequestDetailsAPIRequest $request
     *
     * @return Response
     */
    public function store(CreatePurchaseRequestDetailsAPIRequest $request)
    {
        $input = array_except($request->all(), 'uom');
        $input = $this->convertArrayToValue($input);

        $companySystemID = $input['companySystemID'];

        $item = ItemAssigned::where('itemCodeSystem', $input['itemCode'])
            ->where('companySystemID', $companySystemID)
            ->first();

        if (empty($item)) {
            return $this->sendError('Item not found');
        }

        $purchaseRequest = PurchaseRequest::where('purchaseRequestID', $input['purchaseRequestID'])
            ->first();

        $input['budgetYear'] = $purchaseRequest->budgetYear;


        if (empty($purchaseRequest)) {
            return $this->sendError('Purchase Request Details not found');
        }

        $input['itemPrimaryCode'] = $item->itemPrimaryCode;
        $input['itemDescription'] = $item->itemDescription;
        $input['partNumber'] = $item->secondaryItemCode;
        $input['itemFinanceCategoryID'] = $item->financeCategoryMaster;
        $input['itemFinanceCategorySubID'] = $item->financeCategorySub;
        $input['estimatedCost'] = $item->wacValueLocal;

        $input['companySystemID'] = $item->companySystemID;
        $input['companyID'] = $item->companyID;
        $input['unitOfMeasure'] = $item->itemUnitOfMeasure;
        $input['maxQty'] = $item->maxQty;
        $input['minQty'] = $item->minQty;

        $financeItemCategorySubAssigned = FinanceItemcategorySubAssigned::where('companySystemID', $item->companySystemID)
            ->where('mainItemCategoryID', $item->financeCategoryMaster)
            ->where('itemCategorySubID', $item->financeCategorySub)
            ->first();

        if (empty($financeItemCategorySubAssigned)) {
            return $this->sendError('Item not found');
        }

        $input['financeGLcodebBSSystemID'] = $financeItemCategorySubAssigned->financeGLcodebBSSystemID;
        $input['financeGLcodebBS'] = $financeItemCategorySubAssigned->financeGLcodebBS;
        $input['financeGLcodePLSystemID'] = $financeItemCategorySubAssigned->financeGLcodePLSystemID;
        $input['financeGLcodePL'] = $financeItemCategorySubAssigned->financeGLcodePL;
        $input['includePLForGRVYN'] = $financeItemCategorySubAssigned->includePLForGRVYN;


        // check policy 18

        $allowPendingApproval = CompanyPolicyMaster::where('companyPolicyCategoryID', 18)
            ->where('companySystemID', $companySystemID)
            ->first();

        if ($allowPendingApproval) {

            if ($allowPendingApproval->isYesNO == 0) {

                 $checkWhether = PurchaseRequest::where('purchaseRequestID','!=',$purchaseRequest->purchaseRequestID)
                     ->where('companySystemID',$companySystemID)
                     ->with(['details' => function ($query) use ($companySystemID) {
                   /* $query->groupBy(
                        'erp_purchaserequestdetails.itemCode',
                        'erp_purchaserequestdetails.itemPrimaryCode',
                        'erp_purchaserequestdetails.selectedForPO',
                        'erp_purchaserequestdetails.prClosedYN',
                        'erp_purchaserequestdetails.fullyOrdered'
                    )->select([
                                'erp_purchaserequestdetails.itemCode',
                                'erp_purchaserequestdetails.itemPrimaryCode',
                                'erp_purchaserequestdetails.selectedForPO',
                                'erp_purchaserequestdetails.prClosedYN',
                                'erp_purchaserequestdetails.fullyOrdered'
                             ]);*/
                }])
                    ->select([
                        'erp_purchaserequest.purchaseRequestID',
                        'erp_purchaserequest.companySystemID',
                        'erp_purchaserequest.serviceLineCode',
                        'erp_purchaserequest.purchaseRequestCode',
                        'erp_purchaserequest.PRConfirmedYN',
                        'erp_purchaserequest.approved',
                        'erp_purchaserequest.cancelledYN'
                    ])
                    ->groupBy(
                        'erp_purchaserequest.purchaseRequestID',
                        'erp_purchaserequest.companySystemID',
                        'erp_purchaserequest.serviceLineCode',
                        'erp_purchaserequest.purchaseRequestCode',
                        'erp_purchaserequest.PRConfirmedYN',
                        'erp_purchaserequest.approved',
                        'erp_purchaserequest.cancelledYN'
                    );
                    //->take(10)
                    //->get();
                /*
                 SELECT erp_purchaserequest.purchaseRequestID,
                      erp_purchaserequest.companyID,
                      erp_purchaserequest.serviceLineCode,
                      erp_purchaserequest.purchaseRequestCode,
                      erp_purchaserequestdetails.itemCode,
                      erp_purchaserequestdetails.itemPrimaryCode,
                      erp_purchaserequest.PRConfirmedYN,
                      erp_purchaserequest.approved,
                      erp_purchaserequest.cancelledYN,
                      erp_purchaserequestdetails.selectedForPO,
                      erp_purchaserequestdetails.prClosedYN,
                      erp_purchaserequestdetails.fullyOrdered
                      FROM erp_purchaserequest
                      LEFT JOIN erp_purchaserequestdetails ON erp_purchaserequest.purchaseRequestID = erp_purchaserequestdetails.purchaseRequestID
                      GROUP BY erp_purchaserequest.purchaseRequestID,
                      erp_purchaserequest.companyID,
                      erp_purchaserequest.serviceLineCode,
                      erp_purchaserequest.purchaseRequestCode,
                      erp_purchaserequestdetails.itemCode,
                      erp_purchaserequestdetails.itemPrimaryCode,
                      erp_purchaserequest.PRConfirmedYN,
                      erp_purchaserequest.approved,
                      erp_purchaserequest.cancelledYN,
                      erp_purchaserequestdetails.selectedForPO,
                      erp_purchaserequestdetails.prClosedYN,
                      erp_purchaserequestdetails.fullyOrdered;
                */

                /*$anyPendingApproval = $checkWhether->where()
                                                    ->where()
                                                    ->get();

                    purchaseRequestID <> currentPR and
                                        companyID=companyID and
                                        serviceLineCode=servicelinecode and
                                        itemCode=selecteditem And approved=0 And cancelledYN=0*/


                      if (!empty($anyPendingApproval)) {
                          $purchaseRequestCode = "";
                          return $this->sendError("There is a purchase request (".$purchaseRequestCode.") pending for approval for the item you are trying to add. Please check again.");
                      }

            }
        }

        die();

        /*
     SELECT erp_purchaseorderdetails.companyID,
            erp_purchaseorderdetails.itemCode,
            erp_purchaseorderdetails.itemPrimaryCode,
            Sum(erp_purchaseorderdetails.noQty) AS SumOfnoQty,
            erp_purchaseordermaster.poCancelledYN,
            erp_purchaseordermaster.approved

           FROM erp_purchaseorderdetails
           INNER JOIN erp_purchaseordermaster ON erp_purchaseorderdetails.purchaseOrderMasterID = erp_purchaseordermaster.purchaseOrderID
           GROUP BY erp_purchaseorderdetails.companyID,
                    erp_purchaseorderdetails.itemCode,
                    erp_purchaseorderdetails.itemPrimaryCode,
                    erp_purchaseordermaster.poCancelledYN,
                    erp_purchaseordermaster.approved;

         */


        $poQty = PurchaseOrderDetails::with(['order' => function ($query) use ($companySystemID) {
            $query->where('companySystemID', $companySystemID)
                ->where('approved', -1)
                ->where('poCancelledYN', 0)
                ->groupBy('erp_purchaseordermaster.poCancelledYN',
                    'erp_purchaseordermaster.approved');
        }])
            ->where('itemCode', $input['itemCode'])
            ->groupBy('erp_purchaseorderdetails.companySystemID',
                'erp_purchaseorderdetails.itemCode',
                'erp_purchaseorderdetails.itemPrimaryCode'
            )
            ->select(
                [
                    'erp_purchaseorderdetails.companySystemID',
                    'erp_purchaseorderdetails.itemCode',
                    'erp_purchaseorderdetails.itemPrimaryCode'
                ]
            )
            ->sum('noQty');

        /*SELECT erp_grvmaster.companyID,
                 erp_grvdetails.itemCode,
                 Sum(erp_grvdetails.noQty) AS SumOfnoQty,
                 erp_grvmaster.grvType
                 FROM erp_grvdetails
                 INNER JOIN erp_grvmaster ON erp_grvdetails.grvAutoID = erp_grvmaster.grvAutoID
                 GROUP BY erp_grvdetails.companyID,
                          erp_grvdetails.itemCode,
                          erp_grvmaster.grvType;*/

        $grvQty = GRVDetails::with(['master' => function ($query) use ($companySystemID) {
            $query->where('companySystemID', $companySystemID)
                ->groupBy('erp_grvmaster.companySystemID', 'erp_grvmaster.grvType');
        }])
            ->where('itemCode', $input['itemCode'])
            ->groupBy('erp_grvdetails.itemCode')
            ->select(
                [
                    'erp_grvdetails.companySystemID',
                    'erp_grvdetails.itemCode'
                ])
            ->sum('noQty');

        $quantityOnOrder = $poQty - $grvQty;
        $quantityInHand = $poQty;

        $input['poQuantity'] = $poQty;
        $input['quantityOnOrder'] = $quantityOnOrder;
        $input['quantityInHand'] = $quantityInHand;

        $purchaseRequestDetails = $this->purchaseRequestDetailsRepository->create($input);

        return $this->sendResponse($purchaseRequestDetails->toArray(), 'Purchase Request Details saved successfully');
    }

    /**
     * Display the specified PurchaseRequestDetails.
     * GET|HEAD /purchaseRequestDetails/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var PurchaseRequestDetails $purchaseRequestDetails */
        $purchaseRequestDetails = $this->purchaseRequestDetailsRepository->findWithoutFail($id);

        if (empty($purchaseRequestDetails)) {
            return $this->sendError('Purchase Request Details not found');
        }

        return $this->sendResponse($purchaseRequestDetails->toArray(), 'Purchase Request Details retrieved successfully');
    }

    /**
     * Update the specified PurchaseRequestDetails in storage.
     * PUT/PATCH /purchaseRequestDetails/{id}
     *
     * @param  int $id
     * @param UpdatePurchaseRequestDetailsAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePurchaseRequestDetailsAPIRequest $request)
    {
        $input = array_except($request->all(), 'uom');
        $input = $this->convertArrayToValue($input);


        /** @var PurchaseRequestDetails $purchaseRequestDetails */
        $purchaseRequestDetails = $this->purchaseRequestDetailsRepository->findWithoutFail($id);

        if (empty($purchaseRequestDetails)) {
            return $this->sendError('Purchase Request Details not found');
        }

        $purchaseRequestDetails = $this->purchaseRequestDetailsRepository->update($input, $id);

        return $this->sendResponse($purchaseRequestDetails->toArray(), 'PurchaseRequestDetails updated successfully');
    }

    /**
     * Remove the specified PurchaseRequestDetails from storage.
     * DELETE /purchaseRequestDetails/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var PurchaseRequestDetails $purchaseRequestDetails */
        $purchaseRequestDetails = $this->purchaseRequestDetailsRepository->findWithoutFail($id);

        if (empty($purchaseRequestDetails)) {
            return $this->sendError('Purchase Request Details not found');
        }

        $purchaseRequestDetails->delete();

        return $this->sendResponse($id, 'Purchase Request Details deleted successfully');
    }
}
