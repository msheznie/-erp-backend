<?php

namespace App\Services\ProcurementOrder;

use App\helper\CreateExcel;
use App\helper\Helper;
use App\Models\Company;
use App\Models\ProcumentOrder;
use App\Services\WebPushNotificationService;
use Illuminate\Support\Facades\Log;

class ExportPODetailExcel {
    private $output;
    private $input;
    private $supplierId;
    private $serviceLineSystemId;
    private $companyId;
    private $documentId;
    private $poType_N;
    private $poCancelledYN;
    private $poConfirmedYN;
    private $approved;
    private $grvRecieved;
    private $invoicedBooked;
    private $month;
    private $year;
    private $sentToSupplier;
    private $logisticsAvailable;
    private $poTypeId;
    private $financeCategory;
    private $search;
    private $userId;
    private $companyCode;
    private $data = [];

    public function __construct($request) {
        $this->input = $request;
        $this->setData($this->input);
    }

    private function setData($request) {
        $this->supplierId = $request['supplierID'] ?? null;
        $this->serviceLineSystemId = $request['serviceLineSystemID'] ?? null;
        $this->companyId = $request['companyId'] ?? null;
        $this->documentId = $request['documentId'] ?? null;
        $this->poType_N = $request['poType_N'] ?? null;
        $this->poCancelledYN = $request['poCancelledYN'] ?? null;
        $this->poConfirmedYN = $request['poConfirmedYN'] ?? null;
        $this->approved = $request['approved'] ?? null;
        $this->grvRecieved = $request['grvRecieved'] ?? null;
        $this->invoicedBooked = $request['invoicedBooked'] ?? null;
        $this->month = $request['month'] ?? null;
        $this->year = $request['year'] ?? null;
        $this->sentToSupplier = $request['sentToSupplier'] ?? null;
        $this->logisticsAvailable = $request['logisticsAvailable'] ?? null;
        $this->poTypeId = $request['poTypeID'] ?? null;
        $this->financeCategory= $request['financeCategory'] ?? null;
        $this->search = $request['search']['value'] ?? null;
        $this->userId = $request['userId'] ?? null;

        $this->supplierId = collect((array) $this->supplierId)->pluck('id');
        $this->serviceLineSystemId = collect((array) $this->serviceLineSystemId)->pluck('id');

        $companyMaster = Company::find($this->companyId);
        $this->companyCode = $companyMaster->CompanyID ?? 'common';
    }

    public function export() {
        $this->output = $this->getMasterData();
        $this->processExportData();
        $basePath = CreateExcel::processDetailExport($this->data, $this->companyCode);
        Log::info('Export completed', ['result' => $basePath]);
        $this->sendNotification($basePath);

        if($basePath == '') {
            return ['success' => false , 'message' => 'Unable to export excel'];
        }

        return ['success' => true , 'message' =>  trans('custom.success_export')];
    }

    private function getMasterData() {
        $this->output = ProcumentOrder::query()
            ->where('companySystemID', $this->companyId)
            ->where('documentSystemID', $this->documentId);
        $this->search();
        $this->applyFilters();
        return $this->processData();
    }

    private function search() {
        if($this->search) {
            $search = str_replace("\\", "\\\\", $this->search);
            $this->output = $this->output->where(function ($query) use ($search) {
                $query->where('purchaseOrderCode', 'LIKE', "%{$search}%")
                    ->orWhere('narration', 'LIKE', "%{$search}%")
                    ->orWhere('referenceNumber', 'LIKE', "%{$search}%")
                    ->orWhere('supplierPrimaryCode', 'LIKE', "%{$search}%")
                    ->orWhere('supplierName', 'LIKE', "%{$search}%");
            });
        }
    }

