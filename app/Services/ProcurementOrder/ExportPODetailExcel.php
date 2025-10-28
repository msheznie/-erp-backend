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
    private $userLang;

    public function __construct($request, $userLang = 'en') {
        $this->input = $request;
        $this->setData($this->input);
        $this->userLang = $userLang;
        app()->setLocale($this->userLang);
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
            return ['success' => false , 'message' => trans('custom.unable_to_export_excel')];
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
            trans('custom.hash') => trans('custom.hash'),
            trans('custom.company_id') => trans('custom.company_id'),
            trans('custom.company_name') => trans('custom.company_name'),
            trans('custom.order_code') => trans('custom.order_code'),
            trans('custom.segment') => trans('custom.segment'),
            trans('custom.created_at') => trans('custom.created_at'),
            trans('custom.created_by') => trans('custom.created_by'),
            trans('custom.category') => trans('custom.category'),
            trans('custom.narration') => trans('custom.narration'),
            trans('custom.supplier_code') => trans('custom.supplier_code'),
            trans('custom.supplier_name') => trans('custom.supplier_name'),
            trans('custom.credit_period') => trans('custom.credit_period'),
            trans('custom.supplier_country') => trans('custom.supplier_country'),
            trans('custom.expected_delivery_date') => trans('custom.expected_delivery_date'),
            trans('custom.delivery_terms') => trans('custom.delivery_terms'),
            trans('custom.penalty_terms') => trans('custom.penalty_terms'),
            trans('custom.confirmed_status') => trans('custom.confirmed_status'),
            trans('custom.confirmed_date') => trans('custom.confirmed_date'),
            trans('custom.confirmed_by') => trans('custom.confirmed_by'),
            trans('custom.approved_status') => trans('custom.approved_status'),
            trans('custom.approved_date') => trans('custom.approved_date'),
            trans('custom.transaction_currency') => trans('custom.transaction_currency'),
            trans('custom.transaction_amount') => trans('custom.transaction_amount'),
            trans('custom.local_amount') . ($localCurrencyCode ? " ({$localCurrencyCode})" : '') => trans('custom.local_amount') . ($localCurrencyCode ? " ({$localCurrencyCode})" : ''),
            trans('custom.reporting_amount') . ($reportingCurrencyCode ? " ({$reportingCurrencyCode})" : '') => trans('custom.reporting_amount') . ($reportingCurrencyCode ? " ({$reportingCurrencyCode})" : ''),
            trans('custom.advance_payment_available') => trans('custom.advance_payment_available'),
            trans('custom.total_advance_payment_amount') => trans('custom.total_advance_payment_amount'),
        ];
    }

    private function headerDetails($val, $counter) {
        $this->data[] = [
            trans('custom.hash') => $counter,
            trans('custom.company_id') => $val->companyID,
            trans('custom.company_name') => optional($val->company)->CompanyName,
            trans('custom.order_code') => $val->purchaseOrderCode,
            trans('custom.segment') => optional($val->segment)->ServiceLineDes,
            trans('custom.created_at') => \Helper::dateFormat($val->createdDateTime),
            trans('custom.created_by') => optional($val->created_by)->empName,
            trans('custom.category') => optional($val->fcategory)->categoryDescription ?? trans('custom.other'),
            trans('custom.narration') => $val->narration ?: '-',
            trans('custom.supplier_code') => $val->supplierPrimaryCode,
            trans('custom.supplier_name') => $val->supplierName,
            trans('custom.credit_period') => $val->creditPeriod,
            trans('custom.supplier_country') => optional($val->supplier->country)->countryName,
            trans('custom.expected_delivery_date') => \Helper::dateFormat($val->expectedDeliveryDate),
            trans('custom.delivery_terms') => $val->deliveryTerms,
            trans('custom.penalty_terms') => $val->panaltyTerms,
            trans('custom.confirmed_status') => $val->poConfirmedYN == 1 ? trans('custom.yes') : trans('custom.no'),
            trans('custom.confirmed_date') => \Helper::dateFormat($val->poConfirmedDate),
            trans('custom.confirmed_by') => $val->poConfirmedByName,
            trans('custom.approved_status') => $val->approved == -1 ? trans('custom.yes') : trans('custom.no'),
            trans('custom.approved_date') => \Helper::dateFormat($val->approvedDate),
            trans('custom.transaction_currency') => optional($val->currency)->CurrencyCode,
            trans('custom.transaction_amount') => $val->poTotalSupplierTransactionCurrency,
            trans('custom.local_amount') => $val->poTotalLocalCurrency,
            trans('custom.reporting_amount') => $val->poTotalComRptCurrency,
            trans('custom.advance_payment_available') => $val->advance_summary ? trans('custom.yes') : trans('custom.no'),
            trans('custom.total_advance_payment_amount') => $val->advance_summary ? $val->advance_summary->advanceSum : 0
        ];
    }

    private function detailDetails($val) {
        if (!empty($val) && count($val) > 0) {
            $hasPR = $this->checkRequest($val);

            $headerOne [''] = '';
            $headerOne [trans('custom.order_details')] = trans('custom.order_details');

            $this->data[] = $headerOne;
            $header = [];
            $header[''] = '';
            if ($hasPR) $header[trans('custom.pr_number')] = trans('custom.pr_number');
            $header[trans('custom.item_code')] = trans('custom.item_code');
            $header[trans('custom.item_description')] = trans('custom.item_description');
            $header[trans('custom.comments')] = trans('custom.comments');
            if ($hasPR) $header[trans('custom.pr_qty')] = trans('custom.pr_qty');
            $header[trans('custom.uom')] = trans('custom.uom');
            $header[trans('custom.no_qty')] = trans('custom.no_qty');
            $header[trans('custom.unit_cost')] = trans('custom.unit_cost');
            $header[trans('custom.discount_percentage')] = trans('custom.discount_percentage');
            $header[trans('custom.discount')] = trans('custom.discount');
            $header[trans('custom.vat_percentage')] = trans('custom.vat_percentage');
            $header[trans('custom.vat_amount')] = trans('custom.vat_amount');
            $header[trans('custom.net_amount')] = trans('custom.net_amount');
            $this->data[] = $header;

            foreach ($val as $detail) {
                $row = [];
                $row [''] = '';
                if ($hasPR) {
                    $row[trans('custom.pr_number')] = $detail->requestDetail->purchase_request->purchaseRequestCode ?? '';
                }
                $row[trans('custom.item_code')] = $detail->itemPrimaryCode ?? '';
                $row[trans('custom.item_description')] = $detail->itemDescription ?? '';
                $row[trans('custom.comments')] = $detail->comment ?? '';
                if ($hasPR) {
                    $row[trans('custom.pr_qty')] = $detail->requestDetail->quantityRequested ?? '';
                }
                $row[trans('custom.uom')] = $detail->unit->UnitDes ?? '';
                $row[trans('custom.no_qty')] = $detail->noQty ?? '';
                $row[trans('custom.unit_cost')] = $detail->unitCost ?? '';
                $row[trans('custom.discount_percentage')] = $detail->discountPercentage ?? '';
                $row[trans('custom.discount')] = $detail->discountAmount ?? '';
                $row[trans('custom.vat_percentage')] = $detail->VATPercentage ?? '';
                $row[trans('custom.vat_amount')] = $detail->VATAmount ?? '';
                $row[trans('custom.net_amount')] = $detail->netAmount ?? '';
                $this->data[] = $row;
            }
            $this->data[] = [];
        }
    }

    private function logisticDetails($val) {
        if (!empty($val) && count($val) > 0) {
            $headerOne [''] = '';
            $headerOne [trans('custom.logistics_details')] = trans('custom.logistics_details');

            $this->data[] = $headerOne;
            $header = [];
            $header[''] = '';
            $header[trans('custom.category')] = trans('custom.category');
            $header[trans('custom.supplier_code')] = trans('custom.supplier_code');
            $header[trans('custom.supplier_name')] = trans('custom.supplier_name');
            $header[trans('custom.grv_code')] = trans('custom.grv_code');
            $header[trans('custom.currency')] = trans('custom.currency');
            $header[trans('custom.amount')] = trans('custom.amount');
            $header[trans('custom.local_amount')] = trans('custom.local_amount');
            $header[trans('custom.reporting_amount')] = trans('custom.reporting_amount');
            $header[trans('custom.add_vat_on_po')] = trans('custom.add_vat_on_po');
            $header[trans('custom.vat_percentage')] = trans('custom.vat_percentage');
            $header[trans('custom.vat_amount')] = trans('custom.vat_amount');
            $header[trans('custom.vat_sub_category')] = trans('custom.vat_sub_category');
            $this->data[] = $header;

            foreach ($val as $detail) {
                $row = [];
                $row [''] = '';
                $row[trans('custom.category')] = $detail->category_by->costCatDes ?? '';
                $row[trans('custom.supplier_code')] = $detail->SupplierPrimaryCode ?? '';
                $row[trans('custom.supplier_name')] = $detail->supplier_by->supplierName ?? '';
                $row[trans('custom.grv_code')] = $detail->grv_by->grvPrimaryCode ?? '';
                $row[trans('custom.currency')] = $detail->currency->CurrencyCode ?? '';
                $row[trans('custom.amount')] = $detail->reqAmount ?? '';
                $row[trans('custom.local_amount')] = $detail->reqAmountInPOLocalCur ?? '';
                $row[trans('custom.reporting_amount')] = $detail->reqAmountInPORptCur ?? '';
                $row[trans('custom.add_vat_on_po')] = ($detail->addVatOnPO ?? false) ? trans('custom.yes') : trans('custom.no');
                $row[trans('custom.vat_percentage')] = $detail->VATPercentage ?? '';
                $row[trans('custom.vat_amount')] = $detail->VATAmount ?? '';
                $row[trans('custom.vat_sub_category')] = $detail->vat_sub_category->subCategoryDescription ?? '';
                $this->data[] = $row;
            }
            $this->data[] = [];
        }
    }

    private function addonDetails($val) {
        if (!empty($val) && count($val) > 0) {
            $headerOne [''] = '';
            $headerOne [trans('custom.addon_details')] = trans('custom.addon_details');

            $this->data[] = $headerOne;
            $header = [];
            $header[''] = '';
            $header[trans('custom.category')] = trans('custom.category');
            $header[trans('custom.amount')] = trans('custom.amount');
            $this->data[] = $header;

            foreach ($val as $detail) {
                $row = [];
                $row [''] = '';
                $row[trans('custom.category')] = $detail->category->costCatDes ?? '';
                $row[trans('custom.amount')] = $detail->amount ?? '';
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
