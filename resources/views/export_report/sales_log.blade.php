<html>
<center>
    <table>
    <thead>
    <tr></tr>
    <tr>
        <td colspan="1">Company</td>
        <th style="font-size:15px;">{{($companyData->CompanyName)}}</th>
    </tr>
    <tr>
        <td colspan="1">Report Name</td>
        <th style="font-size:15px;">Sales Log Report</th>
    </tr>
    <tr>
        <td colspan="1">Report Date</td>
        <th style="font-size:15px;">{{ $report_date }}</th>
    </tr>
    <tr>
        <td colspan="1">Currency</td>
        <th style="font-size:15px;">{{$currency->CurrencyCode}}</th>

    </tr>
    <tr></tr>
    <tr></tr>
    </thead>
    </table>
</center>

<table>
    <thead>
    <tr>
        <th>Customer Name</th>
        <th>Current Year</th>
        <th>%</th>
        <th>Last Year</th>
        <th>%</th>
    </tr>
    </thead>
    <tbody>
    @php $total = 0; $total2=0; @endphp
    @foreach($data as $item)
        <tr>
            <td>{{$item->customer->CustomerName}}</td>
            <td>{{$item->currentYearValue}}</td>
            <td>{{number_format($item->currentYearPercentage,2)}}</td>
            <td>{{$item->previousYearValue}}</td>
            <td>{{number_format($item->previousYearPercentage,2)}}</td>
        </tr>
        @php $total += $item['currentYearValue'] @endphp
        @php $total2 += $item['previousYearValue'] @endphp

    @endforeach
    <tr>
        <td>Total</td>
        <td>{{$total}}</td>
        <td></td>
        <td>{{$total2}}</td>
        <td></td>

    </tr>
    </tbody>
</table>