    private function applyFilters() {
        if (array_key_exists('serviceLineSystemID', $this->input)) {
            if ($this->serviceLineSystemId && !is_null($this->serviceLineSystemId)) {
                $this->output = $this->output->whereIn('serviceLineSystemID', $this->serviceLineSystemId);
            }
        }

        if (array_key_exists('poType_N', $this->input)) {
            if (($this->poType_N == 5 || $this->poType_N == 6) && !is_null($this->poType_N)) {
                $this->output = $this->output->where('poType_N', $this->poType_N);
            }
        }

        if (array_key_exists('financeCategory', $this->input) && !empty($this->financeCategory)
            && is_numeric($this->financeCategory)) {
            if ($this->financeCategory && !is_null($this->financeCategory)) {
                $this->output = $this->output->where('financeCategory', $this->financeCategory);
            }
        }

        if (array_key_exists('poTypeID', $this->input) && !empty($this->poTypeId) && is_numeric($this->poTypeId)) {
            if ($this->poTypeId && !is_null($this->poTypeId)) {
                $this->output = $this->output->where('poTypeID', $this->poTypeId);
            }
        }

        if (array_key_exists('poCancelledYN', $this->input)) {
            if (($this->poCancelledYN == 0 || $this->poCancelledYN == -1) && !is_null($this->poCancelledYN)) {
                $this->output = $this->output->where('poCancelledYN', $this->poCancelledYN);
            }
        }

        if (array_key_exists('poConfirmedYN', $this->input)) {
            if (($this->poConfirmedYN == 0 || $this->poConfirmedYN == 1) && !is_null($this->poConfirmedYN)) {
                $this->output = $this->output->where('poConfirmedYN', $this->poConfirmedYN);
            }
        }

        if (array_key_exists('approved', $this->input)) {
            if (($this->approved== 0 || $this->approved== -1) && !is_null($this->approved)) {
                $this->output = $this->output->where('approved', $this->approved);
            }
        }

        if (array_key_exists('grvRecieved', $this->input)) {
            if (($this->grvRecieved == 0 || $this->grvRecieved == 1 || $this->grvRecieved == 2)
                && !is_null($this->grvRecieved)) {
                $this->output = $this->output->where('grvRecieved', $this->grvRecieved);
            }
        }

        if (array_key_exists('invoicedBooked', $this->input)) {
            if (($this->invoicedBooked == 0 || $this->invoicedBooked == 1 || $this->invoicedBooked == 2)
                && !is_null($this->invoicedBooked)) {
                $this->output = $this->output->where('invoicedBooked', $this->invoicedBooked);
            }
        }

        if (array_key_exists('month', $this->input) && !empty($this->month) && is_numeric($this->month)) {
            if ($this->month && !is_null($this->month)) {
                $this->output = $this->output->whereMonth('createdDateTime', '=', $this->month);
            }
        }

        if (array_key_exists('year', $this->input) && !empty($this->year) && is_numeric($this->year)) {
            if ($this->year && !is_null($this->year)) {
                $this->output = $this->output->whereYear('createdDateTime', '=', $this->year);
            }
        }

        if (array_key_exists('supplierID', $this->input)) {
            if ($this->supplierId && !is_null($this->supplierId)) {
                $this->output = $this->output->whereIn('supplierID', $this->supplierId);
            }
        }

        if (array_key_exists('sentToSupplier', $this->input)) {
            if (($this->sentToSupplier == 0 || $this->sentToSupplier == -1) && !is_null($this->sentToSupplier)) {
                $this->output = $this->output->where('sentToSupplier', $this->sentToSupplier);
            }
        }

        if (array_key_exists('logisticsAvailable', $this->input)) {
            if (($this->logisticsAvailable == 0 || $this->logisticsAvailable == -1)
                && !is_null($this->logisticsAvailable)) {
                $this->output = $this->output->where('logisticsAvailable', $this->logisticsAvailable);
            }
        }
    }

    private function processData() {
        $this->output = $this->output->with([
            'created_by',
            'confirmed_by',
            'currency',
            'localcurrency',
            'reportingcurrency',
            'fcategory',
            'segment',
            'supplier',
            'company',
            'detail',
            'detail.unit',
            'detail.requestDetail.purchase_request',
            'po_logistics_details',
            'addon_details',
            'addon_details.category',
            'advance_summary'
        ])
        ->orderBy('purchaseOrderID', 'desc');
        return $this->output->get();
    }

