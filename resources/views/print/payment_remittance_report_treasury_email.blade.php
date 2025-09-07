<html @if(isset($lang) && $lang === 'ar') dir="rtl" @endif>
<head>
    <title>
        @if($masterdata->documentType == 13)
            {{ __('custom.customer_invoice_receipt') }}
        @endif
        @if($masterdata->documentType == 14)
            {{ __('custom.direct_receipt') }}
        @endif
    </title>
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
            height: 10px;
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
            font-size: 14px;
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
            font-weight: 400 !important;
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
            border-left: 1px solid #ffffffff !important;
            background-color: #ffffff !important;
            border-right: 1px solid #ffffffff !important;
        }
    </style>
</head>
<body>
<div class="footer">
    <table style="width:100%;">
        <tr>
            <td style="width:50%;font-size: 14px;">
                <span style="width: 50%;"> {{date("l, F d,Y", strtotime(now()))}}</span>
            </td>
            <td style="width:50%; text-align: right;font-size: 14px;">
                <span style="text-align: center">{{ __('custom.page') }} <span class="pagenum"></span></span><br>
            </td>
        </tr>
    </table>
</div>
<div id="watermark"></div>
<div class="card-body content" id="print-section">
    <table style="width: 100%">
        <tr style="width: 100%">
            <td valign="top" style="width: 10%" class="text-center">
                @if($masterdata->company)
                    <span style="font-size: 20px;font-weight: 700"> {{$masterdata->company->CompanyName}}</span><br><br>
                    <span style="font-size: 20px;font-weight: 700">{{ __('custom.remittance_advice') }}</span>
                @endif
            </td>
        </tr>
    </table>
    <br><br>
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
                            {{$masterdata->directPaymentPayee}}
                        </td>
                    </tr>
                    <tr>
                        <td width="50px">
                            <span style="font-weight: bold;">{{ __('custom.supplier_address') }}</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight: bold;">:</span>
                        </td>
                        <td valign="top">
                            @if($masterdata->supplier)
                                {{--{{$masterdata->supplier->address}}--}}
                                {!! nl2br($masterdata->supplier->address) !!}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td width="50px">
                            <span style="font-weight: bold;">{{ __('custom.cheque_transfer_number') }}</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight: bold;">:</span>
                        </td>
                        <td>
                            <span>{{$masterdata->BPVchequeNo}}</span>
                        </td>
                    </tr>
                    <tr>
                        <td width="50px">
                            <span style="font-weight: bold;">{{ __('custom.cheque_date') }}</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight: bold;">:</span>
                        </td>
                        <td>
                            {{ \App\helper\Helper::dateFormat($masterdata->BPVchequeDate)}}
                        </td>
                    </tr>
                    <tr>
                        <td width="70px">
                            <span style="font-weight: bold;">{{ __('custom.narration') }}</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight: bold;">:</span>
                        </td>
                        <td>
                            <span>{{$masterdata->BPVNarration}}</span>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width: 40%">
                <table style="width: 100%">
                    <tr style="width: 100%">
                        <td width="100px">
                            <span style="font-weight: bold;">{{ __('custom.document_code') }}</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight: bold;">:</span>
                        </td>
                        <td>
                            <span>{{$masterdata->BPVcode}}</span>
                        </td>
                    </tr>
                    <tr>
                        <td width="70px">
                            <span style="font-weight: bold;">{{ __('custom.date') }}</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight: bold;">:</span>
                        </td>
                        <td>
                            <span>
                                {{ \App\helper\Helper::dateFormat($masterdata->BPVdate)}}
                            </span>
                        </td>
                    </tr>
                    <tr> <td colspan="3"> &nbsp; </td> </tr>
                    <tr> <td colspan="3">&nbsp;</td> </tr>
                    <tr> <td colspan="3">&nbsp;</td> </tr>
                    <tr>
                        <td width="70px">
                        </td>
                        <td width="10px">

                        </td>
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
    @if($masterdata->invoiceType == 2)
        <div style="margin-top: 30px">
            <table class="table table-bordered" style="width: 100%;">
                <thead>
                <tr class="theme-tr-head">
                    <th class="text-center">{{ __('custom.system_invoice_number') }}</th>
                    <th class="text-center">{{ __('custom.supplier_invoice_no') }}</th>
                    <th class="text-center">{{ __('custom.invoice_date') }}</th>
                    <th class="text-center">{{ __('custom.invoice_amount') }}</th>
                    <th class="text-center">{{ __('custom.amount_paid') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($masterdata->supplierdetail as $ddet)
                    {{$suppliPayment = 0}}
                    {{$suppliPayment = ($ddet->supplierInvoiceAmount - $ddet->supplierPaymentAmount) }}
                    <tr style="border-top: 1px solid #ffffff !important;border-bottom: 1px solid #ffffff !important;width: 100%;">
                        <td>{{$ddet->bookingInvDocCode}}</td>
                        <td>{{$ddet->supplierInvoiceNo}}</td>
                        <td>{{ \App\helper\Helper::dateFormat($ddet->supplierInvoiceDate)}}</td>
                        <td class="text-right">{{number_format($ddet->supplierInvoiceAmount, $transDecimal)}}</td>
                        <td class="text-right">{{number_format($ddet->supplierPaymentAmount, $transDecimal)}}</td>
                    </tr>
                @endforeach
                <tr style="border-top: 1px solid #333 !important;border-bottom: 1px solid #333 !important;">
                    <td colspan="3" class="border-bottom-remov" style="font-size: 12.5px"><b>Electronically Generated Advice</b></td>
                    <td class="text-right" style="background-color: rgb(215,215,215)">{{ __('custom.total_payment') }}</td>
                    <td class="text-right"
                        style="background-color: rgb(215,215,215)">{{number_format($supplierdetailTotTra, $transDecimal)}}</td>
                </tr>
                </tbody>
            </table>
        </div>
    @endif
    @if($masterdata->invoiceType == 5)
        <div style="margin-top: 30px">
            <table class="table table-bordered" style="width: 100%;">
                <thead>
                <tr class="theme-tr-head">
                    <th class="text-center">{{ __('custom.purchase_order_number') }}</th>
                    <th class="text-center">{{ __('custom.comments') }}</th>
                    <th class="text-center">{{ __('custom.amount') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($masterdata->advancedetail as $item)
                    <tr style="border-top: 1px solid #ffffff !important;border-bottom: 1px solid #ffffff !important;">
                        <td>{{$item->purchaseOrderCode}}</td>
                        <td>{{$item->comments}}</td>
                        <td class="text-right">{{number_format($item->paymentAmount, $transDecimal)}}</td>
                    </tr>
                @endforeach
                <tr style="border-top: 1px solid #333 !important;border-bottom: 1px solid #333 !important;">
                    <td colspan="1" class="border-bottom-remov" style="font-size: 12.5px"><b>Electronically Generated Advice</b></td>
                    <td class="text-right" style="background-color: rgb(215,215,215)">{{ __('custom.total_payment') }}</td>
                    <td class="text-right"
                        style="background-color: rgb(215,215,215)">{{number_format($advancePayDetailTotTra, $transDecimal)}}</td>
                </tr>
                </tbody>
            </table>
        </div>
    @endif
</div>