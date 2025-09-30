<html>
<head>
    <title>{{ __('custom.budget_transfer') }}</title>
    <style>
        @page {
            margin-left: 30px;
            margin-right: 30px;
            margin-top: 30px;
            margin-bottom: 0px;
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
            padding-left: 6px !important;
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
        #watermark { position: fixed; bottom: 0px; right: 0px; width: 200px; height: 200px; opacity: .1; }
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
            border-bottom: 1px solid #ffffff00 !important;
            border-left: 1px solid #ffffff00 !important;
            background-color: #ffffff !important;
            border-right: 0;
        }

        .border-right-dif{
         border-right: 1px solid #ffffffff !important;
        }

        .border-bottom{
             border-bottom: 1px solid #ffffff00 !important;
        }

        .tbody td {
            padding-left: 6px;
        }

    </style>
</head>
<body>
<div id="watermark"></div>
<div class="card-body content" id="print-section">

    <table style="width: 100%" class="table_height">
        <tr style="width: 100%">
            <td valign="top" style="width: 20%">
                @if($budget->company)
                    <img src="{{$budget->company->logo_url}}" width="100" class="container">
                @endif
            </td>
            <td valign="top" style="width: 80%">
                
                <span style="font-size: 24px;font-weight: 400"> {{ $budget->company?$budget->company->CompanyName:'' }}</span>
                <br>
                <span style="font-weight: bold"> {{ $budget->company?$budget->company->CompanyAddress:'' }}</span>
                <br>
            </td>
        </tr>
    </table>
    <hr style="color: #d3d9df border-top: 2px solid black; height: 2px; color: black">
    
    <table style="width: 100%" class="table_height">
        <tr style="width: 100%">
            <td style="text-align: center; font-weight: bold">
                <div>
                    <span style="font-size: 18px">
                        {{ __('custom.budget_transfer') }}
                    </span>
                </div>
            </td>
        </tr>
    </table>

    <table style="width: 100%">
        <tr style="width:100%">
            <td style="width: 50%">
                <table>

                    <tr>
                        <td width="125px">
                            <span style="font-weight:bold;">{{ __('custom.document_code') }}</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight:bold;">:</span>
                        </td>
                        <td>
                            {{ $budget->transferVoucherNo?$budget->transferVoucherNo:'-' }}
                        </td>
                    </tr>

                    <tr>
                        <td width="125px">
                            <span style="font-weight:bold;">{{ __('custom.comments') }}</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight:bold;">:</span>
                        </td>
                        <td>
                            <span>
                                {{ $budget->comments?$budget->comments:'-' }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td width="125px">
                            <span style="font-weight:bold;">{{ __('custom.finance_years') }}</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight:bold;">:</span>
                        </td>
                         <td>
                            @foreach ($budget->years as $year)
                                @if ($year->companyFinanceYearID === $budget->companyFinanceYearID)
                                     {{ $year->bigginingDate }} - {{  $year->endingDate }} 
                                @endif
                            @endforeach
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width: 50%">
                <table>
                    <tr>
                        <td width="125px">
                            <span style="font-weight:bold;">{{ __('custom.template') }} </span>
                        </td>
                        <td width="10px">
                            <span style="font-weight:bold;">:</span>
                        </td>
                        <td>
                            @foreach ($budget->template as $template)
                                @if ($template->companyReportTemplateID === $budget->templatesMasterAutoID)
                                    {{ $template->description }}
                                @endif
                            @endforeach
                        </td>
                    </tr>

                    <tr>
                        <td width="125px">
                            <span style="font-weight:bold;">{{ __('custom.created_date') }} </span>
                        </td>
                        <td width="10px">
                            <span style="font-weight:bold;">:</span>
                        </td>
                        <td>
                            <span>
                                {{ \App\helper\Helper::dateFormat($budget->createdDateTime)}}
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <td width="125px">
                            <span style="font-weight:bold;">{{ __('custom.created_by') }} </span>
                        </td>
                        <td width="10px">
                            <span style="font-weight:bold;">:</span>
                        </td>
                        <td>
                            @if($budget->created_by)
                                 {{ $budget->created_by->empName }}
                            @else
                               - -
                            @endif
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    {{--<hr>--}}
    <div style="margin-top: 30px">
        <table class="table table-bordered" style="width: 100%;">
            <thead>
                <tr style="background-color: #e9e3ec;">
                    <th colspan="3" class="text-center border-right-dif border-bottom" style="border-bottom: 1px solid #ffffff00 !important;">
                       {{ __('custom.from') }}
                    </th>
                    <th colspan="3" class="text-center border-right-dif" style="border-bottom: 1px solid #ffffff00 !important;">
                         {{ __('custom.to') }}</th>
                     <th colspan="3" class="text-center border-right-dif" style="border-bottom: 1px solid #ffffff00 !important;">
                         {{ __('custom.adjustment') }}</th>
                </tr>
                 <tr style="background-color: #e9e3ec;">
                    <th style="width:15%;border-bottom: 1px solid #ffffff00 !important" >{{ __('custom.template_description') }}</th>
                    <th style="width:15%;border-bottom: 1px solid #ffffff00 !important">{{ __('custom.gl_account') }}</th>
                    <th style="width:15%;border-bottom: 1px solid #ffffff00 !important" class="border-right-dif">
                        {{ __('custom.department') }}</th>
                    <th style="width:15%;border-bottom: 1px solid #ffffff00 !important">{{ __('custom.template_description') }}</th>
                    <th style="width:15%;border-bottom: 1px solid #ffffff00 !important">{{ __('custom.gl_account') }}</th>
                    <th style="width:15%;border-bottom: 1px solid #ffffff00 !important" class="border-right-dif">
                        {{ __('custom.department') }}</th>
                    <th style="width:20%;border-bottom: 1px solid #ffffff00 !important ">{{ __('custom.comments') }}</th>
                    <th style="width:20%;border-bottom: 1px solid #ffffff00 !important ">{{ __('custom.currency') }}</th>
                    <th style="width:20%;border-bottom: 1px solid #ffffff00 !important">{{ __('custom.amount') }}
                    </th>
                </tr>
            </thead>
            <tbody class="tbody">
                @php
                    $sum = 0;
                @endphp
                @foreach ($budget->detail as $item)
                  @php
                    $sum += floatval($item->adjustmentAmountRpt);
                 @endphp
                <tr style="border-top: 2px solid #333 !important;border-bottom: 2px solid #333 !important;">
                    <td style="padding-left: 6px">
                        @if($item->isFromContingency == 1)
                            {{ $item->contingency->contingencyBudgetNo ?? '' }} &nbsp; | &nbsp; {{ $item->contingency->comments ?? '' }}
                        @else
                            {{ $item->from_template->description ?? '' }}
                        @endif
                    </td>
                     <td style="padding-left: 6px">
                        @if($item->isFromContingency != 1)
                            {{ $item->FromGLCode }} | {{ $item->FromGLCodeDescription }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="border-right-dif" style="padding-left: 6px">
                        {{ $item->from_segment->ServiceLineDes ?? '' }}
                    </td>
                    <td style="padding-left: 6px">
                        {{ $item->to_template->description ?? '' }}
                    </td>
                    <td style="padding-left: 6px">
                        {{ $item->toGLCode }} | {{ $item->toGLCodeDescription }}
                    </td>
                    <td class="border-right-dif" style="padding-left: 6px">
                        {{ $item->to_segment->ServiceLineDes ?? '' }}
                    </td>
                    <td style="padding-left: 6px">{{ $item->remarks ?? '' }}
                    </td>
                    <td style="padding-left: 6px"> 
                        @if($budget->company->reportingcurrency)
                            {{ $budget->company->reportingcurrency->CurrencyCode }}
                        @else
                            USD
                        @endif
                    
                    </td>
                    <td class="text-right" style="padding-right: 6px">
                        {{ number_format($item->adjustmentAmountRpt, 2) }}
                    </td>
                </tr>
                @endforeach

               @if(count($budget->detail) > 0)
                <tr>
                    <td colspan="8" class="text-right" style="padding-right: 6px"><b>{{ __('custom.total') }}</b></td>
                        <td class="text-right" style="padding-right: 6px">
                            {{ number_format($sum, 2) }}
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
