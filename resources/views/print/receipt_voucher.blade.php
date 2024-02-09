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

        .img_size
        {
            width: 160px;
            height: auto;
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
                <img src="{{$masterdata->company->logo_url}}" class="container">
             
                @endif


            </td>
            <td valign="top" style="width: 80%">
                @if($masterdata->company)
                    <span style="font-size: 24px;font-weight: 400">{{$masterdata->company->CompanyName}}</span>
                @endif
                <br>
                <table>
                    <tr>
                        <td width="100px">
                            <span style="font-weight:bold;">{{ __('custom.doc_code') }}</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight:bold;">:</span>
                        </td>
                        <td>
                            <span>{{$masterdata->custPaymentReceiveCode}}</span>
                        </td>
                    </tr>
                    <tr>
                        <td width="70px">
                            <span style="font-weight:bold;">{{ __('custom.doc_date') }}</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight:bold;">:</span>
                        </td>
                        <td>
                            <span>
                                {{ \App\helper\Helper::dateFormat($masterdata->custPaymentReceiveDate)}}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td width="70px">
                            <span style="font-weight:bold;">{{ __('custom.payment_mode') }}</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight:bold;">:</span>
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

    <hr style="color: #d3d9df border-top: 2px solid black; height: 2px; color: black">

    <table style="width: 100%" class="table_height">
        <tr style="width: 100%">
            <td>
                <div>
                    <span style="font-size: 18px">
                        @if($masterdata->documentType == 13)
                            {{ __('custom.doc_code') }}
                        @endif
                        @if($masterdata->documentType == 14)
                            {{ __('custom.doc_code') }}
                        @endif
                        @if($masterdata->documentType == 15)
                            {{ __('custom.advance_receipt') }}
                        @endif
                    </span>
                </div>
            </td>
            <td valign="bottom" class="text-right">
                <span style="font-weight:bold;">
                    <h3 class="text-muted">
                        @if($masterdata->confirmedYN == 0 && $masterdata->approved == 0)
                            {{ __('custom.not_confirmed') }}
                        @elseif($masterdata->confirmedYN == 1 && $masterdata->approved == 0)
                            {{ __('custom.pending_approval') }}
                        @elseif($masterdata->confirmedYN == 1 && ($masterdata->approved == 1 ||  $masterdata->approved == -1))
                            {{ __('custom.fully_approved') }}
                        @endif
                    </h3>
                </span>
            </td>
        </tr>
    </table>
    <br>

    @if($masterdata->documentType == 13 || $masterdata->documentType == 15)
        <table style="width: 100%">
            <tr style="width: 100%">
                <td style="width: 58%; vertical-align:top;">
                    <table style="width: 100%">
                        <tr>
                            <td width="150px" style="font-weight:bold; vertical-align:top;">{{ __('custom.customer_name') }}</td>
                            <td width="10px" style="font-weight:bold; vertical-align:top;">:</td>
                            <td colspan="3">
                                @if ($masterdata->customer)
                                    {{$masterdata->customer->CustomerName}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td width="150px" style="font-weight:bold; vertical-align:top;">{{ __('custom.customer_code') }}</td>
                            <td width="10px" style="font-weight:bold; vertical-align:top;">:</td>
                            <td colspan="3">
                                @if ($masterdata->customer)
                                    {{$masterdata->customer->CutomerCode}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td width="150px" style="font-weight:bold; vertical-align:top;">{{ __('custom.customer_address') }}</td>
                            <td width="10px" style="font-weight:bold; vertical-align:top;">:</td>
                            <td colspan="3">
                                @if ($masterdata->customer)
                                    {{$masterdata->customer->customerAddress1}}
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="width: 42%;  vertical-align:top;">
                    <table style="width: 100%">
                        <tr>
                            <td width="150px" style="font-weight:bold; vertical-align:top;">{{ __('custom.bank_name') }}</td>
                            <td width="10px" style="font-weight:bold; vertical-align:top;">:</td>
                            <td colspan="3">
                                @if($masterdata->bank)
                                    {{$masterdata->bank->bankName}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td width="150px" style="font-weight:bold; vertical-align:top;">{{ __('custom.account_number') }}</td>
                            <td width="10px" style="font-weight:bold; vertical-align:top;">:</td>
                            <td colspan="3">
                                @if($masterdata->bank)
                                    {{$masterdata->bank->AccountNo}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td width="150px" style="font-weight:bold; vertical-align:top;">{{ __('custom.bank_currency') }}</td>
                            <td width="10px" style="font-weight:bold; vertical-align:top;">:</td>
                            <td colspan="3">
                                @if($masterdata->bank_currency)
                                    {{$masterdata->bank_currency->CurrencyCode}}
                                @endif
                            </td>
                        </tr>
                        @if($masterdata->isVATApplicable)
                            <tr>
                                <td width="150px" style="font-weight:bold; vertical-align:top;">{{ __('custom.vat_percentage') }} (%)</td>
                                <td width="10px" style="font-weight:bold; vertical-align:top;">:</td>
                                <td colspan="3">
                                    @if ($masterdata->VATPercentage)
                                        {{$masterdata->VATPercentage}}
                                    @endif
                                </td>
                            </tr>
                        @endif
                        
                        <tr>
                            <td width="150px" style="font-weight:bold; vertical-align:top;">{{ __('custom.currency') }}</td>
                            <td width="10px" style="font-weight:bold; vertical-align:top;">:</td>
                            <td colspan="3">
                                @if($masterdata->currency)
                                    {{$masterdata->currency->CurrencyCode}}
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <table style="width: 100%">
            <tr style="width:100%">
                <td style="width: 100%">
                    <table>
                        <tr style="width: 100%">
                            <td style="vertical-align: top;">
                                <span style="font-weight:bold;">{{ __('custom.comments') }} :</span>
                            </td>
                        </tr>
                        <tr style="width: 100%">
                            <td style="vertical-align: top;">
                                @if ($masterdata->narration)
                                    {{$masterdata->narration}}
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
                        @if($masterdata->payeeTypeID && $masterdata->payeeTypeID != 3)
                        <tr>
                            <td width="150px">
                                <span style="font-weight:bold;">{{ __('custom.payee_code') }}</span>
                            </td>
                            <td width="10px">
                                <span style="font-weight:bold;">:</span>
                            </td>
                            <td>
                                @if($masterdata->payeeTypeID == 1)
                                    {{$masterdata->customer->CutomerCode}}
                                @endif
                                @if($masterdata->payeeTypeID == 2)
                                     {{$masterdata->employee->empID}}
                                @endif
                            </td>
                        </tr>
                        @endif
                            @if($masterdata->payeeTypeID)
                                <tr>
                                    <td width="150px">
                                        @if($masterdata->payeeTypeID == 1)
                                            <span style="font-weight:bold;">{{ __('custom.customer_name') }}</span>
                                        @endif
                                        @if($masterdata->payeeTypeID == 2)
                                         <span style="font-weight:bold;">{{ __('custom.employee_name') }}</span>
                                        @endif
                                        @if($masterdata->payeeTypeID == 3)
                                            <span style="font-weight:bold;">{{ __('custom.payee_name') }}</span>
                                        @endif
                                    </td>
                                    <td width="10px">
                                        <span style="font-weight:bold;">:</span>
                                    </td>
                                    <td>
                                        @if($masterdata->payeeTypeID == 1)
                                            {{$masterdata->customer->CustomerName}}
                                        @endif
                                        @if($masterdata->payeeTypeID == 2)
                                            {{$masterdata->employee->empName}}
                                        @endif
                                            @if($masterdata->payeeTypeID == 3)
                                            {{$masterdata->PayeeName}}
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if($masterdata->payeeTypeID)
                                <tr>
                                    <td width="150px">
                                        <span style="font-weight:bold;">{{ __('custom.doc_code') }}</span>
                                    </td>
                                    <td width="10px">
                                        <span style="font-weight:bold;">:</span>
                                    </td>
                                    <td>
                                        @if($masterdata->payeeTypeID == 1)
                                            {{ __('custom.customer') }}
                                        @endif
                                        @if($masterdata->payeeTypeID == 2)
                                            {{ __('custom.employee') }}
                                        @endif
                                        @if($masterdata->payeeTypeID == 3)
                                            {{ __('custom.other') }}
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        <tr>
                            <td width="50px">
                                <span style="font-weight:bold;">{{ __('custom.cheque_no') }}</span>
                            </td>
                            <td width="10px">
                                <span style="font-weight:bold;">:</span>
                            </td>
                            <td>
                                <span>{{$masterdata->custChequeNo}}</span>
                            </td>
                        </tr>
                        <tr>
                            <td width="50px">
                                <span style="font-weight:bold;">{{ __('custom.cheque_date') }}</span>
                            </td>
                            <td width="10px">
                                <span style="font-weight:bold;">:</span>
                            </td>
                            <td>
                                {{ \App\helper\Helper::dateFormat($masterdata->custChequeDate)}}
                            </td>
                        </tr>
                        @if($masterdata->isVATApplicable)
                            <tr>
                                <td width="70px">
                                    <span style="font-weight:bold;">{{ __('custom.vat_percentage') }} (%) </span>
                                </td>
                                <td width="10px">
                                    <span style="font-weight:bold;">:</span>
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
                        <tr>
                            <td width="150px">
                                <span style="font-weight:bold;">{{ __('custom.bank_name') }}</span>
                            </td>
                            <td width="10px">
                                <span style="font-weight:bold;">:</span>
                            </td>
                            <td>
                                @if($masterdata->bank)
                                    {{$masterdata->bank->bankName}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td width="150px">
                                <span style="font-weight:bold;">{{ __('custom.account_number') }}</span>
                            </td>
                            <td width="10px">
                                <span style="font-weight:bold;">:</span>
                            </td>
                            <td>
                                @if($masterdata->bank)
                                    {{$masterdata->bank->AccountNo}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td width="150px">
                                <span style="font-weight:bold;">{{ __('custom.bank_currency') }}</span>
                            </td>
                            <td width="10px">
                                <span style="font-weight:bold;">:</span>
                            </td>
                            <td>
                                @if($masterdata->bank_currency)
                                    {{$masterdata->bank_currency->CurrencyCode}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td width="150px">
                                <span style="font-weight:bold;">{{ __('custom.currency') }}</span>
                            </td>
                            <td width="10px">
                                <span style="font-weight:bold;">:</span>
                            </td>
                            <td>
                                @if($masterdata->currency)
                                    {{$masterdata->currency->CurrencyCode}}
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <table style="width: 100%">
            <tr style="width:100%">
                <td style="width: 100%">
                    <table>
                        <tr style="width: 100%">
                            <td style="vertical-align: top;">
                                <span style="font-weight:bold;">{{ __('custom.narration') }} :</span>
                            </td>
                        </tr>
                        <tr style="width: 100%">
                            <td style="vertical-align: top;">
                                @if ($masterdata->narration)
                                    {{$masterdata->narration}}
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
                        <th class="text-center">{{ __('custom.account_code') }}</th>
                        <th class="text-center">{{ __('custom.account_description') }}</th>
                    @endif
                    @if($masterdata->documentType == 14 && $isProjectBase)
                        <th colspan="3" class="text-center">{{ __('custom.project') }}</th>
                    @endif
                    <th class="text-center">{{ __('custom.department') }}</th>
                    @if($masterdata->documentType == 14)
                        <th class="text-center">{{ __('custom.contract') }}</th>
                    @endif
                    <th colspan="2" class="text-center">{{ __('custom.comments') }}</th>
                    <th class="text-center">{{ __('custom.amount') }}</th>
                    @if($masterdata->isVATApplicable)
                        <th class="text-center">{{ __('custom.vat_amount') }}</th>
                        <th class="text-center">{{ __('custom.net_amount') }}</th>
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
                        @if($masterdata->documentType == 14 && $isProjectBase)
                            <td colspan="3">
                                @if($item->project)
                                    {{$item->project->projectCode}} - {{$item->project->description}}
                                @endif
                            </td>
                        @endif
                        <td>  @if($item->segment)
                                {{$item->segment->ServiceLineDes}}
                            @endif
                        </td>
                        @if($masterdata->documentType == 14)
                            <td>{{$item->contractID}}</td>
                        @endif
                        <td colspan="2">{{$item->comments}}</td>
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
                    @if($masterdata->documentType == 14 && $isProjectBase)
                        <td colspan="3" class="text-right border-bottom-remov">&nbsp;</td>
                    @endif
                    @if($masterdata->documentType == 15)
                        <td colspan="2" class="text-right border-bottom-remov">&nbsp;</td>
                    @endif
                    <td colspan="2" class="text-right" style="background-color: rgb(215,215,215)">{{ __('custom.total_payment') }}</td>
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
                    <th class="text-center">{{ __('custom.sales_order_no') }}</th>
                    <th class="text-center">{{ __('custom.comments') }}</th>
                    @if($masterdata->isVATApplicable)
                        <th class="text-center">{{ __('custom.vat_amount') }}</th>
                    @endif
                    <th class="text-center">{{ __('custom.receive_amount') }}</th>
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
                        <td class="text-right" colspan="4" style="background-color: rgb(215,215,215)">{{ __('custom.total_payment') }}</td>
                    @endif
                    @if(!$masterdata->isVATApplicable)
                        <td class="text-right" colspan="3" style="background-color: rgb(215,215,215)">{{ __('custom.total_payment') }}</td>
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
                    <th class="text-center">{{ __('custom.invoice_code') }}</th>
                    <th class="text-center">{{ __('custom.invoice_date') }}</th>
                    <th class="text-center">{{ __('custom.comments') }}</th>
                    <th class="text-center">{{ __('custom.amount') }}</th>
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
                    <td class="text-right" style="background-color: rgb(215,215,215)">total{{ __('custom.doc_code') }}</td>
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
                    <th class="text-center">{{ __('custom.account_code') }}</th>
                    <th class="text-center">{{ __('custom.account_description') }}</th>
                    <th class="text-center">{{ __('custom.department') }}</th>
                    <th class="text-center">{{ __('custom.amount') }}</th>
                    <th class="text-center">{{ __('custom.local_amt') }} (
                        @if($masterdata->localCurrency)
                            {{$masterdata->localCurrency->CurrencyCode}}
                        @endif
                        )
                    </th>
                    <th class="text-center">{{ __('custom.rpt_amt') }} (
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
                    <td class="text-right" style="background-color: rgb(215,215,215)">{{ __('custom.total_payment') }}</td>
                    <td class="text-right"
                        style="background-color: rgb(215,215,215)">{{number_format($directTotTra, $transDecimal)}}</td>
                    <td class="text-right border-bottom-remov"></td>
                    <td class="text-right border-bottom-remov"></td>
                </tr>

                @if ($masterdata->details)
                    <tr>
                        <td class="text-right border-bottom-remov border-top-remov"></td>
                    </tr>
                    <tr>
                        <td colspan="5" class="text-right border-bottom-remov border-top-remov">&nbsp;</td>
                        <td class="text-right border-bottom-remov border-top-remov" style="font-size: 13px;" >{{ __('custom.net_total') }}</td>
                        <td class="text-right border-bottom-remov border-top-remov" style="font-size: 13px;" >{{number_format(($directTotTra + $ciDetailTotTra), $transDecimal)}}</td>
                        <td class="text-right border-bottom-remov border-top-remov"></td>
                        <td class="text-right border-bottom-remov border-top-remov"></td>
                    </tr>
                @endif
                
                </tbody>
            </table>
        </div>
    @endif


    @if($masterdata->pdcChequeYN == 1)
        <div style="margin-top: 30px">
            <table class="table table-bordered" style="width: 100%;">
                <thead>
                <tr class="theme-tr-head">
                    <th>#</th>
                    <th class="text-center">{{ __('custom.cheque_no') }}</th>
                    <th class="text-center">{{ __('custom.cheque_date') }}</th>
                    <th class="text-center">{{ __('custom.comment') }}</th>
                    <th class="text-center">{{ __('custom.amount') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($masterdata->pdc_cheque as $item)
                    <tr style="border-top: 1px solid #ffffff !important;border-bottom: 1px solid #ffffff !important;">
                        <td>{{$loop->iteration}}</td>
                        <td>{{$item->chequeNo}}</td>
                        <td>{{ \App\helper\Helper::dateFormat($item->chequeDate)}}</td>
                        <td>{{$item->comments}}</td>
                        <td class="text-right">{{number_format($item->amount, $transDecimal)}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