    private function mainHeader($localCurrencyCode = '', $reportingCurrencyCode = '') {
        $this->data[] = [
            '#' => '#',
            'Company ID' => 'Company ID',
            'Company Name' => 'Company Name',
            'Order Code' => 'Order Code',
            'Segment' => 'Segment',
            'Created at' => 'Created at',
            'Created By' => 'Created By',
            'Category' => 'Category',
            'Narration' => 'Narration',
            'Supplier Code' => 'Supplier Code',
            'Supplier Name' => 'Supplier Name',
            'Credit Period' => 'Credit Period',
            'Supplier Country' => 'Supplier Country',
            'Expected Delivery Date' => 'Expected Delivery Date',
            'Delivery Terms' => 'Delivery Terms',
            'Penalty Terms' => 'Penalty Terms',
            'Confirmed Status' => 'Confirmed Status',
            'Confirmed Date' => 'Confirmed Date',
            'Confirmed By' => 'Confirmed By',
            'Approved Status' => 'Approved Status',
            'Approved Date' => 'Approved Date',
            'Transaction Currency' => 'Transaction Currency',
            'Transaction Amount' => 'Transaction Amount',
            'Local Amount' => 'Local Amount' . ($localCurrencyCode ? " ({$localCurrencyCode})" : ''),
            'Reporting Amount' => 'Reporting Amount' . ($reportingCurrencyCode ? " ({$reportingCurrencyCode})" : ''),
            'Advance Payment Available' => 'Advance Payment Available',
            'Total Advance Payment Amount' => 'Total Advance Payment Amount',
        ];
    }

    private function headerDetails($val, $counter) {
        $this->data[] = [
            '#' => $counter,
            'Company ID' => $val->companyID,
            'Company Name' => optional($val->company)->CompanyName,
            'Order Code' => $val->purchaseOrderCode,
            'Segment' => optional($val->segment)->ServiceLineDes,
            'Created at' => \Helper::dateFormat($val->createdDateTime),
            'Created By' => optional($val->created_by)->empName,
            'Category' => optional($val->fcategory)->categoryDescription ?? 'Other',
            'Narration' => $val->narration ?: '-',
            'Supplier Code' => $val->supplierPrimaryCode,
            'Supplier Name' => $val->supplierName,
            'Credit Period' => $val->creditPeriod,
            'Supplier Country' => optional($val->supplier->country)->countryName,
            'Expected Delivery Date' => \Helper::dateFormat($val->expectedDeliveryDate),
            'Delivery Terms' => $val->deliveryTerms,
            'Penalty Terms' => $val->panaltyTerms,
            'Confirmed Status' => $val->poConfirmedYN == 1 ? 'Yes' : 'No',
            'Confirmed Date' => \Helper::dateFormat($val->poConfirmedDate),
            'Confirmed By' => $val->poConfirmedByName,
            'Approved Status' => $val->approved == -1 ? 'Yes' : 'No',
            'Approved Date' => \Helper::dateFormat($val->approvedDate),
            'Transaction Currency' => optional($val->currency)->CurrencyCode,
            'Transaction Amount' => $val->poTotalSupplierTransactionCurrency,
            'Local Amount' => $val->poTotalLocalCurrency,
            'Reporting Amount' => $val->poTotalComRptCurrency,
            'Advance Payment Available' => $val->advance_summary ? 'Yes' : 'No',
            'Total Advance Payment Amount' => $val->advance_summary ? $val->advance_summary->advanceSum : 0
        ];
    }

    private function detailDetails($val) {
        if (!empty($val) && count($val) > 0) {
            $hasPR = $this->checkRequest($val);

            $headerOne [''] = '';
            $headerOne ['Order Details'] = 'Order Details';

            $this->data[] = $headerOne;
            $header = [];
            $header[''] = '';
            if ($hasPR) $header['PR Number'] = 'PR Number';
            $header['item Code'] = 'Item Code';
            $header['item Description'] = 'Item Description';
            $header['comments'] = 'Comments';
            if ($hasPR) $header['PR QTY'] = 'PR QTY';
            $header['UOM'] = 'UOM';
            $header['No qty'] = 'No Qty';
            $header['Unit Cost'] = 'Unit Cost';
            $header['Dis %'] = 'Discount %';
            $header['Discount'] = 'Discount';
            $header['VAT %'] = 'VAT %';
            $header['VAT Amount'] = 'VAT Amount';
            $header['Net Amount'] = 'Net Amount';
            $this->data[] = $header;

            foreach ($val as $detail) {
                $row = [];
                $row [''] = '';
                if ($hasPR) {
                    $row['PR Number'] = $detail->requestDetail->purchase_request->purchaseRequestCode ?? '';
                }
                $row['item Code'] = $detail->itemPrimaryCode ?? '';
                $row['item Description'] = $detail->itemDescription ?? '';
                $row['comments'] = $detail->comment ?? '';
                if ($hasPR) {
                    $row['PR QTY'] = $detail->requestDetail->quantityRequested ?? '';
                }
                $row['UOM'] = $detail->unit->UnitDes ?? '';
                $row['No qty'] = $detail->noQty ?? '';
                $row['Unit Cost'] = $detail->unitCost ?? '';
                $row['Dis %'] = $detail->discountPercentage ?? '';
                $row['Discount'] = $detail->discountAmount ?? '';
                $row['VAT %'] = $detail->VATPercentage ?? '';
                $row['VAT Amount'] = $detail->VATAmount ?? '';
                $row['Net Amount'] = $detail->netAmount ?? '';
                $this->data[] = $row;
            }
            $this->data[] = [];
        }
    }

