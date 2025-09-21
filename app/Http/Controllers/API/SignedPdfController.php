<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Jobs\CleanExpiredSignedPdfUrls;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SignedPdfController extends AppBaseController
{
    /**
     * Generate a temporary signed URL for PDF access
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateSignedUrl(Request $request)
    {
        try {
            $routeName = $request->input('route');
            $params = $request->input('params', []);
            $expiresInMinutes = env('SIGNED_PDF_EXPIRES_IN_MINUTES', 3);

            if (!$routeName) {
                return $this->sendError('Route name is required');
            }

            // Validate that the route exists in our allowed PDF routes
            $allowedRoutes = $this->getAllowedPdfRoutes();
            if (!in_array($routeName, $allowedRoutes)) {
                return $this->sendError('Invalid or unauthorized route');
            }

            // Generate unique token
            $token = Str::random(32);
            $expires = time() + ($expiresInMinutes * 60);

            // Get current user information for context
            $user = Auth::user();
            $userId = $user ? $user->id : null;

            // Store URL data in cache
            $urlData = [
                'route' => $routeName,
                'params' => $params,
                'user_id' => $userId,
                'created_at' => time()
            ];

            // Cache the URL data with expiration
            Cache::put("signed_pdf_url:{$token}", $urlData, $expiresInMinutes);
            
            // Track active token for cleanup job
            $this->trackActiveToken($token);
            
            // Schedule cleanup of expired tokens
            $this->scheduleCleanup();

            // Generate signature
            $signature = $this->generateSignature($token, $expires, $urlData);

            $signedUrl = "api/v1/pdf/stream" . "?" . http_build_query([
                'token' => $token,
                'signature' => $signature,
                'expires' => $expires
            ]);

            return $this->sendResponse([
                'signed_url' => $signedUrl,
                'expires_at' => date('Y-m-d H:i:s', $expires),
                'expires_in_seconds' => $expires - time()
            ], 'Signed URL generated successfully');

        } catch (\Exception $e) {
            return $this->sendError('Failed to generate signed URL');
        }
    }

    /**
     * Handle direct PDF requests (backward compatibility and fallback)
     * This method handles all PDF routes through the SignedPdfController
     *
     * @param Request $request
     * @return mixed
     */
    public function handleDirectPdf(Request $request)
    {
        try {
            // Get the route name from the current route
            $routeName = $request->route()->getName();
            if (!$routeName) {
                // Extract route name from the URI pattern
                $uri = $request->route()->uri();
                $routeName = $uri;
                
                // If still no route name, try to get it from the action name
                $action = $request->route()->getAction();
                if (isset($action['as'])) {
                    $routeName = $action['as'];
                }
            }

            // If we still don't have a route name, extract from the request path
            if (!$routeName) {
                $path = $request->path();
                // Remove 'api/v1/' prefix if present
                $routeName = str_replace('api/v1/', '', $path);
            }

            // Map route names to controller methods
            $controllerMapping = $this->getPdfControllerMapping();
            
            if (!isset($controllerMapping[$routeName])) {
                return response()->json(['error' => "PDF route '{$routeName}' not found"], 404);
            }

            $controllerInfo = $controllerMapping[$routeName];
            $controllerClass = $controllerInfo['controller'];
            $method = $controllerInfo['method'];

            $controller = app()->make($controllerClass);

            return $controller->$method($request);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to generate PDF: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Stream PDF using signed URL
     *
     * @param Request $request
     * @return mixed
     */
    public function streamPdf(Request $request)
    {
        try {
            // Get the PDF route from middleware
            $routeName = $request->attributes->get('pdf_route');
            
            if (!$routeName) {
                return response()->json(['error' => 'Invalid PDF route'], 403);
            }

            // Map route names to controller methods
            $controllerMapping = $this->getPdfControllerMapping();
            
            if (!isset($controllerMapping[$routeName])) {
                return response()->json(['error' => 'PDF route not found'], 404);
            }

            $controllerInfo = $controllerMapping[$routeName];
            $controllerClass = $controllerInfo['controller'];
            $method = $controllerInfo['method'];

            // Create controller instance
            $controller = app()->make($controllerClass);

            // Call the PDF generation method
            return $controller->$method($request);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to generate PDF'], 500);
        }
    }

    /**
     * Generate signature for URL validation
     *
     * @param string $token
     * @param int $expires
     * @param array $urlData
     * @return string
     */
    private function generateSignature($token, $expires, $urlData)
    {
        $payload = $token . $expires . serialize($urlData);
        return hash_hmac('sha256', $payload, config('app.key'));
    }

    /**
     * Get list of allowed PDF routes
     *
     * @return array
     */
    private function getAllowedPdfRoutes()
    {
        return [
            'printSupplierInvoice',
            'getPoLogisticPrintPDF',
            'getReportPDF',
            'goodReceiptVoucherPrintPDF',
            'printItemIssue',
            'deliveryPrintItemIssue',
            'printCustomerInvoice',
            'printReceiptVoucher',
            'printPaymentVoucher',
            'printPurchaseRequest',
            'printMaterielRequest',
            'printBudgetTransfer',
            'printStockTransfer',
            'printItemReturn',
            'printStockReceive',
            'printPurchaseReturn',
            'printExpenseClaim',
            'printDebitNote',
            'printExpenseClaimMaster',
            'printBankReconciliation',
            'printPaymentMatching',
            'getSalesQuotationPrintPDF',
            'printDeliveryOrder',
            'printSalesReturn',
            'printRecurringVoucher',
            'printChartOfAccount',
            'pvSupplierPrint',
            'printCreditNote',
            'printEvaluationTemplate',
            'supplierEvaluationPrintPDF',
            'printJournalVoucher',
            'getProcumentOrderPrintPDF',
            'generateARReportPDF',
            'generateAPReportPDF',
            'printChequeItems',
            'printSuppliers',
            'getBatchSubmissionDetailsPrintPDF',
            'exportPaymentBankTransfer',
            'BidSummaryReport',
            'SupplierRankingSummaryReport',
            'MinutesofTenderAwardingReport',
            'MinutesofBidOpeningReport',
            'supplier-item-wise-report',
            'schedule-wise-report',
            'SupplierScheduleWiseExportReport',
            'genearetBarcode'
        ];
    }

    /**
     * Track active token for cleanup job
     */
    private function trackActiveToken($token)
    {
        try {
            $activeTokens = Cache::get('signed_pdf_active_tokens', []);
            
            // Add new token
            $activeTokens[] = $token;
            
            // Remove duplicates and limit size
            $activeTokens = array_unique($activeTokens);
            if (count($activeTokens) > 1000) {
                $activeTokens = array_slice($activeTokens, -1000);
                Log::info('Active tokens list trimmed to 1000 entries');
            }
            
            // Store with 24 hour TTL
            Cache::put('signed_pdf_active_tokens', array_values($activeTokens), 60 * 24);
            
        } catch (\Exception $e) {
            Log::warning('Failed to track active token: ' . $e->getMessage());
        }
    }

    /**
     * Schedule cleanup of expired signed URL tokens using background job
     */
    private function scheduleCleanup()
    {
        if (rand(1, 100) <= 10) {
            try {
                CleanExpiredSignedPdfUrls::dispatch()->delay(now()->addMinutes(1));
            } catch (\Exception $e) {
                Log::error('Failed to schedule signed PDF URL cleanup job: ' . $e->getMessage());
            }
        }
    }

    /**
     * Get mapping of PDF routes to their controllers
     *
     * @return array
     */
    private function getPdfControllerMapping()
    {
        return [
            'printSupplierInvoice' => [
                'controller' => 'App\Http\Controllers\API\BookInvSuppMasterAPIController',
                'method' => 'printSupplierInvoice'
            ],
            'getPoLogisticPrintPDF' => [
                'controller' => 'App\Http\Controllers\API\PoAdvancePaymentAPIController',
                'method' => 'getPoLogisticPrintPDF'
            ],
            'getReportPDF' => [
                'controller' => 'App\Http\Controllers\API\ReportAPIController',
                'method' => 'pdfExportReport'
            ],
            'goodReceiptVoucherPrintPDF' => [
                'controller' => 'App\Http\Controllers\API\GRVMasterAPIController',
                'method' => 'goodReceiptVoucherPrintPDF'
            ],
            'printItemIssue' => [
                'controller' => 'App\Http\Controllers\API\ItemIssueMasterAPIController',
                'method' => 'printItemIssue'
            ],
            'deliveryPrintItemIssue' => [
                'controller' => 'App\Http\Controllers\API\ItemIssueMasterAPIController',
                'method' => 'deliveryPrintItemIssue'
            ],
            'printCustomerInvoice' => [
                'controller' => 'App\Http\Controllers\API\CustomerInvoiceDirectAPIController',
                'method' => 'printCustomerInvoice'
            ],
            'printReceiptVoucher' => [
                'controller' => 'App\Http\Controllers\API\CustomerReceivePaymentAPIController',
                'method' => 'printReceiptVoucher'
            ],
            'printPaymentVoucher' => [
                'controller' => 'App\Http\Controllers\API\PaySupplierInvoiceMasterAPIController',
                'method' => 'printPaymentVoucher'
            ],
            'printPurchaseRequest' => [
                'controller' => 'App\Http\Controllers\API\PurchaseRequestAPIController',
                'method' => 'printPurchaseRequest'
            ],
            'printMaterielRequest' => [
                'controller' => 'App\Http\Controllers\API\MaterielRequestAPIController',
                'method' => 'printMaterielRequest'
            ],
            'printBudgetTransfer' => [
                'controller' => 'App\Http\Controllers\API\BudgetTransferFormAPIController',
                'method' => 'printBudgetTransfer'
            ],
            'printStockTransfer' => [
                'controller' => 'App\Http\Controllers\API\StockTransferAPIController',
                'method' => 'printStockTransfer'
            ],
            'printItemReturn' => [
                'controller' => 'App\Http\Controllers\API\ItemReturnMasterAPIController',
                'method' => 'printItemReturn'
            ],
            'printStockReceive' => [
                'controller' => 'App\Http\Controllers\API\StockReceiveAPIController',
                'method' => 'printStockReceive'
            ],
            'printPurchaseReturn' => [
                'controller' => 'App\Http\Controllers\API\PurchaseReturnAPIController',
                'method' => 'printPurchaseReturn'
            ],
            'printExpenseClaim' => [
                'controller' => 'App\Http\Controllers\API\ExpenseClaimAPIController',
                'method' => 'printExpenseClaim'
            ],
            'printDebitNote' => [
                'controller' => 'App\Http\Controllers\API\DebitNoteAPIController',
                'method' => 'printDebitNote'
            ],
            'printExpenseClaimMaster' => [
                'controller' => 'App\Http\Controllers\API\ExpenseClaimMasterAPIController',
                'method' => 'printExpenseClaimMaster'
            ],
            'printBankReconciliation' => [
                'controller' => 'App\Http\Controllers\API\BankReconciliationAPIController',
                'method' => 'printBankReconciliation'
            ],
            'printPaymentMatching' => [
                'controller' => 'App\Http\Controllers\API\MatchDocumentMasterAPIController',
                'method' => 'printPaymentMatching'
            ],
            'getSalesQuotationPrintPDF' => [
                'controller' => 'App\Http\Controllers\API\QuotationMasterAPIController',
                'method' => 'getSalesQuotationPrintPDF'
            ],
            'printDeliveryOrder' => [
                'controller' => 'App\Http\Controllers\API\DeliveryOrderAPIController',
                'method' => 'printDeliveryOrder'
            ],
            'printSalesReturn' => [
                'controller' => 'App\Http\Controllers\API\SalesReturnAPIController',
                'method' => 'printSalesReturn'
            ],
            'printRecurringVoucher' => [
                'controller' => 'App\Http\Controllers\API\RecurringVoucherSetupAPIController',
                'method' => 'printRecurringVoucher'
            ],
            'printChartOfAccount' => [
                'controller' => 'App\Http\Controllers\API\ChartOfAccountAPIController',
                'method' => 'printChartOfAccount'
            ],
            'pvSupplierPrint' => [
                'controller' => 'App\Http\Controllers\API\BankLedgerAPIController',
                'method' => 'pvSupplierPrint'
            ],
            'printCreditNote' => [
                'controller' => 'App\Http\Controllers\API\CreditNoteAPIController',
                'method' => 'printCreditNote'
            ],
            'printEvaluationTemplate' => [
                'controller' => 'App\Http\Controllers\API\SupplierEvaluationTemplateAPIController',
                'method' => 'printEvaluationTemplate'
            ],
            'supplierEvaluationPrintPDF' => [
                'controller' => 'App\Http\Controllers\API\SupplierEvaluationController',
                'method' => 'printSupplierEvaluation'
            ],
            'printJournalVoucher' => [
                'controller' => 'App\Http\Controllers\API\JvMasterAPIController',
                'method' => 'printJournalVoucher'
            ],
            'getProcumentOrderPrintPDF' => [
                'controller' => 'App\Http\Controllers\API\ProcumentOrderAPIController',
                'method' => 'getProcumentOrderPrintPDF'
            ],
            'generateARReportPDF' => [
                'controller' => 'App\Http\Controllers\API\AccountsReceivableReportAPIController',
                'method' => 'pdfExportReport'
            ],
            'generateAPReportPDF' => [
                'controller' => 'App\Http\Controllers\API\AccountsPayableReportAPIController',
                'method' => 'pdfExportReport'
            ],
            'printChequeItems' => [
                'controller' => 'App\Http\Controllers\API\BankLedgerAPIController',
                'method' => 'printChequeItems'
            ],
            'printSuppliers' => [
                'controller' => 'App\Http\Controllers\API\SupplierMasterAPIController',
                'method' => 'printSuppliers'
            ],
            'getBatchSubmissionDetailsPrintPDF' => [
                'controller' => 'App\Http\Controllers\API\CustomerInvoiceTrackingAPIController',
                'method' => 'getBatchSubmissionDetailsPrintPDF'
            ],
            'exportPaymentBankTransfer' => [
                'controller' => 'App\Http\Controllers\API\PaymentBankTransferAPIController',
                'method' => 'exportPaymentBankTransfer'
            ],
            'BidSummaryReport' => [
                'controller' => 'App\Http\Controllers\API\BidSubmissionMasterAPIController',
                'method' => 'BidSummaryExportReport'
            ],
            'SupplierRankingSummaryReport' => [
                'controller' => 'App\Http\Controllers\API\TenderFinalBidsAPIController',
                'method' => 'getFinalBidsReport'
            ],
            'MinutesofTenderAwardingReport' => [
                'controller' => 'App\Http\Controllers\API\TenderFinalBidsAPIController',
                'method' => 'getTenderAwardingReport'
            ],
            'MinutesofBidOpeningReport' => [
                'controller' => 'App\Http\Controllers\API\TenderMasterAPIController',
                'method' => 'getTenderBidOpeningReport'
            ],
            'supplier-item-wise-report' => [
                'controller' => 'App\Http\Controllers\API\BidSubmissionMasterAPIController',
                'method' => 'SupplierItemWiseExportReport'
            ],
            'schedule-wise-report' => [
                'controller' => 'App\Http\Controllers\API\BidSubmissionMasterAPIController',
                'method' => 'SupplierSheduleWiseReport'
            ],
            'SupplierScheduleWiseExportReport' => [
                'controller' => 'App\Http\Controllers\API\BidSubmissionMasterAPIController',
                'method' => 'SupplierScheduleWiseExportReport'
            ],
            'genearetBarcode' => [
                'controller' => 'App\Http\Controllers\API\BarcodeConfigurationAPIController',
                'method' => 'genearetBarcode'
            ]
        ];
    }
}
