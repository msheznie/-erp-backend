<?php

namespace App\Http\Controllers\API;

use App\Models\Company;
use App\Repositories\RecurringVoucherSetupRepository;
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
use App\Repositories\MonthlyAdditionsMasterRepository;
use App\Repositories\PaySupplierInvoiceMasterRepository;
use App\Repositories\CustomerInvoiceDirectRepository;
use App\Repositories\CreditNoteRepository;
use App\Repositories\CustomerReceivePaymentRepository;
use App\Repositories\CustomerInvoiceTrackingRepository;
use App\Repositories\QuotationMasterRepository;
use App\Repositories\DeliveryOrderRepository;
use App\Repositories\SalesReturnRepository;
use App\Repositories\JvMasterRepository;
use App\Repositories\StockCountRepository;
use App\Repositories\BudgetTransferFormRepository;
use App\Repositories\ConsoleJVMasterRepository;
use App\Repositories\BankAccountRepository;
use App\Repositories\BankReconciliationRepository;
use App\Repositories\PaymentBankTransferRepository;
use App\Repositories\FixedAssetMasterRepository;
use App\Repositories\FixedAssetDepreciationMasterRepository;
use App\Repositories\PdcLogRepository;
use App\helper\CreateExcel;
class TransactionsExportExcel extends AppBaseController
{
    private $gRVMasterRepository;
    private $materielRequestRepository;
    private $itemIssueMasterRepository;
    private $itemReturnMasterRepository;
    private $stockTransferRepository;
    private $stockReceiveRepository;
    private $stockAdjustmentRepository;
    private $stockCountRepository;
    private $purchaseReturnRepository;
    private $inventoryReclassificationRepository;
    private $purchaseRequestRepository;
    private $bookInvSuppMasterRepository;
    private $expenseClaimRepository;
    private $matchDocumentMasterRepository;
    private $monthlyAdditionsMasterRepository;
    private $paySupplierInvoiceMasterRepository;
    private $customerInvoiceDirectRepository;
    private $creditNoteRepository;
    private $customerReceivePaymentRepository;
    private $customerInvoiceTrackingRepository;
    private $quotationMasterRepository;
    private $deliveryOrderRepository;
    private $salesReturnRepository;
    private $jvMasterRepository;
    private $budgetTransferFormRepository;
    private $consoleJVMasterRepository;
    private $bankAccountRepository;
    private $bankReconciliationRepository;
    private $paymentBankTransferRepository;
    private $fixedAssetMasterRepository;
    private $fixedAssetDepreciationMasterRepository;
    private $pdcLogRepository;
    private $recurringVoucherSetupRepository;

