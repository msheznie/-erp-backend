<html>

<table>
    <thead>
    <tr>
        <td>Period From: </td>
        <td>{{ date('d/m/Y', strtotime($fromDate)) }}</td>
        <td>Period To:</td>
        <td>{{ date('d/m/Y', strtotime($toDate)) }}</td>

    </tr>
    </thead>
</table>
    @foreach($reportData as $name => $key)
    <div>
        <h3>{{ $name }}</h3>
    </div>
    @foreach($reportData[$name] as $currencyKey => $key)

        <table>
        <thead>
    <tr>
        <td>Document Code</td>
        <td>Posted Date</td>
        <td>Invoice Number</td>
        <td>Invoice date</td>
        <td>Document Narration</td>
        <td>Currency</td>
        <td>Document Amount</td>
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
        <td>{{ number_format($data->invoiceAmount,$data->balanceDecimalPlaces) }}</td>
            @php
                $total += $data->invoiceAmount;
            @endphp
    </tr>
    @endforeach
    </tbody>
    <tfoot>
    <tr>
        <td colspan="5"></td>
        <td>Total</td>
        @if(isset($reportData[$name][$currencyKey][0]) != null)
        <td>{{ number_format($total,$reportData[$name][$currencyKey][0]->balanceDecimalPlaces) }}</td>
        @endif
    </tr>
    </tfoot>
</table>
@endforeach
@endforeach
<table>
    <tr>
        <td colspan="5"></td>
        <td>Grand Total</td>
        <td>{{ number_format($invoiceAmount,$currencyDecimalPlace) }}</td>
    </tr>
</table>
</html>

