<html @if(isset($lang) && $lang === 'ar') dir="rtl" @endif>
<head>
    <title>{{ __('custom.debit_note_voucher') }}</title>
    <style>
        @page {
            margin-left: 30px;
            margin-right: 30px;
            margin-top: 30px;
            margin-bottom: 0px;
        }

        body {
            font-size: 12px;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
        }

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
            background-color: #ffffff !important;
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

        tr td {
            padding: 5px 0;
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

        .header,
        .footer {
            width: 100%;
            text-align: left;
            position: fixed;
        }

        .header {
            top: 0px;
        }

        .footer {
            bottom: 40px;
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
        .border-top-remov{
            border-top: 1px solid #ffffff00 !important;
            border-left: 1px solid #ffffff00 !important;
            background-color: #ffffff !important;
            border-right: 0;
        }
        .border-bottom-remov{
            border-bottom: 1px solid #ffffffff !important;
            background-color: #ffffff !important;
            border-right:  1px solid #ffffffff !important;
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
    {{--Footer Page <span class="pagenum"></span>--}}
    <span class="white-space-pre-line font-weight-bold">{!! nl2br($entity->docRefNo) !!}</span>
</div>
<div id="watermark"></div>
<div class="card-body content" id="print-section">

    <table style="width: 100%" class="table_height">
       {{-- <tr style="width: 100%">
            <td colspan="3" style="bottom: 0;position: absolute;text-align: right">
            --}}{{--<span style="font-weight: bold;">
                <h3 class="text-muted">
                    @if($entity->confirmedYN == 0 && $entity->approved == 0)
                       {{ __('custom.not_confirmed') }}
                    @elseif($entity->confirmedYN == 1 && $entity->approved == 0)
                       {{ __('custom.pending_approval') }}
                    @elseif($entity->confirmedYN == 1 && ($entity->approved == 1 ||  $entity->approved == -1))
                        {{ __('custom.fully_approved') }}
                    @endif
                    </h3>
`             </span>--}}{{--
            </td>
        </tr>--}}
        <tr style="width: 100%">
            <td valign="top" style="width: 20%">
                @if($entity->company)
                <img src="{{$entity->company->logo_url}}" width="180px" height="60px" class="container">
                @endif
            </td>
            <td  valign="top" style="width: 80%">
                @if($entity->company)
                    <span style="font-size: 24px;font-weight: 400"> {{$entity->company->CompanyName}}</span>
                @endif
                <br>
                    <table>
                        <tr>
                            <td width="100px">
                                <span style="font-weight: bold;">{{ __('custom.doc_code') }}</span>
                            </td>
                            <td width="10px">
                                <span style="font-weight: bold;">:</span>
                            </td>
                            <td>
                                <span>{{$entity->debitNoteCode}}</span>
                            </td>
                        </tr>
                        <tr>
                            <td width="70px">
                                <span style="font-weight: bold;">{{ __('custom.doc_date') }}</span>
                            </td>
                            <td width="10px">
                                <span style="font-weight: bold;">:</span>
                            </td>
                            <td>
                            <span>
                                {{ \App\helper\Helper::dateFormat($entity->debitNoteDate)}}
                            </span>
                            </td>
                        </tr>
                    </table>
            </td>
        </tr>
    </table>
    <hr style="color: #d3d9df">
    <div>
        <span style="font-size: 18px">{{ __('custom.debit_note_voucher') }}</span>
    </div>
    <br>
    <br>
    <table style="width: 100%">
        <tr style="width:100%">
            <td style="width: 80%">
                <table>
                    <tr>
                       @if($entity->type == 1)
                        <td width="150px">
                            <span style="font-weight: bold;">{{ __('custom.supplier_code') }}</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight: bold;">:</span>
                        </td>
                        <td>
                            @if($entity->supplier)
                                {{$entity->supplier->primarySupplierCode}}
                            @endif
                        </td>
                        @endif

                        @if($entity->type == 2)
                        <td width="150px">
                            <span style="font-weight: bold;">{{ __('custom.employee_code') }}</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight: bold;">:</span>
                        </td>
                        <td>
                            @if($entity->employee)
                                {{$entity->employee->empID}}
                            @endif
                        </td>
                        @endif
                    </tr>
                    <tr>
                       @if($entity->type == 1)
                        <td width="50px">
                            <span style="font-weight: bold;">{{ __('custom.supplier_name') }}</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight: bold;">:</span>
                        </td>
                        <td>
                            @if($entity->supplier)
                                {{$entity->supplier->supplierName}}
                            @endif
                        </td>
                        @endif

                        @if($entity->type == 2)
                        <td width="50px">
                            <span style="font-weight: bold;">{{ __('custom.employee_name') }}</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight: bold;">:</span>
                        </td>
                        <td>
                            @if($entity->employee)
                                {{$entity->employee->empName}}
                            @endif
                        </td>
                        @endif
                    </tr>
                    <tr>
                        <td width="50px">
                            <span style="font-weight: bold;">{{ __('custom.invoice_number') }}</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight: bold;">:</span>
                        </td>
                        <td>
                            {{$entity->invoiceNumber}}
                        </td>
                    </tr>
                    <tr>
                        <td width="50px">
                            <span style="font-weight: bold;">{{ __('custom.reference_number') }}</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight: bold;">:</span>
                        </td>
                        <td>
                            {{$entity->referenceNumber}}
                        </td>
                    </tr>
                    <tr>
                        <td width="70px">
                            <span style="font-weight: bold;">{{ __('custom.narration') }} </span>
                        </td>
                        <td width="10px">
                            <span style="font-weight: bold;">:</span>
                        </td>
                        <td>
                            <span>{{$entity->comments}}</span>
                        </td>
                    </tr>
                    @if($entity->isVATApplicable)
                    <tr>
                        <td width="70px">
                            <span style="font-weight: bold;">{{ __('custom.vat_percentage') }} (%) </span>
                        </td>
                        <td width="10px">
                            <span style="font-weight: bold;">:</span>
                        </td>
                        <td>
                            <span>{{$entity->VATPercentage}}</span>
                        </td>
                    </tr>
                    @endif
                </table>
            </td>
            <td style="width: 20%" valign="bottom" class="text-right">
                <span style="font-weight: bold;"> {{ __('custom.currency') }}:</span>
                @if($entity->transactioncurrency)
                    {{$entity->transactioncurrency->CurrencyCode}}
                @endif
            </td>
        </tr>
    </table>
    {{--<hr>--}}
    <div style="margin-top: 30px">
        <table class="table table-bordered" style="width: 100%;">
            <thead>
            <tr class="theme-tr-head">
                <th></th>
                <th class="text-center">{{ __('custom.gl_code') }}</th>
                <th class="text-center">{{ __('custom.gl_code_description') }}</th>
                <th class="text-center">{{ __('custom.segment') }}</th>
                <th class="text-center">{{ __('custom.amount') }}</th>
                @if($entity->isVATApplicable)
                    <th class="text-center">{{ __('custom.vat_amount') }}</th>
                    <th class="text-center">{{ __('custom.net_amount') }}</th>
                @endif
                {{--<th class="text-center">{{ __('custom.amount_local') }} (
                    @if($entity->localcurrency)
                        {{$entity->localcurrency->CurrencyCode}}
                    @endif
                    )</th>
                <th class="text-center">{{ __('custom.amount_rpt') }} (@if($entity->rptcurrency)
                        {{$entity->rptcurrency->CurrencyCode}}
                    @endif)</th>--}}
            </tr>
            </thead>
            <tbody>
            @foreach ($entity->detail as $item)
                <tr style="border-top: 1px solid #ffffff !important;border-bottom: 1px solid #ffffff !important;">
                    <td>{{$loop->iteration}}</td>
                    <td>{{$item->glCode}}</td>
                    <td>{{$item->glCodeDes}}</td>
                    <td>
                        @if($item->segment)
                            {{$item->segment->ServiceLineDes}}
                        @endif
                    </td>
                    <td class="text-right">{{round($item->debitAmount,$entity->transDecimal)}}</td>
                    @if($entity->isVATApplicable)
                        <td class="text-right">{{round($item->VATAmount,$entity->transDecimal)}}</td>
                        <td class="text-right">{{round($item->netAmount,$entity->transDecimal)}}</td>
                    @endif
                    {{--<td class="text-right">{{round($item->localAmount,$entity->localDecimal)}}</td>
                    <td class="text-right">{{round($item->comRptAmount,$entity->rptDecimal)}}</td>--}}
                </tr>
            @endforeach
            <tr style="border-top: 1px solid #333 !important;border-bottom: 1px solid #333 !important;">
                <td class="text-right"></td>
                <td class="text-right"></td>
                <td class="text-right"></td>
                <td class="text-right" style="background-color: #DEDEDE !important; border-bottom: 1px solid #333 !important;"><b>{{ __('custom.total_payment') }}:</b></td>
                <td class="text-right">{{round($entity->totalAmount,$entity->transDecimal)}}</td>
                @if($entity->isVATApplicable)
                    <td class="text-right">{{round($entity->totalVATAmount,$entity->transDecimal)}}</td>
                    <td class="text-right">{{round($entity->totalNetAmount,$entity->transDecimal)}}</td>
                @endif
               {{-- <td class="text-right border-bottom-remov"></td>
                <td class="text-right border-bottom-remov"></td>--}}
            </tr>
            </tbody>
        </table>
    </div>
    {{--<hr>--}}
    <div class="row" style="margin-top: 60px;margin-left: -8px">
        <table>
            <tr width="100%">
                <td width="60%">
                    <table width="100%">
                        <tr>
                            <td width="70px">
                                <span style="font-weight: bold;">{{ __('custom.confirmed_by') }} :</span>
                            </td>
                            <td width="400px">
                                @if($entity->confirmed_by)
                                    {{$entity->confirmed_by->empName}}
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>
                <td width="10%">

                </td>
                <td width="30%">
                </td>
            </tr>
        </table>
    </div>
    <div class="row" style="margin-top: 10px">
        <span style="font-weight: bold;">{{ __('custom.electronically_approved_by') }} :</span>
    </div>
    <div style="margin-top: 10px">
        <table>
            <tr>
                @foreach ($entity->approved_by as $det)
                    <td style="padding-right: 25px">
                        @if($det->employee)
                            {{$det->employee->empFullName }}
                            @if($det->employee->details)
                                @if($det->employee->details->designation)
                                    <br>{{$det->employee->details->designation->designation}}
                                @endif
                            @endif
                        @endif
                        <br>
                        @if($det->employee)
                            @if($det->approvedYN == -1)
                                {{ \App\helper\Helper::convertDateWithTime($det->approvedDate)}}
                            @elseif($det->rejectedYN == -1)
                                {{ \App\helper\Helper::convertDateWithTime($det->rejectedDate)}}
                            @endif
                        @endif
                    </td>
                @endforeach
            </tr>
        </table>
    </div>
</div>
</body>
</html>