    public function __construct(
        GRVMasterRepository $gRVMasterRepo, 
        MaterielRequestRepository $materielRequestRepo, 
        ItemIssueMasterRepository $itemIssueMasterRepo,
        ItemReturnMasterRepository $itemReturnMasterRepo,
        StockTransferRepository $stockTransferRepo,
        StockReceiveRepository $stockReceiveRepo,
        StockAdjustmentRepository $stockAdjustmentRepo,
        StockCountRepository $stockCountRepo,
        PurchaseReturnRepository $purchaseReturnRepo,
        InventoryReclassificationRepository $inventoryReclassificationRepo,
        PurchaseRequestRepository $purchaseRequestRepo,
        BookInvSuppMasterRepository $bookInvSuppMasterRepo,
        ExpenseClaimRepository $expenseClaimRepo,
        MatchDocumentMasterRepository $matchDocumentMasterRepo,
        MonthlyAdditionsMasterRepository $monthlyAdditionsMasterRepo,
        PaySupplierInvoiceMasterRepository $paySupplierInvoiceMasterRepo,
        CustomerInvoiceDirectRepository $customerInvoiceDirectRepo,
        CreditNoteRepository $creditNoteRepo,
        CustomerReceivePaymentRepository $customerReceivePaymentRepo,
        CustomerInvoiceTrackingRepository $customerInvoiceTrackingRepo,
        QuotationMasterRepository $quotationMasterRepo,
        DeliveryOrderRepository $deliveryOrderRepo,
        SalesReturnRepository $salesReturnRepo,
        JvMasterRepository $jvMasterRepo,
        BudgetTransferFormRepository $budgetTransferFormRepo,
        ConsoleJVMasterRepository $consoleJVMasterRepo,
        BankAccountRepository $bankAccountRepo,
        BankReconciliationRepository $bankReconciliationRepo,
        PaymentBankTransferRepository $paymentBankTransferRepo,
        FixedAssetMasterRepository $fixedAssetMasterRepo,
        FixedAssetDepreciationMasterRepository $fixedAssetDepreciationMasterRepo,
        PdcLogRepository $pdcLogRepository,
        RecurringVoucherSetupRepository $recurringVoucherSetupRepository
    )
    {
        $this->gRVMasterRepository = $gRVMasterRepo;
        $this->materielRequestRepository = $materielRequestRepo;
        $this->itemIssueMasterRepository = $itemIssueMasterRepo;
        $this->itemReturnMasterRepository = $itemReturnMasterRepo;
        $this->stockTransferRepository = $stockTransferRepo;
        $this->stockReceiveRepository = $stockReceiveRepo;
        $this->stockAdjustmentRepository = $stockAdjustmentRepo;
        $this->stockCountRepository = $stockCountRepo;
        $this->purchaseReturnRepository = $purchaseReturnRepo;
        $this->inventoryReclassificationRepository = $inventoryReclassificationRepo;
        $this->purchaseRequestRepository = $purchaseRequestRepo;
        $this->bookInvSuppMasterRepository = $bookInvSuppMasterRepo;
        $this->expenseClaimRepository = $expenseClaimRepo;
        $this->matchDocumentMasterRepository = $matchDocumentMasterRepo;
        $this->monthlyAdditionsMasterRepository = $monthlyAdditionsMasterRepo;
        $this->paySupplierInvoiceMasterRepository = $paySupplierInvoiceMasterRepo;
        $this->customerInvoiceDirectRepository = $customerInvoiceDirectRepo;
        $this->creditNoteRepository = $creditNoteRepo;
        $this->customerReceivePaymentRepository = $customerReceivePaymentRepo;
        $this->customerInvoiceTrackingRepository = $customerInvoiceTrackingRepo;
        $this->quotationMasterRepository = $quotationMasterRepo;
        $this->deliveryOrderRepository = $deliveryOrderRepo;
        $this->salesReturnRepository = $salesReturnRepo;
        $this->jvMasterRepository = $jvMasterRepo;
        $this->budgetTransferFormRepository = $budgetTransferFormRepo;
        $this->consoleJVMasterRepository = $consoleJVMasterRepo;
        $this->bankAccountRepository = $bankAccountRepo;
        $this->bankReconciliationRepository = $bankReconciliationRepo;
        $this->paymentBankTransferRepository = $paymentBankTransferRepo;
        $this->fixedAssetMasterRepository = $fixedAssetMasterRepo;
        $this->fixedAssetDepreciationMasterRepository = $fixedAssetDepreciationMasterRepo;
        $this->pdcLogRepository = $pdcLogRepository;
        $this->recurringVoucherSetupRepository = $recurringVoucherSetupRepository;
    }

    public function exportRecord(Request $request) { 

        $input = $request->all();
        $type = $input['type'];
        $search = $request->input('search.value');

        switch($input['documentId']) {

            case '1':
            case '50':
            case '51':
            $input = $this->convertArrayToSelectedValue($input,
                array('serviceLineSystemID', 'cancelledYN', 'PRConfirmedYN', 'approved', 'month',
                    'year', 'buyerEmpSystemID'));
                $serviceLineSystemID = collect((array) $request['serviceLineSystemID'])->pluck('id');
                $buyerEmpSystemId = collect((array) $request['buyerEmpSystemID'])->pluck('id');
                $dataQry = $this->purchaseRequestRepository->purchaseRequestListQuery(
                    $request, $input, $search, $serviceLineSystemID, $buyerEmpSystemId);
                $data = $this->purchaseRequestRepository->setExportExcelData($dataQry);
                break;
            case '3':
                $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'grvLocation', 'poCancelledYN', 'poConfirmedYN', 'approved', 'grvRecieved', 'month', 'year', 'invoicedBooked', 'grvTypeID', 'projectID'));
                $grvLocation = $request['grvLocation'];
                $grvLocation = (array)$grvLocation;
                $grvLocation = collect($grvLocation)->pluck('id');

                $serviceLineSystemID = $request['serviceLineSystemID'];
                $serviceLineSystemID = (array)$serviceLineSystemID;
                $serviceLineSystemID = collect($serviceLineSystemID)->pluck('id');