    private function logisticDetails($val) {
        if (!empty($val) && count($val) > 0) {
            $headerOne [''] = '';
            $headerOne ['Logistics Details'] = 'Logistics Details';

            $this->data[] = $headerOne;
            $header = [];
            $header[''] = '';
            $header['Category'] = 'Category';
            $header['Supplier Code'] = 'Supplier Code';
            $header['Supplier Name'] = 'Supplier Name';
            $header['GRV Code'] = 'GRV Code';
            $header['Currency'] = 'Currency';
            $header['Amount'] = 'Amount';
            $header['Local Amount'] = 'Local Amount';
            $header['Reporting Amount'] = 'Reporting Amount';
            $header['Add VAT On PO'] = 'Add VAT On PO';
            $header['VAT Percentage'] = 'VAT Percentage';
            $header['VAT Amount'] = 'VAT Amount';
            $header['VAT Sub Category'] = 'VAT Sub Category';
            $this->data[] = $header;

            foreach ($val as $detail) {
                $row = [];
                $row [''] = '';
                $row['Category'] = $detail->category_by->costCatDes ?? '';
                $row['Supplier Code'] = $detail->SupplierPrimaryCode ?? '';
                $row['Supplier Name'] = $detail->supplier_by->supplierName ?? '';
                $row['GRV Code'] = $detail->grv_by->grvPrimaryCode ?? '';
                $row['Currency'] = $detail->currency->CurrencyCode ?? '';
                $row['Amount'] = $detail->reqAmount ?? '';
                $row['Local Amount'] = $detail->reqAmountInPOLocalCur ?? '';
                $row['Reporting Amount'] = $detail->reqAmountInPORptCur ?? '';
                $row['Add VAT On PO'] = ($detail->addVatOnPO ?? false) ? 'Yes' : 'No';
                $row['VAT Percentage'] = $detail->VATPercentage ?? '';
                $row['VAT Amount'] = $detail->VATAmount ?? '';
                $row['VAT Sub Category'] = $detail->vat_sub_category->subCategoryDescription ?? '';
                $this->data[] = $row;
            }
            $this->data[] = [];
        }
    }

    private function addonDetails($val) {
        if (!empty($val) && count($val) > 0) {
            $headerOne [''] = '';
            $headerOne ['Addon Details'] = 'Addon Details';

            $this->data[] = $headerOne;
            $header = [];
            $header[''] = '';
            $header['Category'] = 'Category';
            $header['Amount'] = 'Amount';
            $this->data[] = $header;

            foreach ($val as $detail) {
                $row = [];
                $row [''] = '';
                $row['Category'] = $detail->category->costCatDes ?? '';
                $row['Amount'] = $detail->amount ?? '';
                $this->data[] = $row;
            }
            $this->data[] = [];
        }
    }

    private function processExportData() {
        $this->data = [];
        $counter = 1;
        foreach ($this->output as $val) {
            $localCode = optional($val->localcurrency)->CurrencyCode;
            $reportingCode = optional($val->reportingcurrency)->CurrencyCode;

            $this->mainHeader($localCode, $reportingCode);
            $this->headerDetails($val, $counter);
            $this->data[] = [];
            $this->detailDetails($val->detail);
            $this->logisticDetails($val->po_logistics_details);
            $this->addonDetails($val->addon_details);
            $this->data[] = [];
            $this->data[] = [];

            $counter++;
        }
    }


    private function checkRequest($val) {
        $hasPR = false;
        foreach ($val as $detail) {
            if (isset($detail->requestDetail)) {
                $hasPR = true;
                break;
            }
        }
        return $hasPR;
    }

    private function sendNotification($basePath) {
        $webPushData = [
            'title' => "purchase_order_detailed_excel_generated",
            'body' => '',
            'url' => '',
            'path' => $basePath,
        ];
       return WebPushNotificationService::sendNotification($webPushData, 3, [$this->userId]);
    }
}
