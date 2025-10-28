
<html>
    <table>
        <thead>
            <tr>
                <h1 colspan="9" style="text-align: center;">{{$companyName}}</h1>
            </tr>
            <tr>
                <h2  colspan="9" style="text-align: center;">{{trans('custom.customer_ledger')}}</h2>
            </tr>
            <tr>
                <h4  colspan="9" style="text-align: center;">{{trans('custom.from')}} {{\App\helper\Helper::dateFormat($fromDate)}} {{trans('custom.to')}} {{\App\helper\Helper::dateFormat($toDate)}}</h4>
            </tr>
        </thead>
        @foreach($reportData as $name => $value)
            <thead>
                <tr></tr>
                <tr></tr>
                <tr>
                    <th>{{$name}}</th>
                </tr>
            </thead>

            @foreach($reportData[$name] as $currencyKey => $value)
                <thead>
                    <tr></tr>
                    <tr>
                        <th></th>
                        <th>{{ trans('custom.document_code') }}</th>
                        <th>{{ trans('custom.posted_date') }}</th>
                        <th>{{ trans('custom.account') }}</th>
                        <th>{{ trans('custom.invoice_number') }}</th>
                        <th>{{ trans('custom.invoice_date') }}</th>
                        <th>{{ trans('custom.narration') }}</th>
                        <th>{{ trans('custom.currency') }}</th>
                        <th>{{ trans('custom.amount') }}</th>
                    </tr>
                </thead>
                
                <tbody>
                    @foreach ($reportData[$name][$currencyKey] as $data => $value)
                        <tr style="text-align: left;">
                            <td></td>
                            @if ($value->DocumentCode)
                                <td>{{$value->DocumentCode}}</td>
                            @else
                                <td>-</td>
                            @endif

                            @if ($value->PostedDate && $value->PostedDate != '1970-01-01')
                                <td>{{ \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(\App\helper\Helper::dateFormat($value->PostedDate))}}</td>
                            @else
                                <td>-</td>
                            @endif

                            @if ($value->AccountDescription)
                                <td>{{ $value->AccountDescription }}</td>
                            @else
                                <td>-</td>
                            @endif

                            @if ($value->invoiceNumber)
                                <td>{{$value->invoiceNumber}}</td>
                            @else
                                <td>-</td>
                            @endif

                            @if ($value->InvoiceDate)
                                <td>{{ \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(\App\helper\Helper::dateFormat($value->InvoiceDate))}}</td>
                            @else
                                <td>-</td>
                            @endif

                            @if ($value->DocumentNarration)
                                <td>{{$value->DocumentNarration}}</td>
                            @else
                                <td>-</td>
                            @endif

                            @if ($value->documentCurrency)
                                <td>{{$value->documentCurrency}}</td>
                            @else
                                <td>-</td>
                            @endif

                            @if ($value->invoiceAmount)
                                <td>{{\App\Services\Currency\CurrencyService::convertNumberFormatToNumber($value->invoiceAmount)}}</td>
                            @else
                                <td>-</td>
                            @endif
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="8" style="text-align: right;"><b>{{ trans('custom.total') }}:</b></td>
                        <td style="text-align: left;  font-weight: bold">{{\App\Services\Currency\CurrencyService::convertNumberFormatToNumber(\App\helper\Helper::customerLedgerReportSum($reportData[$name][$currencyKey], 'invoice'))}}</td>
                        <td></td>
                    </tr>
                </tbody>
            @endforeach
        @endforeach
        <tbody>
            <tr>
                <td colspan="8" style="text-align: right"><b>{{ trans('custom.grand_total') }}:</b></td>
                <td style="text-align: left; font-weight: bold;">
                        @if(isset($invoiceAmount))
                        {{\App\Services\Currency\CurrencyService::convertNumberFormatToNumber(round($invoiceAmount, $currencyDecimalPlace))}}
                        @else
                        0
                        @endif
                </td>
                <td></td>
            </tr>
        </tbody>
    </table>
</html>
