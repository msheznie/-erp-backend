<html dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}" lang="{{ app()->getLocale() }}">
<head>
    <title>Direct Invoice Voucher</title>
    <style>
        @page {
            margin-left: 30px;
            margin-right: 30px;
            margin-top: 30px;
        }
        .footer {
            bottom: 0;
            height: 100px;
        }

        .footer {
            width: 100%;
            text-align: center;
            position: fixed;
            font-size: 10px;
            padding-top: -20px;
        }

        body {
            font-size: 12px;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
        }

        h3 {
            font-size: 24.5px;
        }

        h6 {
            font-size: 14px;
        }

        h6, h3 {
            margin-top: 0px;
            margin-bottom: 0px;
            font-family: inherit;
            font-weight: bold;
            line-height: 1.2;
            color: inherit;
        }

        table > tbody > tr > td {
            font-size: 11.5px;
        }

        .theme-tr-head {
            background-color: rgb(215, 215, 215) !important;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .font-weight-bold {
            font-weight: 700 !important;
        }

        .table thead th {
            border-bottom: none !important;
        }

        .white-space-pre-line {
            white-space: pre-line;
            white-space: pre;
            word-wrap: normal;
        }

        .text-muted {
            color: #dedede !important;
        }

        .font-weight-bold {
            font-weight: 700 !important;
        }

        .table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid #c2cfd6;
        }

        table.table-bordered {
            border: 1px solid #000;
        }

        .table th, .table td {
            padding: 6.4px !important;
        }

        table.table-bordered {
            border-collapse: collapse;
        }

        table.table-bordered, .table-bordered th, .table-bordered td {
            border: 1px solid #e2e3e5;
        }

        table > thead > tr > th {
            font-size: 11.5px;
        }

        hr {
            margin-top: 16px;
            margin-bottom: 16px;
            border: 0;
            border-top: 1px solid
        }

        hr {
            -webkit-box-sizing: content-box;
            box-sizing: content-box;
            height: 0;
            overflow: visible;
        }

        .header {
            top: 0px;
        }

        .pagenum:before {
            content: counter(page);
        }

        #watermark {
            position: fixed;
            bottom: 0px;
            right: 0px;
            width: 200px;
            height: 200px;
            opacity: .1;
        }

        .content {
            margin-bottom: 45px;
        }

        .border-top-remov {
            border-top: 1px solid #ffffff00 !important;
            border-left: 1px solid #ffffff00 !important;
            background-color: #ffffff !important;
            border-right: 0;
        }

        .border-bottom-remov {
            border-bottom: 1px solid #ffffffff !important;
            background-color: #ffffff !important;
            border-right: 1px solid #ffffffff !important;
        }
        .container
        {
            display: block;
            max-width:230px;
            max-height:95px;
            width: auto;
            height: auto;
        }

        .table_height
        {
            max-height: 60px !important;
        }
        .wrap
        {
            border: 1px solid;
            padding: 18px;
        }
        [dir="rtl"] {
        text-align: right;
        }
    
        [dir="rtl"] .text-left {
            text-align: right !important;
        }
    
        [dir="rtl"] .text-right {
            text-align: left !important;
        }
    
        [dir="rtl"] table {
            direction: rtl;
        }
    
        [dir="rtl"] td {
            text-align: right;
        }
    
        [dir="rtl"] .text-center {
            text-align: center !important;
        }
    
        [dir="rtl"] th {
            text-align: right;
    }
    </style>
