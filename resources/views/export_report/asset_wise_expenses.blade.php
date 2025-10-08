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
    <tr><th><B>{{ $header[0]['AssetCode'] }} - {{ $header[0]['AssetDescription'] }}</B></th></tr>
    <table>
        <thead>
        <tr>
            <th>{{ trans('custom.account_code') }}</th>
            <th>{{ trans('custom.account_description') }}</th>
            <th>{{ trans('custom.document_code') }}</th>
            <th>{{ trans('custom.document_date') }}</th>
            <th>{{ trans('custom.amount') }}</th>
        </tr>
        </thead>
        <tbody>
        @php $total = 0 @endphp
        @foreach($reportData as $item)
            @if($item['AssetCode'] == $header[0]['AssetCode'])
                <tr>
                    <td>{{$item['AccountCode']}}</td>
                    <td>{{$item['AccountDescription']}}</td>
                    <td>{{$item['DocumentCode']}}</td>
                    <td>{{ \Carbon\Carbon::parse($item['DocumentDate'])->format("d/m/Y") }}</td>
                    <td>{{$item['Amount']}}</td>
                </tr>

                @php $total += $item['Amount'] @endphp
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

