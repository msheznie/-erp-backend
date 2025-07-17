<?php

namespace App\Jobs;

use App\helper\CommonJobService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\CustomerMaster;
use App\Models\AccountsReceivableLedger;
use App\Models\Company;
use App\Models\CustomerReceivePayment;
use App\Models\MatchDocumentMaster;
use App\Models\CustomerReceivePaymentDetail;
use App\Models\PaySupplierInvoiceDetail;
use App\Jobs\InitiateWebhook;
use App\Models\AdvanceReceiptDetails;
use App\Models\CompanyPolicyMaster;
use App\Models\CreditNote;
use App\Models\CreditNoteDetails;
use App\Models\CustomerAssigned;
use App\Models\CustomerInvoice;
use App\Models\CustomerInvoiceDirectDetail;
use App\Models\DirectReceiptDetail;
use App\Models\SalesReturnDetail;
use App\Services\API\ReceiptMatchingAPIService;
use App\Traits\DocumentSystemMappingTrait;
use GuzzleHttp\Client;

class CreateReceiptMatching implements ShouldQueue
{

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, DocumentSystemMappingTrait;

    public $input;
    public $timeout = 500;
    public $db;
    public $apiExternalKey;
    public $apiExternalUrl;
    public $authorization;
    public $externalReference;
    public $tenantUuid;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($input, $db, $apiExternalKey, $apiExternalUrl, $authorization, $externalReference, $tenantUuid)
    {
        if(env('QUEUE_DRIVER_CHANGE','database') == 'database'){
            if(env('IS_MULTI_TENANCY',false)){
                self::onConnection('database_main');
            }else{
                self::onConnection('database');
            }
        }else{
            self::onConnection(env('QUEUE_DRIVER_CHANGE','database'));
        }

        $this->input = $input;
        $this->db = $db;
        $this->apiExternalKey = $apiExternalKey;
        $this->apiExternalUrl = $apiExternalUrl;
        $this->authorization = $authorization;
        $this->externalReference = $externalReference;
        $this->tenantUuid = $tenantUuid;
    }

    public function handle()
    {
        \Log::useFiles(storage_path() . '/logs/create_receipt_matching.log');
        CommonJobService::db_switch($this->db);

        $input = $this->input;
        $externalReference = $this->externalReference;
        $matchings = $input['matchings'] ?? [];
        $results = [];
        $successCount = 0;
        $failCount = 0;

        $companyId = $input['company_id'] ?? null;
        $company = $companyId ? Company::find($companyId) : null;
        $companySystemId = $company ? $company->companySystemID : null;

        foreach ($matchings as $index => $matching) {
            $header = $matching['header'] ?? [];
            $details = $matching['details'] ?? [];
            
            $headerValidationResult = $this->validateHeader($header, $companyId, $companySystemId);
            $headerErrors = $headerValidationResult['errors'];
            $validationData = $headerValidationResult['data'];

            $detailsErrors = [];
            if (empty($headerErrors)) {
                $detailsErrors = $this->validateDetails($details, $header, $validationData, $companySystemId);
            }

            if (!empty($headerErrors) || !empty($detailsErrors)) {
                $failCount++;
                $result = [
                    'index' => $index,
                    'status' => 'failed',
                    'message' => 'Validation failed',
                ];
                if (!empty($headerErrors)) {
                    $result['errors'] = $headerErrors;
                }
                if (!empty($detailsErrors)) {
                    $result['detailsErrors'] = $detailsErrors;
                }
                $results[] = $result;
                continue;
            }

            // Simulate creation of erp_matchdocumentmaster and details
            try {
                $erpRef = $this->createReceiptMatching($header, $details, $externalReference, $companyId, $companySystemId, $validationData);
                $successCount++;
                $results[] = [
                    'index' => $index,
                    'status' => 'success',
                    'message' => 'Receipt Matching posted successfully',
                    'reference' => $erpRef,
                ];
            } catch (\Exception $e) {
                $failCount++;
                $results[] = [
                    'index' => $index,
                    'status' => 'failed',
                    'message' => 'Exception: ' . $e->getMessage(),
                ];
            }
        }

        $total = count($matchings);
        $status = ($successCount === $total) ? 'completed' : (($successCount > 0) ? 'partial_success' : 'failed');

        $response = [
            'externalReference ' => $externalReference,
            'status' => $status,
            'summary' => [
                'total' => $total,
                'success' => $successCount,
                'failed' => $failCount
            ],
            'results' => $results
        ];

        // Log the response
        \Log::error($response);

        // Dispatch webhook job
        $webhookPayload = $response;
        InitiateWebhook::dispatch(
            $this->db,
            $this->apiExternalKey,
            $this->apiExternalUrl,
            $this->input['webhook_url'],
            $webhookPayload,
            $this->externalReference,
            $this->tenantUuid,
            $this->input['company_id'],
            $this->input['log_id'],
            $this->input['thirdPartyIntegrationKeyId']
        );
    }

