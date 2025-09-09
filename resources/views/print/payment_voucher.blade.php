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

        /* RTL Support for Arabic */
        @if(app()->getLocale() == 'ar')
        body {
            direction: rtl;
            text-align: right;
        }
        
        .rtl-text-left {
            text-align: right !important;
        }
        
        .rtl-text-right {
            text-align: left !important;
        }
        
        .rtl-float-left {
            float: right !important;
        }
        
        .rtl-float-right {
            float: left !important;
        }
        
        .rtl-margin-left {
            margin-right: 0 !important;
            margin-left: auto !important;
        }
        
        .rtl-margin-right {
            margin-left: 0 !important;
            margin-right: auto !important;
        }
        
        .rtl-padding-left {
            padding-right: 0 !important;
            padding-left: auto !important;
        }
        
        .rtl-padding-right {
            padding-left: 0 !important;
            padding-right: auto !important;
        }
        
        table {
            direction: rtl;
        }
        
        .table th, .table td {
            text-align: right;
        }
        
        .text-right {
            text-align: left !important;
        }
        
        .text-left {
            text-align: right !important;
        }
        @endif

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
                            <span style="font-weight: bold">{{ __('custom.doc_code') }}</span>
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
                            <span style="font-weight: bold">{{ __('custom.doc_date') }}</span>
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
                            <span style="font-weight: bold">{{ __('custom.payment_mode') }}</span>
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
    <hr style="color: #d3d9df border-top: 2px solid black; height: 2px; color: black">
    <div>
        <span style="font-size: 18px">
            @if($masterdata->invoiceType == 2)
                 {{ __('custom.supplier_payment') }}
            @endif
            @if($masterdata->invoiceType == 3)
                    {{ __('custom.direct_payment') }}
            @endif
            @if($masterdata->invoiceType == 5)
                    {{ __('custom.supplier_advance_payment') }}
            @endif
            @if($masterdata->invoiceType == 6)
                    {{ __('custom.employee_payment') }}
                @endif
            @if($masterdata->invoiceType == 7)
                 {{ __('custom.employee_advance_payment') }}
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
                                <span style="font-weight: bold"> {{ __('custom.payee_code') }}</span>
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
                                <span style="font-weight: bold">{{ __('custom.employee_name') }}</span>
                            @endif
                            @if($masterdata->invoiceType != 6 && $masterdata->invoiceType != 7)
                                <span style="font-weight: bold">{{ __('custom.payee_name') }}</span>
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
                            <span style="font-weight: bold">{{ __('custom.bank_name') }}</span>
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
                            <span style="font-weight: bold">{{ __('custom.bank') }}</span>
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
                            <span style="font-weight: bold">{{ __('custom.cheque_no') }}</span>
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
                            <span style="font-weight: bold">{{ __('custom.cheque_date') }}</span>
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
                            <span style="font-weight: bold">{{ __('custom.narration') }} </span>
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
                        <td valign="bottom" style="text-align: right">
                            <span style="font-weight: bold"> {{ __('custom.currency') }}:</span>
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
                    <th style="text-align: center">{{ __('custom.booking_inv_code') }}</th>
                    <th style="text-align: center">{{ __('custom.po_number') }}</th>
                    <th style="text-align: center">{{ __('custom.supplier_invoice_no') }}</th>
                    <th style="text-align: center">{{ __('custom.invoice_date') }}</th>
                    <th style="text-align: center">{{ __('custom.invoice_amount') }}</th>
                    <th style="text-align: center">{{ __('custom.amount_paid') }}</th>
                    <th style="text-align: center">{{ __('custom.balance') }}</th>
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
                @if ($bankChargeCount == 0)
                    <tr style="border-top: 1px solid #333 !important;border-bottom: 1px solid #333 !important;">
                        <td colspan="5" style="border-bottom: 1px solid #ffffffff; background-color:#ffffff; border-right: 1px solid #ffffffff">&nbsp;</td>
                        <td style="text-align: right" style="background-color: rgb(215,215,215)">{{ __('custom.total_payment') }}</td>
                        <td style="text-align: right"
                            style="background-color: rgb(215,215,215)">{{number_format($supplierdetailTotTra, $transDecimal)}}</td>
                        <td style="border-bottom: 1px solid #ffffffff; background-color:#ffffff; border-right: 1px solid #ffffffff"></td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>

        @if ($masterdata->invoiceType == 2 && $bankChargeCount > 0)
        <div style="margin-top: 30px">
            <h4>{{ __('custom.bank_charges_others') }}</h4>
            <table class="table table-bordered" style="width: 100%;">
                <thead>
                <tr class="theme-tr-head">
                    <th>#</th>
                    <th style="text-align: center">{{ __('custom.gl_account') }}</th>
                    <th style="text-align: center">{{ __('custom.segment') }}</th>
                    <th style="text-align: center">{{ __('custom.comments') }}</th>
                    <th style="text-align: center">{{ __('custom.amount') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($masterdata->bank_charge as $bank_charge)
                    <tr style="border-top: 1px solid #ffffff !important;border-bottom: 1px solid #ffffff !important;">
                        <td>{{$loop->iteration}}</td>
                        <td>{{$bank_charge->glCode}} | {{$bank_charge->glCodeDescription}}</td>
                        <td>{{$bank_charge->segment->ServiceLineDes}}</td>
                        <td>{{$bank_charge->comment}}</td>
                        <td style="text-align: right">{{number_format($bank_charge->dpAmount, $transDecimal)}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @endif

        @if ($bankChargeCount > 0)
            <div style="margin-top: 30px">
                <table class="table" style="width: 25%; margin-left: auto; margin-right: 0;">
                    <tbody>
                        <tr style="border-top: 1px solid #333 !important; border-bottom: 1px solid #333 !important;">
                            <td colspan="2" style="text-align: right; background-color: rgb(215,215,215);">
                                {{ __('custom.total_payment') }} : {{ number_format($supplierdetailTotTra + $bankChargeAndOthersTot, $transDecimal) }}
                            </td>
                        </tr>
                    </tbody>
                </table>            
            </div>
        @endif
        <br>
    @endif
    @if($masterdata->invoiceType == 3)
        <div style="margin-top: 30px">
            <table class="table table-bordered" style="width: 100%;">
                <thead>
                <tr class="theme-tr-head">
                    <th>#</th>
                    <th style="text-align: center">{{ __('custom.gl_code') }}</th>
                    <th style="text-align: center">{{ __('custom.gl_code_description') }}</th>
                    @if($masterdata->invoiceType == 3 && $isProjectBase)
                        <th colspan="4" style="text-align: center">{{ __('custom.project') }}</th>
                    @endif
                    <th style="text-align: center">{{ __('custom.segment') }}</th>
                    <th style="text-align: center">{{ __('custom.amount') }}</th>
                    @if($masterdata->invoiceType == 3 && $masterdata->expenseClaimOrPettyCash != 15)
                    <th style="text-align: center">{{ __('custom.vat') }}</th>
                    @endif
                    <th style="text-align: center">{{ __('custom.payment_amount') }}</th>
                    <th style="text-align: center">{{ __('custom.local_amt') }} (
                        @if($masterdata->localCurrency)
                            {{$masterdata->localCurrency->CurrencyCode}}
                        @endif
                        )
                    </th>
                    <th style="text-align: center">{{ __('custom.rpt_amt') }} (
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
                        @if($masterdata->invoiceType == 3 && $masterdata->expenseClaimOrPettyCash != 15)
                        <td style="text-align: right">{{number_format($item->vatAmount, $transDecimal)}}</td>
                        @endif
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
                    <td colspan="{{ ($masterdata->invoiceType == 3 && $masterdata->expenseClaimOrPettyCash != 15) ? 5 : 4 }}" style="border-bottom: 1px solid #ffffffff; background-color:#ffffff; border-right: 1px solid #ffffffff">&nbsp;</td>
                    <td style="text-align: right" style="background-color: rgb(215,215,215)">{{ __('custom.total_payment') }}</td>
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
                        <th style="text-align: center">{{ __('custom.purchase_order_no') }}</th>
                    @endif
                    <th style="text-align: center">{{ __('custom.comment') }}</th>
                    <th style="text-align: center">{{ __('custom.total_payment') }}</th>
                    <th style="text-align: center">{{ __('custom.local_amt') }} (
                        @if($masterdata->localCurrency)
                            {{$masterdata->localCurrency->CurrencyCode}}
                        @endif
                        )
                    </th>
                    <th style="text-align: center">{{ __('custom.rpt_amt') }} (
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
                    <td style="text-align: right" style="background-color: rgb(215,215,215)">{{ __('custom.total_payment') }}</td>
                    <td style="text-align: right"
                        style="background-color: rgb(215,215,215)">{{number_format($advancePayDetailTotTra, $transDecimal)}}</td>
                    <td style="border-bottom: 1px solid #ffffffff; background-color:#ffffff; border-right: 1px solid #ffffffff"></td>
                    <td style="border-bottom: 1px solid #ffffffff; background-color:#ffffff; border-right: 1px solid #ffffffff"></td>
                </tr>
                </tbody>
            </table>
        </div>
    @endif

    @if($masterdata->pdcChequeYN == 1)
        <div style="margin-top: 30px">
            <table class="table table-bordered" style="width: 100%;">
                <thead>
                <tr class="theme-tr-head">
                    <th style="text-align: center">{{ __('custom.cheque_no') }}</th>
                    <th style="text-align: center">{{ __('custom.cheque_date') }}</th>
                    <th style="text-align: center">{{ __('custom.comment') }}</th>
                    <th style="text-align: center">{{ __('custom.amount') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($masterdata->pdc_cheque as $pdc_cheque)
                    <tr style="border-top: 1px solid #ffffff !important;border-bottom: 1px solid #ffffff !important;">
                        <td>{{$pdc_cheque->chequeNo}}</td>
                        <td>{{ \App\helper\Helper::dateFormat($pdc_cheque->chequeDate)}}</td>
                        <td>{{$pdc_cheque->comments}}</td>
                        <td style="text-align: right">{{number_format($pdc_cheque->amount, $transDecimal)}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div style="padding-bottom: 20px!important; padding-top: 35px!important; page-break-inside: avoid; !important;">
    <table style="width:100%;">
        <tr>
            <td width="40%"><span style="font-weight: bold">{{ __('custom.confirmed_by') }} :</span> {{ $masterdata->confirmed_by? $masterdata->confirmed_by->empFullName:'' }}
            </td>
            <td><span style="font-weight: bold">{{ __('custom.review_by') }} :</span></td>
        </tr>
        <tr>
            <td><span style="font-weight: bold">{{ __('custom.electronically_approved_by') }}:</span>
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
