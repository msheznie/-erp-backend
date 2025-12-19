
<div id="header">
    <div class="row">
        <div class="col-md-12">
            <table>
                    <tr></tr>
                    <tr>
                        <td colspan="22" style="text-align:center;">
                            <h1>{{$companyName}}</h1>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="22"  style="text-align: center;">
                            <h2>{{ trans('custom.customer_summary_report') }} </h2>
                        </td>
                    </tr>

                    <tr></tr>
                    <tr>
                        <th colspan="1" style="text-align:left;">{{ trans('custom.as_of') }} </th>
                        <th colspan="2" style="text-align:left;">{{\App\helper\Helper::dateFormat($fromDate) }}</th>
                    </tr>
                    <tr>
                        <th colspan="1" style="text-align:left;">{{ trans('custom.year') }} </th>
                        <th colspan="2" style="text-align:left;">{{ $year }}</th>

                    </tr>
                    <tr>
                        <th colspan="1" style="text-align:left;">{{ trans('custom.currency') }} </th>
                        <th colspan="2" style="text-align:left;">{{ $currency }}</th>

                    </tr>
                    <tr></tr>
                    <tr></tr>
            </table>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <h3>{{ trans('custom.revenue_summary') }}</h3>
    </div>
</div>
<div >
    <table>
        <thead>
            <tr style="background-color: #d6e6f4">
                <th colspan="2">{{ trans('custom.customer') }}</th>
                <th colspan="1">{{ trans('custom.jan') }}</th>
                <th colspan="1">{{ trans('custom.feb') }}</th>
                <th colspan="1">{{ trans('custom.mar') }}</th>
                <th colspan="1">{{ trans('custom.apr') }}</th>
                <th colspan="1">{{ trans('custom.may') }}</th>
                <th colspan="1">{{ trans('custom.jun') }}</th>
                <th colspan="1">{{ trans('custom.jul') }}</th>
                <th colspan="1">{{ trans('custom.aug') }}</th>
                <th colspan="1">{{ trans('custom.sep') }}</th>
                <th colspan="1">{{ trans('custom.oct') }}</th>
                <th colspan="1">{{ trans('custom.nov') }}</th>
                <th colspan="1">{{ trans('custom.dec') }}</th>
                <th colspan="7">{{ trans('custom.total') }}</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($revenueData as $data )
        <tr>
            <td colspan="2"  style="text-align: right">{{ $data->CustomerName}}</td>
            <td colspan="1"  style="text-align: right">{{ number_format($data->Jan, $decimalPlace)}}</td>
            <td colspan="1"  style="text-align: right">{{ number_format($data->Feb, $decimalPlace)}}</td>
            <td colspan="1"  style="text-align: right">{{ number_format($data->March, $decimalPlace)}}</td>
            <td colspan="1"  style="text-align: right">{{ number_format($data->April, $decimalPlace)}}</td>
            <td colspan="1"  style="text-align: right">{{ number_format($data->May, $decimalPlace)}}</td>
            <td colspan="1"  style="text-align: right">{{ number_format($data->June, $decimalPlace)}}</td>
            <td colspan="1"  style="text-align: right">{{ number_format($data->July, $decimalPlace)}}</td>
            <td colspan="1"  style="text-align: right">{{ number_format($data->Aug, $decimalPlace)}}</td>
            <td colspan="1"  style="text-align: right">{{ number_format($data->Sept, $decimalPlace)}}</td>
            <td colspan="1"  style="text-align: right">{{ number_format($data->Oct, $decimalPlace)}}</td>
            <td colspan="1"  style="text-align: right">{{ number_format($data->Nov, $decimalPlace)}}</td>
            <td colspan="1"  style="text-align: right">{{ number_format($data->Dece, $decimalPlace)}}</td>
            <td colspan="7"  style="text-align: right">{{ number_format($data->Total, $decimalPlace)}}</td>
        </tr>
        @endforeach
        </tbody>
        <tfoot>
            @if (!empty($revenueTotal) )
                <tr style="background-color: #d6e6f4;">
                    <td  colspan="2"  style="text-align: right">
                        <b style="text-align: right;"> {{ trans('custom.total') }}:</b>
                    </td>
                    <td colspan="1"  style="text-align: right">
                            {{isset($revenueTotal['Jan']) ? number_format($revenueTotal['Jan'], $decimalPlace) : 0}}
                    </td>
                    <td colspan="1"  style="text-align: right">
                            {{isset($revenueTotal['Feb']) ? number_format($revenueTotal['Feb'], $decimalPlace) : 0}}
                    </td>
                    <td colspan="1"  style="text-align: right">
                            {{isset($revenueTotal['March']) ? number_format($revenueTotal['March'], $decimalPlace) : 0}}
                    </td>
                    <td colspan="1"  style="text-align: right">
                            {{isset($revenueTotal['April']) ? number_format($revenueTotal['April'], $decimalPlace) : 0}}
                    </td>
                    <td colspan="1"  style="text-align: right">
                            {{isset($revenueTotal['May']) ? number_format($revenueTotal['May'], $decimalPlace) : 0}}
                    </td>
                    <td colspan="1"  style="text-align: right">
                            {{isset($revenueTotal['June']) ? number_format($revenueTotal['June'], $decimalPlace) : 0}}
                    </td>
                    <td colspan="1"  style="text-align: right">
                            {{isset($revenueTotal['July']) ? number_format($revenueTotal['July'], $decimalPlace) : 0}}
                    </td>
                    <td colspan="1"  style="text-align: right">
                            {{isset($revenueTotal['Aug']) ? number_format($revenueTotal['Aug'], $decimalPlace) : 0}}
                    </td>
                    <td colspan="1"  style="text-align: right">
                            {{isset($revenueTotal['Sept']) ? number_format($revenueTotal['Sept'], $decimalPlace) : 0}}
                    </td>
                    <td colspan="1"  style="text-align: right">
                            {{isset($revenueTotal['Oct']) ? number_format($revenueTotal['Oct'], $decimalPlace) : 0}}
                    </td>
                    <td colspan="1"  style="text-align: right">
                            {{isset($revenueTotal['Nov']) ? number_format($revenueTotal['Nov'], $decimalPlace) : 0}}
                    </td>
                    <td colspan="1"  style="text-align: right">
                            {{isset($revenueTotal['Dece']) ? number_format($revenueTotal['Dece'], $decimalPlace) : 0}}
                    </td>
                    <td colspan="7"  style="text-align: right">
                            {{isset($revenueTotal['Total']) ? number_format($revenueTotal['Total'], $decimalPlace) : 0}}
                    </td>
                </tr>
            @endif
        </tfoot>
        <tr></tr>
        <tr></tr>
        <tr></tr>
    </table>
