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
        <td><B>{{ __('custom.period_from') }}: </B></td>
        <td><B>{{ date('d/m/Y', strtotime($fromDate)) }}</B></td>
        <td><B>{{ __('custom.period_to') }}:</B></td>
        <td><B>{{ date('d/m/Y', strtotime($toDate)) }}</B></td>

    </tr>
    </thead>
</table>


    <br>
    @foreach($reportData as $name => $key)
    <div>
        <h4>{{ $name }}</h4>
        <h4>{{ __('custom.supplier_group') }} :    @if (isset($reportData[$name]['supplierGroupName']))
                                    {{ $reportData[$name]['supplierGroupName'] }}</h4>
                                @endif
    </div>
    @foreach($reportData[$name]['data'] as $currencyKey => $key)

        <table>
        <thead>
    <tr>
        <th>{{ __('custom.document_code') }}</th>
        <th>{{ __('custom.posted_date') }}</th>
        <th>{{ __('custom.account') }}</th>
        <th>{{ __('custom.invoice_number') }}</th>
        <th>{{ __('custom.invoice_date') }}</th>
        <th>{{ __('custom.document_narration') }}</th>
        <th>{{ __('custom.currency') }}</th>
        <th>{{ __('custom.document_amount') }}</th>
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
        <td><B>{{ __('custom.total') }}</B></td>
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
        <td><B>{{ __('custom.grand_total') }}</B></td>
        <td style="text-align: right"><B>{{ number_format($invoiceAmount,$currencyDecimalPlace) }}</B></td>
    </tr>
</table>
</html>