    private function validateHeader($header, $companyId, $companySystemId)
    {
        $errors = [];
        $data = ['availableBalance' => 0, 'matchDocument' => null];

        $customerCode = $header['customer'] ?? null;
        $matchingType = $header['matchingType'] ?? null;
        $brvOrCreditNoteCode = $header['brvOrCreditNoteCode'] ?? null;
        
        $customer = null;

        // General validations
        if (empty($customerCode)) $errors[] = 'Customer is required.';
        if (!in_array($matchingType, [1, 2])) $errors[] = 'Invalid Type selected. Please choose the correct type.';
        if (empty($brvOrCreditNoteCode)) $errors[] = 'brvOrCreditNoteCode is required.';

        if (!empty($errors)) return ['errors' => $errors, 'data' => $data];

        if (isset($customerCode)) {
            $approvedCustomer = CustomerMaster::where(function ($query) use ($customerCode) {
                                                    $query->where('CutomerCode', $customerCode)
                                                        ->orWhere('customer_registration_no', $customerCode);
                                                })
                                                ->first();

            if(!$approvedCustomer){
                $errors[] = [
                    'field' => "customer",
                    'message' => ["Selected Customer is not available in the system"]
                ];
            }

            if ($approvedCustomer) {
                if($approvedCustomer->approvedYN == 0) {
                    $errors[] = [
                        'field' => "customer",
                        'message' => ["Selected Customer is not approved"]
                    ];
                } else {

                    if($approvedCustomer->isCustomerActive == 0){
                        $errors[] = [
                            'field' => "customer",
                            'message' => ["Selected Customer is not active"]
                        ];
                    } else {
                        $customer = CustomerAssigned::Where('CutomerCode',$approvedCustomer->CutomerCode)
                        ->where('companySystemID', $companySystemId)
                        ->where('isAssigned', -1)
                        ->first();
        
                        if(!$customer){
                            $errors[] = [
                                'field' => "customer",
                                'message' => ["Selected Customer is not assigned to the company"]
                            ];
                        } else {
                            if($customer->isActive == 0) {
                                $errors[] = [
                                    'field' => "customer",
                                    'message' => ["Company assigned Customer is not active"]
                                ];
                            }
                        }
                    }
                }
            }
        }

        if (!empty($errors)) return ['errors' => $errors, 'data' => $data];

        // Type-specific validations
        if ($matchingType == 1) { // Advance Receipt
            $brv = CustomerReceivePayment::where('custPaymentReceiveCode', $brvOrCreditNoteCode)
                ->where('companySystemID', $companySystemId)
                ->first();

            if (!$brv) {
                $errors[] = 'Advance receipt voucher document code not matching with system';
            } else {

                $isMultipleSegmentDetails = DirectReceiptDetail::where('directReceiptAutoID', $brv->custReceivePaymentAutoID)->get();

                if (!empty($isMultipleSegmentDetails) && $isMultipleSegmentDetails->count() > 1) {
                    $uniqueSegments = $isMultipleSegmentDetails->pluck('serviceLineSystemID')->unique();
                    if (!empty($uniqueSegments) && $uniqueSegments->count() > 1) {
                        $errors[] = 'The advance receipt voucher contains multiple lines with different segments.';
                        return ['errors' => $errors, 'data' => $data];
                    }
                }

                if($brv->customerID != $customer->customerCodeSystem){
                    $errors[] = "The selected document {$brvOrCreditNoteCode} does not belong to the selected customer.";
                } else {
                    if ($brv->approved != -1) {
                        $errors[] = 'Advance receipt voucher not approved';
                    } else {
                        $existingMatch = MatchDocumentMaster::where('PayMasterAutoId', $brv->custReceivePaymentAutoID)
                            ->where('documentSystemID', 21)
                            ->where('companySystemID', $companySystemId)
                            ->where('matchingConfirmedYN', 0)
                            ->first();
                        if ($existingMatch) {
                            $errors[] = 'A matching document for the selected advance receipt voucher is created and not confirmed. Please confirm the previously created document and try again.';
                        } else {
                             $doc = $this->getDocumentForMatching($matchingType, $brvOrCreditNoteCode, $companySystemId, $customer->customerCodeSystem);
                             if ($doc) {
                                $data['matchDocument'] = $doc;
                                $data['availableBalance'] = $doc->BalanceAmt;

                             } else {
                                $existingMatch = MatchDocumentMaster::where('PayMasterAutoId', $brv->custReceivePaymentAutoID)
                                ->where('documentSystemID', 21)
                                ->where('companySystemID', $companySystemId)
                                ->where('matchingConfirmedYN', 1)
                                ->first();
                                if($existingMatch){
                                    $errors[] = 'Selected Advance receipt voucher already fully matched';
                                }
                             }
                        }
                    }
                }

            }
        } elseif ($matchingType == 2) {
            $creditNoteValidation = $this->validateCreditNote($brvOrCreditNoteCode, $companySystemId, $customer->customerCodeSystem);
            if (!empty($creditNoteValidation['errors'])) {
                $errors = array_merge($errors, $creditNoteValidation['errors']);
            } else {
                 $data['matchDocument'] = $creditNoteValidation['data']['matchDocument'];
                 $data['availableBalance'] = $creditNoteValidation['data']['availableBalance'];
            }
        }

        // Final common validations
        $matchingDate = $header['matchingDate'] ?? null;
        $narration = $header['narration'] ?? null;
        if (!$matchingDate) {
            $errors[] = 'Matching date is required.';
        } else {
            $date = \DateTime::createFromFormat('d-m-Y', $matchingDate);
            if (!$date) $errors[] = 'Matching date format should be DD-MM-YYYY.';
            else if ($date > new \DateTime()) $errors[] = 'Matching date should be equal and less than current date.';
        }
        if (empty($narration)) $errors[] = 'The narration field is mandatory.';

        return ['errors' => $errors, 'data' => $data];
    }

