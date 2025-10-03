<html @if(isset($lang) && $lang === 'ar') dir="rtl" @endif>
<style type="text/css">
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

    body {
        font-size: 9px;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol"
    }

    h3 {
        font-size: 1.53125rem;
    }

    h6 {
        font-size: 0.875rem;
    }

    h6, h3 {
        margin-bottom: 0.1rem;
        font-weight: 500;
        line-height: 1.2;
        color: inherit;
    }

    table > tbody > th > tr > td {
        font-size: 9px;
    }

    table > thead > th {
        font-size: 9px;
    }

    .theme-tr-head {
        background-color: #EBEBEB !important;
    }

    .text-left {
        text-align: left;
    }

    table {
        border-collapse: collapse;
    }

    .font-weight-bold {
        font-weight: 700 !important;
    }

    .table th, .table td {
        padding: 0.4rem !important;
        vertical-align: top;
        border: 1px solid #dee2e6 !important;
        /* border-bottom: 1px solid rgb(127, 127, 127) !important;*/
    }

    .table th {
        background-color: #D7E4BD !important;
    }

    tfoot > tr > td {
        border: 1px solid rgb(127, 127, 127);
    }

    .text-right {
        text-align: right !important;
    }

    .font-weight-bold {
        font-weight: 700 !important;
    }

    hr {
        border: 0;
        border-top: 1px solid rgba(0, 0, 0, 0.1);
    }

    th {
        text-align: inherit;
        font-weight: bold;
    }

    .table-striped tbody tr:nth-of-type(odd) {
        background-color: #f9f9f9;
    }

    p {
        margin-top: 0 !important;
    }

    .content {
        margin-bottom: 45px;
    }

    .text-left-footer {
        text-align: left !important;
    }

    .text-right-footer {
        text-align: right !important;
    }

    @page {
        margin: 100px 25px 80px 25px; /* top, right, bottom, left */
    }

    body {
        font-size: 9px;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
        margin: 0;
        padding: 0;
    }

    .footer {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        width: 100%;
        text-align: center;
        font-size: 10px;
        padding: 5px 0;
    }

</style>
<div style="width: 100%; text-align: center; font-size: 10px; margin-bottom: 20px;">
    <table style="width: 100%">
        <tr>
            <td valign="top" style="width: 45%; @if(isset($lang) && $lang === 'ar') text-align: right; @endif">
                <img src="{{$companylogo}}" width="180px" height="60px"><br>
            </td>
            <td valign="top" style="width: 55%; @if(isset($lang) && $lang === 'ar') text-align: right; @endif">
                <br><br>
                <span style="font-weight: bold; font-size: 12px;">{{ trans('custom.revenue_report_year') }} - {{ $year }}</span><br>
                <span style="font-weight: bold; font-size: 12px;">@if(isset($lang) && $lang === 'en') &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; @endif{{ trans('custom.as_of') }} {{ $fromDate }} @if(isset($lang) && $lang === 'ar') &nbsp;&nbsp;&nbsp; @endif</span><br>
                <span style="font-weight: bold; font-size: 12px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ trans('custom.currency') }} {{ $currency }}@if(isset($lang) && $lang === 'ar') &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; @endif</span>
            </td>
        </tr>
        <tr>
            <td colspan="2" valign="top" style="width: 45%; @if(isset($lang) && $lang === 'ar') text-align: right; @endif">
                <span style="font-weight: bold;"> &nbsp;&nbsp;&nbsp;{{$companyName}}</span>
            </td>
            <td>
            </td>
        </tr>
    </table>
