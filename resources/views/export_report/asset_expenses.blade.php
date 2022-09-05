<html>
<center>
    <table>
    <thead>
    <tr></tr>
    <tr>
        <td colspan="2"></td>
        <td><h3>Asset Expenses Report</h3></td>
    </tr>
    <tr>
        <td colspan="2"></td>
        <td><B>From {{(new \Illuminate\Support\Carbon($fromDate))->format('d/m/Y')}} - To {{(new \Illuminate\Support\Carbon($toDate))->format('d/m/Y')}}</B></td>
    </tr>
    <tr>
        <td colspan="2"></td>
        @if($currencyID == 2)
        <td><B>Currency - {{$currency->localCurrency->CurrencyCode}}</B></td>
        @endif
        @if($currencyID == 3)
        <td><B>Currency - {{$currency->reportingcurrency->CurrencyCode}}</B></td>
        @endif
    </tr>
    <tr></tr>
    <tr></tr>
    </thead>
    </table>
</center>
@foreach($headers as $header)
    <tr><th><B>{{ $header[0]['AccountCode'] }} - {{ $header[0]['AccountDescription'] }}</B></th></tr>
<table>
    <thead>
    <tr>
        <th>Asset Code</th>
        <th>Asset Description</th>
        <th>Document Code</th>
        <th>Document Date</th>
        <th>Amount</th>
    </tr>
    </thead>
    <tbody>
    @php $total = 0 @endphp
    @foreach($reportData as $item)
        @if($item['AccountCode'] == $header[0]['AccountCode'])
        <tr>
            <td>{{$item['AssetCode']}}</td>
            <td>{{$item['AssetDescription']}}</td>
            <td>{{$item['DocumentCode']}}</td>
            <td>{{$item['DocumentDate']}}</td>
            <td>{{$item['Amount']}}</td>
        </tr>

        @php $total += $item['Amount'] @endphp
        @endif

    @endforeach

    </tbody>
    <tfoot>
    <tr>
        <td colspan="3"></td>
        <td>Total</td>
        <td>{{$total}}</td>
    </tr>
    </tfoot>
</table>
@endforeach

