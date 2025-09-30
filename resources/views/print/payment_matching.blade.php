<html @if(isset($lang) && $lang === 'ar') dir="rtl" @endif>
<head>
    <title>{{ __('custom.payment_voucher_matching') }}</title>
    <style>
        @if(isset($lang) && $lang === 'ar')
        body {
            direction: rtl;
            text-align: right;
        }
        
        .text-left {
            text-align: right !important;
        }
        
        .text-right {
            text-align: left !important;
        }
        
        table {
            direction: rtl;
        }
        
        .table th, .table td {
            text-align: right;
        }
        @endif
        @page {
            margin-left: 30px;
            margin-right: 30px;
            margin-top: 30px;
        }

        .footer {
            position: absolute;
        }

        .footer {
            bottom: 0;
            height: 50px;
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
    </style>
</head>
<body>
<div class="footer">
    <table style="width:100%;">
        <tr>
            <td width="40%"><span
                        style="font-weight: bold;">{{ __('custom.confirmed_by') }} :</span> {{ $masterdata->confirmed_by? $masterdata->confirmed_by->empFullName:'' }}
            </td>
            <td><span style="font-weight: bold;">{{ __('custom.reviewed_by') }} :</span></td>
        </tr>
    </table>
    <table style="width:100%;">
        <tr>
            <td colspan="3" style="width:100%">
                <hr style="background-color: black">
            </td>
        </tr>
        <tr>
            <td style="width:33%;font-size: 10px;vertical-align: top;">
                <span class="white-space-pre-line font-weight-bold"></span>
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
                            <span style="font-weight: bold;">{{ __('custom.matching_code') }}</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight: bold;">:</span>
                        </td>
                        <td>
                            <span>{{$masterdata->matchingDocCode}}</span>
                        </td>
                    </tr>
                    <tr>
                        <td width="70px">
                            <span style="font-weight: bold;">{{ __('custom.matching_date') }}</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight: bold;">:</span>
                        </td>
                        <td>
                            <span>
                                {{ \App\helper\Helper::dateFormat($masterdata->matchingDocdate)}}
                            </span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <hr style="color: #d3d9df">
    <div>
        <span style="font-size: 18px">
            {{ __('custom.payment_voucher_matching') }}
        </span>
    </div>
    <br>
    <br>

    <table style="width: 100%">
        <tr style="width:100%">
            <td style="width: 60%">
                <table>
                    <tr>
                        <td width="150px">
                            <span style="font-weight: bold;">{{ __('custom.supplier_code') }}</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight: bold;">:</span>
                        </td>
                        <td>
                            @if($masterdata->supplier)
                                {{$masterdata->supplier->primarySupplierCode}}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td width="50px">
                            <span style="font-weight: bold;">{{ __('custom.supplier_name') }}</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight: bold;">:</span>
                        </td>
                        <td>
                            @if($masterdata->supplier)
                                {{$masterdata->supplier->supplierName}}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td width="50px">
                            <span style="font-weight: bold;">
                                @if($masterdata->documentSystemID == 15)
                                    {{ __('custom.debit_note_code') }}
                                @endif
                                @if($masterdata->documentSystemID == 4)
                                    {{ __('custom.payment_voucher_code') }}
                                @endif
                            </span>
                        </td>
                        <td width="10px">
                            <span style="font-weight: bold;">:</span>
                        </td>
                        <td>
                            {{$masterdata->BPVcode}}
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width: 40%">
                <table style="width: 100%">
                    <tr style="width: 100%">
                        <td valign="bottom" class="text-right">
                                         <span style="font-weight: bold;">
                         <h3 class="text-muted">
                             @if($masterdata->matchingConfirmedYN == 0)
                                 {{ __('custom.not_confirmed') }}
                             @elseif($masterdata->matchingConfirmedYN == 1)
                                 {{ __('custom.confirmed') }}
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
                            <span style="font-weight: bold;"> {{ __('custom.currency') }}:</span>
                            @if($masterdata->transactioncurrency)
                                {{$masterdata->transactioncurrency->CurrencyCode}}
                            @endif
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <div style="margin-top: 30px">
    @foreach($masterdata->detail->chunk(18) as $chunkIndex => $chunk)
        <table class="table table-bordered" style="width: 100%;">
            <thead>
            <tr class="theme-tr-head">
                <th></th>
                <th class="text-center">{{ __('custom.booking_inv_code') }}</th>
                <th class="text-center">{{ __('custom.po_number') }}</th>
                <th class="text-center">{{ __('custom.invoice_no') }}</th>
                <th class="text-center">{{ __('custom.invoice_date') }}</th>
                <th class="text-center">{{ __('custom.invoice_amount') }}</th>
                <th class="text-center">{{ __('custom.matched_amount') }}</th>
            </tr>
            </thead>
            <tbody>
            {{ $tot = 0 }}
            @foreach ($chunk as $item)
                <tr style="border-top: 1px solid #ffffff !important;border-bottom: 1px solid #ffffff !important;">
                    <td>{{ $loop->iteration + ($chunkIndex * 12) }}</td>
                    <td>{{$item->bookingInvDocCode}}</td>
                    @if($item->pomaster == null)
                        <td>-</td>
                    @else
                        <td>{{$item->pomaster->purchaseOrderCode}}</td>
                    @endif
                    <td>{{$item->supplierInvoiceNo}}</td>
                    <td>{{ \App\helper\Helper::dateFormat($item->supplierInvoiceDate)}}</td>
                    <td class="text-right">{{number_format($item->supplierInvoiceAmount, $transDecimal)}}</td>
                    <td class="text-right">{{number_format($item->supplierPaymentAmount, $transDecimal)}}</td>
                </tr>
                {{ $tot += $item->supplierPaymentAmount  }}
            @endforeach
            <tr style="border-top: 1px solid #333 !important;border-bottom: 1px solid #333 !important;">
                <td colspan="6" class="text-right border-bottom-remov"></td>
                <td style="font-weight: 600" class="text-right">{{number_format($tot, $transDecimal)}}</td>
            </tr>
            </tbody>
        </table>
          @if(!$loop->last)
            <div style="page-break-after: always;"></div>
          @endif
    @endforeach
   
   
        </div>
</div>
