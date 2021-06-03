<?php

namespace App\Http\Controllers\API;

use Response;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Http\Controllers\Controller;
use App\Repositories\GRVMasterRepository;
use App\Repositories\MaterielRequestRepository;
use App\Repositories\ItemIssueMasterRepository;
use App\Repositories\ItemReturnMasterRepository;
use App\Repositories\StockTransferRepository;
use App\Repositories\StockReceiveRepository;
use App\Repositories\StockAdjustmentRepository;
use App\Repositories\PurchaseReturnRepository;
use App\Repositories\InventoryReclassificationRepository;
use App\Repositories\PurchaseRequestRepository;
use App\Repositories\BookInvSuppMasterRepository;
use App\Repositories\ExpenseClaimRepository;
use App\Repositories\MatchDocumentMasterRepository;

class TransactionsExportExcel extends AppBaseController
{
    private $gRVMasterRepository;
    private $materielRequestRepository;
    private $itemIssueMasterRepository;
    private $itemReturnMasterRepository;
    private $stockTransferRepository;
    private $stockReceiveRepository;
    private $stockAdjustmentRepository;
    private $purchaseReturnRepository;
    private $inventoryReclassificationRepository;
    private $purchaseRequestRepository;
    private $bookInvSuppMasterRepository;
    private $expenseClaimRepository;
    private $matchDocumentMasterRepository;

    public function __construct(
        GRVMasterRepository $gRVMasterRepo, 
        MaterielRequestRepository $materielRequestRepo, 
        ItemIssueMasterRepository $itemIssueMasterRepo,
        ItemReturnMasterRepository $itemReturnMasterRepo,
        StockTransferRepository $stockTransferRepo,
        StockReceiveRepository $stockReceiveRepo,
        StockAdjustmentRepository $stockAdjustmentRepo,
        PurchaseReturnRepository $purchaseReturnRepo,
        InventoryReclassificationRepository $inventoryReclassificationRepo,
        PurchaseRequestRepository $purchaseRequestRepo,
        BookInvSuppMasterRepository $bookInvSuppMasterRepo,
        ExpenseClaimRepository $expenseClaimRepo,
        MatchDocumentMasterRepository $matchDocumentMasterRepo
    )
    {
        $this->gRVMasterRepository = $gRVMasterRepo;
        $this->materielRequestRepository = $materielRequestRepo;
        $this->itemIssueMasterRepository = $itemIssueMasterRepo;
        $this->itemReturnMasterRepository = $itemReturnMasterRepo;
        $this->stockTransferRepository = $stockTransferRepo;
        $this->stockReceiveRepository = $stockReceiveRepo;
        $this->stockAdjustmentRepository = $stockAdjustmentRepo;
        $this->purchaseReturnRepository = $purchaseReturnRepo;
        $this->inventoryReclassificationRepository = $inventoryReclassificationRepo;
        $this->purchaseRequestRepository = $purchaseRequestRepo;
        $this->bookInvSuppMasterRepository = $bookInvSuppMasterRepo;
        $this->expenseClaimRepository = $expenseClaimRepo;
        $this->matchDocumentMasterRepository = $matchDocumentMasterRepo;
    }

    public function exportRecord(Request $request) { 

        $input = $request->all();
        $type = $input['type'];
        $search = $request->input('search.value');

        switch($input['documentId']) {

            case '1':
                $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'cancelledYN', 'PRConfirmedYN', 'approved', 'month', 'year'));
                $dataQry = $this->purchaseRequestRepository->purchaseRequestListQuery($request, $input, $search);
                $data = $this->purchaseRequestRepository->setExportExcelData($dataQry);
                break;

