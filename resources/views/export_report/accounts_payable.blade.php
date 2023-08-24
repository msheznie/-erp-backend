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
        <th style="font-size:15px;">Overdue Payable</th>
    </tr>
    <tr>
        <td colspan="1">Report Date</td>
        <th style="font-size:15px;">{{ $report_date }}</th>
    </tr>
    <tr></tr>
    <tr></tr>
    </thead>
    </table>
</center>

<table>
    <thead>
    <tr>
        <th>Name</th>
        <th>Currency</th>
        <th>Amount</th>
    </tr>
    </thead>
    <tbody>
    @foreach($overduePayable as $item)
        <tr>
            <td>{{$item->supplier->nameOnPaymentCheque}}</td>
            <td>{{$item->rptcurrency->CurrencyCode}}</td>
            <td>{{number_format($item->total,$item->rptcurrency->DecimalPlaces)}}</td>
        </tr>
    @endforeach
    </tbody>
</table>


