<html @if(isset($lang) && $lang === 'ar') dir="rtl" @endif>
<head>
    <title>{{ __('custom.bank_reconciliation') }}</title>
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
            margin-bottom: 0px;
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

        h4 {
            font-size: 18px;
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
            font-size: 10px;
        }

        .theme-tr-head {
            background-color: #DEDEDE !important;
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
            padding: 0 0;
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
            padding: 5px !important;
        }

        table.table-bordered {
            border-collapse: collapse;
        }

        table.table-bordered, .table-bordered th, .table-bordered td {
            border: 1px solid black;
        }

        table > thead > tr > th {
            font-size: 11px;
        }

        hr {
            margin-top: 5px;
            margin-bottom: 5px;
            border: 0;
            border-top: 1px solid;
            color: #e2e3e5;
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
    </style>
</head>
<body>
<div class="footer">
    {{--Footer Page <span class="pagenum"></span>--}}
    <span class="white-space-pre-line font-weight-bold">{!! nl2br($entity->docRefNo) !!}</span>
    <div class="text-right" style="font-size: 10px">{{\App\helper\Helper::dateFormat($date) }}</div>
</div>
<div id="watermark"></div>
<div class="card-body content" id="print-section">
    <table style="width: 100%">
        <tr style="width:100%">
            <td style="width: 30%">
            </td>
            <td style="width: 30%;text-align: center">
            </td>
            <td style="width: 40%">
                <table>
                    <tr>
                        <td colspan="3">
                            @if($entity->company)
                                <h6 style="font-size: 18px"> {{$entity->company->CompanyName}}</h6>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <h6 style="font-size: 18px"> {{ __('custom.bank_reconciliation') }} </h6>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr style="width:100%">
            <td style="width: 30%">
                <img src="{{$entity->company->logo_url}}" width="180px" height="60px">
            </td>
            <td style="width: 30%;text-align: center">
            </td>
            <td style="width: 40%">
                <table>
                    {{-- <tr>
                         <td colspan="3">
                             @if($entity->company)
                                 <h6 style="font-size: 18px"> {{$entity->company->CompanyName}}</h6>
                             @endif
                         </td>
                     </tr>
                     <tr>
                         <td colspan="3">
                             <h6 style="font-size: 18px"> Bank Reconciliation </h6>
                         </td>
                     </tr>--}}
                    <tr>
                        <td>
                            <span style="font-weight: bold;">{{ __('custom.document_code') }}</span>
                        </td>
                        <td>
                            <span style="font-weight: bold;">:</span>
                        </td>
                        <td>
                            <span>{{$entity->bankRecPrimaryCode}}</span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span style="font-weight: bold;">{{ __('custom.currency') }}</span>
                        </td>
                        <td>
                            <span style="font-weight: bold;">:</span>
                        </td>
                        <td>
                            <span>
                                    @if($entity->bank_account)
                                    @if($entity->bank_account->currency)
                                        {{$entity->bank_account->currency->CurrencyCode}}
                                    @endif
                                @endif
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span style="font-weight: bold;">{{ __('custom.as_of') }}</span>
                        </td>
                        <td>
                            <span style="font-weight: bold;">:</span>
                        </td>
                        <td>
                            {{ \App\helper\Helper::dateFormat($entity->bankRecAsOf)}}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span style="font-weight: bold;">{{ __('custom.month') }}</span>
                        </td>
                        <td>
                            <span style="font-weight: bold;">:</span>
                        </td>
                        <td>
                            @if($entity->month_by)
                                {{$entity->month_by->monthDes}}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span style="font-weight: bold;">{{ __('custom.year') }}</span>
                        </td>
                        <td>
                            <span style="font-weight: bold;">:</span>
                        </td>
                        <td>
                            {{$entity->year}}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span style="font-weight: bold;">{{ __('custom.bank') }}</span>
                        <td>
                            <span style="font-weight: bold;">:</span>
                        </td>
                        <td>
                            @if($entity->bank_account)
                                {{$entity->bank_account->bankName}}
                                {{--@if($entity->bank_account->bankBranch)
                                    ({{$entity->bank_account->bankBranch}}) @endif--}}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span style="font-weight: bold;">{{ __('custom.account_no') }}</span>
                        <td>
                            <span style="font-weight: bold;">:</span>
                        </td>
                        <td>
                            @if($entity->bank_account)
                                {{$entity->bank_account->AccountNo}}
                            @endif
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <hr>
    <table width="100%">
        <tr width="100%">
            <td width="50%">
                {{ __('custom.bank_balance') }}
            </td>
            <td width="50%" class="text-right">
                <b>{{number_format($entity->closingBalance,$decimalPlaces)}}</b>
            </td>
        </tr>
    </table>
    <hr>
    <div style="color: #0F6AB4;margin-top: 10px;margin-bottom: 5px">{{ __('custom.uncleared_receipts') }}</div>
    <div>
        <table class="table table-bordered" style="width: 100%;">
            <thead>
            <tr class="theme-tr-head">
                <th></th>
                <th class="text-left">{{ __('custom.document_date') }}</th>
                <th class="text-left">{{ __('custom.document_code') }}</th>
                <th class="text-left">{{ __('custom.payee_name') }}</th>
                <th class="text-left">{{ __('custom.cheque_no') }}</th>
                <th class="text-right">{{ __('custom.amount') }}</th>
                <th class="text-left"></th>
            </tr>
            </thead>
            <tbody>
            @foreach ($entity->unClearedReceipt as $item)
                <tr style="border-top: 2px solid #333 !important;border-bottom: 2px solid #333 !important;">
                    <td>{{$loop->iteration}}</td>
                    <td> {{ \App\helper\Helper::dateFormat($item->documentDate)}}</td>
                    <td>{{$item->documentCode}}</td>
                    <td>{{$item->payeeName}}</td>
                    <td>{{$item->documentChequeNo}}</td>
                    <td class="text-right">{{number_format(($item->payAmountBank * -1),$decimalPlaces)}}</td>
                    <td></td>
                </tr>
            @endforeach
            <tr>
                <td colspan="5" class="text-right"><b>{{ __('custom.total_uncleared_receipts') }}</b></td>
                <td></td>
                <td class="text-right"><b>{{number_format(($entity->totalUnClearedReceipt * -1),$decimalPlaces)}}</b></td>
            </tr>
            </tbody>
        </table>
    </div>
    <div style="color: #0F6AB4;margin-top: 10px;margin-bottom: 5px"> {{ __('custom.uncleared_payments') }}</div>
    <div >
        <table class="table table-bordered" style="width: 100%;">
            <thead>
            <tr class="theme-tr-head">
                <th></th>
                <th class="text-left">{{ __('custom.document_date') }}</th>
                <th class="text-left">{{ __('custom.document_code') }}</th>
                <th class="text-left">{{ __('custom.payee_name') }}</th>
                <th class="text-left">{{ __('custom.cheque_no') }}</th>
                <th class="text-right">{{ __('custom.amount') }}</th>
                <th class="text-left"></th>
            </tr>
            </thead>
            <tbody>
            @foreach ($entity->unClearedPayment as $item)
                <tr style="border-top: 2px solid #333 !important;border-bottom: 2px solid #333 !important;">
                    <td>{{$loop->iteration}}</td>
                    <td> {{ \App\helper\Helper::dateFormat($item->documentDate)}}</td>
                    <td>{{$item->documentCode}}</td>
                    <td>{{$item->payeeName}}</td>
                    <td>{{$item->documentChequeNo}}</td>
                    <td class="text-right">{{number_format(($item->payAmountBank),$decimalPlaces)}}</td>
                    <td></td>
                </tr>
            @endforeach
            <tr>
                <td colspan="5" class="text-right"><b>{{ __('custom.total') }} {{ __('custom.uncleared_payments') }}</b></td>
                <td></td>
                <td class="text-right"><b>{{number_format($entity->totalUnClearedPayment,$decimalPlaces)}}</b></td>
            </tr>
            <tr>
                <td colspan="5" class="text-right"><b>{{ __('custom.book_balance') }}</b></td>
                <td></td>
                <td class="text-right"><b>{{number_format($entity->bookBalance,$decimalPlaces)}}</b></td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="row" style="margin-top: 10px;margin-left: -8px">
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
                    <table>
                        <tr>
                            <td width="70px">
                                <span style="font-weight: bold;">{{ __('custom.reviewed_by') }} :</span>
                            </td>
                            <td>
                                <div style="border-bottom: 1px solid black;width: 200px;margin-top: 7px;"></div>
                            </td>
                        </tr>
                    </table>
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
                                {{ \App\helper\Helper::dateFormat($det->approvedDate)}}
                            @elseif($det->rejectedYN == -1)
                                {{ \App\helper\Helper::dateFormat($det->rejectedDate)}}
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
