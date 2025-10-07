<?php
/**
 * =============================================
 * -- File Name : PurchaseOrderProcessDetailsAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Purchase Order Process Details
 * -- Author : Mohamed Fayas
 * -- Create date : 04- May 2018
 * -- Description : This file contains the all CRUD for Purchase Order Process Details
 * -- REVISION HISTORY
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePurchaseOrderProcessDetailsAPIRequest;
use App\Http\Requests\API\UpdatePurchaseOrderProcessDetailsAPIRequest;
use App\Models\PurchaseOrderProcessDetails;
use App\Repositories\PurchaseOrderProcessDetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PurchaseOrderProcessDetailsController
 * @package App\Http\Controllers\API
 */

class PurchaseOrderProcessDetailsAPIController extends AppBaseController
{
    /** @var  PurchaseOrderProcessDetailsRepository */
    private $purchaseOrderProcessDetailsRepository;

    public function __construct(PurchaseOrderProcessDetailsRepository $purchaseOrderProcessDetailsRepo)
    {
        $this->purchaseOrderProcessDetailsRepository = $purchaseOrderProcessDetailsRepo;
    }

    /**
     * Display a listing of the PurchaseOrderProcessDetails.
     * GET|HEAD /purchaseOrderProcessDetails
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->purchaseOrderProcessDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->purchaseOrderProcessDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $purchaseOrderProcessDetails = $this->purchaseOrderProcessDetailsRepository->all();

        return $this->sendResponse($purchaseOrderProcessDetails->toArray(), trans('custom.purchase_order_process_details_retrieved_successfu'));
    }

    /**
     * Store a newly created PurchaseOrderProcessDetails in storage.
     * POST /purchaseOrderProcessDetails
     *
     * @param CreatePurchaseOrderProcessDetailsAPIRequest $request
     *
     * @return Response
     */
    public function store(CreatePurchaseOrderProcessDetailsAPIRequest $request)
    {
        $input = $request->all();

        $purchaseOrderProcessDetails = $this->purchaseOrderProcessDetailsRepository->create($input);

        return $this->sendResponse($purchaseOrderProcessDetails->toArray(), trans('custom.purchase_order_process_details_saved_successfully'));
    }

    /**
     * Display the specified PurchaseOrderProcessDetails.
     * GET|HEAD /purchaseOrderProcessDetails/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var PurchaseOrderProcessDetails $purchaseOrderProcessDetails */
        $purchaseOrderProcessDetails = $this->purchaseOrderProcessDetailsRepository->findWithoutFail($id);

        if (empty($purchaseOrderProcessDetails)) {
            return $this->sendError(trans('custom.purchase_order_process_details_not_found'));
        }

        return $this->sendResponse($purchaseOrderProcessDetails->toArray(), trans('custom.purchase_order_process_details_retrieved_successfu'));
    }

    /**
     * Update the specified PurchaseOrderProcessDetails in storage.
     * PUT/PATCH /purchaseOrderProcessDetails/{id}
     *
     * @param  int $id
     * @param UpdatePurchaseOrderProcessDetailsAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePurchaseOrderProcessDetailsAPIRequest $request)
    {
        $input = $request->all();

        /** @var PurchaseOrderProcessDetails $purchaseOrderProcessDetails */
        $purchaseOrderProcessDetails = $this->purchaseOrderProcessDetailsRepository->findWithoutFail($id);

        if (empty($purchaseOrderProcessDetails)) {
            return $this->sendError(trans('custom.purchase_order_process_details_not_found'));
        }

        $purchaseOrderProcessDetails = $this->purchaseOrderProcessDetailsRepository->update($input, $id);

        return $this->sendResponse($purchaseOrderProcessDetails->toArray(), trans('custom.purchaseorderprocessdetails_updated_successfully'));
    }

    /**
     * Remove the specified PurchaseOrderProcessDetails from storage.
     * DELETE /purchaseOrderProcessDetails/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var PurchaseOrderProcessDetails $purchaseOrderProcessDetails */
        $purchaseOrderProcessDetails = $this->purchaseOrderProcessDetailsRepository->findWithoutFail($id);

        if (empty($purchaseOrderProcessDetails)) {
            return $this->sendError(trans('custom.purchase_order_process_details_not_found'));
        }

        $purchaseOrderProcessDetails->delete();

        return $this->sendResponse($id, trans('custom.purchase_order_process_details_deleted_successfull'));
    }
}
