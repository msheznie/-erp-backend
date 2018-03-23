<?php
/**
=============================================
-- File Name : PurchaseOrderDetails.php
-- Project Name : ERP
-- Module Name :  Purchase Order Details
-- Author : Mohamed Fayas
-- Create date : 14 - March 2018
-- Description : This file contains the all CRUD for Purchase Order Details(item )
-- REVISION HISTORY
-- Date: 14-March 2018 By: Fayas Description: Added new functions named as getItemMasterPurchaseHistory(),exportPurchaseHistory(),
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePurchaseOrderDetailsAPIRequest;
use App\Http\Requests\API\UpdatePurchaseOrderDetailsAPIRequest;
use App\Models\PurchaseOrderDetails;
use App\Repositories\PurchaseOrderDetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Facades\DB;

/**
 * Class PurchaseOrderDetailsController
 * @package App\Http\Controllers\API
 */
class PurchaseOrderDetailsAPIController extends AppBaseController
{
    /** @var  PurchaseOrderDetailsRepository */
    private $purchaseOrderDetailsRepository;

    public function __construct(PurchaseOrderDetailsRepository $purchaseOrderDetailsRepo)
    {
        $this->purchaseOrderDetailsRepository = $purchaseOrderDetailsRepo;
    }

    /**
     * Display a listing of the PurchaseOrderDetails.
     * GET|HEAD /purchaseOrderDetails
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->purchaseOrderDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->purchaseOrderDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $purchaseOrderDetails = $this->purchaseOrderDetailsRepository->all();

        return $this->sendResponse($purchaseOrderDetails->toArray(), 'Purchase Order Details retrieved successfully');
    }

    /**
     * Display a listing of the PurchaseOrderDetails by Item master.
     * GET /getItemMasterPurchaseHistory
     *
     * @param Request $request
     * @return Response
     */

    public function getItemMasterPurchaseHistory(Request $request)
    {

        $purchaseOrderDetails = DB::table('erp_purchaseorderdetails')
            ->leftJoin('units', 'erp_purchaseorderdetails.unitOfMeasure', '=', 'units.UnitID')
            ->leftJoin('currencymaster', 'erp_purchaseorderdetails.supplierItemCurrencyID', '=', 'currencymaster.currencyID')
            ->Join('companymaster', 'erp_purchaseorderdetails.companyID', '=', 'companymaster.CompanyID')
            ->Join('erp_purchaseordermaster', 'erp_purchaseorderdetails.purchaseOrderMasterID', '=', 'erp_purchaseordermaster.purchaseOrderID')
            ->leftJoin('erp_location', 'erp_purchaseordermaster.poLocation', '=', 'erp_location.locationID')
            ->where('erp_purchaseordermaster.approved', -1)
            ->where('erp_purchaseorderdetails.itemCode', $request['itemCodeSystem'])
            ->select('erp_purchaseorderdetails.purchaseOrderMasterID',
                'erp_purchaseorderdetails.companyID',
                'companymaster.CompanyName',
                'erp_purchaseordermaster.purchaseOrderCode',
                'erp_purchaseordermaster.supplierPrimaryCode',
                'erp_purchaseordermaster.supplierName',
                'erp_purchaseordermaster.poLocation',
                'erp_location.locationName AS Location',
                'erp_purchaseorderdetails.itemCode',
                'erp_purchaseorderdetails.itemPrimaryCode',
                'erp_purchaseorderdetails.itemDescription',
                'erp_purchaseorderdetails.supplierPartNumber',
                'erp_purchaseorderdetails.unitOfMeasure',
                'erp_purchaseorderdetails.noQty',
                'units.UnitShortCode',
                'erp_purchaseorderdetails.unitCost',
                'currencymaster.CurrencyCode',
                'currencymaster.DecimalPlaces',
                'erp_purchaseorderdetails.GRVcostPerUnitSupTransCur',
                'erp_purchaseordermaster.approvedDate',
                'erp_purchaseordermaster.approved')
             ->paginate(15);


        return $this->sendResponse($purchaseOrderDetails, 'Purchase Order Details retrieved successfully');
    }