            case '3':
                $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'grvLocation', 'poCancelledYN', 'poConfirmedYN', 'approved', 'grvRecieved', 'month', 'year', 'invoicedBooked', 'grvTypeID'));
                $dataQry = $this->gRVMasterRepository->grvListQuery($request, $input, $search);
                $data = $this->gRVMasterRepository->setExportExcelData($dataQry);
                break;

            case '6':
                $input = $this->convertArrayToSelectedValue($input, array('confirmedYN', 'glCodeAssignedYN', 'approved', 'year'));
                $dataQry = $this->expenseClaimRepository->expenseClaimListQuery($request, $input, $search);
                $data = $this->expenseClaimRepository->setExportExcelData($dataQry);
                break;

            case '7':
                $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'confirmedYN', 'approved', 'location', 'month', 'year'));
                $dataQry = $this->stockAdjustmentRepository->stockAdjustmentListQuery($request, $input, $search);
                $data = $this->stockAdjustmentRepository->setExportExcelData($dataQry);
                break;

            case '8':
                $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'ConfirmedYN', 'approved'));
                $dataQry = $this->itemIssueMasterRepository->itemIssueListQuery($request, $input, $search);
                $data = $this->itemIssueMasterRepository->setExportExcelData($dataQry);
                break;

            case '9':
                $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'ConfirmedYN', 'approved'));
                $dataQry = $this->materielRequestRepository->materialrequestsListQuery($request, $input, $search);
                $data = $this->materielRequestRepository->setExportExcelData($dataQry);
                break;

            case '10':
                $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'ConfirmedYN', 'approved'));
                $dataQry = $this->stockReceiveRepository->stockReceiveListQuery($request, $input, $search);
                $data = $this->stockReceiveRepository->setExportExcelData($dataQry);
                break;

            case '11':
                $input = $this->convertArrayToSelectedValue($input, array('cancelYN', 'confirmedYN', 'approved', 'month', 'year', 'supplierID', 'documentType'));
                $dataQry = $this->bookInvSuppMasterRepository->bookInvSuppListQuery($request, $input, $search);
                $data = $this->bookInvSuppMasterRepository->setExportExcelData($dataQry);
                break;

            case '12':
                $input =  $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'confirmedYN', 'approved', 'wareHouseLocation', 'month', 'year'));
                $dataQry = $this->itemReturnMasterRepository->itemReturnListQuery($request, $input, $search);
                $data = $this->itemReturnMasterRepository->setExportExcelData($dataQry);
                break;

            case '13':
                $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'locationFrom', 'confirmedYN', 'approved', 'month', 'year', 'interCompanyTransferYN'));
                $dataQry = $this->stockTransferRepository->stockTransferListQuery($request, $input, $search);
                $data = $this->stockTransferRepository->setExportExcelData($dataQry);
                break;

            case '15':
                $input = $this->convertArrayToSelectedValue($input, array('confirmedYN', 'approved', 'month', 'year', 'supplierID'));
                $dataQry = $this->matchDocumentMasterRepository->matchDocumentListQuery($request, $input, $search);
                $data = $this->matchDocumentMasterRepository->setExportExcelData($dataQry);
                break;

            case '24':
                $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID',
                'purchaseReturnLocation', 'confirmedYN', 'approved', 'month', 'year'));
                $dataQry = $this->purchaseReturnRepository->purchaseReturnListQuery($request, $input, $search);
                $data = $this->purchaseReturnRepository->setExportExcelData($dataQry);
                break;

            case '61':
                $input = $this->convertArrayToSelectedValue($input, array('segment_by', 'created_by'));
                $dataQry = $this->inventoryReclassificationRepository->inventoryReclassificationListQuery($request, $input, $search);
                $data = $this->inventoryReclassificationRepository->setExportExcelData($dataQry);
                break;

            default:
                return $this->sendResponse(array(), 'export failed');
        }

        \Excel::create('po_master', function ($excel) use ($data) {
            $excel->sheet('sheet name', function ($sheet) use ($data) {
                $sheet->fromArray($data, null, 'A1', true);
                $sheet->setAutoSize(true);
                $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
            });
            $lastrow = $excel->getActiveSheet()->getHighestRow();
            $excel->getActiveSheet()->getStyle('A1:J' . $lastrow)->getAlignment()->setWrapText(true);
        })->download($type);

        return $this->sendResponse(array(), 'successfully export');
    }
}