                $projectID = $request['projectID'];
                $projectID = (array)$projectID;
                $projectID = collect($projectID)->pluck('id');

                $dataQry = $this->gRVMasterRepository->grvListQuery($request, $input, $search, $grvLocation, $serviceLineSystemID, $projectID);
                $data = $this->gRVMasterRepository->setExportExcelData($dataQry);
                break;

            case '4':
                $input = $this->convertArrayToSelectedValue($input, array('month', 'createdBy' ,'year', 'cancelYN', 'confirmedYN', 'approved', 'invoiceType', 'supplierID', 'chequePaymentYN', 'BPVbank', 'BPVAccount', '','chequeSentToTreasury', 'projectID', 'employeeID'));
                
                $employeeID = $request['employeeID'];
                $employeeID = (array)$employeeID;
                $employeeID = collect($employeeID)->pluck('id');

                $supplierID = $request['supplierID'];
                $supplierID = (array)$supplierID;
                $supplierID = collect($supplierID)->pluck('id');

                $projectID = $request['projectID'];
                $projectID = (array)$projectID;
                $projectID = collect($projectID)->pluck('id');

                $createdBy = $request['createdBy'];
                $createdBy = (array)$createdBy;
                $createdBy = collect($createdBy)->pluck('id');

                $dataQry = $this->paySupplierInvoiceMasterRepository->paySupplierInvoiceListQuery($request, $input, $search, $supplierID, $projectID, $employeeID,$createdBy);
                $data = $this->paySupplierInvoiceMasterRepository->setExportExcelData($dataQry);
                break;

            case '6':
                $input = $this->convertArrayToSelectedValue($input, array('confirmedYN', 'glCodeAssignedYN', 'approved', 'year'));
                $dataQry = $this->expenseClaimRepository->expenseClaimListQuery($request, $input, $search);
                $data = $this->expenseClaimRepository->setExportExcelData($dataQry);
                break;

