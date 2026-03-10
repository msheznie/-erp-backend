<?php

namespace App\Exports\B2B\VendorFile;

use App\Validations\B2B\VendorFile\Detail;
use App\Validations\B2B\VendorFile\Header;
use App\Models\PaymentBankTransfer;
use App\Models\PaySupplierInvoiceMaster;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\helper\Helper;
use App\Models\CurrencyMaster;
class VendorFile
{

    public $headerData;
    public $detailsData;
    public $footerData;

    public $headerErrors;
    public $detailsDataErros;

    /**
     * @param mixed $headerData
     */
    public function setHeaderData($headerData): void
    {
        $this->headerData = $headerData;
        $this->validateHeaderData();
        $this->processHeaderData();
    }

    /**
     * @param mixed $detailsData
     */
    public function setDetailsData($detailsData): void
    {
        $this->detailsData = $detailsData;
        $this->validateDetails();
        $this->processDetailsData();
    }

    /**
     * @param mixed $footerData
     */
    public function setFooterData($bankTransferID): void
    {
        $this->processFooterData($bankTransferID);
    }

    public function header() : array
    {
        return [
           'title' => ['Section Index', 'CompanyCr', 'Debit Account No', 'Transfer Method', 'DebitMode', 'Debit Narrative', 'RequestDate', 'BatchReference'],
            'data' => $this->headerData
        ];
    }


    public function detail() : array
    {
        return [
            'title' => ['Section Index', 'Transfer Method', 'Credit Amount', 'Credit Currency', 'Exchange Rate', 'ValueDate', 'Debit Account No', 'Credit Account No', 'Debit Narrative', 'Debit Narrative 2', 'Credit Narrative', 'Payment Details 1', 'Payment Details 2', 'Payment Details 3', 'Payment Details 4', 'Beneficiary Name', 'Beneficiary Address 1', 'Beneficiary Address 2', 'Institution Name Address 1', 'Institution Name Address 2', 'Institution Name Address 3', 'Institution Name Address 4', 'Swift', 'Intermediary Account', 'Intermediary Swift', 'Intermediary Name', 'Intermediary Address 1', 'Intermediary Address 2', 'Intermediary Address 3', 'Charges Type', 'Sort Code of the beneficiary bank', 'IFSC', 'Fedwire', 'Email', 'Dispatch Mode', 'Transactor Code'],
            'data' => $this->detailsData
        ];
    }

    public function footer() : array
    {
        return [
            'title' => ['Section Index', 'Invoice Number', 'Invoice Date','Invoice Details','Invoice Amount'],
            'data' => $this->footerData
        ];
    }

    private function validateDetails()
    {
        $detailValidaiton = new Detail($this->detailsData);
        $this->detailsDataErros = $detailValidaiton->validaitons;
    }

    private function processDetailsData()
    {
        // Process detailsData to remove special characters from strings
        $processedDetailsData = [];
        
        // Get field indices for email and amount fields
        $detailTitles = ['Section Index', 'Transfer Method', 'Credit Amount', 'Credit Currency', 'Exchange Rate', 'DealReferNo', 'ValueDate', 'Debit Account No', 'Credit Account No', 'TransactionReference', 'Debit Narrative', 'Debit Narrative 2', 'Credit Narrative', 'Payment Details 1', 'Payment Details 2', 'Payment Details 3', 'Payment Details 4', 'Beneficiary Name', 'Beneficiary Address 1', 'Beneficiary Address 2', 'Institution Name Address 1', 'Institution Name Address 2', 'Institution Name Address 3', 'Institution Name Address 4', 'Swift', 'Intermediary Account', 'Intermediary Swift', 'Intermediary Name', 'Intermediary Address 1', 'Intermediary Address 2', 'Intermediary Address 3', 'Charges Type', 'Sort Code of the beneficiary bank', 'IFSC', 'Fedwire', 'Email', 'Dispatch Mode', 'Transactor Code', 'Supporting Document Name'];
        
        // Create array of field names to preserve
        $preserveFields = ['value_date','email','exchange_rate', 'beneficiary_address1', 'beneficiary_address2','institution_name_address_1','institution_name_address_2','institution_name_address_3','institution_name_address_4','intermediary_address1','intermediary_address2','intermediary_address3'];        $preserveFieldIndices = [];
        
        foreach ($this->detailsData as $rowIndex => $row) {
            $processedRow = [];
            foreach ($row as $columnIndex => $value) {
                if (is_string($value)) {
                    // Skip special character removal for email and amount fields
                    if (in_array($columnIndex, $preserveFields)) {
                        // Keep email addresses and amounts as they are
                        $processedRow[$columnIndex] = $value;
                    } else {
                        // Remove special characters, keeping only alphanumeric characters and spaces
                        $processedRow[$columnIndex] = preg_replace('/[^a-zA-Z0-9]/', '', $value);
                    }
                } else {
                    // Keep non-string values as they are
                    $processedRow[$columnIndex] = $value;
                }
            }
            $processedDetailsData[$rowIndex] = $processedRow;
        }
        $this->detailsData = $processedDetailsData;
    }

