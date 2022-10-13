<html>
<head>
    <title>Payment Voucher Matching</title>
    <style>
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
                        class="font-weight-bold">Confirmed By :</span> {{ $masterdata->confirmed_by? $masterdata->confirmed_by->empFullName:'' }}
            </td>
            <td><span class="font-weight-bold">Review By :</span></td>
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
                <span style="text-align: center">Page <span class="pagenum"></span></span><br>
                @if ($masterdata->company)
                    {{$masterdata->company->CompanyName}}
                @endif
            </td>
            <td style="width:33%;font-size: 10px;vertical-align: top;">
                <span style="margin-left: 50%;">Printed Date : {{date("d-M-y", strtotime(now()))}}</span>
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
                            <span class="font-weight-bold">Matching Code</span>
                        </td>
                        <td width="10px">
                            <span class="font-weight-bold">:</span>
                        </td>
                        <td>
                            <span>{{$masterdata->matchingDocCode}}</span>
                        </td>
                    </tr>
                    <tr>
                        <td width="70px">
                            <span class="font-weight-bold">Matching Date</span>
                        </td>
                        <td width="10px">
                            <span class="font-weight-bold">:</span>
                        </td>
                        <td>
                            <span>
                                {{ \App\helper\Helper::convertDateWithTime($masterdata->matchingDocdate)}}
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
            Payment Voucher Matching
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
                            <span class="font-weight-bold">Supplier Code</span>
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
                            <span class="font-weight-bold">Supplier Name</span>
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
                    <tr>
                        <td width="50px">
                            <span class="font-weight-bold">
                                @if($masterdata->documentSystemID == 15)
                                    Debit Note Code
                                @endif
                                @if($masterdata->documentSystemID == 4)
                                    Payment Voucher Code
                                @endif
                            </span>
                        </td>
                        <td width="10px">
                            <span class="font-weight-bold">:</span>
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
                                         <span class="font-weight-bold">
                         <h3 class="text-muted">
                             @if($masterdata->matchingConfirmedYN == 0)
                                 Not Confirmed
                             @elseif($masterdata->matchingConfirmedYN == 1)
                                 Confirmed
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
                            <span class="font-weight-bold"> Currency:</span>
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
        <table class="table table-bordered" style="width: 100%;">
            <thead>
            <tr class="theme-tr-head">
                <th></th>
                <th class="text-center">Booking Inv Code</th>
                <th class="text-center">PO Number</th>
                <th class="text-center">Invoice No</th>
                <th class="text-center">Invoice Date</th>
                <th class="text-center">Invoice Amount</th>
                <th class="text-center">Matched Amount</th>
            </tr>
            </thead>
            <tbody>
            {{ $tot = 0 }}
            @foreach ($masterdata->detail as $item)
                <tr style="border-top: 1px solid #ffffff !important;border-bottom: 1px solid #ffffff !important;">
                    <td>{{$loop->iteration}}</td>
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
    </div>
</div>