</div>

<div class="row">
    <div class="col-md-12">
        <h3>{{ trans('custom.collection_summary') }}</h3>
    </div>
</div>
<div >
    <table>
        <thead>
            <tr style="background-color: #d6e6f4">
                <th colspan="2">{{ trans('custom.customer') }}</th>
                <th colspan="1">{{ trans('custom.jan') }}</th>
                <th colspan="1">{{ trans('custom.feb') }}</th>
                <th colspan="1">{{ trans('custom.mar') }}</th>
                <th colspan="1">{{ trans('custom.apr') }}</th>
                <th colspan="1">{{ trans('custom.may') }}</th>
                <th colspan="1">{{ trans('custom.jun') }}</th>
                <th colspan="1">{{ trans('custom.jul') }}</th>
                <th colspan="1">{{ trans('custom.aug') }}</th>
                <th colspan="1">{{ trans('custom.sep') }}</th>
                <th colspan="1">{{ trans('custom.oct') }}</th>
                <th colspan="1">{{ trans('custom.nov') }}</th>
                <th colspan="1">{{ trans('custom.dec') }}</th>
                <th colspan="7">{{ trans('custom.total') }}</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($outputCollection as $data )
        <tr>
            <td colspan="2"  style="text-align: right">{{ $data->CustomerName}}</td>
            <td colspan="1"  style="text-align: right">{{ number_format($data->Jan, $decimalPlace)}}</td>
            <td colspan="1"  style="text-align: right">{{ number_format($data->Feb, $decimalPlace)}}</td>
            <td colspan="1"  style="text-align: right">{{ number_format($data->March, $decimalPlace)}}</td>
            <td colspan="1"  style="text-align: right">{{ number_format($data->April, $decimalPlace)}}</td>
            <td colspan="1"  style="text-align: right">{{ number_format($data->May, $decimalPlace)}}</td>
            <td colspan="1"  style="text-align: right">{{ number_format($data->June, $decimalPlace)}}</td>
            <td colspan="1"  style="text-align: right">{{ number_format($data->July, $decimalPlace)}}</td>
            <td colspan="1"  style="text-align: right">{{ number_format($data->Aug, $decimalPlace)}}</td>
            <td colspan="1"  style="text-align: right">{{ number_format($data->Sept, $decimalPlace)}}</td>
            <td colspan="1"  style="text-align: right">{{ number_format($data->Oct, $decimalPlace)}}</td>
            <td colspan="1"  style="text-align: right">{{ number_format($data->Nov, $decimalPlace)}}</td>
            <td colspan="1"  style="text-align: right">{{ number_format($data->Dece, $decimalPlace)}}</td>
            <td colspan="7"  style="text-align: right">{{ number_format($data->Total, $decimalPlace)}}</td>
        </tr>
        @endforeach
        </tbody>
        <tfoot>
            @if (!empty($collectionTotal) )
                <tr style="background-color: #d6e6f4;">
                    <td  colspan="2"  style="text-align: right">
                        <b style="text-align: right;">{{ trans('custom.total') }}:</b>
                    </td>
                    <td colspan="1"  style="text-align: right">
                            {{isset($collectionTotal['Jan']) ? number_format($collectionTotal['Jan'], $decimalPlace) : 0}}
                    </td>
                    <td colspan="1"  style="text-align: right">
                            {{isset($collectionTotal['Feb']) ? number_format($collectionTotal['Feb'], $decimalPlace) : 0}}
                    </td>
                    <td colspan="1"  style="text-align: right">
                            {{isset($collectionTotal['March']) ? number_format($collectionTotal['March'], $decimalPlace) : 0}}
                    </td>
                    <td colspan="1"  style="text-align: right">
                            {{isset($collectionTotal['April']) ? number_format($collectionTotal['April'], $decimalPlace) : 0}}
                    </td>
                    <td colspan="1"  style="text-align: right">
                            {{isset($collectionTotal['May']) ? number_format($collectionTotal['May'], $decimalPlace) : 0}}
                    </td>
                    <td colspan="1"  style="text-align: right">
                            {{isset($collectionTotal['June']) ? number_format($collectionTotal['June'], $decimalPlace) : 0}}
                    </td>
                    <td colspan="1"  style="text-align: right">
                            {{isset($collectionTotal['July']) ? number_format($collectionTotal['July'], $decimalPlace) : 0}}
                    </td>
                    <td colspan="1"  style="text-align: right">
                            {{isset($collectionTotal['Aug']) ? number_format($collectionTotal['Aug'], $decimalPlace) : 0}}
                    </td>
                    <td colspan="1"  style="text-align: right">
                            {{isset($collectionTotal['Sept']) ? number_format($collectionTotal['Sept'], $decimalPlace) : 0}}
                    </td>
                    <td colspan="1"  style="text-align: right">
                            {{isset($collectionTotal['Oct']) ? number_format($collectionTotal['Oct'], $decimalPlace) : 0}}
                    </td>
                    <td colspan="1"  style="text-align: right">
                            {{isset($collectionTotal['Nov']) ? number_format($collectionTotal['Nov'], $decimalPlace) : 0}}
                    </td>
                    <td colspan="1"  style="text-align: right">
                            {{isset($collectionTotal['Dece']) ? number_format($collectionTotal['Dece'], $decimalPlace) : 0}}
                    </td>
                    <td colspan="7"  style="text-align: right">
                            {{isset($collectionTotal['Total']) ? number_format($collectionTotal['Total'], $decimalPlace) : 0}}
                    </td>
                </tr>
            @endif
        </tfoot>
        <tr></tr>
        <tr></tr>
        <tr></tr>
    </table>