            case '7':
                $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'confirmedYN', 'approved', 'location', 'month', 'year'));
                $grvLocation = $request['location'];
                $grvLocation = (array)$grvLocation;
                $grvLocation = collect($grvLocation)->pluck('id');

                $serviceLineSystemID = $request['serviceLineSystemID'];
                $serviceLineSystemID = (array)$serviceLineSystemID;
                $serviceLineSystemID = collect($serviceLineSystemID)->pluck('id');
                $reasons = (isset($input['reason'])) ? collect($input['reason'])->pluck('id') : null;
                $dataQry = $this->stockAdjustmentRepository->stockAdjustmentListQuery($request, $input, $search, $grvLocation, $serviceLineSystemID,$reasons);
                $data = $this->stockAdjustmentRepository->setExportExcelData($dataQry);
                break;

            case '8':
                $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'ConfirmedYN', 'approved'));

                $serviceLineSystemID = $request['serviceLineSystemID'];
                $serviceLineSystemID = (array)$serviceLineSystemID;
                $serviceLineSystemID = collect($serviceLineSystemID)->pluck('id');

                $grvLocation = $request['wareHouseFrom'];
                $grvLocation = (array)$grvLocation;
                $grvLocation = collect($grvLocation)->pluck('id');

                $dataQry = $this->itemIssueMasterRepository->itemIssueListQuery($request, $input, $search, $grvLocation, $serviceLineSystemID);
                $data = $this->itemIssueMasterRepository->setExportExcelData($dataQry);
                break;

            case '9':
                $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'ConfirmedYN', 'approved'));
                $serviceLineSystemID = $request['serviceLineSystemID'];
                $serviceLineSystemID = (array)$serviceLineSystemID;
                $serviceLineSystemID = collect($serviceLineSystemID)->pluck('id');
                $dataQry = $this->materielRequestRepository->materialrequestsListQuery($request, $input, $search,  $serviceLineSystemID);
                $data = $this->materielRequestRepository->setExportExcelData($dataQry);
                break;

            case '10':
                $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'ConfirmedYN', 'approved'));
                $serviceLineSystemID = $request['serviceLineSystemID'];
                $serviceLineSystemID = (array)$serviceLineSystemID;
                $serviceLineSystemID = collect($serviceLineSystemID)->pluck('id');
                $dataQry = $this->stockReceiveRepository->stockReceiveListQuery($request, $input, $search, $serviceLineSystemID);
                $data = $this->stockReceiveRepository->setExportExcelData($dataQry);
                break;

            case '11':
                $input = $this->convertArrayToSelectedValue($input, array('cancelYN', 'confirmedYN', 'approved', 'month', 'year', 'supplierID', 'documentType', 'projectID'));
                
                $supplierID = $request['supplierID'];
                $supplierID = (array)$supplierID;
                $supplierID = collect($supplierID)->pluck('id');

                $projectID = $request['projectID'];
                $projectID = (array)$projectID;
                $projectID = collect($projectID)->pluck('id');

                $dataQry = $this->bookInvSuppMasterRepository->bookInvSuppListQuery($request, $input, $search, $supplierID, $projectID);
                $data = $this->bookInvSuppMasterRepository->setExportExcelData($dataQry);
                break;

            case '12':
                $input =  $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'confirmedYN', 'approved', 'wareHouseLocation', 'month', 'year'));
                $serviceLineSystemID = $request['serviceLineSystemID'];
                $serviceLineSystemID = (array)$serviceLineSystemID;
                $serviceLineSystemID = collect($serviceLineSystemID)->pluck('id');

                $grvLocation = $request['wareHouseLocation'];
                $grvLocation = (array)$grvLocation;
                $grvLocation = collect($grvLocation)->pluck('id');
                $dataQry = $this->itemReturnMasterRepository->itemReturnListQuery($request, $input, $search, $grvLocation, $serviceLineSystemID);
                $data = $this->itemReturnMasterRepository->setExportExcelData($dataQry);
                break;

            case '13':
                $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'locationFrom', 'confirmedYN', 'approved', 'month', 'year', 'interCompanyTransferYN'));
                $grvLocation = $request['locationFrom'];
                $grvLocation = (array)$grvLocation;
                $grvLocation = collect($grvLocation)->pluck('id');

                $serviceLineSystemID = $request['serviceLineSystemID'];
                $serviceLineSystemID = (array)$serviceLineSystemID;
                $serviceLineSystemID = collect($serviceLineSystemID)->pluck('id');
                $dataQry = $this->stockTransferRepository->stockTransferListQuery($request, $input, $search, $grvLocation, $serviceLineSystemID);
                $data = $this->stockTransferRepository->setExportExcelData($dataQry);
                break;

            case '15':
                if($input['docName'] == "receipt_voucher-matching") {
                    $input = $this->convertArrayToSelectedValue($input, array('confirmedYN', 'approved', 'month', 'year', 'customerID'));
                    
                    $customerID = $request['customerID'];
                    $customerID = (array)$customerID;
                    $customerID = collect($customerID)->pluck('id');

                    $dataQry = $this->matchDocumentMasterRepository->receiptVoucherMatchingListQuery($request, $input, $search, $customerID);
                    $data = $this->matchDocumentMasterRepository->setReceiptVoucherMatchingExportExcelData($dataQry);
                } else {
                    $input = $this->convertArrayToSelectedValue($input, array('confirmedYN', 'approved', 'month', 'year', 'supplierID'));
                    
                    $supplierID = $request['supplierID'];
                    $supplierID = (array)$supplierID;
                    $supplierID = collect($supplierID)->pluck('id');

                    $dataQry = $this->matchDocumentMasterRepository->matchDocumentListQuery($request, $input, $search, $supplierID);
                    $data = $this->matchDocumentMasterRepository->setExportExcelData($dataQry);
                }
                break;

            case '17':
                $input = $this->convertArrayToSelectedValue($input, array('confirmedYN', 'approved', 'month', 'year', 'jvType'));
                $dataQry = $this->jvMasterRepository->jvMasterListQuery($request, $input, $search);
                $data = $this->jvMasterRepository->setExportExcelData($dataQry);
                break;

            case '19':
                $input = $this->convertArrayToSelectedValue($input, array('confirmedYN', 'month', 'approved', 'year', 'customerID', 'projectID'));

                $customerID = $request['customerID'];
                $customerID = (array)$customerID;
                $customerID = collect($customerID)->pluck('id');

                $projectID = $request['projectID'];
                $projectID = (array)$projectID;
                $projectID = collect($projectID)->pluck('id');

                $dataQry = $this->creditNoteRepository->creditNoteListQuery($request, $input, $search, $customerID, $projectID);
                $data = $this->creditNoteRepository->setExportExcelData($dataQry);
                break;

            case '20':
                $input = $this->convertArrayToSelectedValue($input, array('invConfirmedYN', 'customerID', 'month', 'approved', 'canceledYN', 'year', 'isProforma'));
                
                $customerID = $request['customerID'];
                $customerID = (array)$customerID;
                $customerID = collect($customerID)->pluck('id');

                $dataQry = $this->customerInvoiceDirectRepository->customerInvoiceListQuery($request, $input, $search, $customerID);
                $data = $this->customerInvoiceDirectRepository->setExportExcelData($dataQry);
                break;

            case '21':
                $input = $this->convertArrayToSelectedValue($input, array('confirmedYN', 'month', 'approved', 'year', 'documentType', 'trsClearedYN','createdBy'));
                $dataQry = $this->customerReceivePaymentRepository->customerReceiveListQuery($request, $input, $search);
                $data = $this->customerReceivePaymentRepository->setExportExcelData($dataQry);
                break;

            case '22':
                $input = $this->convertArrayToSelectedValue($input, array('cancelYN', 'confirmedYN', 'approved'));
                $dataQry = $this->fixedAssetMasterRepository->fixedAssetMasterListQuery($request, $input, $search);
                $data = $this->fixedAssetMasterRepository->setExportExcelData($dataQry);
                break;

            case '23':
                $input = $this->convertArrayToSelectedValue($input, array('cancelYN', 'confirmedYN', 'approved'));
                $dataQry = $this->fixedAssetDepreciationMasterRepository->fixedAssetDepreciationListQuery($request, $input, $search);
                $data = $this->fixedAssetDepreciationMasterRepository->setExportExcelData($dataQry);
                break;

            case '24':
                $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID',
                'purchaseReturnLocation', 'confirmedYN', 'approved', 'month', 'year'));
                $serviceLineSystemID = $request['serviceLineSystemID'];
                $serviceLineSystemID = (array)$serviceLineSystemID;
                $serviceLineSystemID = collect($serviceLineSystemID)->pluck('id');
                $grvLocation = $request['purchaseReturnLocation'];
                $grvLocation = (array)$grvLocation;
                $grvLocation = collect($grvLocation)->pluck('id');
                $dataQry = $this->purchaseReturnRepository->purchaseReturnListQuery($request, $input, $search, $serviceLineSystemID, $grvLocation);
                $data = $this->purchaseReturnRepository->setExportExcelData($dataQry);
                break;

            case '28':
                $input = $this->convertArrayToSelectedValue($input, array('confirmedYN', 'approvedYN'));
                $dataQry = $this->monthlyAdditionsMasterRepository->monthlyAdditionsListQuery($request, $input, $search);
                $data = $this->monthlyAdditionsMasterRepository->setExportExcelData($dataQry);
                break;

            case '39':
                $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'contractUID', 'year', 'month', 'customerID'));
                $dataQry = $this->customerInvoiceTrackingRepository->customerInvoiceTrackingListQuery($request, $input, $search);
                $data = $this->customerInvoiceTrackingRepository->setExportExcelData($dataQry);
                break;

            case '46':
                $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'contractUID', 'year', 'month', 'customerID'));
                $dataQry = $this->budgetTransferFormRepository->budgetTransferFormListQuery($request, $input, $search);
                $data = $this->budgetTransferFormRepository->setExportExcelData($dataQry);
                break;

            case '61':
                $input = $this->convertArrayToSelectedValue($input, array('segment_by', 'created_by')); 
                $dataQry = $this->inventoryReclassificationRepository->inventoryReclassificationListQuery($request, $input, $search);
                $data = $this->inventoryReclassificationRepository->setExportExcelData($dataQry);
                break;
                
            case '62':
                $input = $this->convertArrayToSelectedValue($input, array('segment_by', 'created_by'));

                $bankmasterAutoID = $request['bankmasterAutoID'];
                $bankmasterAutoID = (array)$bankmasterAutoID;
                $bankmasterAutoID = collect($bankmasterAutoID)->pluck('id');

                $dataQry = $this->bankReconciliationRepository->bankReconciliationListQuery($request, $input, $search, $bankmasterAutoID);
                $data = $this->bankReconciliationRepository->setExportExcelData($dataQry);
                break;

            case '64':
                $input = $this->convertArrayToSelectedValue($input, array('month', 'year'));

                $bankmasterAutoID = $request['bankmasterAutoID'];
                $bankmasterAutoID = (array)$bankmasterAutoID;
                $bankmasterAutoID = collect($bankmasterAutoID)->pluck('id');

                $dataQry = $this->paymentBankTransferRepository->paymentBankTransferListQuery($request, $input, $search, $bankmasterAutoID);
                $data = $this->paymentBankTransferRepository->setExportExcelData($dataQry);
                break;

            case '66':
                $input = $this->convertArrayToSelectedValue($input, array('bankmasterAutoID', 'isAccountActive'));

                $bankmasterAutoID = $request['bankmasterAutoID'];
                $bankmasterAutoID = (array)$bankmasterAutoID;
                $bankmasterAutoID = collect($bankmasterAutoID)->pluck('id');

                $dataQry = $this->bankAccountRepository->bankAccountListQuery($request, $input, $search, $bankmasterAutoID);
                $data = $this->bankAccountRepository->setExportExcelData($dataQry);
                break;

            case '67':
                $dataQry = $this->quotationMasterRepository->quotationMasterListQuery($request, $input, $search);
                $data = $this->quotationMasterRepository->setExportExcelData($dataQry);
                break;

            case '68':
                $dataQry = $this->quotationMasterRepository->quotationMasterListQuery($request, $input, $search);
                $data = $this->quotationMasterRepository->setExportExcelData($dataQry);
                break;

            case '69':
                $input = $this->convertArrayToSelectedValue($input, array('month', 'year', 'confirmedYN'));
                $dataQry = $this->consoleJVMasterRepository->consoleJVMasterListQuery($request, $input, $search);
                $data = $this->consoleJVMasterRepository->setExportExcelData($dataQry);
                break;

            case '71':
                $dataQry = $this->deliveryOrderRepository->deliveryOrderListQuery($request, $input, $search);
                $data = $this->deliveryOrderRepository->setExportExcelData($dataQry);
                break;

            case '87':
                $dataQry = $this->salesReturnRepository->salesReturnListQuery($request, $input, $search);
                $data = $this->salesReturnRepository->setExportExcelData($dataQry);
                break;
            case '97':
                $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'confirmedYN', 'approved', 'location', 'month', 'year'));

                $grvLocation = $request['location'];
                $grvLocation = (array)$grvLocation;
                $grvLocation = collect($grvLocation)->pluck('id');
                $serviceLineSystemID = $request['serviceLineSystemID'];
                $serviceLineSystemID = (array)$serviceLineSystemID;
                $serviceLineSystemID = collect($serviceLineSystemID)->pluck('id');

                $dataQry = $this->stockCountRepository->stockCountListQuery($request, $input, $search, $grvLocation,$serviceLineSystemID);
                $data = $this->stockCountRepository->setExportExcelData($dataQry);
                break;
            case '58':
                $bankmasterAutoID = $request['bank'];
                $bankmasterAutoID = (array)$bankmasterAutoID;
                $bankmasterAutoID = collect($bankmasterAutoID)->pluck('id');

                $dataQry = $this->pdcLogRepository->pdcIssuedListQuery($request, $input, $search, $bankmasterAutoID);
                $data = $this->pdcLogRepository->setExportExcelData($dataQry,$input);
                break;
            case '119':
                $input = $this->convertArrayToSelectedValue($input, array('confirmedYN', 'approved', 'month', 'year', 'documentType'));
                $dataQry = $this->recurringVoucherSetupRepository->rrvMasterListQuery($request, $input, $search);
                $data = $this->recurringVoucherSetupRepository->setExportExcelData($dataQry);
                break;
            default:
                return $this->sendResponse(array(), 'export failed');
        }

        $companyID = isset($input['companyId']) ? $input['companyId']: null;
        if($companyID == null){
            $companyID = isset($input['companyID']) ? $input['companyID']: null;
            if($companyID == null){
                $companyID = isset($input['companySystemID']) ? $input['companySystemID']: null;
            }
        }

        $companyMaster = Company::find($companyID);
        $companyCode = isset($companyMaster->CompanyID)?$companyMaster->CompanyID:'common';
        $detail_array = array(
            'company_code'=>$companyCode
        );

        $doc_name = $input['docName'].'/';
        $path = 'procurement/'.$doc_name.'excel/';
        $basePath = CreateExcel::process($data,$type,$input['docName'],$path, $detail_array);

        if($basePath == '')
        {
             return $this->sendError('Unable to export excel');
        }
        else
        {
             return $this->sendResponse($basePath, trans('custom.success_export'));
        }
        


    }
}