</div>
<div class="content">
    <table style="width:100%;border:1px solid #9fcdff" class="table">
        @foreach ($reportData as $key => $val)
            <thead>
            <tr>
                <td colspan="14"><b>{{$key}}</b></td>
            </tr>
            <tr>
                <th width="20%" style="background-color: #D7E4BD">{{ trans('custom.customer_name') }}</th>
                <th width="6%">{{ trans('custom.jan') }}</th>
                <th width="6%">{{ trans('custom.feb') }}</th>
                <th width="6%">{{ trans('custom.mar') }}</th>
                <th width="6%">{{ trans('custom.apr') }}</th>
                <th width="6%">{{ trans('custom.may') }}</th>
                <th width="6%">{{ trans('custom.jun') }}</th>
                <th width="6%">{{ trans('custom.jul') }}</th>
                <th width="6%">{{ trans('custom.aug') }}</th>
                <th width="6%">{{ trans('custom.sep') }}</th>
                <th width="6%">{{ trans('custom.oct') }}</th>
                <th width="6%">{{ trans('custom.nov') }}</th>
                <th width="6%">{{ trans('custom.dec') }}</th>
                <th width="15%">{{ trans('custom.total') }}</th>
            </tr>
            </thead>
            <tbody>
            {{ $janTotal = 0 }}
            {{ $febTotal = 0 }}
            {{ $marTotal = 0 }}
            {{ $aprTotal = 0 }}
            {{ $mayTotal = 0 }}
            {{ $junTotal = 0 }}
            {{ $julTotal = 0 }}
            {{ $augTotal = 0 }}
            {{ $sepTotal = 0 }}
            {{ $octTotal = 0 }}
            {{ $novTotal = 0 }}
            {{ $decTotal = 0 }}
            {{ $gTotal = 0 }}
            @foreach ($val as $det)
                {{ $janTotal += $det->Jan }}
                {{ $febTotal += $det->Feb }}
                {{ $marTotal += $det->March }}
                {{ $aprTotal += $det->April }}
                {{ $mayTotal += $det->May }}
                {{ $junTotal += $det->May }}
                {{ $julTotal += $det->July }}
                {{ $augTotal += $det->Aug }}
                {{ $sepTotal += $det->Sept }}
                {{ $octTotal += $det->Oct }}
                {{ $novTotal += $det->Nov }}
                {{ $decTotal += $det->Dece }}
                {{ $gTotal += $det->Total }}
                <tr>
                    <td>{{ $det->CustomerName  }}</td>
                    <td style="text-align: right">{{ number_format($det->Jan,$decimalPlace) }}</td>
                    <td style="text-align: right">{{ number_format($det->Feb,$decimalPlace) }}</td>
                    <td style="text-align: right">{{ number_format($det->March,$decimalPlace) }}</td>
                    <td style="text-align: right">{{ number_format($det->April,$decimalPlace) }}</td>
                    <td style="text-align: right">{{ number_format($det->May,$decimalPlace) }}</td>
                    <td style="text-align: right">{{ number_format($det->June,$decimalPlace) }}</td>
                    <td style="text-align: right">{{ number_format($det->July,$decimalPlace) }}</td>
                    <td style="text-align: right">{{ number_format($det->Aug,$decimalPlace) }}</td>
                    <td style="text-align: right">{{ number_format($det->Sept,$decimalPlace) }}</td>
                    <td style="text-align: right">{{ number_format($det->Oct,$decimalPlace) }}</td>
                    <td style="text-align: right">{{ number_format($det->Nov,$decimalPlace) }}</td>
                    <td style="text-align: right">{{ number_format($det->Dece,$decimalPlace) }}</td>
                    <td style="text-align: right">{{ number_format($det->Total,$decimalPlace) }}</td>
                </tr>
            @endforeach
            <tr>
                <td class="text-right"
                    style=""><b>{{ trans('custom.total') }}:</b>
                </td>
                <td class="text-right"><b>{{number_format($janTotal,$decimalPlace)}}</b></td>
                <td class="text-right"><b>{{number_format($febTotal,$decimalPlace)}}</b></td>
                <td class="text-right"><b>{{number_format($marTotal,$decimalPlace)}}</b></td>
                <td class="text-right"><b>{{number_format($aprTotal,$decimalPlace)}}</b></td>
                <td class="text-right"><b>{{number_format($mayTotal,$decimalPlace)}}</b></td>
                <td class="text-right"><b>{{number_format($junTotal,$decimalPlace)}}</b></td>
                <td class="text-right"><b>{{number_format($julTotal,$decimalPlace)}}</b></td>
                <td class="text-right"><b>{{number_format($augTotal,$decimalPlace)}}</b></td>
                <td class="text-right"><b>{{number_format($sepTotal,$decimalPlace)}}</b></td>
                <td class="text-right"><b>{{number_format($octTotal,$decimalPlace)}}</b></td>
                <td class="text-right"><b>{{number_format($novTotal,$decimalPlace)}}</b></td>
                <td class="text-right"><b>{{number_format($decTotal,$decimalPlace)}}</b></td>
                <td class="text-right"><b>{{number_format($gTotal,$decimalPlace)}}</b></td>
            </tr>
            </tbody>
        @endforeach
        <tfoot>
        <tr>
            <td class="text-right"
                style=""><b>{{ trans('custom.grand_total') }}:</b>
            </td>
            <td class="text-right"><b>{{number_format($total['Jan'],$decimalPlace)}}</b></td>
            <td class="text-right"><b>{{number_format($total['Feb'],$decimalPlace)}}</b></td>
            <td class="text-right"><b>{{number_format($total['March'],$decimalPlace)}}</b></td>
            <td class="text-right"><b>{{number_format($total['April'],$decimalPlace)}}</b></td>
            <td class="text-right"><b>{{number_format($total['May'],$decimalPlace)}}</b></td>
            <td class="text-right"><b>{{number_format($total['June'],$decimalPlace)}}</b></td>
            <td class="text-right"><b>{{number_format($total['July'],$decimalPlace)}}</b></td>
            <td class="text-right"><b>{{number_format($total['Aug'],$decimalPlace)}}</b></td>
            <td class="text-right"><b>{{number_format($total['Sept'],$decimalPlace)}}</b></td>
            <td class="text-right"><b>{{number_format($total['Oct'],$decimalPlace)}}</b></td>
            <td class="text-right"><b>{{number_format($total['Nov'],$decimalPlace)}}</b></td>
            <td class="text-right"><b>{{number_format($total['Dece'],$decimalPlace)}}</b></td>
            <td class="text-right"><b>{{number_format($total['Total'],$decimalPlace)}}</b></td>
        </tr>
        </tfoot>
    </table>
</div>
<div class="footer">
    <table style="width:100%;">
        <tr>
            <td style="width:50%; font-size:10px; vertical-align: bottom; @if(isset($lang) && $lang === 'ar') text-align:right; @endif">
                <span>{{ trans('custom.printed_date') }} : {{ date("d-M-y", strtotime(now())) }}</span>
            </td>
            <td class="@if(isset($lang) && $lang === 'ar') text-left-footer @else text-right-footer @endif"
                style="width:50%; font-size:10px; vertical-align: bottom;">
                <span style="@if(isset($lang) && $lang === 'ar') float:left !important; @else float:right !important; @endif">
                    {{ trans('custom.page') }} <span>{PAGENO}</span>
                </span>
            </td>
        </tr>
    </table>
</div>
</html>
