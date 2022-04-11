<html>

<table>
    <thead>
    <tr>
        <td><B>Period From: </B></td>
        <td><B>{{ date('d/m/Y', strtotime($fromDate)) }}</B></td>
        <td><B>Period To:</B></td>
        <td><B>{{ date('d/m/Y', strtotime($toDate)) }}</B></td>

    </tr>
    </thead>
</table>
    @foreach($reportData as $name => $key)
    <div>
        <B>{{ $name }}</B>
    </div>
    @foreach($reportData[$name] as $currencyKey => $key)

        <table>
        <thead>
    <tr>
        <th>Document Code</th>
        <th>Posted Date</th>
        <th>Invoice Number</th>
        <th>Invoice date</th>
        <th>Document Narration</th>
        <th>Currency</th>
        <th>Document Amount</th>
    </tr>
    </thead>
    <tbody>
    @php
        $total = 0;
    @endphp
    @foreach($reportData[$name][$currencyKey] as $data)
        <tr>
        <td>{{ $data->documentCode }}</td>
            @if($data->documentDate != null)
        <td>&nbsp;{{ date('d/m/Y', strtotime($data->documentDate)) }}</td>
            @else
                <td></td>
            @endif
        <td>{{ $data->invoiceNumber }}</td>
            @if($data->invoiceDate != null)
        <td>&nbsp;{{ date('d/m/Y', strtotime($data->invoiceDate)) }}</td>
            @else
                <td></td>
            @endif
        <td>{{ $data->documentNarration }}</td>
        <td>{{ $data->documentCurrency }}</td>
        <td style="text-align: right">{{ number_format($data->invoiceAmount,$data->balanceDecimalPlaces) }}</td>
            @php
                $total += $data->invoiceAmount;
            @endphp
    </tr>
    @endforeach
    </tbody>
    <tfoot>
    <tr>
        <td colspan="5"></td>
        <td><B>Total</B></td>
        @if(isset($reportData[$name][$currencyKey][0]) != null)
            <td style="text-align: right"><B>{{ number_format($total,$reportData[$name][$currencyKey][0]->balanceDecimalPlaces) }}</B></td>
        @endif
    </tr>
    </tfoot>
</table>
@endforeach
@endforeach
<table>
    <tr>
        <td colspan="5"></td>
        <td><B>Grand Total</B></td>
        <td style="text-align: right"><B>{{ number_format($invoiceAmount,$currencyDecimalPlace) }}</B></td>
    </tr>
</table>
</html>