    private function validateCreditNote($documentCode, $companySystemID, $customerSystemID)
    {
        $errors = [];
        $data = ['availableBalance' => 0, 'matchDocument' => null];

        $creditNote = CreditNote::where('creditNoteCode', $documentCode)
            ->where('companySystemID', $companySystemID)->first();
        if (!$creditNote) {
            $errors[] = 'Credit note document code not matching with system';
            return ['errors' => $errors, 'data' => $data];
        } else {

            $isMultipleSegmentDetails = CreditNoteDetails::where('creditNoteAutoID', $creditNote->creditNoteAutoID)->get();


            if (!empty($isMultipleSegmentDetails) && $isMultipleSegmentDetails->count() > 1) {
                $uniqueSegments = $isMultipleSegmentDetails->pluck('serviceLineSystemID')->unique();
                if (!empty($uniqueSegments) && $uniqueSegments->count() > 1) {
                    $errors[] = 'The credit note contains multiple lines with different segments.';
                    return ['errors' => $errors, 'data' => $data];
                }
            }

            if($creditNote->customerID != $customerSystemID) {
                $errors[] = "The selected document {$documentCode} does not belong to the selected customer.";
                return ['errors' => $errors, 'data' => $data];
            } else {
                if ($creditNote->approved != -1) {
                    $errors[] = 'A selected Credit note not approved';
                    return ['errors' => $errors, 'data' => $data];
                } else {
                    
                    $existingMatch = MatchDocumentMaster::where('PayMasterAutoId', $creditNote->creditNoteAutoID)
                    ->where('documentSystemID', 19)
                    ->where('companySystemID', $companySystemID)
                    ->where('matchingConfirmedYN', 0)
                    ->first();
                    if ($existingMatch) {
                        $errors[] = 'A matching document for the selected credit note is created and not confirmed. Please confirm the previously created document and try again.';
                        return ['errors' => $errors, 'data' => $data];
                    } else {
                        $unconfirmedReceipt = CustomerReceivePaymentDetail::where('bookingInvCodeSystem', $creditNote->creditNoteAutoID)
                        ->where('addedDocumentSystemID', 19) // Credit Note Doc ID
                        ->whereHas('master', function ($q) {
                            $q->where('approved', '!=', -1);
                        })->exists();
                        
                        if ($unconfirmedReceipt) {
                            $errors[] = 'A receipt voucher document for the selected credit note is created and not confirmed. Please confirm and try again.';
                            return ['errors' => $errors, 'data' => $data];
                        } else {
                            $doc = $this->getDocumentForMatching(2, $documentCode, $companySystemID, $customerSystemID);
                            if ($doc) {
                                $data['matchDocument'] = $doc;
                                $data['availableBalance'] = $doc->BalanceAmt;
                            } else {
                                $existingFullyMatched = MatchDocumentMaster::where('PayMasterAutoId', $creditNote->creditNoteAutoID)
                                    ->where('documentSystemID', 19)
                                    ->where('companySystemID', $companySystemID)
                                    ->where('matchingConfirmedYN', 1)
                                    ->first();
                                if($existingFullyMatched){
                                    $errors[] = 'Selected Credit note already fully matched';
                                }
                            }
                        }
                    }
                }
            }
        }
        return ['errors' => $errors, 'data' => $data];
    }