</head>
<body>
<div id="watermark"></div>
<div class="card-body content" id="print-section">
    <table style="width: 100%" class="table_height">
        <tr style="width: 100%">
            <td valign="top" style="width: 20%">
                @if($masterdata->company)
                    <img src="{{$masterdata->company->logo_url}}" width="180px" height="60px" class="container">
                @endif
            </td>
            <td valign="top" style="width: 80%">
                @if($masterdata->company)
                    <span style="font-size: 24px;font-weight: 400"> {{$masterdata->company->CompanyName}}</span>
                @endif
                <br>
                <table>
                    <tr>
                        <td width="100px">
                            <span class="font-weight-bold">{{ __('custom.doc_code') }}</span>
                        </td>
                        <td width="10px">
                            <span class="font-weight-bold">:</span>
                        </td>
                        <td>
                            <span>{{$masterdata->bookingInvCode}}</span>
                        </td>
                    </tr>
                    <tr>
                        <td width="70px">
                            <span class="font-weight-bold">{{ __('custom.doc_date') }}</span>
                        </td>
                        <td width="10px">
                            <span class="font-weight-bold">:</span>
                        </td>
                        <td>
                            <span>
                                {{ \App\helper\Helper::dateFormat($masterdata->bookingDate)}}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td width="70px">
                            <span class="font-weight-bold">{{ __('custom.invoice_number') }}</span>
                        </td>
                        <td width="10px">
                            <span class="font-weight-bold">:</span>
                        </td>
                        <td>
                            <span>
                               {{$masterdata->supplierInvoiceNo}}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td width="70px">
                            <span class="font-weight-bold">{{ __('custom.invoice_date') }}</span>
                        </td>
                        <td width="10px">
                            <span class="font-weight-bold">:</span>
                        </td>
                        <td>
                            <span>
                                {{ \App\helper\Helper::dateFormat($masterdata->supplierInvoiceDate)}}
                            </span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <hr style="color: #d3d9df">

    <table style="width: 100%" class="table_height">
        <tr style="width: 100%">
            <td>
                <div>
                    <span style="font-size: 18px">
                        @if($masterdata->documentType == 0 || $masterdata->documentType == 2)
                            {{ __('custom.booking_invoice') }}
                        @endif
                        @if($masterdata->documentType == 1)
                            {{ __('custom.direct_invoice_voucher') }}
                        @endif
                        @if($masterdata->documentType == 4)
                            {{ __('custom.employee_direct_invoice') }}
                        @endif
                        @if($masterdata->documentType == 3)
                            {{ __('custom.supplier_item_invoice_voucher') }}
                        @endif
                    </span>
                </div>
            </td>
        </tr>
    </table>

    <br>
    <br>

    <table style="width: 100%">
        <tr style="width:100%">
            <td style="width: 60%">
                <table>
                    @if($masterdata->documentType != 4)
                        <tr>
                            <td width="150px">
                                <span class="font-weight-bold">{{ __('custom.supplier_code') }}</span>
                            </td>
                            <td width="10px">
                                <span class="font-weight-bold">:</span>
                            </td>
                            <td>
                                @if($masterdata->supplier)
                                    {{$masterdata->supplier->primarySupplierCode}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td width="50px">
                                <span class="font-weight-bold">{{ __('custom.supplier_name') }}</span>
                            </td>
                            <td width="10px">
                                <span class="font-weight-bold">:</span>
                            </td>
                            <td>
                                @if($masterdata->supplier)
                                    {{$masterdata->supplier->supplierName}}
                                @endif
                            </td>
                        </tr>
                    @endif
                    @if($masterdata->documentType == 4)
                        <tr>
                            <td width="150px">
                                <span class="font-weight-bold">{{ __('custom.employee_code') }}</span>
                            </td>
                            <td width="10px">
                                <span class="font-weight-bold">:</span>
                            </td>
                            <td>
                                @if($masterdata->employee)
                                    {{$masterdata->employee->empID}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td width="50px">
                                <span class="font-weight-bold">{{ __('custom.employee_name') }}</span>
                            </td>
                            <td width="10px">
                                <span class="font-weight-bold">:</span>
                            </td>
                            <td>
                                @if($masterdata->employee)
                                    {{$masterdata->employee->empName}}
                                @endif
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <td width="50px">
                            <span class="font-weight-bold">{{ __('custom.reference_number') }}</span>
                        </td>
                        <td width="10px">
                            <span class="font-weight-bold">:</span>
                        </td>
                        <td>
                            {{$masterdata->secondaryRefNo}}
                        </td>
                    </tr>
                    <tr>
                        <td width="70px">
                            <span class="font-weight-bold">{{ __('custom.narration') }}</span>
                        </td>
                        <td width="10px">
                            <span class="font-weight-bold">:</span>
                        </td>
                        <td>
                            <span>{{$masterdata->comments}}</span>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width: 40%">
                <table style="width: 100%">
                    <tr style="width: 100%">
                        <td valign="bottom" class="text-right">
                                         <span class="font-weight-bold">
                         <h3 class="text-muted">
                             @if($masterdata->confirmedYN == 0 && $masterdata->approved == 0)
                                 {{ __('custom.not_confirmed') }}
                             @elseif($masterdata->confirmedYN == 1 && $masterdata->approved == 0)
                                 {{ __('custom.pending_approval') }}
                             @elseif($masterdata->confirmedYN == 1 && ($masterdata->approved == 1 ||  $masterdata->approved == -1))
                                 {{ __('custom.fully_approved') }}
                             @endif
                         </h3>
 `             </span>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td valign="bottom" class="text-right">
                            <span class="font-weight-bold"> {{ __('custom.currency') }}:</span>
                            @if($masterdata->transactioncurrency)
                                {{$masterdata->transactioncurrency->CurrencyCode}}
                            @endif
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    @if($masterdata->documentType == 0 || $masterdata->documentType == 2)
        <div style="margin-top: 30px">
            <table class="table table-bordered" style="width: 100%;">
                <thead>
                <tr class="theme-tr-head">
                    <th></th>
                    <th class="text-center">{{ __('custom.gl_code') }}</th>
                    <th class="text-center">{{ __('custom.gl_type') }}</th>
                    <th class="text-center">{{ __('custom.gl_code_description') }}</th>
                    <th class="text-center">{{ __('custom.local_currency') }} (
                        @if($masterdata->localcurrency)
                            {{$masterdata->localcurrency->CurrencyCode}}
                        @endif
                        )
                    </th>
                    <th class="text-center">{{ __('custom.supplier_currency') }} (
                        @if($masterdata->transactioncurrency)
                            {{$masterdata->transactioncurrency->CurrencyCode}}
                        @endif
                        )
                    </th>
                </tr>
                <tr class="theme-tr-head">
                    <th colspan="4"></th>
                    <th class="text-center">{{ __('custom.amount') }}</th>
                    <th class="text-center">{{ __('custom.amount') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($masterdata->detail as $item)
                    <tr style="border-top: 1px solid #ffffff !important;border-bottom: 1px solid #ffffff !important;">
                        <td>{{$loop->iteration}}</td>
                        <td>
                            @if($masterdata->suppliergrv)
                                {{$masterdata->suppliergrv->AccountCode}}
                            @endif
                        </td>
                        <td>
                            @if($masterdata->suppliergrv)
                                {{$masterdata->suppliergrv->catogaryBLorPL}}
                            @endif
                        </td>
                        <td>
                            @if($masterdata->suppliergrv)
                                {{$masterdata->suppliergrv->AccountDescription}}
                            @endif
                        </td>
                        <td class="text-right">{{number_format($item->totLocalAmount, $localDecimal)}}</td>
                        <td class="text-right">{{number_format($item->totTransactionAmount, $transDecimal)}}</td>
                    </tr>
                @endforeach
                <tr style="border-top: 1px solid #333 !important;border-bottom: 1px solid #333 !important;">
                    <td colspan="4" class="text-right border-bottom-remov"></td>
                    <td class="text-right ">{{number_format($grvTotLoc, $localDecimal)}}</td>
                    <td class="text-right ">{{number_format($grvTotTra, $transDecimal)}}</td>
                </tr>
                </tbody>
            </table>
        </div>
    @endif
    @if($masterdata->documentType == 3)
        <div style="margin-top: 30px">
            <table class="table table-bordered" style="width: 100%;">
                <thead>
                <tr class="theme-tr-head">
                    <th></th>
                    <th class="text-center">{{ __('custom.gl_code') }}</th>
                    <th class="text-center">{{ __('custom.gl_type') }}</th>
                    <th class="text-center">{{ __('custom.gl_code_description') }}</th>
                    <th class="text-center">{{ __('custom.supplier_currency') }} (
                        @if($masterdata->transactioncurrency)
                            {{$masterdata->transactioncurrency->CurrencyCode}}
                        @endif
                        )
                    </th>
                </tr>
                <tr class="theme-tr-head">
                    <th colspan="4"></th>
                    <th class="text-center">{{ __('custom.amount') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($masterdata->item_details as $item)
                    <tr style="border-top: 1px solid #ffffff !important;border-bottom: 1px solid #ffffff !important;">
                        <td>{{$loop->iteration}}</td>
                        <td>
                            @if($masterdata->suppliergrv)
                                {{$masterdata->suppliergrv->AccountCode}}
                            @endif
                        </td>
                        <td>
                            @if($masterdata->suppliergrv)
                                {{$masterdata->suppliergrv->catogaryBLorPL}}
                            @endif
                        </td>
                        <td>
                            @if($masterdata->suppliergrv)
                                {{$masterdata->suppliergrv->AccountDescription}}
                            @endif
                        </td>
                        <td class="text-right">{{number_format(($item->netAmount + ($item->VATAmount * $item->noQty)), $transDecimal)}}</td>
                    </tr>
                @endforeach
                <tr style="border-top: 1px solid #333 !important;border-bottom: 1px solid #333 !important;">
                    <td colspan="4" class="text-right border-bottom-remov"></td>
                    <td class="text-right ">{{number_format($grvTotTra, $transDecimal)}}</td>
                </tr>
                </tbody>
            </table>
        </div>
    @endif

    @if($masterdata->documentType == 3)
        <div style="margin-top: 30px">
            <table class="table table-bordered" style="width: 100%;">
                <thead>
                <tr class="border-bottom-remov">
                    <th colspan="2" style="background-color: rgb(215,215,215)">{{ __('custom.item_details') }}</th>
                    <th colspan="9">&nbsp;</th>
                </tr>
                <tr style="border-top: 1px solid black;">
                    <th style="text-align: center">#</th>
                    <th style="text-align: center">{{ __('custom.item_code') }}</th>
                    <th style="text-align: center">{{ __('custom.item_description') }}</th>
                    <th style="text-align: center">{{ __('custom.manufacture_part_no') }}</th>
                    <th style="text-align: center">{{ __('custom.uom') }}</th>
                    <th style="text-align: center">{{ __('custom.qty') }}</th>
                    <th style="text-align: center">{{ __('custom.unit_cost') }}</th>
                    <th style="text-align: center">{{ __('custom.dis_per_unit') }}</th>
                    @if ($masterdata->isVatEligible)
                        <th style="text-align: center">{{ __('custom.vat_per_unit') }}</th>
                    @endif
                    <th style="text-align: center">{{ __('custom.net_cost_per_unit') }}</th>
                    <th style="text-align: center">{{ __('custom.net_amount') }}</th>
                </tr>
                </thead>
                <tbody style="width: 100%">
                {{ $subTotal = 0 }}
                {{ $VATTotal = 0 }}
                {{ $x = 1 }}
                {{ $subColspan = $masterdata->isVatEligible ? 1 : 0}}
                @foreach ($masterdata->item_details as $det)
                    {{ $netUnitCost = 0 }}
                    {{ $subTotal += $det->netAmount }}
                    {{ $VATTotal += ($det->VATAmount * $det->noQty) }}
                    {{ $netUnitCost = $det->unitCost - $det->discountAmount + $det->VATAmount }}
                    <tr style="border-bottom: 1px solid black; width: 100%">
                        <td>{{ $x  }}</td>
                        <td>{{$det->itemPrimaryCode}}</td>
                        <td nobr="true" style="width: 30%">{{$det->itemDescription}}</td>
                        <td>{{$det->supplierPartNumber}}</td>
                        <td>{{$det->unit->UnitShortCode}}</td>
                        <td class="text-right">{{$det->noQty}}</td>
                        <td class="text-right">{{number_format($det->unitCost, $transDecimal)}}</td>
                        <td class="text-right">{{number_format($det->discountAmount, $transDecimal)}}</td>
                        @if ($masterdata->isVatEligible)
                            <td class="text-right">{{number_format($det->VATAmount, $transDecimal)}}</td>
                        @endif
                        <td class="text-right">{{number_format($netUnitCost, $transDecimal)}}</td>
                        <td class="text-right">{{number_format($det->netAmount, $transDecimal)}}</td>
                    </tr>
                    {{ $x++ }}
                @endforeach
                </tbody>
            </table>
        </div>

    @endif
    @if($masterdata->documentType == 1 || $masterdata->documentType == 4)
        <div style="margin-top: 30px">
            <table class="table table-bordered" style="width: 100%;">
                <thead>
                <tr class="theme-tr-head">
                    <th></th>
                    <th class="text-center">{{ __('custom.gl_code') }}</th>
                    <th class="text-center">{{ __('custom.gl_code_description') }}</th>
                    @if($masterdata->documentType == 1 && $isProjectBase)
                        <th colspan="3" class="text-center">{{ __('custom.project') }}</th>
                    @endif
                    <th class="text-center">{{ __('custom.segment') }}</th>
                    <th class="text-center">{{ __('custom.amount') }}</th>
                    @if($isVATEligible)
                        <th class="text-center">{{ __('custom.vat_amount') }}</th>
                        <th class="text-center">{{ __('custom.net_amount') }}</th>
                    @endif
                    <!--   <th class="text-center">Local Amt (
                        @if($masterdata->localcurrency)
                        {{$masterdata->localcurrency->CurrencyCode}}
                    @endif
                    )</th>
                    <th class="text-center">Rpt Amt (
                        @if($masterdata->rptcurrency)
                        {{$masterdata->rptcurrency->CurrencyCode}}
                    @endif
                    )</th> -->
                </tr>
                </thead>
                <tbody>
                @foreach ($masterdata->directdetail as $item)
                    <tr style="border-top: 1px solid #ffffff !important;border-bottom: 1px solid #ffffff !important;">
                        <td>{{$loop->iteration}}</td>
                        <td>{{$item->glCode}}</td>
                        <td>{{$item->glCodeDes}}</td>
                        @if($masterdata->documentType == 1 && $isProjectBase)
                            <td colspan="3">
                                @if($item->project)
                                    {{$item->project->projectCode}} - {{$item->project->description}}
                                @endif
                            </td>
                        @endif
                        <td>
                            @if($item->segment)
                                {{$item->segment->ServiceLineDes}}
                            @endif
                        </td>
                        <td class="text-right">{{number_format($item->DIAmount, $transDecimal)}}</td>
                        @if($isVATEligible)
                            <td class="text-right">{{number_format($item->VATAmount, $transDecimal)}}</td>
                            <td class="text-right">{{number_format($item->netAmount, $transDecimal)}}</td>
                        @endif
                    </tr>
                @endforeach
                <tr style="border-top: 1px solid #333 !important;border-bottom: 1px solid #333 !important;">
                    @if($isVATEligible)
                        <td colspan="5" class="text-right border-bottom-remov">&nbsp;</td>
                    @else
                        <td colspan="3" class="text-right border-bottom-remov">&nbsp;</td>
                    @endif
                    @if($masterdata->documentType == 1 && $isProjectBase)
                        <td colspan="3" class="text-right border-bottom-remov">&nbsp;</td>
                    @endif
                    <td class="text-right" style="background-color: rgb(215,215,215)">{{ __('custom.total') }}</td>
                    <td class="text-right"
                        style="background-color: rgb(215,215,215)">{{number_format($directTotTra, $transDecimal)}}</td>
                </tr>
                @if($isVATEligible)
                    <tr style="border-top: 1px solid #333 !important;border-bottom: 1px solid #333 !important;">
                        <td colspan="5" class="text-right border-bottom-remov">&nbsp;</td>
                        @if($masterdata->documentType == 1 && $isProjectBase)
                            <td colspan="3" class="text-right border-bottom-remov">&nbsp;</td>
                        @endif
                        <td class="text-right" style="background-color: rgb(215,215,215)">{{ __('custom.vat') }}</td>
                        <td class="text-right"
                            style="background-color: rgb(215,215,215)">{{number_format(($directTotVAT - $retentionVatPortion), $transDecimal)}}</td>

                    </tr>
                    <tr style="border-top: 1px solid #333 !important;border-bottom: 1px solid #333 !important;">
                        <td colspan="5" class="text-right border-bottom-remov">&nbsp;</td>
                        @if($masterdata->documentType == 1 && $isProjectBase)
                            <td colspan="3" class="text-right border-bottom-remov">&nbsp;</td>
                        @endif
                        <td class="text-right" style="background-color: rgb(215,215,215)">{{ __('custom.net_total') }}</td>
                        @if($masterdata->rcmActivated)
                            <td class="text-right"
                                style="background-color: rgb(215,215,215)">{{number_format($directTotNet, $transDecimal)}}</td>
                        @else
                            <td class="text-right"
                                style="background-color: rgb(215,215,215)">{{number_format((($directTotNet + $directTotVAT) - $retentionVatPortion), $transDecimal)}}</td>
                        @endif
                    </tr>
                    @if ($masterdata->documentType != 4)
                        <tr style="border-top: 1px solid #333 !important;border-bottom: 1px solid #333 !important;">
                            <td colspan="5" class="text-right border-bottom-remov">&nbsp;</td>
                            @if($masterdata->documentType == 1 && $isProjectBase)
                                <td colspan="3" class="text-right border-bottom-remov">&nbsp;</td>
                            @endif
                            <td class="text-right" style="background-color: rgb(215,215,215)">{{ __('custom.retention_amount') }}</td>
                            @if($masterdata->rcmActivated)
                                <td class="text-right"
                                    style="background-color: rgb(215,215,215)">{{number_format($directTotNet * ($masterdata->retentionPercentage/100), $transDecimal)}}</td>
                            @else
                                <td class="text-right"
                                    style="background-color: rgb(215,215,215)">{{number_format((($directTotNet + $directTotVAT) * ($masterdata->retentionPercentage/100) - $retentionVatPortion), $transDecimal)}}</td>
                            @endif
                        </tr>
                        <tr style="border-top: 1px solid #333 !important;border-bottom: 1px solid #333 !important;">
                            <td colspan="5" class="text-right border-bottom-remov">&nbsp;</td>
                            @if($masterdata->documentType == 1 && $isProjectBase)
                                <td colspan="3" class="text-right border-bottom-remov">&nbsp;</td>
                            @endif
                            <td class="text-right" style="background-color: rgb(215,215,215)">{{ __('custom.net_of_retention_amount') }}</td>
                            @if($masterdata->rcmActivated)
                                <td class="text-right"
                                    style="background-color: rgb(215,215,215)">{{number_format($directTotNet - ($directTotNet * ($masterdata->retentionPercentage/100)), $transDecimal)}}</td>
                            @else
                                <td class="text-right"
                                    style="background-color: rgb(215,215,215)">{{number_format(($directTotNet + $directTotVAT) - (($directTotNet + $directTotVAT) * ($masterdata->retentionPercentage/100)), $transDecimal)}}</td>
                            @endif
                        </tr>
                    @endif
                @endif
                </tbody>
            </table>
        </div>
    @endif
    <div class="{{ $masterdata->documentType == 0 && count($masterdata->directdetail) > 0? 'wrap' : '' }}">
        @if($masterdata->documentType == 0 || $masterdata->documentType == 2)
            <div style="margin-top: 30px">
                <table class="table table-bordered" style="width: 100%;">
                    <thead>
                    <tr class="border-bottom-remov">
                        <th style="background-color: rgb(215,215,215)">{{ __('custom.grv_details') }}</th>
                        <th colspan="5">&nbsp;</th>
                    </tr>
                    <tr class="theme-tr-head">
                        <th class="text-center">{{ __('custom.grv_code') }}</th>
                        <th class="text-center">{{ __('custom.grv_date') }}</th>
                        <th class="text-center">{{ __('custom.document_narration') }}</th>
                        <th class="text-center">{{ __('custom.local_currency') }} (
                            @if($masterdata->localcurrency)
                                {{$masterdata->localcurrency->CurrencyCode}}
                            @endif
                            )</th>
                        <th class="text-center">{{ __('custom.rpt_currency') }} (
                            @if($masterdata->rptcurrency)
                                {{$masterdata->rptcurrency->CurrencyCode}}
                            @endif
                            )</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($masterdata->grvdetail as $item)
                        <tr style="border-top: 1px solid #ffffff !important;border-bottom: 1px solid #ffffff !important;">
                            <td>
                                @if($item->grvmaster)
                                    {{$item->grvmaster->grvPrimaryCode}}
                                @endif
                            </td>
                            <td>
                                @if($item->grvmaster)
                                    {{ \App\helper\Helper::dateFormat($item->grvmaster->grvDate)}}
                                @endif
                            </td>
                            <td>
                                @if($item->grvmaster)
                                    {{$item->grvmaster->grvNarration}}
                                @endif
                            </td>
                            <td class="text-right">{{number_format($item->totLocalAmount, $localDecimal)}}</td>
                            <td class="text-right">{{number_format($item->totRptAmount, $rptDecimal)}}</td>
                        </tr>
                    @endforeach
                    <tr style="border-top: 1px solid #333 !important;border-bottom: 1px solid #333 !important;">
                        <td colspan="3" class="text-right border-bottom-remov"></td>
                        <td class="text-right">{{number_format($grvTotLoc, $localDecimal )}}</td>
                        <td class="text-right">{{number_format($grvTotRpt, $rptDecimal)}}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        @endif
        @if(($masterdata->documentType == 0 || $masterdata->documentType == 3) && count($masterdata->directdetail) > 0)
            <div style="margin-top: 30px">
                <table class="table table-bordered" style="width: 100%;">
                    <thead>
                    <tr class="border-bottom-remov">
                        <th colspan="1" style="background-color: rgb(215,215,215)">
                            @if($masterdata->documentType == 0)
                                {{ __('custom.extra_charges') }}
                            @elseif($masterdata->documentType == 3)
                                {{ __('custom.other_charges') }}
                            @else
                                {{ __('custom.charges') }}
                            @endif
                        </th>
                    </tr>
                    <tr class="theme-tr-head">
                        @if($masterdata->documentType == 0)
                            <th class="text-center">{{ __('custom.gl_account') }}</th>
                        @endif
                        <th class="text-center">{{ __('custom.gl_account') }}</th>
                        <th class="text-center">{{ __('custom.segment') }}</th>
                        @if($masterdata->documentType == 0)
                            <th class="text-center">{{ __('custom.local_currency') }}</th>
                            <th class="text-center">{{ __('custom.rpt_currency') }}</th>
                        @endif
                        @if($masterdata->documentType == 3)
                            <th class="text-center">{{ __('custom.comments') }}</th>
                            <th class="text-center">{{ __('custom.amount') }}</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($masterdata->directdetail as $item)
                        <tr style="border-top: 1px solid #ffffff !important;border-bottom: 1px solid #ffffff !important;">
                            @if($masterdata->documentType == 0)
                                <td>{{$item->purchase_order->purchaseOrderCode}}</td>
                            @endif
                            <td>{{$item->glCode}} | {{$item->glCodeDes}}</td>
                            <td>
                                @if($item->segment)
                                    {{$item->segment->ServiceLineDes}}
                                @endif
                            </td>
                            @if($masterdata->documentType == 0)
                                <td class="text-right">{{number_format($item->localAmount, $transDecimal)}}</td>
                                <td class="text-right">{{number_format($item->comRptAmount, $transDecimal)}}</td>
                            @endif
                            @if($masterdata->documentType == 3)
                                <td>{{$item->comments}}</td>
                                <td class="text-right">{{number_format($item->DIAmount, $transDecimal)}}</td>
                            @endif
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    @if($masterdata->documentType == 0)
                        <tr>
                            <td colspan="5" class="no-border spacer-row"></td>
                        </tr>
                            <tr style="border-top: 2px solid #333 !important;border-bottom: 2px solid #333 !important; background-color: #DEDEDE !important;">
                            <td class="text-right" style="border-bottom: 1px solid #333 !important; background-color: #DEDEDE !important;"></td>
                            <td class="text-right" style="border-bottom: 1px solid #333 !important; background-color: #DEDEDE !important;"></td>
                            <td class="text-right" style="background-color: #DEDEDE !important; border-bottom: 1px solid #333 !important;"><b>{{ __('custom.total') }}:</b></td>
                            <td class="text-right">{{number_format($grvTotLoc + $directTotLoc, $localDecimal )}}</td>
                            <td class="text-right">{{number_format($grvTotRpt + $directAmountReport, $rptDecimal)}}</td>
                        </tr>
                    @endif
                    </tfoot>

                </table>
            </div>
        @endif
    </div>
    @if($masterdata->documentType == 3)
        <div class="row" style="margin-top: 30px">
            <table style="width:100%;" class="table table-bordered">
                <tbody>
                <tr>
                    <td style="border-bottom: none !important;border-left: none !important;width: 60%;">&nbsp;</td>
                    <td class="text-right" style="width: 20%;border-left: 1px solid rgb(127, 127, 127)!important;"><span
                                class="font-weight-bold" style="font-size: 11px">{{ __('custom.total_order_amount') }}</span></td>
                    <td class="text-right"
                        style="font-size: 11px;width: 20%;border-left: 1px solid rgb(127, 127, 127) !important;border-right: 1px solid rgb(127, 127, 127) !important;">
                    <span class="font-weight-bold">
                        @if ($masterdata->item_details)
                            {{number_format($subTotal + $directTotTra, $transDecimal)}}
                        @endif
                    </span>
                    </td>
                </tr>
                @if ($masterdata->isVatEligible || $masterdata->vatRegisteredYN)
                    <tr>
                        <td style="border-bottom: none !important;border-top: none !important;border-left: none !important;">
                            &nbsp;</td>
                        <td class="text-right" style="border-left: 1px solid rgb(127, 127, 127)!important;"><span
                                    class="font-weight-bold"
                                    style="font-size: 11px">{{ __('custom.total_order_amount') }} VAT{{--({{$masterdata->VATPercentage .'%'}})--}}
                            </span></td>
                        <td class="text-right"
                            style="font-size: 11px;border-left: 1px solid rgb(127, 127, 127) !important;border-right: 1px solid rgb(127, 127, 127) !important;"><span
                                    class="font-weight-bold">{{number_format(($VATTotal - $retentionVatPortion), $transDecimal)}}</span>
                        </td>
                    </tr>
                @endif
                <tr>
                    <td style="border-bottom: none !important;border-top: none !important;border-left: none !important;">
                        &nbsp;</td>
                    <td class="text-right" style="border-left: 1px solid rgb(127, 127, 127)!important;"><span
                                class="font-weight-bold"
                                style="font-size: 11px">{{ __('custom.net_amount') }}</span>
                    </td>
                    <td class="text-right"
                        style="font-size: 11px;border-left: 1px solid rgb(127, 127, 127) !important;border-right: 1px solid rgb(127, 127, 127) !important;">
                    <span class="font-weight-bold">
                        @if ($masterdata->detail)
                            {{number_format((($subTotal + $VATTotal + $directTotTra) - $retentionVatPortion), $transDecimal)}}
                        @endif
                    </span>
                    </td>
                </tr>
                <tr>
                    <td style="border-bottom: none !important;border-top: none !important;border-left: none !important;">
                        &nbsp;</td>
                    <td class="text-right" style="border-left: 1px solid rgb(127, 127, 127)!important;"><span
                                class="font-weight-bold"
                                style="font-size: 11px">{{ __('custom.retention_amount') }}</span>
                    </td>
                    <td class="text-right"
                        style="font-size: 11px;border-left: 1px solid rgb(127, 127, 127) !important;border-right: 1px solid rgb(127, 127, 127) !important;">
                    <span class="font-weight-bold">
                        @if ($masterdata->detail)
                            {{number_format((($subTotal + $VATTotal + $directTotTra) * ($masterdata->retentionPercentage/100) - $retentionVatPortion), $transDecimal)}}
                        @endif
                    </span>
                    </td>
                </tr>
                <tr>
                    <td style="border-bottom: none !important;border-top: none !important;border-left: none !important;">
                        &nbsp;</td>
                    <td class="text-right" style="border-left: 1px solid rgb(127, 127, 127)!important;"><span
                                class="font-weight-bold"
                                style="font-size: 11px">{{ __('custom.net_of_retention_amount') }}</span>
                    </td>
                    <td class="text-right"
                        style="font-size: 11px;border-left: 1px solid rgb(127, 127, 127) !important;border-right: 1px solid rgb(127, 127, 127) !important;">
                    <span class="font-weight-bold">
                        @if ($masterdata->detail)
                            {{number_format(($subTotal + $VATTotal + $directTotTra) - (($subTotal + $VATTotal + $directTotTra)* ($masterdata->retentionPercentage/100)), $transDecimal)}}
                        @endif
                    </span>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    @endif
</div>
<div class="" style="margin-top: 30px;">
    <table style="width:100%;">
        <tr>
            <td width="40%"><span
                        class="font-weight-bold">{{ __('custom.confirmed_by') }} :</span> {{ $masterdata->confirmed_by? $masterdata->confirmed_by->empFullName:'' }}
            </td>
            <td><span class="font-weight-bold">{{ __('custom.review_by') }} :</span></td>
        </tr>
    </table>
    <table style="width:100%;">
        <tr>
            <td><span class="font-weight-bold">{{ __('custom.electronically_approved_by') }} :</span></td>
        </tr>
        <tr>
            &nbsp;
        </tr>
    </table>
    <table style="width:100%;">
        <tr>
            @if ($masterdata->approved_by)
                @foreach ($masterdata->approved_by as $det)
                    <td style="padding-right: 25px;font-size: 9px;">
                        <div>
                            @if($det->employee)
                                {{$det->employee->empFullName }}
                            @endif
                        </div>
                        <div><span>
                @if(!empty($det->approvedDate))
                                    {{ \App\helper\Helper::convertDateWithTime($det->approvedDate)}}
                                @endif
              </span></div>
                        <div style="width: 3px"></div>
                    </td>
                @endforeach
            @endif
        </tr>
    </table>
</div>
<div class="footer">
    <table style="width:100%;">
        <tr>
            <td colspan="3" style="width:100%">
                <hr style="background-color: black">
            </td>
        </tr>
        <tr>
            <td style="width:33%;font-size: 10px;vertical-align: top;">
                <span class="white-space-pre-line font-weight-bold">{!! nl2br($docRef) !!}</span>
            </td>
            <td style="width:33%; text-align: center;font-size: 10px;vertical-align: top;">
                <span style="text-align: center">{{ __('custom.page') }} <span class="pagenum"></span></span><br>
                @if ($masterdata->company)
                    {{$masterdata->company->CompanyName}}
                @endif
            </td>
            <td style="width:33%;font-size: 10px;vertical-align: top;">
                <span style="margin-left: 50%;">{{ __('custom.printed_date') }} : {{date("d-M-y", strtotime(now()))}}</span>
            </td>
        </tr>
    </table>
</div>
</html>