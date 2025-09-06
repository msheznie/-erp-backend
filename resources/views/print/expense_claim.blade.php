<html @if(isset($lang) && $lang === 'ar') dir="rtl" @endif>
<head>
    <title>{{ __('custom.expense_claim') }}</title>
    <style>
        @page {
            margin-left: 30px;
            margin-right: 30px;
            margin-top: 30px;
            margin-bottom: 0px;
        }

        /* RTL Support for Arabic */
        @if(isset($lang) && $lang === 'ar')
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
            border: 1px solid black;
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
            border-left:  1px solid #ffffffff !important;
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
        <tr style="width: 100%">
            <td valign="top" style="width: 20%">
                @if($entity->company)
                    <img src="{{$entity->company->logo_url}}" width="180px" height="60px" class="container">
                @endif
            </td>
            <td valign="top" style="width: 80%">
                @if($entity->company)
                    <span style="font-size: 24px;font-weight: 400"> {{$entity->company->CompanyName}}</span>
                @endif
                <br>
                <table>
                    <tr>
                        <td width="100px">
                            <span class="font-weight-bold">{{ __('custom.document_code') }}</span>
                        </td>
                        <td width="10px">
                            <span class="font-weight-bold">:</span>
                        </td>
                        <td>
                            <span>{{$entity->expenseClaimCode}}</span>
                        </td>
                    </tr>
                    <tr>
                        <td width="70px">
                            <span class="font-weight-bold">{{ __('custom.document_date') }} </span>
                        </td>
                        <td width="10px">
                            <span class="font-weight-bold">:</span>
                        </td>
                        <td>
                            <span>
                                {{ \App\helper\Helper::dateFormat($entity->expenseClaimDate)}}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td width="100px">
                            <span class="font-weight-bold">{{ __('custom.comments') }}</span>
                        </td>
                        <td width="10px">
                            <span class="font-weight-bold">:</span>
                        </td>
                        <td>
                            <span>{{$entity->comments}}</span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <hr style="color: #d3d9df">
    <div>
        <span style="font-size: 18px">
            {{ __('custom.expense_claim') }}
        </span>
    </div>
    <br>
    <br>
    <div style="margin-top: 30px">
        <table class="table table-bordered" style="width: 100%;">
            <thead>
            <tr class="theme-tr-head">
                <th style="width:3%">#</th>
                <th style="width:27%">{{ __('custom.gl_description') }}</th>
                <th style="width:27%">{{ __('custom.description') }}</th>
                <th style="width:7%">{{ __('custom.currency') }}</th>
                <th style="width:8%" class="text-center">{{ __('custom.amount') }}</th>
                <th style="width:8%" class="text-center">{{ __('custom.local_amount') }}({{$entity->localCurrencyCode}})
                </th>
            </tr>
            </thead>
            <tbody>
            @foreach ($entity->details as $item)
                <tr style="border-top: 2px solid #333 !important;border-bottom: 2px solid #333 !important;">
                    <td>{{$loop->iteration}}</td>
                    <td>
                        {{$item->category->glCode .' - '. $item->category->glCodeDescription}}
                    </td>
                    <td>{{$item->description}}</td>
                    <td>
                        @if($item->currency)
                            {{$item->currency->CurrencyCode}}
                        @endif
                    </td>
                    <td class="text-right">
                        {{ number_format($item->transactionAmount,$item->currencyDecimal)}}
                    </td>
                    <td class="text-right">
                        {{number_format($item->companyLocalAmount,$item->localDecimal)}}

                    </td>
                </tr>
            @endforeach
            <tr style="border-top: 2px solid #333 !important;border-bottom: 2px solid #333 !important;">
                <td colspan="3" class="text-right border-bottom-remov"></td>
                <td colspan="2" class="text-right" style="background-color: #DEDEDE !important;"><b>{{ __('custom.total_payment') }}:</b></td>
                <td class="text-right" style="background-color: #DEDEDE !important;">
                    {{number_format($entity->total,$entity->localDecimal)}}
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    {{--<hr>--}}
    <div class="row" style="margin-top: 10px;margin-left: -8px">
        <table width="100%">
            <tr width="100%">
                <td width="30%">
                    <table width="100%">
                        <tr width="100%">
                            <td width="70px">
                                <span class="font-weight-bold">{{ __('custom.claimed_by') }} :</span>
                            </td>
                            <td>
                                @if($entity->confirmed_by)
                                    {{$entity->confirmed_by->empFullName}}
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>
                <td width="5%">
                    &nbsp;
                </td>
                <td width="30%">
                    <table width="100%">
                        <tr width="100%">
                            <td width="75px">
                                <span class="font-weight-bold">{{ __('custom.checked_by') }} :</span>
                            </td>
                            <td>
                                <div style="border-bottom: 1px solid black;width:200px;margin-top: 7px;"></div>
                            </td>
                        </tr>
                    </table>
                </td>
                <td width="5%">
                    &nbsp;
                </td>
                <td width="30%">
                    <table width="100%">
                        <tr width="100%">
                            <td width="80px">
                                <span class="font-weight-bold" style="">{{ __('custom.approved_by') }} :</span>
                            </td>
                            <td style="padding-left: 2px" valign="top">
                                <br><br>
                                @foreach ($entity->approved_by as $det)
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
                                @endforeach

                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</div>
</body>
</html>