<html>
<center>
    <table>
        <thead>
        <tr></tr>
        <tr>
            <td colspan="2"></td>
            <td><b><h1>{{ trans('custom.asset_expenses_report') }}</h1></b></td>
        </tr>
        <tr>
            <td colspan="2"></td>
            <th><b>{{ trans('custom.from') }} {{(new \Illuminate\Support\Carbon($fromDate))->format('d/m/Y')}} - {{ trans('custom.to') }} {{(new \Illuminate\Support\Carbon($toDate))->format('d/m/Y')}}</b></th>
        </tr>
        <tr>
            <td colspan="2"></td>
            @if($currencyID == 2)
                <th><b>{{ trans('custom.currency_dash') }} {{$currency->localCurrency->CurrencyCode}}</b></th>
            @endif
            @if($currencyID == 3)
                <th><b>{{ trans('custom.currency_dash') }} {{$currency->reportingcurrency->CurrencyCode}}</b></th>
            @endif
        </tr>
        <tr></tr>
        <tr></tr>
        </thead>
    </table>
</center>
@php $grandTotal = 0 @endphp

@foreach($headers as $header)
    <tr><th><b>{{ $header[0][trans('custom.asset_code')] }} - {{ $header[0][trans('custom.asset_description')] }}</b></th></tr>
    <table>
        <thead>
        <tr>
            <th><b>{{ trans('custom.account_code') }}</b></th>
            <th><b>{{ trans('custom.account_description') }}</b></th>
            <th><b>{{ trans('custom.document_code') }}</b></th>
            <th><b>{{ trans('custom.document_date') }}</b></th>
            <th><b>{{ trans('custom.amount') }}</b></th>
        </tr>
        </thead>
        <tbody>
        @php $total = 0 @endphp
        @foreach($reportData as $item)
            @if($item[trans('custom.asset_code')] == $header[0][trans('custom.asset_code')])
                <tr>
                    <td>{{$item[trans('custom.account_code')]}}</td>
                    <td>{{$item[trans('custom.account_description')]}}</td>
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