    private function getDocumentForMatching($matchType, $documentCode, $companySystemID, $customerSystemID)
    {
        $internalMatchType = $matchType;
        $escapedDocumentCode = DB::connection()->getPdo()->quote($documentCode);

        if ($matchType == 1) { // API sends 1, but query needs 3
            $internalMatchType = 3;
        } elseif ($matchType == 2) {
            $internalMatchType = 2;
        }

        $queryResult = [];
        if ($internalMatchType == 1) {
            $queryResult = DB::select("SELECT
                                           erp_customerreceivepayment.custReceivePaymentAutoID as masterAutoID,
                                           erp_customerreceivepayment.documentSystemID,
                                           erp_customerreceivepayment.companySystemID,
                                           erp_customerreceivepayment.companyID,
                                           erp_customerreceivepayment.custPaymentReceiveCode as docMatchedCode,
                                           erp_customerreceivepayment.custPaymentReceiveDate as docMatchedDate,
                                           erp_customerreceivepayment.customerID,
                                           Sum(
                                               erp_custreceivepaymentdet.receiveAmountTrans
                                           ) AS SumOfreceiveAmountTrans,
                                           Sum(
                                               erp_custreceivepaymentdet.receiveAmountLocal
                                           ) AS SumOfreceiveAmountLocal,
                                           Sum(
                                               erp_custreceivepaymentdet.receiveAmountRpt
                                           ) AS SumOfreceiveAmountRpt,
                                           IFNULL(advd.SumOfmatchingAmount, 0) AS SumOfmatchingAmount,
                                           ROUND((COALESCE (SUM(erp_custreceivepaymentdet.receiveAmountTrans),0) - IFNULL(advd.SumOfmatchingAmount, 0)
                                           ),currency.DecimalPlaces) AS BalanceAmt,
                                               currency.CurrencyCode,
                                               currency.currencyID,
                                           currency.DecimalPlaces
                                        FROM
                                           erp_customerreceivepayment
                                        INNER JOIN erp_custreceivepaymentdet ON erp_custreceivepaymentdet.custReceivePaymentAutoID = erp_customerreceivepayment.custReceivePaymentAutoID
                                        INNER JOIN currencymaster AS currency ON currency.currencyID = erp_customerreceivepayment.custTransactionCurrencyID
                                        LEFT JOIN (
                                           SELECT
                                               erp_matchdocumentmaster.PayMasterAutoId,
                                               erp_matchdocumentmaster.documentSystemID,
                                               erp_matchdocumentmaster.companySystemID,
                                               erp_matchdocumentmaster.BPVcode,
                                               COALESCE (
                                                   SUM(
                                                       erp_matchdocumentmaster.matchingAmount
                                                   ),
                                                   0
                                               ) AS SumOfmatchingAmount
                                           FROM
                                               erp_matchdocumentmaster
                                           GROUP BY
                                               erp_matchdocumentmaster.PayMasterAutoId,
                                               erp_matchdocumentmaster.documentSystemID,
                                               erp_matchdocumentmaster.companySystemID
                                        ) AS advd ON (
                                           erp_customerreceivepayment.custReceivePaymentAutoID = advd.PayMasterAutoId
                                           AND erp_customerreceivepayment.documentSystemID = advd.documentSystemID
                                           AND erp_customerreceivepayment.companySystemID = advd.companySystemID
                                        )
                                        WHERE
                                           erp_custreceivepaymentdet.companySystemID = " . $companySystemID . "
                                        AND erp_custreceivepaymentdet.bookingInvCode = '0'
                                        AND erp_customerreceivepayment.approved = -1
                                        AND erp_customerreceivepayment.custPaymentReceiveCode = " . $escapedDocumentCode . "
                                        AND customerID = " . $customerSystemID . "
                                        AND erp_customerreceivepayment.matchInvoice < 2
                                        GROUP BY
                                           erp_custreceivepaymentdet.custReceivePaymentAutoID,
                                           erp_customerreceivepayment.documentSystemID,
                                           erp_custreceivepaymentdet.companySystemID,
                                           erp_customerreceivepayment.customerID
                                        HAVING
                                           (
                                               ROUND(
                                                   BalanceAmt,
                                                   1
                                               ) > 0
                                           )");
        } elseif ($internalMatchType == 2) {
            $queryResult = DB::select("SELECT
                                            erp_creditnotedetails.creditNoteDetailsID AS masterDetailAutoID,
                                            erp_creditnote.creditNoteAutoID AS masterAutoID,
                                            erp_creditnote.documentSystemiD AS documentSystemID,
                                            erp_creditnote.companySystemID,
                                            erp_creditnote.companyID,
                                            erp_creditnote.creditNoteCode AS docMatchedCode,
                                            erp_creditnote.creditNoteDate AS docMatchedDate,
                                            erp_creditnote.customerID,
                                            currency.CurrencyCode,
                                            currency.currencyID,
                                            currency.DecimalPlaces,
                                            SUM(erp_creditnotedetails.creditAmount) AS SumOfreceiveAmountTrans,
                                            erp_creditnotedetails.serviceLineSystemID AS serviceLineSystemID,
                                            (
                                                SUM(erp_creditnotedetails.creditAmount) - (
                                                    (IFNULL(
                                                        receipt.SumOfreceiptAmount,
                                                        0
                                                    )* -1) + IFNULL(advd.SumOfmatchingAmount, 0)
                                                )
                                            ) AS BalanceAmt
                                        FROM
                                            erp_creditnotedetails
                                        INNER JOIN erp_creditnote AS erp_creditnote ON erp_creditnote.creditNoteAutoID = erp_creditnotedetails.creditNoteAutoID
                                        LEFT JOIN currencymaster AS currency ON currency.currencyID = erp_creditnote.customerCurrencyID
                                        LEFT JOIN (
                                            SELECT
                                                custReceivePaymentAutoID,
                                                addedDocumentSystemID,
                                                bookingInvCodeSystem,
                                                bookingInvCode,
                                                erp_custreceivepaymentdet.companySystemID,
                                                erp_accountsreceivableledger.serviceLineSystemID,
                                                COALESCE (SUM(receiveAmountTrans), 0) AS SumOfreceiptAmount
                                            FROM
                                                erp_custreceivepaymentdet
                                            LEFT JOIN erp_accountsreceivableledger ON erp_accountsreceivableledger.arAutoID = erp_custreceivepaymentdet.arAutoID
                                            WHERE
                                                bookingInvCode <> '0'
                                            GROUP BY
                                                addedDocumentSystemID,
                                                bookingInvCodeSystem,
                                                companySystemID,
                                                erp_accountsreceivableledger.serviceLineSystemID
                                        ) AS receipt ON (
                                            receipt.bookingInvCodeSystem = erp_creditnote.creditNoteAutoID
                                            AND receipt.addedDocumentSystemID = erp_creditnote.documentSystemiD
                                            AND receipt.companySystemID = erp_creditnote.companySystemID
                                            AND receipt.serviceLineSystemID = erp_creditnotedetails.serviceLineSystemID
                                        )
                                        LEFT JOIN (
                                            SELECT
                                                erp_matchdocumentmaster.PayMasterAutoId,
                                                erp_matchdocumentmaster.documentSystemID,
                                                erp_matchdocumentmaster.companySystemID,
                                                erp_matchdocumentmaster.BPVcode,
                                                erp_matchdocumentmaster.serviceLineSystemID,
                                                COALESCE (
                                                    SUM(
                                                        erp_matchdocumentmaster.matchingAmount
                                                    ),
                                                    0
                                                ) AS SumOfmatchingAmount
                                            FROM
                                                erp_matchdocumentmaster
                                            GROUP BY
                                                erp_matchdocumentmaster.PayMasterAutoId,
                                                erp_matchdocumentmaster.documentSystemID,
                                                erp_matchdocumentmaster.companySystemID,
                                                erp_matchdocumentmaster.serviceLineSystemID
                                        ) AS advd ON (
                                            erp_creditnote.creditNoteAutoID = advd.PayMasterAutoId
                                            AND erp_creditnote.documentSystemiD = advd.documentSystemID
                                            AND erp_creditnote.companySystemID = advd.companySystemID
                                            AND erp_creditnotedetails.serviceLineSystemID = advd.serviceLineSystemID
                                        )
                                        WHERE
                                            erp_creditnote.companySystemID = " . $companySystemID . "
                                        AND erp_creditnote.approved = -1
                                        AND erp_creditnote.creditNoteCode = " . $escapedDocumentCode . "
                                        AND erp_creditnote.customerID = " . $customerSystemID . "
                                        GROUP BY
                                            erp_creditnotedetails.serviceLineSystemID,
                                            erp_creditnote.creditNoteAutoID,
                                            erp_creditnote.documentSystemiD,
                                            erp_creditnote.companySystemID,
                                            erp_creditnote.customerID
                                        HAVING
                                            (
                                                ROUND(BalanceAmt, DecimalPlaces) > 0
                                            ) ORDER BY erp_creditnote.creditNoteDate");
        } else if ($internalMatchType == 3) {
            $queryResult = DB::select("SELECT *  FROM
                                        (SELECT
                                            erp_directreceiptdetails.directReceiptAutoID AS masterAutoID,
                                            erp_customerreceivepayment.documentSystemID,
                                            erp_customerreceivepayment.companySystemID,
                                            erp_customerreceivepayment.companyID,
                                            erp_customerreceivepayment.custPaymentReceiveCode AS docMatchedCode,
                                            erp_customerreceivepayment.custPaymentReceiveDate AS docMatchedDate,
                                            erp_customerreceivepayment.customerID,
                                             erp_directreceiptdetails.serviceLineSystemID AS serviceLineSystemID,
                                            Sum( erp_directreceiptdetails.DRAmount ) AS SumOfreceiveAmountTrans,
                                            Sum( erp_directreceiptdetails.localAmount ) AS SumOfreceiveAmountLocal,
                                            Sum( erp_directreceiptdetails.comRptAmount ) AS SumOfreceiveAmountRpt,
                                            IFNULL( advd.SumOfmatchingAmount, 0 ) AS SumOfmatchingAmount,
                                            ROUND(
                                            ( COALESCE ( SUM( erp_directreceiptdetails.DRAmount ), 0 ) - IFNULL( advd.SumOfmatchingAmount, 0 ) ),
                                            currency.DecimalPlaces 
                                            ) AS BalanceAmt,
                                            currency.CurrencyCode,
                                            currency.currencyID,
                                            currency.DecimalPlaces,
                                            1 AS tableType
                                        FROM
                                            erp_customerreceivepayment
                                            INNER JOIN erp_directreceiptdetails ON erp_directreceiptdetails.directReceiptAutoID = erp_customerreceivepayment.custReceivePaymentAutoID
                                            INNER JOIN currencymaster AS currency ON currency.currencyID = erp_customerreceivepayment.custTransactionCurrencyID
                                            LEFT JOIN (
                                        SELECT
                                            erp_matchdocumentmaster.PayMasterAutoId,
                                            erp_matchdocumentmaster.documentSystemID,
                                            erp_matchdocumentmaster.companySystemID,
                                            erp_matchdocumentmaster.BPVcode,
                                            erp_matchdocumentmaster.tableType,
                                            erp_matchdocumentmaster.serviceLineSystemID,
                                            COALESCE ( SUM( erp_matchdocumentmaster.matchingAmount ), 0 ) AS SumOfmatchingAmount 
                                        FROM
                                            erp_matchdocumentmaster 
                                            where companySystemID = " . $companySystemID . " 
                                        GROUP BY
                                            erp_matchdocumentmaster.PayMasterAutoId,
                                            erp_matchdocumentmaster.documentSystemID,
                                            erp_matchdocumentmaster.companySystemID, 
                                            erp_matchdocumentmaster.serviceLineSystemID
                                            ) AS advd ON ( erp_directreceiptdetails.directReceiptAutoID = advd.PayMasterAutoId AND erp_customerreceivepayment.documentSystemID = advd.documentSystemID AND erp_customerreceivepayment.companySystemID = advd.companySystemID AND advd.tableType = 1 AND erp_directreceiptdetails.serviceLineSystemID = advd.serviceLineSystemID) 
                                        WHERE
                                            erp_directreceiptdetails.companySystemID = " . $companySystemID . " 
                                            AND erp_customerreceivepayment.documentType = 15 
                                            AND erp_customerreceivepayment.approved = - 1 
                                            AND erp_customerreceivepayment.custPaymentReceiveCode = " . $escapedDocumentCode . "
                                            AND customerID = " . $customerSystemID . "
                                            AND erp_customerreceivepayment.matchInvoice < 2 
                                        GROUP BY
                                            erp_directreceiptdetails.serviceLineSystemID,
                                            erp_directreceiptdetails.directReceiptAutoID,
                                            erp_customerreceivepayment.documentSystemID,
                                            erp_directreceiptdetails.companySystemID,
                                            erp_customerreceivepayment.customerID 
                                        HAVING
                                            ( ROUND( BalanceAmt, 1 ) > 0 )
                                            
                                            Union 
                                            
                                            SELECT
                                            erp_advancereceiptdetails.advanceReceiptDetailAutoID AS masterAutoID,
                                            erp_customerreceivepayment.documentSystemID,
                                            erp_customerreceivepayment.companySystemID,
                                            erp_customerreceivepayment.companyID,
                                            erp_customerreceivepayment.custPaymentReceiveCode AS docMatchedCode,
                                            erp_customerreceivepayment.custPaymentReceiveDate AS docMatchedDate,
                                            erp_customerreceivepayment.customerID,
                                            erp_advancereceiptdetails.serviceLineSystemID AS serviceLineSystemID,
                                            Sum( erp_advancereceiptdetails.supplierTransAmount ) AS SumOfreceiveAmountTrans,
                                            Sum( erp_advancereceiptdetails.localAmount ) AS SumOfreceiveAmountLocal,
                                            Sum( erp_advancereceiptdetails.comRptAmount ) AS SumOfreceiveAmountRpt,
                                            IFNULL( advd.SumOfmatchingAmount, 0 ) AS SumOfmatchingAmount,
                                            ROUND(
                                            ( COALESCE ( SUM( erp_advancereceiptdetails.supplierTransAmount ), 0 ) - IFNULL( advd.SumOfmatchingAmount, 0 ) ),
                                            currency.DecimalPlaces 
                                            ) AS BalanceAmt,
                                            currency.CurrencyCode,
                                            currency.currencyID,
                                            currency.DecimalPlaces ,
                                            2 AS tableType
                                        FROM
                                            erp_customerreceivepayment
                                            INNER JOIN erp_advancereceiptdetails ON erp_advancereceiptdetails.custReceivePaymentAutoID = erp_customerreceivepayment.custReceivePaymentAutoID
                                            INNER JOIN currencymaster AS currency ON currency.currencyID = erp_customerreceivepayment.custTransactionCurrencyID
                                            LEFT JOIN (
                                        SELECT
                                            erp_matchdocumentmaster.PayMasterAutoId,
                                            erp_matchdocumentmaster.documentSystemID,
                                            erp_matchdocumentmaster.companySystemID,
                                            erp_matchdocumentmaster.BPVcode,
                                            erp_matchdocumentmaster.tableType,
                                            erp_matchdocumentmaster.serviceLineSystemID,
                                            COALESCE ( SUM( erp_matchdocumentmaster.matchingAmount ), 0 ) AS SumOfmatchingAmount 
                                        FROM
                                            erp_matchdocumentmaster 
                                            where companySystemID = " . $companySystemID . "
                                        GROUP BY
                                            erp_matchdocumentmaster.PayMasterAutoId,
                                            erp_matchdocumentmaster.documentSystemID,
                                            erp_matchdocumentmaster.companySystemID,
                                            erp_matchdocumentmaster.serviceLineSystemID 
                                            ) AS advd ON ( erp_advancereceiptdetails.custReceivePaymentAutoID = advd.PayMasterAutoId AND erp_customerreceivepayment.documentSystemID = advd.documentSystemID AND erp_customerreceivepayment.companySystemID = advd.companySystemID AND advd.tableType = 2 AND erp_advancereceiptdetails.serviceLineSystemID = advd.serviceLineSystemID) 
                                        WHERE
                                            erp_advancereceiptdetails.companySystemID = " . $companySystemID . "
                                            AND erp_customerreceivepayment.documentType = 15 
                                            AND erp_customerreceivepayment.approved = - 1 
                                            AND erp_customerreceivepayment.custPaymentReceiveCode = " . $escapedDocumentCode . "
                                            AND customerID = " . $customerSystemID . "
                                            AND erp_customerreceivepayment.matchInvoice < 2 
                                        GROUP BY
                                            erp_advancereceiptdetails.custReceivePaymentAutoID,
                                            erp_advancereceiptdetails.serviceLineSystemID,
                                            erp_customerreceivepayment.documentSystemID,
                                            erp_advancereceiptdetails.companySystemID,
                                            erp_customerreceivepayment.customerID 
                                        HAVING
                                            ( ROUND( BalanceAmt, 1 ) > 0 )) as final");
        }
        return $queryResult[0] ?? null;
    }

    // Stub for details validation
    private function validateDetails($details, $header, $validationData, $companySystemId)
    {
        $detailsErrors = [];
        if($validationData['matchDocument']==null){
            $detailsErrors[] = ['detailsIndex' => 'summary', 'errors' => ['Correct matching document not found.']];
            return $detailsErrors;
        }
        $matchingDate = $header['matchingDate'] ?? null;
        $matchingType = $header['matchingType'] ?? null;
        $availableBalance = $validationData['availableBalance'] ?? 0;
        $companySystemID = $companySystemId ?? null;
        $customerCodeSystem = $validationData['matchDocument']->customerID ?? null;
        $currencyID = $validationData['matchDocument']->currencyID ?? null;
        $segmentID = $validationData['matchDocument']->serviceLineSystemID ?? null;
        $totalMatchingAmount = 0;
        foreach ($details as $index => $detail) {
            $err = [];
            $bookingInvCode = $detail['bookingInvCode'] ?? null;
            $matchingAmount = $detail['matchingAmount'] ?? 0;

            // Validate required fields
            if (empty($bookingInvCode)) {
                $detailsErrors[] = ['detailsIndex' => $index, 'errors' => ['Booking Invoice Code is required.']];
            }
            if(isset($detail['matchingAmount'])){
                if($matchingAmount <= 0){
                    $detailsErrors[] = ['detailsIndex' => $index, 'errors' => ['The matching amount should be positive value.']];
                }
            } else {
                $detailsErrors[] = ['detailsIndex' => $index, 'errors' => ['Matching Amount is required.']];
            }

            if ($bookingInvCode) {
                $invoice = CustomerInvoice::where('bookingInvCode', $bookingInvCode)
                                            ->where('companySystemID', $companySystemID)
                                            ->first();
                $invoiceDetails = CustomerInvoiceDirectDetail::where('custInvoiceDirectID', $invoice->custInvoiceDirectAutoID)->first();
                if ($invoice) {
                    // Check approval 
                    if ($invoice->approved != -1){
                        $err[] = 'Customer invoice not approved.';
                    } else {
                        if($invoice->customerID != $customerCodeSystem){
                            $err[] = "The selected customer invoice document code {$bookingInvCode} does not belong to the selected customer.";
                        } else {
                            
                            $notApprovedReceiptVoucher = CustomerReceivePaymentDetail::where('bookingInvCodeSystem', $invoice->custInvoiceDirectAutoID)
                            ->whereHas('master', function($query) {
                                $query->where('approved', 0);
                            })
                            ->exists();

                            if ($notApprovedReceiptVoucher) {
                                $err[] = 'A receipt voucher document for the selected customer invoice is created and not approved. Please approve the previously created document and try again.';
                            }

                            $unconfirmedMatch = CustomerReceivePaymentDetail::where('bookingInvCodeSystem', $invoice->custInvoiceDirectAutoID)
                            ->whereHas('matching_master', function($query) {
                                $query->where('matchingConfirmedYN', 0);
                            })
                            ->exists();

                            if ($unconfirmedMatch) {
                                $err[] = 'A matching document for the selected customer invoice is created and not confirmed. Please confirm the previously created document and try again.';
                            }

                            $notApprovedSalesReturn = SalesReturnDetail::where('custInvoiceDirectAutoID', $invoice->custInvoiceDirectAutoID)
                            ->whereHas('master', function($query) {
                                $query->where('approvedYN', 0);
                            })
                            ->exists();

                            if ($notApprovedSalesReturn) {
                                $err[] = 'A sales return document for the selected customer invoice is created and not approved. Please approve the previously created document and try again.';
                            }

                            if ($matchingDate) {
                                $invoiceBookingDate = \Carbon\Carbon::parse($invoice->bookingDate)->startOfDay();
                                $matchingDateObject = \Carbon\Carbon::createFromFormat('d-m-Y', $matchingDate)->startOfDay();
                                
                                if ($invoiceBookingDate->gt($matchingDateObject)) {
                                    $err[] = 'The customer invoice date is greater than matching date.';
                                } else {    
                                    if($invoice->custTransactionCurrencyID != $currencyID){
                                        $err[] = 'Can not match the two different currency documents.';
                                    } else {
                                        
                                        $isMultipleSegmentnvoiceDetails = CustomerInvoiceDirectDetail::where('custInvoiceDirectID', $invoice->custInvoiceDirectAutoID)->get();
                                        if (!empty($isMultipleSegmentnvoiceDetails) && $isMultipleSegmentnvoiceDetails->count() > 1) {
                                            $uniqueSegments = $isMultipleSegmentnvoiceDetails->pluck('serviceLineSystemID')->unique();
                                            if (!empty($uniqueSegments) && $uniqueSegments->count() > 1) {
                                                $err[] = 'The customer invoice contains multiple lines with different segments.';
                                            }
                                        }else{
                                            $isCheckSegmentonRVM = CompanyPolicyMaster::where('companyPolicyCategoryID', 95)
                                            ->where('companySystemID', $companySystemID)
                                            ->first();

                                            if($isCheckSegmentonRVM && $isCheckSegmentonRVM->isYesNO == 0){
                                                if($invoiceDetails && $invoiceDetails->serviceLineSystemID != $segmentID){
                                                    $err[] = 'Selected customer invoice segment not matching with advance or credit note segment.';
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                         }
                    }
                } else {
                        $err[] = "Customer invoice document code not matching with system.";
                }
            }
        }

        if ($matchingAmount !== null) {
            $totalMatchingAmount += $matchingAmount;
        }

        if (!empty($err)) {
            $detailsErrors[] = ['detailsIndex' => $index, 'errors' => $err];
        } else {
            if ($totalMatchingAmount > $availableBalance) {
                $detailsErrors[] = ['detailsIndex' => 'summary', 'errors' => ['Total matching amount exceeds the available balance of the document.']];
            }
        }

        return $detailsErrors;
    }

    // Stub for creating receipt matching
    private function createReceiptMatching($header, $details, $externalReference, $companyId, $companySystemId, $validationData)
    {
        return DB::transaction(function () use ($header, $details, $externalReference, $companyId, $companySystemId, $validationData) {
            $input = [];
            $input['companySystemID'] = $companySystemId;
            $input['custReceivePaymentAutoID'] = $validationData['matchDocument']->masterAutoID;
            $input['customerID'] = $validationData['matchDocument']->customerID;
            $input['isDelegation'] = false;
            $input['matchBalanceAmount'] = $validationData['availableBalance'];
            $input['matchingDocdate'] = $header['matchingDate'];
            $input['narration'] = $header['narration'];
            if ($header['matchingType'] == 1) {
                $input['matchType'] = 3;
                $input['tableType'] = 1;
            } elseif ($header['matchingType'] == 2) {
                $input['matchType'] = 2;
            }
            $input['isAutoCreateDocument'] = true;
            
            $masterInsert = ReceiptMatchingAPIService::createReceiptMatching($input);
            
            // Check if master insert was successful before proceeding with details
                        if (!$masterInsert['status']) {
                throw new \Exception($masterInsert['message'] ?? 'Failed to create receipt matching master record.');
            } else {
                    $customerCodeSystem = $validationData['matchDocument']->customerID ?? null;
                    foreach ($details as $index => $detail) {
            
                        $invoice = AccountsReceivableLedger::where('documentCode', $detail['bookingInvCode'])->first();
            
                        if(!$invoice){
                        // It's good practice to handle cases where the invoice isn't found
                        continue; 
                        }
            
                        $input = [
                        'matchDocumentMasterAutoID' => $masterInsert['data']['matchDocumentMasterAutoID'],
                        'arAutoID' => $invoice->arAutoID,
                        'bookingInvCodeSystem' => $invoice->documentID,
                        'addedDocumentSystemID' => $invoice->documentSystemID,
                        'bookingInvCode' => $detail['bookingInvCode'],
                        'receiveAmountTrans' => $detail['matchingAmount'],
                        'companySystemID' => $companySystemId,
                        'companyID' => $companyId,
                        'customerID' => $customerCodeSystem,
                        'supplierTransCurrency' => $invoice->custTransCurrencyID,
                        ];
            
                        $detailInsert = ReceiptMatchingAPIService::createReceiptMatchingDetails($input);
            
                        if (!$detailInsert['status']) {
                            throw new \Exception($detailInsert['message'] ?? 'Failed to create receipt matching detail.');
                        }
                    }
                    if($masterInsert['status']){
                        $inputData = [
                            'matchDocumentMasterAutoID' => $masterInsert['data']['matchDocumentMasterAutoID'],
                            'documentSystemID' => $masterInsert['data']['documentSystemID'],
                            'companySystemID' => $masterInsert['data']['companySystemID'],	
                            'companyID' => $masterInsert['data']['companyID'],
                            'matchBalanceAmount' => $masterInsert['data']['matchBalanceAmount'],
                            'matchingDocdate' => $masterInsert['data']['matchingDocdate'],
                            'PayMasterAutoId' => $masterInsert['data']['PayMasterAutoId'],
                            'BPVNarration' => $masterInsert['data']['BPVNarration'],
                            'matchingDocCode' => $masterInsert['data']['matchingDocCode'],
                            'isAutoCreateDocument' => true,
                        ];
                        $updateReceiptMatching = ReceiptMatchingAPIService::updateReceiptMatching($inputData,true);
                        if (!$updateReceiptMatching['status']) {
                            if (count($updateReceiptMatching['message']) > 0) {
                                    throw new \Exception(Arr::flatten($updateReceiptMatching['message'])[0] ?? 'Failed to update receipt matching.');
                                } else {
                                    throw new \Exception($updateReceiptMatching['message'] ?? 'Failed to update receipt matching.');
                                }
                        } else {
                            $this->storeToDocumentSystemMapping(70,$masterInsert['data']['matchDocumentMasterAutoID'],$this->authorization);
                        }
 
                    }
                    $masterData = MatchDocumentMaster::where('matchDocumentMasterAutoID',$masterInsert['data']['matchDocumentMasterAutoID'])->first();
            
                    return $masterData->matchingDocCode;
            }
            
            

        });
    }
} 
