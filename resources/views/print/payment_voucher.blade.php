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
            font-weight: bold
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
            font-weight: bold
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
            border-bottom: 1px solid #ffffffff;
            background-color: #ffffff;
            border-right: 1px solid #ffffffff;
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
                            <span style="font-weight: bold">Doc Code</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight: bold">:</span>
                        </td>
                        <td>
                            <span>{{$masterdata->BPVcode}}</span>
                        </td>
                    </tr>
                    <tr>
                        <td width="70px">
                            <span style="font-weight: bold">Doc Date </span>
                        </td>
                        <td width="10px">
                            <span style="font-weight: bold">:</span>
                        </td>
                        <td>
                            <span>
                                {{ \App\helper\Helper::dateFormat($masterdata->BPVdate)}}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td width="100px">
                            <span style="font-weight: bold">Payment Mode</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight: bold">:</span>
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
    <div>
        <span style="font-size: 18px">
            @if($masterdata->invoiceType == 2)
                Supplier Payment
            @endif
            @if($masterdata->invoiceType == 3)
                Direct Payment
            @endif
            @if($masterdata->invoiceType == 5)
                Supplier Advance Payment
            @endif
            @if($masterdata->invoiceType == 6)
                Employee Payment
            @endif
            @if($masterdata->invoiceType == 7)
                Employee Advance Payment
            @endif
        </span>
    </div>
    <br>
    <br>
    <table style="width: 100%">
        <tr style="width:100%">
            <td style="width: 60%">
                <table>
                    @if($masterdata->invoiceType != 6 && $masterdata->invoiceType != 7)
                        <tr>
                            <td width="150px">
                                <span style="font-weight: bold">Payee Code</span>
                            </td>
                            <td width="10px">
                                <span style="font-weight: bold">:</span>
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
                            @if($masterdata->invoiceType == 6 || $masterdata->invoiceType == 7)
                                <span style="font-weight: bold">Employee Name</span>
                            @endif
                            @if($masterdata->invoiceType != 6 && $masterdata->invoiceType != 7)
                                <span style="font-weight: bold">Payee Name</span>
                            @endif
                        </td>
                        <td width="10px">
                            <span style="font-weight: bold">:</span>
                        </td>
                        <td>
                            {{$masterdata->directPaymentPayee}}
                        </td>
                    </tr>
                    <tr>
                        <td width="50px">
                            <span style="font-weight: bold">Bank Name</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight: bold">:</span>
                        </td>
                        <td>
                            @if($masterdata->bankaccount)
                                {{$masterdata->bankaccount->bankName}}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td width="50px">
                            <span style="font-weight: bold">Bank</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight: bold">:</span>
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
                            <span style="font-weight: bold">Cheque No</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight: bold">:</span>
                        </td>
                        <td>
                            <span>{{$masterdata->BPVchequeNo}}</span>
                        </td>
                    </tr>
                    @endif
                    @if($masterdata->payment_mode == 2)
                    <tr>
                        <td width="50px">
                            <span style="font-weight: bold">Cheque Date</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight: bold">:</span>
                        </td>
                        <td>
                            {{ \App\helper\Helper::convertDateWithTime($masterdata->BPVchequeDate)}}
                        </td>
                    </tr>
                    @endif
                    <tr>
                        <td width="70px">
                            <span style="font-weight: bold">Narration </span>
                        </td>
                        <td width="10px">
                            <span style="font-weight: bold">:</span>
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
                        <td valign="bottom" style="text-align: right">
                                         <span style="font-weight: bold">
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
                        <td valign="bottom" style="text-align: right">
                            <span style="font-weight: bold"> Currency:</span>
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
                    <th style="text-align: center">Booking Inv Code</th>
                    <th style="text-align: center">PO Number</th>
                    <th style="text-align: center">Supplier Invoice No</th>
                    <th style="text-align: center">Invoice Date</th>
                    <th style="text-align: center">Invoice Amount</th>
                    <th style="text-align: center">Amount Paid</th>
                    <th style="text-align: center">Balance</th>
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
                        <td style="text-align: right">{{number_format($ddet->supplierInvoiceAmount, $transDecimal)}}</td>
                        <td style="text-align: right">{{number_format($ddet->supplierPaymentAmount, $transDecimal)}}</td>
                        <td style="text-align: right">{{number_format($ddet->paymentBalancedAmount, $transDecimal)}}</td>
                    </tr>
                @endforeach
                <tr style="border-top: 1px solid #333 !important;border-bottom: 1px solid #333 !important;">
                    <td colspan="5" style="border-bottom: 1px solid #ffffffff; background-color:#ffffff; border-right: 1px solid #ffffffff">&nbsp;</td>
                    <td style="text-align: right" style="background-color: rgb(215,215,215)">Total Payment</td>
                    <td style="text-align: right"
                        style="background-color: rgb(215,215,215)">{{number_format($supplierdetailTotTra, $transDecimal)}}</td>
                    <td style="border-bottom: 1px solid #ffffffff; background-color:#ffffff; border-right: 1px solid #ffffffff"></td>
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
                    <th style="text-align: center">GL Code</th>
                    <th style="text-align: center">GL Code Description</th>
                    @if($masterdata->invoiceType == 3 && $isProjectBase)
                        <th colspan="4" style="text-align: center">Project</th>
                    @endif
                    <th style="text-align: center">Segment</th>
                    <th style="text-align: center">Amount</th>
                    <th style="text-align: center">VAT</th>
                    <th style="text-align: center">Payment Amount</th>
                    <th style="text-align: center">Local Amt (
                        @if($masterdata->localCurrency)
                            {{$masterdata->localCurrency->CurrencyCode}}
                        @endif
                        )
                    </th>
                    <th style="text-align: center">Rpt Amt (
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

                        @if($masterdata->invoiceType == 3 && $isProjectBase)
                            <td colspan="4">
                                @if($item->project)
                                    {{$item->project->projectCode}} - {{$item->project->description}}
                                @endif
                            </td>
                        @endif

                        <td>@if($item->segment)
                                {{$item->segment->ServiceLineDes}}
                            @endif
                        </td>
                        <td style="text-align: right">{{number_format($item->DPAmount, $transDecimal)}}</td>
                        <td style="text-align: right">{{number_format($item->vatAmount, $transDecimal)}}</td>
                        <td style="text-align: right">{{number_format($item->DPAmount + $item->vatAmount, $transDecimal)}}</td>
                        <td style="text-align: right">{{number_format($item->localAmount + $item->VATAmountLocal, $localDecimal)}}</td>
                        <td style="text-align: right">{{number_format($item->comRptAmount + $item->VATAmountRpt, $rptDecimal)}}</td>
                        @php
                            $tot += $item->DPAmount + $item->vatAmount;
                            $totLocal += $item->localAmount + $item->VATAmountLocal;
                            $totRpt += $item->comRptAmount + $item->VATAmountRpt;
                        @endphp
                    </tr>
                @endforeach
                <tr style="border-top: 1px solid #333 !important;border-bottom: 1px solid #333 !important;">
                    @if($masterdata->invoiceType == 3 && $isProjectBase)
                        <td colspan="4" style="border-bottom: 1px solid #ffffffff; background-color:#ffffff; border-right: 1px solid #ffffffff">&nbsp;</td>
                    @endif
                    <td colspan="5" style="border-bottom: 1px solid #ffffffff; background-color:#ffffff; border-right: 1px solid #ffffffff">&nbsp;</td>
                    <td style="text-align: right" style="background-color: rgb(215,215,215)">Total Payment</td>
                    <td style="text-align: right"
                        style="background-color: rgb(215,215,215)">{{number_format($tot, $transDecimal)}}</td>
                    <td style="text-align: right"
                        style="background-color: rgb(215,215,215)">{{number_format($totLocal, $localDecimal)}}</td>
                    <td style="text-align: right"
                        style="background-color: rgb(215,215,215)">{{number_format($totRpt, $rptDecimal)}}</td>
                   
                </tr>
                </tbody>
            </table>
        </div>
    @endif
    @if($masterdata->invoiceType == 5 || $masterdata->invoiceType == 7)
        <div style="margin-top: 30px">
            <table class="table table-bordered" style="width: 100%;">
                <thead>
                <tr class="theme-tr-head">
                    <th>#</th>
                    @if($masterdata->invoiceType == 5)
                        <th style="text-align: center">Purchase Order No</th>
                    @endif
                    <th style="text-align: center">Comment</th>
                    <th style="text-align: center">Payment Amount</th>
                    <th style="text-align: center">Local Amt (
                        @if($masterdata->localCurrency)
                            {{$masterdata->localCurrency->CurrencyCode}}
                        @endif
                        )
                    </th>
                    <th style="text-align: center">Rpt Amt (
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
                        @if($masterdata->invoiceType == 5)
                            <td>{{$item->purchaseOrderCode}}</td>
                        @endif
                        <td>{{$item->comments}}</td>
                        <td style="text-align: right">{{number_format($item->paymentAmount, $transDecimal)}}</td>
                        <td style="text-align: right">{{number_format($item->localAmount, $localDecimal)}}</td>
                        <td style="text-align: right">{{number_format($item->comRptAmount, $rptDecimal)}}</td>
                    </tr>
                @endforeach
                <tr style="border-top: 1px solid #333 !important;border-bottom: 1px solid #333 !important;">
                    @if($masterdata->invoiceType == 5)
                        <td colspan="2" style="border-bottom: 1px solid #ffffffff; background-color:#ffffff; border-right: 1px solid #ffffffff; text-align: right">&nbsp;</td>
                    @else
                        <td style="border-bottom: 1px solid #ffffffff; background-color:#ffffff; border-right: 1px solid #ffffffff; text-align: right">&nbsp;</td>
                    @endif
                    <td style="text-align: right" style="background-color: rgb(215,215,215)">Total Payment</td>
                    <td style="text-align: right"
                        style="background-color: rgb(215,215,215)">{{number_format($advancePayDetailTotTra, $transDecimal)}}</td>
                    <td style="border-bottom: 1px solid #ffffffff; background-color:#ffffff; border-right: 1px solid #ffffffff"></td>
                    <td style="border-bottom: 1px solid #ffffffff; background-color:#ffffff; border-right: 1px solid #ffffffff"></td>
                </tr>
                </tbody>
            </table>
        </div>
    @endif
    <div style="padding-bottom: 20px!important; padding-top: 15px!important; page-break-inside: avoid; !important;">
    <table style="width:100%;">
        <tr>
            <td width="40%"><span style="font-weight: bold">Confirmed By :</span> {{ $masterdata->confirmed_by? $masterdata->confirmed_by->empFullName:'' }}
            </td>
            <td><span style="font-weight: bold">Review By :</span></td>
        </tr>
        <tr>
            <td><span style="font-weight: bold">Electronically Approved By :</span>
                @if ($masterdata->approved_by)
                    @foreach ($masterdata->approved_by as $det)
                        <div style="padding-right: 25px;font-size: 9px;">
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
                        </div>
                    @endforeach
                @endif
            </td>
        </tr>
        <tr>

        </tr>
    </table>
    </div>
</div>



</body>
