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
            <td><span class="font-weight-bold">Electronically Approved By :</span></td>
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
                        @if($masterdata->documentType == 15)
                            Advance Receipt
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
                            <span>{{$masterdata->custPaymentReceiveCode}}</span>
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
                                {{ \App\helper\Helper::dateFormat($masterdata->custPaymentReceiveDate)}}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td width="70px">
                            <span class="font-weight-bold">Payment Mode </span>
                        </td>
                        <td width="10px">
                            <span class="font-weight-bold">:</span>
                        </td>
                        <td>
                            @if($masterdata->payment_type)
                                <span>
                                    {{ $masterdata->payment_type->description}}
                                </span>
                            @endif
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <hr style="color: #d3d9df">
    @if($masterdata->documentType == 13 || $masterdata->documentType == 15)
        <table style="width: 100%">
            <tr style="width:100%">
                <td style="width: 60%">
                    <table>
                        <tr>
                            <td colspan="3">
                                <span class="font-weight-bold">To :</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                @if ($masterdata->customer)
                                    {{$masterdata->customer->CutomerCode}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                @if($masterdata->customer)
                                    {!! nl2br($masterdata->customer->customerAddress1) !!}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td width="70px">
                                <span class="font-weight-bold">Comments :</span>
                            </td>
                            <td colspan="2">
                                <span>{{$masterdata->narration}}</span>
                            </td>
                        </tr>
                        @if($masterdata->isVATApplicable)
                            <tr>
                                <td width="70px">
                                    <span class="font-weight-bold">VAT Percentage (%) :</span>
                                </td>
                                <td colspan="2">
                                    <span>{{$masterdata->VATPercentage}}</span>
                                </td>
                            </tr>
                        @endif
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
                                @if($masterdata->currency)
                                    {{$masterdata->currency->CurrencyCode}}
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    @endif
    @if($masterdata->documentType == 14)
        <table style="width: 100%">
            <tr style="width:100%">
                <td style="width: 60%">
                    <table>
                        <tr>
                            <td width="150px">
                                <span class="font-weight-bold">Bank</span>
                            </td>
                            <td width="10px">
                                <span class="font-weight-bold">:</span>
                            </td>
                            <td>
                                @if($masterdata->bank)
                                    {{$masterdata->bank->AccountNo}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td width="50px">
                                <span class="font-weight-bold">Cheque No</span>
                            </td>
                            <td width="10px">
                                <span class="font-weight-bold">:</span>
                            </td>
                            <td>
                                <span>{{$masterdata->custChequeNo}}</span>
                            </td>
                        </tr>
                        <tr>
                            <td width="50px">
                                <span class="font-weight-bold">Cheque Date</span>
                            </td>
                            <td width="10px">
                                <span class="font-weight-bold">:</span>
                            </td>
                            <td>
                                {{ \App\helper\Helper::dateFormat($masterdata->custChequeDate)}}
                            </td>
                        </tr>
                        <tr>
                            <td width="70px">
                                <span class="font-weight-bold">Narration </span>
                            </td>
                            <td width="10px">
                                <span class="font-weight-bold">:</span>
                            </td>
                            <td>
                                <span>{{$masterdata->narration}}</span>
                            </td>
                        </tr>
                        @if($masterdata->isVATApplicable)
                            <tr>
                                <td width="70px">
                                    <span class="font-weight-bold">VAT Percentage (%) </span>
                                </td>
                                <td width="10px">
                                    <span class="font-weight-bold">:</span>
                                </td>
                                <td>
                                    <span>{{$masterdata->VATPercentage}}</span>
                                </td>
                            </tr>
                        @endif
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
                                @if($masterdata->currency)
                                    {{$masterdata->currency->CurrencyCode}}
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    @endif
    @if($masterdata->documentType == 14 || ($masterdata->documentType == 15 && count($masterdata->advance_receipt_details) == 0))
        <div style="margin-top: 30px">
            <table class="table table-bordered" style="width: 100%;">
                <thead>
                <tr class="theme-tr-head">
                    <th>#</th>
                    @if($masterdata->documentType == 14)
                        <th class="text-center">Account Code</th>
                        <th class="text-center">Account Description</th>
                    @endif
                    <th class="text-center">Department</th>
                    @if($masterdata->documentType == 14)
                        <th class="text-center">Contract</th>
                    @endif
                    <th class="text-center">Comments</th>
                    <th class="text-center">Amount</th>
                    @if($masterdata->isVATApplicable)
                        <th class="text-center">VAT Amount</th>
                        <th class="text-center">Net Amount</th>
                    @endif
                    {{--<th class="text-center">Local Amt (
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
                    </th>--}}
                </tr>
                </thead>
                <tbody>
                @foreach ($masterdata->directdetails as $item)
                    <tr style="border-top: 1px solid #ffffff !important;border-bottom: 1px solid #ffffff !important;">
                        <td>{{$loop->iteration}}</td>
                        @if($masterdata->documentType == 14)
                            <td>{{$item->glCode}}</td>
                            <td>{{$item->glCodeDes}}</td>
                        @endif
                        <td>  @if($item->segment)
                                {{$item->segment->ServiceLineDes}}
                            @endif
                        </td>
                        @if($masterdata->documentType == 14)
                            <td>{{$item->contractID}}</td>
                        @endif
                        <td>{{$item->comments}}</td>
                        <td class="text-right">{{number_format($item->DRAmount, $transDecimal)}}</td>
                        @if($masterdata->isVATApplicable)
                            <td class="text-right">{{number_format($item->VATAmount, $transDecimal)}}</td>
                            <td class="text-right">{{number_format($item->netAmount, $transDecimal)}}</td>
                        @endif
                        {{--<td class="text-right">{{number_format($item->localAmount, $localDecimal)}}</td>
                        <td class="text-right">{{number_format($item->comRptAmount, $rptDecimal)}}</td>--}}
                    </tr>
                @endforeach
                <tr style="border-top: 1px solid #333 !important;border-bottom: 1px solid #333 !important;">
                    @if($masterdata->documentType == 14)
                        <td colspan="5" class="text-right border-bottom-remov">&nbsp;</td>
                    @endif
                    @if($masterdata->documentType == 15)
                        <td colspan="2" class="text-right border-bottom-remov">&nbsp;</td>
                    @endif
                    <td class="text-right" style="background-color: rgb(215,215,215)">Total Payment</td>
                    <td class="text-right"
                        style="background-color: rgb(215,215,215)">{{number_format($directTotTra, $transDecimal)}}</td>
                    @if($masterdata->isVATApplicable)
                        <td class="text-right"
                            style="background-color: rgb(215,215,215)">{{number_format($directTotalVAT, $transDecimal)}}</td>
                        <td class="text-right"
                            style="background-color: rgb(215,215,215)">{{number_format($directTotalNet, $transDecimal)}}</td>
                    @endif
                    {{--<td class="text-right border-bottom-remov"></td>
                    <td class="text-right border-bottom-remov"></td>--}}
                </tr>
                </tbody>
            </table>
        </div>
    @endif

    @if($masterdata->documentType == 15 && count($masterdata->advance_receipt_details) > 0 )
        <div style="margin-top: 30px">
            <table class="table table-bordered" style="width: 100%;">
                <thead>
                <tr class="theme-tr-head">
                    <th>#</th>
                    <th class="text-center">Sales Order No</th>
                    <th class="text-center">Comments</th>
                    @if($masterdata->isVATApplicable)
                        <th class="text-center">VAT Amount</th>
                    @endif
                    <th class="text-center">Payment Amount</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($masterdata->advance_receipt_details as $item)
                    <tr style="border-top: 1px solid #ffffff !important;border-bottom: 1px solid #ffffff !important;">
                        <td>{{$loop->iteration}}</td>
                        <td>{{$item->salesOrderCode}}</td>
                        <td>{{$item->comments}}</td>
                        @if($masterdata->isVATApplicable)
                            <td class="text-right">{{number_format($item->VATAmount, $transDecimal)}}</td>
                        @endif
                        <td class="text-right">{{number_format($item->paymentAmount, $transDecimal)}}</td>
                    </tr>
                @endforeach
                <tr style="border-top: 1px solid #333 !important;border-bottom: 1px solid #333 !important;">
                    @if($masterdata->isVATApplicable)
                        <td class="text-right" colspan="4" style="background-color: rgb(215,215,215)">Total Payment</td>
                    @endif
                    @if(!$masterdata->isVATApplicable)
                        <td class="text-right" colspan="3" style="background-color: rgb(215,215,215)">Total Payment</td>
                    @endif
                    @if($masterdata->isVATApplicable)
                        <td class="text-right"
                            style="background-color: rgb(215,215,215)">{{number_format($advanceDetailsTotalNet, $transDecimal)}}</td>
                    @endif
                </tr>
                </tbody>
            </table>
        </div>
    @endif


    @if($masterdata->documentType == 13)
        <div style="margin-top: 30px">
            <table class="table table-bordered" style="width: 100%;">
                <thead>
                <tr class="theme-tr-head">
                    <th>#</th>
                    <th class="text-center">Invoice Code</th>
                    <th class="text-center">Invoice Date</th>
                    <th class="text-center">Comments</th>
                    <th class="text-center">Amount</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($masterdata->details as $item)
                    <tr style="border-top: 1px solid #ffffff !important;border-bottom: 1px solid #ffffff !important;">
                        <td>{{$loop->iteration}}</td>
                        <td>{{$item->bookingInvCode}}</td>
                        <td>{{ \App\helper\Helper::dateFormat($masterdata->bookingDate)}}</td>
                        <td>{{$item->comments}}</td>
                        <td class="text-right">{{number_format($item->receiveAmountTrans, $transDecimal)}}</td>
                    </tr>
                @endforeach
                <tr style="border-top: 1px solid #333 !important;border-bottom: 1px solid #333 !important;">
                    <td colspan="3" class="text-right border-bottom-remov">&nbsp;</td>
                    <td class="text-right" style="background-color: rgb(215,215,215)">Total</td>
                    <td class="text-right"
                        style="background-color: rgb(215,215,215)">{{number_format($ciDetailTotTra, $transDecimal)}}</td>
                </tr>
                </tbody>
            </table>
        </div>
    @endif
    @if($masterdata->documentType == 13)
        <div style="margin-top: 30px">
            <table class="table table-bordered" style="width: 100%;">
                <thead>
                <tr class="theme-tr-head">
                    <th>#</th>
                    <th class="text-center">Account Code</th>
                    <th class="text-center">Account Description</th>
                    <th class="text-center">Department</th>
                    <th class="text-center">Amount</th>
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
                @foreach ($masterdata->directdetails as $item)
                    <tr style="border-top: 1px solid #ffffff !important;border-bottom: 1px solid #ffffff !important;">
                        <td>{{$loop->iteration}}</td>
                        <td>{{$item->glCode}}</td>
                        <td>{{$item->glCodeDes}}</td>
                        <td>  @if($item->segment)
                                {{$item->segment->ServiceLineDes}}
                            @endif
                        </td>
                        <td class="text-right">{{number_format($item->DRAmount, $transDecimal)}}</td>
                        <td class="text-right">{{number_format($item->localAmount, $localDecimal)}}</td>
                        <td class="text-right">{{number_format($item->comRptAmount, $rptDecimal)}}</td>
                    </tr>
                @endforeach
                <tr style="border-top: 1px solid #333 !important;border-bottom: 1px solid #333 !important;">
                    <td colspan="3" class="text-right border-bottom-remov">&nbsp;</td>
                    <td class="text-right" style="background-color: rgb(215,215,215)">Total Payment</td>
                    <td class="text-right"
                        style="background-color: rgb(215,215,215)">{{number_format($directTotTra, $transDecimal)}}</td>
                    <td class="text-right border-bottom-remov"></td>
                    <td class="text-right border-bottom-remov"></td>
                </tr>
                </tbody>
            </table>
        </div>
    @endif
</div>
