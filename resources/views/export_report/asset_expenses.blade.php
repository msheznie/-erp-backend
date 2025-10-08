<html>
<center>
    <table>
    <thead>
    <tr></tr>
    <tr>
        <td colspan="2"></td>
        <td><h1>{{ trans('custom.asset_expenses_report') }}</h1></td>
    </tr>
    <tr>
        <td colspan="2"></td>
        <th style="font-size:15px;">{{ trans('custom.from') }} {{(new \Illuminate\Support\Carbon($fromDate))->format('d/m/Y')}} - {{ trans('custom.to') }} {{(new \Illuminate\Support\Carbon($toDate))->format('d/m/Y')}}</B></th>
    </tr>
    <tr>
        <td colspan="2"></td>
        @if($currencyID == 2)
        <th style="font-size:15px;">{{ trans('custom.currency_dash') }} {{$currency->localCurrency->CurrencyCode}}</th>
        @endif
        @if($currencyID == 3)
        <th style="font-size:15px;">{{ trans('custom.currency_dash') }} {{$currency->reportingcurrency->CurrencyCode}}</th>
        @endif
    </tr>
    <tr></tr>
    <tr></tr>
    </thead>
    </table>
</center>
@php $grandTotal = 0 @endphp

@foreach($headers as $header)
    <tr><th><B>{{ $header[0][trans('custom.account_code')] }} - {{ $header[0][trans('custom.account_description')] }}</B></th></tr>
<table>
    <thead>
        <tr>
            <th>{{ trans('custom.asset_code') }}</th>
            <th>{{ trans('custom.asset_description') }}</th>
            <th>{{ trans('custom.document_code') }}</th>
            <th>{{ trans('custom.document_date') }}</th>
            <th>{{ trans('custom.amount') }}</th>
        </tr>
    </thead>
    <tbody>
    @php $total = 0 @endphp
    @foreach($reportData as $item)
        @if($item[trans('custom.account_code')] == $header[0][trans('custom.account_code')])
        <tr>
            <td>{{$item[trans('custom.asset_code')]}}</td>
            <td>{{$item[trans('custom.asset_description')]}}</td>
            <td>{{$item[trans('custom.document_code')]}}</td>
            <td>{{ \Carbon\Carbon::parse($item[trans('custom.document_date')])->format("d/m/Y") }}</td>
            <td>{{$item[trans('custom.amount')]}}</td>
        </tr>

        @php $total += $item[trans('custom.amount')] @endphp
        @endif

    @endforeach

    </tbody>
    <tfoot>
    <tr>
        <td colspan="3"></td>
        <td>{{ trans('custom.total') }}</td>
        <td>{{$total}}</td>
    </tr>
    @php $grandTotal += $total @endphp

    </tfoot>
</table>
@endforeach
<tr>
    <td colspan="3"></td>
    <td>{{ trans('custom.grand_total') }}</td>
    <td>{{$grandTotal}}</td>
</tr>
