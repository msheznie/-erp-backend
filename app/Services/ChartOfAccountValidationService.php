<?php


namespace App\Services;

use App\Models\ChartOfAccount;
use App\Services\GeneralLedger\CreditNoteGlService;
use App\Services\GeneralLedger\CustomerInvoiceGlService;
use App\Services\GeneralLedger\CustomerReceivePaymentGlService;
use App\Services\GeneralLedger\DebitNoteGlService;
use App\Services\GeneralLedger\DeliveryOrderGlService;
use App\Services\GeneralLedger\FixedAssetDipreciationGlService;
use App\Services\GeneralLedger\FixedAssetDisposalGlService;
use App\Services\GeneralLedger\FixedAssetMasterGlService;
use App\Services\GeneralLedger\GPOSSalesGlService;
use App\Services\GeneralLedger\GrvGlService;
use App\Services\GeneralLedger\InventoryReclassificationGlService;
use App\Services\GeneralLedger\JournalVoucherGlService;
use App\Services\GeneralLedger\MaterialIssueGlService;
use App\Services\GeneralLedger\MaterialReturnGlService;
use App\Services\GeneralLedger\PaymentVoucherGlService;
use App\Services\GeneralLedger\PurchaseReturnGlService;
use App\Services\GeneralLedger\RPOSSalesGlService;
use App\Services\GeneralLedger\SalesReturnGlService;
use App\Services\GeneralLedger\StockAdjustmentGlService;
use App\Services\GeneralLedger\StockCountGlService;
use App\Services\GeneralLedger\StockRecieveGlService;
use App\Services\GeneralLedger\StockTransferGlService;
use App\Services\GeneralLedger\SupplierInvoiceGlService;
use Illuminate\Http\Request;

class ChartOfAccountValidationService
{

    public function __construct()
    {

    }

    public function checkChartOfAccountStatus($documentSystemID, $autoID, $companySystemID, $uploadEmployeeID = null)
    {
        $masterModel = [
            'employeeSystemID' => is_null($uploadEmployeeID) ? \Helper::getEmployeeSystemID() : $uploadEmployeeID,
            'autoID' => $autoID,
            'documentSystemID' => $documentSystemID,
            'companySystemID' => $companySystemID
        ];

        switch ($masterModel["documentSystemID"]) {
            case 3: // GRV
                $result = GrvGlService::processEntry($masterModel);
                break;
            case 8: // MI - Material issue
                $result = MaterialIssueGlService::processEntry($masterModel);
                break;
            case 12: // SR - Material Return
                $result = MaterialReturnGlService::processEntry($masterModel);
                break;
            case 13: // ST - Stock Transfer
                $result = StockTransferGlService::processEntry($masterModel);
                break;
            case 10: // RS - Stock Receive
                $result = StockRecieveGlService::processEntry($masterModel);
                break;
            case 61: // INRC - Inventory Reclassififcation
                $result = InventoryReclassificationGlService::processEntry($masterModel);
                break;
            case 24: // PRN - Purchase Return
                $result = PurchaseReturnGlService::processEntry($masterModel);
                break;
            case 20:
                $result = CustomerInvoiceGlService::processEntry($masterModel);
                break;
            case 7: // SA - Stock Adjustment
                $result = StockAdjustmentGlService::processEntry($masterModel);
                break;
            case 11: // SI - Supplier Invoice
                $result = SupplierInvoiceGlService::processEntry($masterModel);
                break;
            case 15: // DN - Debit Note
                $result = DebitNoteGlService::processEntry($masterModel);
                break;
            case 19: // CN - Credit Note
                $result = CreditNoteGlService::processEntry($masterModel);
                break;
            case 4: // PV - Payment Voucher
                $result = PaymentVoucherGlService::processEntry($masterModel);
                break;
            case 21: // BRV - Customer Receive Payment
                $result = CustomerReceivePaymentGlService::processEntry($masterModel);
                break;
            case 17: // JV - Journal Voucher
                $result = JournalVoucherGlService::processEntry($masterModel);
                break;
            case 22: // FA - Fixed Asset Master
                $result = FixedAssetMasterGlService::processEntry($masterModel);
                break;
            case 23: // FAD - Fixed Asset Depreciation
                $result = FixedAssetDipreciationGlService::processEntry($masterModel);
                break;
            case 41: // FADS - Fixed Asset Disposal
                $result = FixedAssetDisposalGlService::processEntry($masterModel);
                break;
            case 71:
                $result = DeliveryOrderGlService::processEntry($masterModel);
                break;
            case 87: // sales return
                $result = SalesReturnGlService::processEntry($masterModel);
                break;
            case 97: // SA - Stock Count
                $result = StockCountGlService::processEntry($masterModel);
                break;
            case 110: // GPOS Sales
                $result = GPOSSalesGlService::processEntry($masterModel);
                break;
            case 111: // RPOS Sales
                $result = RPOSSalesGlService::processEntry($masterModel);
                break;
            default:
                $result = ['status' => false, 'message' => "Document ID not found"];
        }

        $resData = ((isset($result['status']) && $result['status']) && (isset($result['data']['finalData']) && $result['data']['finalData'])) ? $result['data']['finalData'] : [];

        $accountCodes = [];

        foreach ($resData as $key => $value) {
            $chartOfAccounts = ChartOfAccount::where('isActive', 0)->where('chartOfAccountSystemID', $value['chartOfAccountSystemID'])->first();
            if(!empty($chartOfAccounts)) {
                $accountCodes[] = $chartOfAccounts['AccountCode'];
            }
        }
        $accountCodesString = implode(',', $accountCodes);

        $error = "The Chart of Account/s  $accountCodesString are inactive. Update or change the linked Chart of Account to proceed";

        return ['accountCodes' => $accountCodes, 'errorMsg' => $error];
    }

}
