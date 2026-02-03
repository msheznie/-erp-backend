<html>
<head>
    <title> {{ __('custom.asset_depreciation') }} </title>
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
        /* RTL Support for Arabic */
        @if(app()->getLocale() == 'ar')
        body {
            direction: rtl;
            text-align: right;
            font-family: 'Noto Sans Arabic', sans-serif;
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
            bottom: -45px;
        }

        .pagenum:before {
            content: counter(page);
        }
        #watermark { position: fixed; bottom: 0px; right: 0px; width: 200px; height: 200px; opacity: .1; }
        .content {
            margin-bottom: 45px;
        }
        .company-logo {
            max-width: 230px;
            max-height: 95px;
            width: auto !important;
            height: auto !important;
        }
    </style>
</head>
<body>
<div id="watermark"></div>
<div class="card-body content" id="print-section">
    @if(isset($showHeader) && $showHeader)
    <table style="width: 100%" class="table_height">
        <tr style="width: 100%">
            <td valign="top" style="width: 20%">
                @if($dbdata->company)
                    <img src="{{$dbdata->company->logo_url}}" width="100" class="container">
                @endif
            </td>
            <td valign="top" style="width: 80%">

                <span style="font-size: 24px;font-weight: 400"> {{ $dbdata->company?$dbdata->company->CompanyName:'' }}</span>
                <br>
                <span style="font-weight: bold"> {{ $dbdata->company?$dbdata->company->CompanyAddress:'' }}</span>
                <br>
            </td>
        </tr>
    </table>
    <hr style="border-top: 2px solid black; height: 2px; color: black">

    <table style="width: 100%" class="table_height">
        <tr style="width: 100%">
            <td style="text-align: center; font-weight: bold">
                <div>
                    <span style="font-size: 18px">
                        {{ __('custom.asset_depreciation_print') }}
                    </span>
                </div>
            </td>
        </tr>
    </table>
    <br>
    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="width: 50%;">
            <table>
                <tr>
                    <td width="170px"><span style="font-weight:bold;">{{ __('custom.dep_month_year') }} </span></td>
                    <td width="10px"><span style="font-weight:bold;">:</span></td>
                    <td>
                        <span>{{ $dbdata->depMonthYear ?? '-' }}</span>
                    </td>
                </tr>
                <tr>
                    <td width="170px"><span style="font-weight:bold;">{{ __('custom.doc_code') }} </span></td>
                    <td width="10px"><span style="font-weight:bold;">:</span></td>
                    <td>
                        <span>{{ $dbdata->depCode ?? '-' }}</span>
                    </td>
                </tr>
                <tr>
                    <td width="170px"><span style="font-weight:bold;">{{ __('custom.doc_date') }} </span></td>
                    <td width="10px"><span style="font-weight:bold;">:</span></td>
                    <td>
                        <span>{{ $dbdata->depDate ? \App\helper\Helper::dateFormat($dbdata->depDate) : '-' }}</span>
                    </td>
                </tr>
                <tr>
                    <td width="170px"><span style="font-weight:bold;">{{ __('custom.financial_year') }} </span></td>
                    <td width="10px"><span style="font-weight:bold;">:</span></td>
                    <td>
                        <span>@if(isset($dbdata->FYBiggin) && isset($dbdata->FYEnd))
                                {{ \App\helper\Helper::dateFormat($dbdata->FYBiggin) }} {{__('custom.to')}} {{ \App\helper\Helper::dateFormat($dbdata->FYEnd) }} @else - @endif</span>
                    </td>
                </tr>
                <tr>
                    <td width="170px"><span style="font-weight:bold;">{{ __('custom.financial_period') }} </span></td>
                    <td width="10px"><span style="font-weight:bold;">:</span></td>
                    <td>
                        <span>@if(isset($dbdata->FYPeriodDateFrom) && isset($dbdata->FYPeriodDateTo))
                                {{ \App\helper\Helper::dateFormat($dbdata->FYPeriodDateFrom) }} {{__('custom.to')}} {{ \App\helper\Helper::dateFormat($dbdata->FYPeriodDateTo) }} @else - @endif</span>
                    </td>
                </tr>
            </table>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="width: 60%;">
            <table>
                <tr>
                    <td width="170px"><span style="font-weight:bold;">{{ __('custom.dep_type') }}</span></td>
                    <td width="10px"><span style="font-weight:bold;">:</span></td>
                    <td>
                        <span>
                            @if($dbdata->is_acc_dep == 1)
                                {{ __('custom.accumulated_depreciation') }}
                            @else
                                {{ __('custom.monthly_dep') }}
                            @endif
                        </span>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    @endif
    <br>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <table class="table table-bordered">
                <thead>
                <tr  style="background-color: #DEDEDE !important; border-color:#000">
                    <th></th>
                    <th>{{ __('custom.fa_code') }}</th>
                    <th>{{ __('custom.asset_description') }}</th>
                    <th>{{ __('custom.excel_segment') }}</th>
                    <th>{{ __('custom.finance_category') }}</th>
                    <th>{{ __('custom.category') }}</th>
                    <th style="width:10%">{{ __('custom.dep_percentage') }}</th>
                    <th style="width:10%">{{ __('custom.cost_unit') }}</th>
                    <th style="width:10%">{{ __('custom.cost_unit_rpt') }}</th>
                    <th style="width:10%">{{ __('custom.dep_amount_local') }}</th>
                    <th style="width:10%">{{ __('custom.dep_amount_rpt') }}</th>
                </tr>
                </thead>
                <tbody>
                @if(isset($dbdata->details) && count($dbdata->details) > 0)
                    @foreach($dbdata->details as $index => $detail)
                        <tr>
                            <td style="padding-left: 3px; padding-right: 3px;">{{ isset($startingRowNumber) ? ($startingRowNumber + $index) : ($index + 1) }}</td>
                            <td style="padding-left: 5px; padding-right: 3px;">{{ $detail->faCode ?? '-' }}</td>
                            <td style="padding-left: 5px; padding-right: 3px;">{{ $detail->assetDescription ?? '-' }}</td>
                            <td style="padding-left: 5px; padding-right: 3px;">@if(isset($detail->serviceline_by)){{ $detail->serviceline_by->ServiceLineDes }}@else - @endif</td>
                            <td style="padding-left: 5px; padding-right: 3px;">@if(isset($detail->financecategory_by)){{ $detail->financecategory_by->financeCatDescription }}@else - @endif</td>
                            <td style="padding-left: 5px; padding-right: 3px;">@if(isset($detail->maincategory_by)){{ $detail->maincategory_by->catDescription }}@else - @endif</td>
                            <td class="text-right" style="padding-right: 5px;">{{ number_format($detail->depPercent ?? 0, 2) }}%</td>
                            <td class="text-right" style="padding-right: 5px;">{{ number_format($detail->COSTUNIT ?? 0, isset($dbdata->localcurrency) ? $dbdata->localcurrency->DecimalPlaces : 2) }}</td>
                            <td class="text-right" style="padding-right: 5px;">{{ number_format($detail->costUnitRpt ?? 0, isset($dbdata->rptcurrency) ? $dbdata->rptcurrency->DecimalPlaces : 2) }}</td>
                            <td class="text-right" style="padding-right: 5px;">{{ number_format($detail->depAmountLocal ?? 0, isset($dbdata->localcurrency) ? $dbdata->localcurrency->DecimalPlaces : 2) }}</td>
                            <td class="text-right" style="padding-right: 5px;">{{ number_format($detail->depAmountRpt ?? 0, isset($dbdata->rptcurrency) ? $dbdata->rptcurrency->DecimalPlaces : 2) }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="11">{{ __('custom.no_records_found') }}</td>
                    </tr>
                @endif
                </tbody>
                @if(isset($dbdata->details) && count($dbdata->details) > 0 && isset($showFooterDetails) && $showFooterDetails)
                    @php
                        $totalLocal = $totalFromJob ? $dbdata->totalDepAmountLocal : collect($dbdata->details)->sum('depAmountLocal');
                        $totalRpt = $totalFromJob ? $dbdata->totalDepAmountRpt : collect($dbdata->details)->sum('depAmountRpt');
                    @endphp
                    <tfoot>
                    <tr>
                        <td colspan="9" class="text-right" style="padding-right: 5px; text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};"><span style="font-weight:bold;">{{ __('custom.total') }} </span></td>
                        <td class="text-right" style="padding-right: 5px;">
                            <span style="font-weight:bold;">{{ number_format($totalLocal, isset($dbdata->localcurrency) ? $dbdata->localcurrency->DecimalPlaces : 2) }}</span>
                        </td>
                        <td class="text-right" style="padding-right: 5px;">
                            <span style="font-weight:bold;">{{ number_format($totalRpt, isset($dbdata->rptcurrency) ? $dbdata->rptcurrency->DecimalPlaces : 2) }}</span>
                        </td>
                    </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@if(isset($showFooterDetails) && $showFooterDetails)
<div class="footer">
    <table style="width:100%;">
        <tr>
            <td>
                <span style="font-weight:bold;">{{ __('custom.confirmed_by') }} : </span> {{ $dbdata->confirmed_by?$dbdata->confirmed_by->empName:'' }}
            </td>
        </tr>
        <tr>
            &nbsp;
        </tr>
        <tr>
            <td>
                <span style="font-weight:bold;">{{ __('custom.electronically_approved_by') }} : </span>

            <td>

            </td>
        </tr>
    </table>

    <table style="width:100%">
        <tr>
            @if ($dbdata->approved_by)
                @foreach ($dbdata->approved_by as $det)
                    <td style="font-size: 9px;">
                        <div>
                            <span>
                                @if($det->employee)
                                    {{$det->employee->empFullName }}
                                @endif
                            </span>
                        </div>
                        <div>
                            <span>
                                @if(isset($det->employee->hr_emp->designation))
                                    {{$det->employee->hr_emp->designation->DesDescription }}
                                @endif
                            </span>
                        </div>
                        <div>
                            <span>
                                @if(!empty($det->approvedDate))
                                    {{ \App\helper\Helper::dateFormat($det->approvedDate)}}
                                @endif
                            </span>
                        </div>
                    </td>
                @endforeach
            @endif
        </tr>
    </table>

</div>
@endif
</body>
</html>
