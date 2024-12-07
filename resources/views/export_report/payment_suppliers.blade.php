<html>
<table>
        <thead>
        <div>
                <td colspan="3"></td>
                <td><B>{{$companyName}} </B></td>
            </div>
        </thead>
    </table>
    <table>
        <thead>
        <div>
                <td colspan="3"></td>
                <td><B>{{$Title}}</B></td>
            </div>
        </thead>
    </table>
    <br>
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


    <br>
    @foreach($reportData as $name => $key)
    <div>
        <h4>{{ $name }}</h4>
        <h4>Supplier Group :    @if (isset($reportData[$name]['supplierGroupName']))
                                    {{ $reportData[$name]['supplierGroupName'] }}</h4>
                                @endif
    </div>
    @foreach($reportData[$name]['data'] as $currencyKey => $key)

        <table>
        <thead>
    <tr>
        <th>Document Code</th>
        <th>Posted Date</th>
        <th>Account</th>
        <th>Invoice Number</th>
        <th>Invoice date</th>
        <th>Document Narration</th>
        <th>Currency</th>
        <th>Document Amount</th>
    </tr>
    </thead>
    <tbody>
    @php
        $total = 0.00;
    @endphp
    @foreach($reportData[$name]['data'][$currencyKey] as $data)
        <tr>
        <td>{{ $data->documentCode }}</td>
            @if($data->documentDate != null)
                <td>{{ $data->documentDate }}</td>
            @else
                <td>-</td>
            @endif
        <td>{{ $data->glCode }} - {{ $data->accountDescription }}</td>
        <td>{{ $data->invoiceNumber }}</td>
            @if($data->invoiceDate != null)
        <td>{{ $data->invoiceDate }}</td>
            @else
                <td>-</td>
            @endif
        <td>{{ $data->documentNarration }}</td>
        <td>{{ $data->documentCurrency }}</td>
        <td style="text-align: right">{{ $data->invoiceAmount }}</td>
            @php
                $total += $data->documentAmount;
            @endphp
    </tr>
    @endforeach
    </tbody>
    <tfoot>
    <tr>
        <td colspan="6"></td>
        <td><B>Total</B></td>
        @if(isset($reportData[$name][$currencyKey][0]) != null)
            <td style="text-align: right; font-weight: bold;">{{ \App\Services\Currency\CurrencyService::convertNumberFormatToNumber(number_format(trim($total),$reportData[$name][$currencyKey][0]->balanceDecimalPlaces))}}</td>
        @endif
    </tr>
    </tfoot>
</table>
@endforeach
@endforeach
<table>
    <tr>
        <td colspan="6"></td>
        <td><B>Grand Total</B></td>
        <td style="text-align: right"><B>{{ number_format($invoiceAmount,$currencyDecimalPlace) }}</B></td>
    </tr>
</table>
</html>