    /**
     * Get payment vouchers by bank transfer ID via API (datatable format).
     * Params are passed as paymentBankTransferID; the API method is unchanged.
     *
     * @param int $paymentBankTransferID
     * @return array
     */
    private function getPaymentsByBankTransfer(int $paymentBankTransferID): array
    {
        $bankTransfer = PaymentBankTransfer::find($paymentBankTransferID);
        if (!$bankTransfer) {
            return [];
        }

        $request = new Request([
            'companyId' => $bankTransfer->companySystemID,
            'paymentBankTransferID' => $paymentBankTransferID,
            'bankAccountAutoID' => $bankTransfer->bankAccountAutoID,
            'isFromHistory' => 0,
            'order' => [['column' => 0, 'dir' => 'asc']],
        ]);

        $response = app('App\Http\Controllers\API\BankLedgerAPIController')->getPaymentsByBankTransfer($request);
        $content = $response->getData(true);
        $data = $content['data'] ?? [];

        // Only payment vouchers with Bank Transfer YN checked (pulledToBankTransferYN == -1)
        return collect($data)->filter(function ($row) {
            $yn = is_array($row) ? ($row['pulledToBankTransferYN'] ?? null) : ($row->pulledToBankTransferYN ?? null);
            return (int) $yn === -1;
        })->values()->all();
    }

    private function processFooterData($bankTransferID)
    {
        // One S3 row: S3, comma-separated invoice codes, comma-separated dates (dd/mm/yyyy), blank detail, comma-separated bank amounts (invoice order)
        $processedFooterData = [];
        $seenInvoiceIds = [];
        $invoiceCodes = [];
        $invoiceDates = [];
        $invoiceAmounts = [];

        $bankTransfer = PaymentBankTransfer::find($bankTransferID);
        $bankTransferDetails = $bankTransfer ? $this->getPaymentsByBankTransfer((int) $bankTransferID) : [];
        foreach ($bankTransferDetails as $bankTransferDetail) {
            if (($bankTransferDetail['documentSystemID'] ?? null) != 4) {
                continue;
            }
            $documentSystemCode = $bankTransferDetail['documentSystemCode'] ?? null;
            if (empty($documentSystemCode)) {
                continue;
            }
            $paymentVoucher = PaySupplierInvoiceMaster::with([
                'supplierdetail' => function ($q) {
                    $q->where('addedDocumentSystemID', 11)->whereHas('supplier_invoice', function ($q2) {
                        $q2->where('approved', -1);
                    });
                },
                'supplierdetail.supplier_invoice',
            ])->whereIn('invoiceType', [2, 6])->find($documentSystemCode);

            if (!$paymentVoucher) {
                continue;
            }
            if (!isset($paymentVoucher->supplierTransCurrencyID)) {
                continue;
            }

            $supplierDetails = $paymentVoucher->supplierdetail ?? collect();
            if (is_array($supplierDetails)) {
                $supplierDetails = collect($supplierDetails);
            }
            $currency = CurrencyMaster::find($paymentVoucher->supplierTransCurrencyID);
            $decimalPlaces = $currency ? (int) $currency->DecimalPlaces : 2;
            $pvAmount = round(
                ($paymentVoucher->payAmountBank ?? 0) + ($paymentVoucher->retentionVatAmount ?? 0) + ($paymentVoucher->VATAmountBank ?? 0),
                $decimalPlaces
            );

            if($supplierDetails->isEmpty()){
                $invoiceAmounts[] = $pvAmount;
            }else {
                foreach ($supplierDetails as $detail) {
                    $invoice = $detail->supplier_invoice ?? null;
                    if (!$invoice) {
                        continue;
                    }
                    $invoiceId = $invoice->bookingSuppMasInvAutoID ?? $invoice->id ?? null;
                    if ($invoiceId === null) {
                        $invoiceId = ($invoice->bookingInvCode ?? '') . '-' . ($invoice->bookingDate ?? '');
                    }
                    if (isset($seenInvoiceIds[$invoiceId])) {
                        continue;
                    }
                    $seenInvoiceIds[$invoiceId] = true;
    
                    $bookingInvCode = (string) ($invoice->bookingInvCode ?? '');
                    $invoiceCodes[] = preg_replace('/[^a-zA-Z0-9]/', '', $bookingInvCode);
    
                    try {
                        $invoiceDates[] = $invoice->supplierInvoiceDate
                            ? Carbon::parse($invoice->supplierInvoiceDate)->format('d/m/Y')
                            : '';
                    } catch (\Throwable $e) {
                        $invoiceDates[] = '';
                    }
    
                    $invoiceAmounts[] = $pvAmount;
                }
            }

        }

        // if (!empty($invoiceCodes)) {
            $processedFooterData[] = [
                'S3',
                (count($invoiceCodes) > 0) ? implode(',', $invoiceCodes) : '',
                (count($invoiceDates) > 0) ? implode(',', $invoiceDates) : '',
                '',
               (count($invoiceAmounts) > 0) ? implode(',', $invoiceAmounts) : '',
            ];
        // }

        $this->footerData = $processedFooterData;
    }

    private function processHeaderData()
    {
        // Process headerData to remove special characters from strings
        $processedHeaderData = [];
        
        foreach ($this->headerData as $rowIndex => $row) {
            $processedRow = [];
            foreach ($row as $columnIndex => $value) {
                if (is_string($value)) {
                    // Remove special characters, keeping only alphanumeric characters and spaces
                    if($columnIndex != 6) {
                        $processedRow[$columnIndex] = preg_replace('/[^a-zA-Z0-9]/', '', $value);
                    } else {
                        $processedRow[$columnIndex] = $value;
                    }
                } else {
                    // Keep non-string values as they are
                    $processedRow[$columnIndex] = $value;
                }
            }
            $processedHeaderData[$rowIndex] = $processedRow;
        }
        
        $this->headerData = $processedHeaderData;
    }

    private function validateHeaderData()
    {
        $header = new Header($this->headerData);
        $this->headerErrors = $header->validaitons;
    }
}