    /**
     * Export cvs - list of PurchaseOrderDetails by Item.
     * GET /getItemMasterPurchaseHistory
     *
     * @param Request $request
     * @return Response
     */
    public function exportPurchaseHistory(Request $request){

        $type = $request['type'];
        $purchaseOrderDetails = DB::table('erp_purchaseorderdetails')
            ->leftJoin('units', 'erp_purchaseorderdetails.unitOfMeasure', '=', 'units.UnitID')
            ->leftJoin('currencymaster', 'erp_purchaseorderdetails.supplierItemCurrencyID', '=', 'currencymaster.currencyID')
            ->Join('companymaster', 'erp_purchaseorderdetails.companyID', '=', 'companymaster.CompanyID')
            ->Join('erp_purchaseordermaster', 'erp_purchaseorderdetails.purchaseOrderMasterID', '=', 'erp_purchaseordermaster.purchaseOrderID')
            ->leftJoin('erp_location', 'erp_purchaseordermaster.poLocation', '=', 'erp_location.locationID')
            ->where('erp_purchaseordermaster.approved', -1)
            ->where('erp_purchaseorderdetails.itemCode', $request['itemCodeSystem'])
            ->select('erp_purchaseorderdetails.purchaseOrderMasterID',
                'erp_purchaseorderdetails.companyID',
                'companymaster.CompanyName',
                'erp_purchaseordermaster.purchaseOrderCode',
                'erp_purchaseordermaster.supplierPrimaryCode',
                'erp_purchaseordermaster.supplierName',
                'erp_purchaseordermaster.poLocation',
                'erp_location.locationName AS Location',
                'erp_purchaseorderdetails.itemCode',
                'erp_purchaseorderdetails.itemPrimaryCode',
                'erp_purchaseorderdetails.itemDescription',
                'erp_purchaseorderdetails.supplierPartNumber',
                'erp_purchaseorderdetails.unitOfMeasure',
                'erp_purchaseorderdetails.noQty',
                'units.UnitShortCode',
                'erp_purchaseorderdetails.unitCost',
                'currencymaster.CurrencyCode',
                'currencymaster.DecimalPlaces',
                'erp_purchaseorderdetails.GRVcostPerUnitSupTransCur',
                'erp_purchaseordermaster.approvedDate',
                'erp_purchaseordermaster.approved')
            ->get();

        foreach ($purchaseOrderDetails as $order)
        {
            $data[] = array(
                //'purchaseOrderMasterID' => $order->purchaseOrderMasterID,
                'Company Name' => $order->CompanyName,
                'PO Code'=> $order->purchaseOrderCode,
                'Supplier Code'=> $order->supplierPrimaryCode,
                'Approved Date'=> date("d/m/Y", strtotime($order->approvedDate)),
                'supplier Name'=> $order->supplierName,
                'Part Number'=> $order->supplierPartNumber,
                'UOM'=> $order->UnitShortCode,
                'Currency'=> $order->CurrencyCode,
                'PO Qty'=> $order->noQty,
                'Unit Cost'=> $order->unitCost,
            );
        }

        $csv =  \Excel::create('purchaseHistory', function($excel) use ($data) {

            $excel->sheet('sheet name', function($sheet) use ($data)
            {
                $sheet->fromArray($data);
                //$sheet->getStyle('A1')->getAlignment()->setWrapText(true);
                $sheet->setAutoSize(true);
                $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
            });
            $lastrow= $excel->getActiveSheet()->getHighestRow();
            $excel->getActiveSheet()->getStyle('A1:J'.$lastrow)->getAlignment()->setWrapText(true);
        })->download($type);

        return $this->sendResponse($csv, 'successfully export');
    }

    /**
     * Store a newly created PurchaseOrderDetails in storage.
     * POST /purchaseOrderDetails
     *
     * @param CreatePurchaseOrderDetailsAPIRequest $request
     *
     * @return Response
     */
    public function store(CreatePurchaseOrderDetailsAPIRequest $request)
    {
        $input = $request->all();

        $purchaseOrderDetails = $this->purchaseOrderDetailsRepository->create($input);

        return $this->sendResponse($purchaseOrderDetails->toArray(), 'Purchase Order Details saved successfully');
    }

    /**
     * Display the specified PurchaseOrderDetails.
     * GET|HEAD /purchaseOrderDetails/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var PurchaseOrderDetails $purchaseOrderDetails */
        $purchaseOrderDetails = $this->purchaseOrderDetailsRepository->findWithoutFail($id);

        if (empty($purchaseOrderDetails)) {
            return $this->sendError('Purchase Order Details not found');
        }

        return $this->sendResponse($purchaseOrderDetails->toArray(), 'Purchase Order Details retrieved successfully');
    }

    /**
     * Update the specified PurchaseOrderDetails in storage.
     * PUT/PATCH /purchaseOrderDetails/{id}
     *
     * @param  int $id
     * @param UpdatePurchaseOrderDetailsAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePurchaseOrderDetailsAPIRequest $request)
    {
        $input = $request->all();

        /** @var PurchaseOrderDetails $purchaseOrderDetails */
        $purchaseOrderDetails = $this->purchaseOrderDetailsRepository->findWithoutFail($id);

        if (empty($purchaseOrderDetails)) {
            return $this->sendError('Purchase Order Details not found');
        }

        $purchaseOrderDetails = $this->purchaseOrderDetailsRepository->update($input, $id);

        return $this->sendResponse($purchaseOrderDetails->toArray(), 'PurchaseOrderDetails updated successfully');
    }

    /**
     * Remove the specified PurchaseOrderDetails from storage.
     * DELETE /purchaseOrderDetails/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var PurchaseOrderDetails $purchaseOrderDetails */
        $purchaseOrderDetails = $this->purchaseOrderDetailsRepository->findWithoutFail($id);

        if (empty($purchaseOrderDetails)) {
            return $this->sendError('Purchase Order Details not found');
        }

        $purchaseOrderDetails->delete();

        return $this->sendResponse($id, 'Purchase Order Details deleted successfully');
    }
}