</div>

<div class="row">
    <div class="col-md-12">
        <h3>{{ trans('custom.summary') }}</h3>
    </div>
</div>
<div >
    <table>
        <thead>
            <tr style="background-color: #d6e6f4">
                <th colspan="2"></th>
                <th colspan="1">{{ trans('custom.jan') }}</th>
                <th colspan="1">{{ trans('custom.feb') }}</th>
                <th colspan="1">{{ trans('custom.mar') }}</th>
                <th colspan="1">{{ trans('custom.apr') }}</th>
                <th colspan="1">{{ trans('custom.may') }}</th>
                <th colspan="1">{{ trans('custom.jun') }}</th>
                <th colspan="1">{{ trans('custom.jul') }}</th>
                <th colspan="1">{{ trans('custom.aug') }}</th>
                <th colspan="1">{{ trans('custom.sep') }}</th>
                <th colspan="1">{{ trans('custom.oct') }}</th>
                <th colspan="1">{{ trans('custom.nov') }}</th>
                <th colspan="1">{{ trans('custom.dec') }}</th>
                <th colspan="7">{{ trans('custom.total') }}</th>
            </tr>
        </thead>
{{--        <tbody>--}}
{{--        @foreach ($outputServiceLine as $data )--}}
{{--        <tr>--}}
{{--            <td colspan="2" class="text-right">{{ $data->CompanyName}}</td>--}}
{{--            <td colspan="5" class="text-right">{{ $data->ServiceLineDes}}</td>--}}
{{--            <td colspan="1" class="text-right">{{ number_format($data->Jan)}}</td>--}}
{{--            <td colspan="1" class="text-right">{{ number_format($data->Feb)}}</td>--}}
{{--            <td colspan="1" class="text-right">{{ number_format($data->March)}}</td>--}}
{{--            <td colspan="1" class="text-right">{{ number_format($data->April)}}</td>--}}
{{--            <td colspan="1" class="text-right">{{ number_format($data->May)}}</td>--}}
{{--            <td colspan="1" class="text-right">{{ number_format($data->June)}}</td>--}}
{{--            <td colspan="1" class="text-right">{{ number_format($data->July)}}</td>--}}
{{--            <td colspan="1" class="text-right">{{ number_format($data->Aug)}}</td>--}}
{{--            <td colspan="1" class="text-right">{{ number_format($data->Sept)}}</td>--}}
{{--            <td colspan="1" class="text-right">{{ number_format($data->Oct)}}</td>--}}
{{--            <td colspan="1" class="text-right">{{ number_format($data->Nov)}}</td>--}}
{{--            <td colspan="1" class="text-right">{{ number_format($data->Dece)}}</td>--}}
{{--            <td colspan="2" class="text-right">{{ number_format($data->Total)}}</td>--}}
{{--        </tr>--}}
{{--        @endforeach--}}
{{--        </tbody>--}}
        <tfoot>
            @if (!empty($serviceLineTotal) )
                <tr style="background-color: #d6e6f4;">
                    <td  colspan="2"  style="text-align: left">
                        <b style="text-align: right;">{{ trans('custom.revenue_total') }}:</b>
                    </td>
                    <td colspan="1"  style="text-align: right">
                            {{isset($revenueTotal['Jan']) ? number_format($revenueTotal['Jan'], $decimalPlace) : 0}}
                    </td>
                    <td colspan="1"  style="text-align: right">
                            {{isset($revenueTotal['Feb']) ? number_format($revenueTotal['Feb'], $decimalPlace) : 0}}
                    </td>
                    <td colspan="1"  style="text-align: right">
                            {{isset($revenueTotal['March']) ? number_format($revenueTotal['March'], $decimalPlace) : 0}}
                    </td>
                    <td colspan="1"  style="text-align: right">
                            {{isset($revenueTotal['April']) ? number_format($revenueTotal['April'], $decimalPlace) : 0}}
                    </td>
                    <td colspan="1"  style="text-align: right">
                            {{isset($revenueTotal['May']) ? number_format($revenueTotal['May'], $decimalPlace) : 0}}
                    </td>
                    <td colspan="1"  style="text-align: right">
                            {{isset($revenueTotal['June']) ? number_format($revenueTotal['June'], $decimalPlace) : 0}}
                    </td>
                    <td colspan="1"  style="text-align: right">
                            {{isset($revenueTotal['July']) ? number_format($revenueTotal['July'], $decimalPlace) : 0}}
                    </td>
                    <td colspan="1"  style="text-align: right">
                            {{isset($revenueTotal['Aug']) ? number_format($revenueTotal['Aug'], $decimalPlace) : 0}}
                    </td>
                    <td colspan="1"  style="text-align: right">
                            {{isset($revenueTotal['Sept']) ? number_format($revenueTotal['Sept'], $decimalPlace) : 0}}
                    </td>
                    <td colspan="1"  style="text-align: right">
                            {{isset($revenueTotal['Oct']) ? number_format($revenueTotal['Oct'], $decimalPlace) : 0}}
                    </td>
                    <td colspan="1"  style="text-align: right">
                            {{isset($revenueTotal['Nov']) ? number_format($revenueTotal['Nov'], $decimalPlace) : 0}}
                    </td>
                    <td colspan="1"  style="text-align: right">
                            {{isset($revenueTotal['Dece']) ? number_format($revenueTotal['Dece'], $decimalPlace) : 0}}
                    </td>
                    <td colspan="7"  style="text-align: right">
                            {{isset($revenueTotal['Total']) ? number_format($revenueTotal['Total'],$decimalPlace) : 0}}
                    </td>
                </tr>
            @endif
            @if (!empty($collectionTotal) )
                <tr style="background-color: #d6e6f4;">
                    <td  colspan="2"  style="text-align: right">
                        <b style="text-align: right;">{{ trans('custom.collection_total') }}:</b>
                    </td>
                    <td colspan="1"  style="text-align: right">
                            {{isset($collectionTotal['Jan']) ? number_format($collectionTotal['Jan'], $decimalPlace) : 0}}
                    </td>
                    <td colspan="1"  style="text-align: right">
                            {{isset($collectionTotal['Feb']) ? number_format($collectionTotal['Feb'], $decimalPlace) : 0}}
                    </td>
                    <td colspan="1"  style="text-align: right">
                            {{isset($collectionTotal['March']) ? number_format($collectionTotal['March'], $decimalPlace) : 0}}
                    </td>
                    <td colspan="1"  style="text-align: right">
                            {{isset($collectionTotal['April']) ? number_format($collectionTotal['April'], $decimalPlace) : 0}}
                    </td>
                    <td colspan="1"  style="text-align: right">
                            {{isset($collectionTotal['May']) ? number_format($collectionTotal['May'], $decimalPlace) : 0}}
                    </td>
                    <td colspan="1"  style="text-align: right">
                            {{isset($collectionTotal['June']) ? number_format($collectionTotal['June'], $decimalPlace) : 0}}
                    </td>
                    <td colspan="1"  style="text-align: right">
                            {{isset($collectionTotal['July']) ? number_format($collectionTotal['July'], $decimalPlace) : 0}}
                    </td>
                    <td colspan="1"  style="text-align: right">
                            {{isset($collectionTotal['Aug']) ? number_format($collectionTotal['Aug'], $decimalPlace) : 0}}
                    </td>
                    <td colspan="1"  style="text-align: right">
                            {{isset($collectionTotal['Sept']) ? number_format($collectionTotal['Sept'], $decimalPlace) : 0}}
                    </td>
                    <td colspan="1"  style="text-align: right">
                            {{isset($collectionTotal['Oct']) ? number_format($collectionTotal['Oct'], $decimalPlace) : 0}}
                    </td>
                    <td colspan="1"  style="text-align: right">
                            {{isset($collectionTotal['Nov']) ? number_format($collectionTotal['Nov'], $decimalPlace) : 0}}
                    </td>
                    <td colspan="1"  style="text-align: right">
                            {{isset($collectionTotal['Dece']) ? number_format($collectionTotal['Dece'], $decimalPlace) : 0}}
                    </td>
                    <td colspan="7" style="text-align: right">
                            {{isset($collectionTotal['Total']) ? number_format($collectionTotal['Total'], $decimalPlace) : 0}}
                    </td>
                </tr>
            @endif
        </tfoot>
    </table>
</div>
