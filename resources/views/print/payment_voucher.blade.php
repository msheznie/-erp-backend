<html>
<head>
    <title>
        @if($masterdata->documentType == 13)
            Customer Invoice Receipt
        @endif
        @if($masterdata->documentType == 14)
            Direct Receipt
        @endif
    </title>
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
            height: 60px;
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
            margin-top: 12px;
            margin-bottom: 12px;
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
    </style>
</head>
<body>
<div class="footer">
    <table style="width:100%; margin-top: 1em!important;">
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
                                    {{ \App\helper\Helper::dateFormat($det->approvedDate)}}
                                @endif
              </span></div>
                        <div style="width: 3px"></div>
                    </td>
                @endforeach
            @endif
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
                <span class="white-space-pre-line font-weight-bold">{!! nl2br($docRef) !!}</span>
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
    <table style="width: 100%">
        <tr style="width: 100%">
            <td valign="top" style="width: 50%">
                @if($masterdata->company)
                    <img src="{{$masterdata->company->logo_url}}" width="180px" height="60px">
                @endif
                <br>

                <div>
                    <span style="font-size: 18px">
                        @if($masterdata->documentType == 13)
                            Customer Invoice Receipt
                        @endif
                        @if($masterdata->documentType == 14)
                            Direct Receipt
                        @endif
                    </span>
                </div>
            </td>
            <td valign="top" style="width: 50%">
                @if($masterdata->company)
                    <span style="font-size: 24px;font-weight: 400"> {{$masterdata->company->CompanyName}}</span>
                @endif
                <br>
                <table>
                    <tr>
                        <td width="100px">
                            <span class="font-weight-bold">Doc Code</span>
                        </td>
                        <td width="10px">
                            <span class="font-weight-bold">:</span>
                        </td>
                        <td>
                            <span>{{$masterdata->BPVcode}}</span>
                        </td>
                    </tr>
                    <tr>
                        <td width="70px">
                            <span class="font-weight-bold">Doc Date </span>
                        </td>
                        <td width="10px">
                            <span class="font-weight-bold">:</span>
                        </td>
                        <td>
                            <span>
                                {{ \App\helper\Helper::dateFormat($masterdata->BPVdate)}}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td width="100px">
                            <span class="font-weight-bold">Payment Mode</span>
                        </td>
                        <td width="10px">
                            <span class="font-weight-bold">:</span>
                        </td>
                        <td>
                            @if ($masterdata->paymentmode)
                                <span>{{$masterdata->paymentmode->description}}</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <hr style="color: #d3d9df">
    <table style="width: 100%">
        <tr style="width:100%">
            <td style="width: 60%">
                <table>
                    @if($masterdata->invoiceType != 6)
                        <tr>
                            <td width="150px">
                                <span class="font-weight-bold">Payee Code</span>
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
                    @endif
                    <tr>
                        <td width="50px">
                            @if($masterdata->invoiceType == 6)
                                <span class="font-weight-bold">Employee Name</span>
                            @endif
                            @if($masterdata->invoiceType != 6)
                                <span class="font-weight-bold">Payee Name</span>
                            @endif
                        </td>
                        <td width="10px">
                            <span class="font-weight-bold">:</span>
                        </td>
                        <td>
                            {{$masterdata->directPaymentPayee}}
                        </td>
                    </tr>
                    <tr>
                        <td width="50px">
                            <span class="font-weight-bold">Bank Name</span>
                        </td>
                        <td width="10px">
                            <span class="font-weight-bold">:</span>
                        </td>
                        <td>
                            @if($masterdata->bankaccount)
                                {{$masterdata->bankaccount->bankName}}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td width="50px">
                            <span class="font-weight-bold">Bank</span>
                        </td>
                        <td width="10px">
                            <span class="font-weight-bold">:</span>
                        </td>
                        <td>
                            @if($masterdata->bankaccount)
                                {{$masterdata->bankaccount->AccountNo}}
                            @endif
                        </td>
                    </tr>
                    @if($masterdata->payment_mode == 2)
                    <tr>
                        <td width="50px">
                            <span class="font-weight-bold">Cheque No</span>
                        </td>
                        <td width="10px">
                            <span class="font-weight-bold">:</span>
                        </td>
                        <td>
                            <span>{{$masterdata->BPVchequeNo}}</span>
                        </td>
                    </tr>
                    @endif
                    @if($masterdata->payment_mode == 2)
                    <tr>
                        <td width="50px">
                            <span class="font-weight-bold">Cheque Date</span>
                        </td>
                        <td width="10px">
                            <span class="font-weight-bold">:</span>
                        </td>
                        <td>
                            {{ \App\helper\Helper::dateFormat($masterdata->BPVchequeDate)}}
                        </td>
                    </tr>
                    @endif
                    <tr>
                        <td width="70px">
                            <span class="font-weight-bold">Narration </span>
                        </td>
                        <td width="10px">
                            <span class="font-weight-bold">:</span>
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
                        <td valign="bottom" class="text-right">
                                         <span class="font-weight-bold">
                         <h3 class="text-muted">
                             @if($masterdata->confirmedYN == 0 && $masterdata->approved == 0)
                                 Not Confirmed
                             @elseif($masterdata->confirmedYN == 1 && $masterdata->approved == 0)
                                 Pending Approval
                             @elseif($masterdata->confirmedYN == 1 && ($masterdata->approved == 1 ||  $masterdata->approved == -1))
                                 Fully Approved
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
    @if($masterdata->invoiceType == 2 || $masterdata->invoiceType == 6)
        <div style="margin-top: 30px">
            <table class="table table-bordered" style="width: 100%;">
                <thead>
                <tr class="theme-tr-head">
                    <th>#</th>
                    <th class="text-center">Booking Inv Code</th>
                    <th class="text-center">PO Number</th>
                    <th class="text-center">Supplier Invoice No</th>
                    <th class="text-center">Invoice Date</th>
                    <th class="text-center">Invoice Amount</th>
                    <th class="text-center">Amount Paid</th>
                    <th class="text-center">Balance</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($masterdata->supplierdetail as $ddet)
                    {{$suppliPayment = 0}}
                    {{$suppliPayment = ($ddet->supplierInvoiceAmount - $ddet->supplierPaymentAmount) }}
                    <tr style="border-top: 1px solid #ffffff !important;border-bottom: 1px solid #ffffff !important;">
                        <td>{{$loop->iteration}}</td>
                        <td>{{$ddet->bookingInvDocCode}}</td>
                        @if($ddet->pomaster == null)
                            <td>-</td>
                        @else
                            <td>{{$ddet->pomaster->purchaseOrderCode}}</td>
                        @endif
                        <td>{{$ddet->supplierInvoiceNo}}</td>
                        <td>{{ \App\helper\Helper::dateFormat($ddet->supplierInvoiceDate)}}</td>
                        <td class="text-right">{{number_format($ddet->supplierInvoiceAmount, $transDecimal)}}</td>
                        <td class="text-right">{{number_format($ddet->supplierPaymentAmount, $transDecimal)}}</td>
                        <td class="text-right">{{number_format($ddet->paymentBalancedAmount, $transDecimal)}}</td>
                    </tr>
                @endforeach
                <tr style="border-top: 1px solid #333 !important;border-bottom: 1px solid #333 !important;">
                    <td colspan="5" class="text-right border-bottom-remov">&nbsp;</td>
                    <td class="text-right" style="background-color: rgb(215,215,215)">Total Payment</td>
                    <td class="text-right"
                        style="background-color: rgb(215,215,215)">{{number_format($supplierdetailTotTra, $transDecimal)}}</td>
                    <td class="text-right border-bottom-remov"></td>
                </tr>
                </tbody>
            </table>
        </div>
    @endif
    @if($masterdata->invoiceType == 3)
        <div style="margin-top: 30px">
            <table class="table table-bordered" style="width: 100%;">
                <thead>
                <tr class="theme-tr-head">
                    <th>#</th>
                    <th class="text-center">GL Code</th>
                    <th class="text-center">GL Code Description</th>
                    <th class="text-center">Segment</th>
                    <th class="text-center">Amount</th>
                    <th class="text-center">VAT</th>
                    <th class="text-center">Payment Amount</th>
                    <th class="text-center">Local Amt (
                        @if($masterdata->localCurrency)
                            {{$masterdata->localCurrency->CurrencyCode}}
                        @endif
                        )
                    </th>
                    <th class="text-center">Rpt Amt (
                        @if($masterdata->rptCurrency)
                            {{$masterdata->rptCurrency->CurrencyCode}}
                        @endif
                        )
                    </th>
                </tr>
                </thead>
                <tbody>
                @php
                    $tot= 0;
                    $totLocal= 0;
                    $totRpt = 0;
                @endphp
                @foreach ($masterdata->directdetail as $item)
                    <tr style="border-top: 1px solid #ffffff !important;border-bottom: 1px solid #ffffff !important;">
                        <td>{{$loop->iteration}}</td>
                        <td>{{$item->glCode}}</td>
                        <td>{{$item->glCodeDes}}</td>
                        <td>@if($item->segment)
                                {{$item->segment->ServiceLineDes}}
                            @endif
                        </td>
                        <td class="text-right">{{number_format($item->DPAmount, $transDecimal)}}</td>
                        <td class="text-right">{{number_format($item->vatAmount, $transDecimal)}}</td>
                        <td class="text-right">{{number_format($item->DPAmount + $item->vatAmount, $transDecimal)}}</td>
                        <td class="text-right">{{number_format($item->localAmount + $item->VATAmountLocal, $localDecimal)}}</td>
                        <td class="text-right">{{number_format($item->comRptAmount + $item->VATAmountRpt, $rptDecimal)}}</td>
                        @php
                            $tot += $item->DPAmount + $item->vatAmount;
                            $totLocal += $item->localAmount + $item->VATAmountLocal;
                            $totRpt += $item->comRptAmount + $item->VATAmountRpt;
                        @endphp
                    </tr>
                @endforeach
                <tr style="border-top: 1px solid #333 !important;border-bottom: 1px solid #333 !important;">
                    <td colspan="5" class="text-right border-bottom-remov">&nbsp;</td>
                    <td class="text-right" style="background-color: rgb(215,215,215)">Total Payment</td>
                    <td class="text-right"
                        style="background-color: rgb(215,215,215)">{{number_format($tot, $transDecimal)}}</td>
                    <td class="text-right"
                        style="background-color: rgb(215,215,215)">{{number_format($totLocal, $localDecimal)}}</td>
                    <td class="text-right"
                        style="background-color: rgb(215,215,215)">{{number_format($totRpt, $rptDecimal)}}</td>
                   
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
                    <th>#</th>
                    <th class="text-center">Purchase Order No</th>
                    <th class="text-center">Comment</th>
                    <th class="text-center">Payment Amount</th>
                    <th class="text-center">Local Amt (
                        @if($masterdata->localCurrency)
                            {{$masterdata->localCurrency->CurrencyCode}}
                        @endif
                        )
                    </th>
                    <th class="text-center">Rpt Amt (
                        @if($masterdata->rptCurrency)
                            {{$masterdata->rptCurrency->CurrencyCode}}
                        @endif
                        )
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach ($masterdata->advancedetail as $item)
                    <tr style="border-top: 1px solid #ffffff !important;border-bottom: 1px solid #ffffff !important;">
                        <td>{{$loop->iteration}}</td>
                        <td>{{$item->purchaseOrderCode}}</td>
                        <td>{{$item->comments}}</td>
                        <td class="text-right">{{number_format($item->paymentAmount, $transDecimal)}}</td>
                        <td class="text-right">{{number_format($item->localAmount, $transDecimal)}}</td>
                        <td class="text-right">{{number_format($item->comRptAmount, $transDecimal)}}</td>
                    </tr>
                @endforeach
                <tr style="border-top: 1px solid #333 !important;border-bottom: 1px solid #333 !important;">
                    <td colspan="2" class="text-right border-bottom-remov">&nbsp;</td>
                    <td class="text-right" style="background-color: rgb(215,215,215)">Total Payment</td>
                    <td class="text-right"
                        style="background-color: rgb(215,215,215)">{{number_format($advancePayDetailTotTra, $transDecimal)}}</td>
                    <td class="text-right border-bottom-remov"></td>
                    <td class="text-right border-bottom-remov"></td>
                </tr>
                </tbody>
            </table>
        </div>
    @endif
    <div style="padding-bottom: 15px!important; padding-top: 5px!important;">
    <table style="width:100%;">
        <tr>
            <td width="40%"><span
                        class="font-weight-bold">Confirmed By :</span> {{ $masterdata->confirmed_by? $masterdata->confirmed_by->empFullName:'' }}
            </td>
            <td><span class="font-weight-bold">Review By :</span></td>
        </tr>
        <tr>
            <td><span class="font-weight-bold">Electronically Approved By :</span></td>
        </tr>
        <tr>
            &nbsp;
        </tr>
    </table>
    </div>
</div>



</body>
