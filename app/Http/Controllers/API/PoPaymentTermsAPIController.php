<?php
/**
 * =============================================
 * -- File Name : PoPaymentTermsAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Po Payment Terms
 * -- Author : Mohamed Nazir
 * -- Create date : 20 - April 2018
 * -- Description : This file contains the all CRUD for Po Payment Terms
 * -- REVISION HISTORY
 * -- Date: 20-April 2018 By: Nazir Description: Added new functions named as getProcumentOrderPaymentTerms(),
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePoPaymentTermsAPIRequest;
use App\Http\Requests\API\UpdatePoPaymentTermsAPIRequest;
use App\Models\PoPaymentTerms;
use App\Models\SupplierMaster;
use App\Models\ProcumentOrder;
use App\Models\PurchaseOrderDetails;
use App\Repositories\PoPaymentTermsRepository;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\PoAdvancePayment;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

/**
 * Class PoPaymentTermsController
 * @package App\Http\Controllers\API
 */
class PoPaymentTermsAPIController extends AppBaseController
{
    /** @var  PoPaymentTermsRepository */
    private $poPaymentTermsRepository;

    public function __construct(PoPaymentTermsRepository $poPaymentTermsRepo)
    {
        $this->poPaymentTermsRepository = $poPaymentTermsRepo;
    }

    /**
     * Display a listing of the PoPaymentTerms.
     * GET|HEAD /poPaymentTerms
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->poPaymentTermsRepository->pushCriteria(new RequestCriteria($request));
        $this->poPaymentTermsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $poPaymentTerms = $this->poPaymentTermsRepository->all();

        return $this->sendResponse($poPaymentTerms->toArray(), 'Po Payment Terms retrieved successfully');
    }

    /**
     * Store a newly created PoPaymentTerms in storage.
     * POST /poPaymentTerms
     *
     * @param CreatePoPaymentTermsAPIRequest $request
     *
     * @return Response
     */
    public function store(CreatePoPaymentTermsAPIRequest $request)
    {
        $input = $request->all();
        $purchaseOrderID = $input['poID'];

        if (isset($input['comDate'])) {
            if ($input['comDate']) {
                $input['comDate'] = new Carbon($input['comDate']);
            }
        }

        $prDetailExist = PurchaseOrderDetails::select(DB::raw('purchaseOrderDetailsID'))
            ->where('purchaseOrderMasterID', $purchaseOrderID)
            ->first();

        if(empty($prDetailExist)){
            return $this->sendError('At least one item should added to create payment term');
        }

        $purchaseOrder = ProcumentOrder::where('purchaseOrderID', $purchaseOrderID)
            ->first();

        if (empty($purchaseOrder)) {
            return $this->sendError('Purchase Order not found');
        }

        $supplier = SupplierMaster::where('supplierCodeSystem', $purchaseOrder['supplierID'])->first();
        if ($supplier) {
            $input['inDays'] = $supplier->creditPeriod;
        }

        if(!empty($purchaseOrder->expectedDeliveryDate) && !empty($supplier->creditPeriod)){
            $addedDate = strtotime("+$supplier->creditPeriod day", strtotime($purchaseOrder->expectedDeliveryDate));
            $input['comDate'] = date("Y-m-d", $addedDate);
        }else{
            $input['comDate'] = '';
        }

        $poPaymentTerms = $this->poPaymentTermsRepository->create($input);

        return $this->sendResponse($poPaymentTerms->toArray(), 'Po Payment Terms saved successfully');
    }

    /**
     * Display the specified PoPaymentTerms.
     * GET|HEAD /poPaymentTerms/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var PoPaymentTerms $poPaymentTerms */
        $poPaymentTerms = $this->poPaymentTermsRepository->findWithoutFail($id);

        if (empty($poPaymentTerms)) {
            return $this->sendError('Po Payment Terms not found');
        }

        return $this->sendResponse($poPaymentTerms->toArray(), 'Po Payment Terms retrieved successfully');
    }

    /**
     * Update the specified PoPaymentTerms in storage.
     * PUT/PATCH /poPaymentTerms/{id}
     *
     * @param  int $id
     * @param UpdatePoPaymentTermsAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePoPaymentTermsAPIRequest $request)
    {
        $input = $request->all();

        $input = $this->convertArrayToValue($input);

        if (isset($input['comDate'])) {
            if ($input['comDate']) {
               // $input['comDate'] = new Carbon($input['comDate']);
            }
        }

        $purchaseOrderID = $input['poID'];

        $purchaseOrder = ProcumentOrder::where('purchaseOrderID', $purchaseOrderID)
            ->first();

        if (empty($purchaseOrder)) {
            return $this->sendError('Purchase Order not found');
        }

        $supplier = SupplierMaster::where('supplierCodeSystem', $purchaseOrder['supplierID'])->first();
        if ($supplier) {
            $input['inDays'] = $supplier->creditPeriod;
        }

        if(!empty($purchaseOrder->expectedDeliveryDate) && !empty($supplier->creditPeriod)){
            $addedDate = strtotime("+$supplier->creditPeriod day", strtotime($purchaseOrder->expectedDeliveryDate));
            $input['comDate'] = date("Y-m-d", $addedDate);
        }

        /** @var PoPaymentTerms $poPaymentTerms */
        $poPaymentTerms = $this->poPaymentTermsRepository->findWithoutFail($id);

        if (empty($poPaymentTerms)) {
            return $this->sendError('Po Payment Terms not found');
        }

        $poPaymentTerms = $this->poPaymentTermsRepository->update($input, $id);

        return $this->sendResponse($poPaymentTerms->toArray(), 'PoPaymentTerms updated successfully');
    }

    /**
     * Remove the specified PoPaymentTerms from storage.
     * DELETE /poPaymentTerms/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var PoPaymentTerms $poPaymentTerms */
        $poPaymentTerms = $this->poPaymentTermsRepository->findWithoutFail($id);
        ;
        if (empty($poPaymentTerms)) {
            return $this->sendError('Po Payment Terms not found');
        }

        $poPaymentTerms->delete();

        $deleteAdvancePayment = PoAdvancePayment::where('poTermID', $id)->delete();

        return $this->sendResponse($id, 'Po Payment Terms deleted successfully');
    }

    public function getProcumentOrderPaymentTerms(Request $request)
    {
        $input = $request->all();

        $poAdvancePaymentType = PoPaymentTerms::select(DB::raw('*, DATE_FORMAT(comDate, "%d/%m/%Y") as comDate'))
            ->where('poID', $input['purchaseOrderID'])
            ->orderBy('paymentTermID', 'ASC')
            ->get();

        return $this->sendResponse($poAdvancePaymentType->toArray(), 'Data retrieved successfully');
    }

}
