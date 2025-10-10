<html @if(isset($lang) && $lang === 'ar') dir="rtl" @endif>
<head>
    <style>
        @if(isset($lang) && $lang === 'ar')
        body {
            direction: rtl;
            text-align: right;
        }

        table {
            direction: rtl;
        }

        .table th, .table td {
            text-align: right;
        }

        .table .text-left {
            text-align: left !important;
        }
        @endif
        body {
            font-size: 10px;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol"
        }


        table {
            border-collapse: collapse;
        }

        table > tbody > th > tr > td {
            font-size: 10px;
        }

        table > thead > th {
            font-size: 10px;
        }

        .table th, .table td {
            padding: 0.4rem !important;
            vertical-align: top;
            border: 1px solid #dee2e6 !important;
            /* border-bottom: 1px solid rgb(127, 127, 127) !important;*/
        }

        .table th {
            background-color: #D7E4BD !important;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f9f9f9;
        }
        .font-weight-bold {
            font-weight: 700 !important;
            font-size: 12px;
            text-align: center;
        }
        .text-right {
            text-align: right !important;
        }

        .text-left {
            text-align: left !important;
        }

    </style>

<body>
<div id="content">
    <table style="width:100%;border:1px solid #9fcdff" class="table">
        @foreach ($reportData as $key => $val)
            <tr>
                <td colspan="9"><b>{{$key}}</b></td>
            </tr>
            <tr>
                <th width="10%">{{ trans('custom.document_code') }}</th>
                <th width="10%">{{ trans('custom.posted_date') }}</th>
                <th width="25%">{{ trans('custom.narration') }}</th>
                <th width="10%">{{ trans('custom.contract') }}</th>
                <th width="10%">{{ trans('custom.po_number') }}</th>
                <th width="10%">{{ trans('custom.invoice_number') }}</th>
                <th width="10%">{{ trans('custom.invoice_date') }}</th>
                <th width="5%">{{ trans('custom.currency') }}</th>
                <th width="10%">{{ trans('custom.balance_amount') }}</th>
            </tr>
            <tbody>
            @foreach ($val as $det)
                {{ $lineTotal = 0 }}
                @foreach ($det as $det2)
                    <tr>
                        <td>{{ $det2->DocumentCode }}</td>
                        <td> {{ \App\helper\Helper::dateFormat($det2->PostedDate)}}</td>
                        <td>{{ $det2->DocumentNarration }}</td>
                        <td>{{ $det2->Contract }}</td>
                        <td>{{ $det2->PONumber }}</td>
                        <td>{{ $det2->invoiceNumber }}</td>
                        <td> {{ \App\helper\Helper::dateFormat($det2->InvoiceDate)}}</td>
                        <td>{{ $det2->documentCurrency }}</td>
                        <td style="text-align: right"> {{ number_format($det2->balanceAmount, $currencyDecimalPlace) }}</td>
                    </tr>
                    {{$lineTotal += $det2->balanceAmount}}
                @endforeach
                <tr>
                    <td colspan="8" style="border-bottom: none; border-left: none;"
                        class="@if(isset($lang) && $lang === 'ar') text-left @else text-right @endif"><b>{{ trans('custom.total') }}:</b></td>
                    <td style="text-align: right"><b>{{ number_format($lineTotal, $currencyDecimalPlace) }}</b></td>
                </tr>
            @endforeach
            </tbody>
        @endforeach
        <tfoot>
        <tr>
            <td colspan="8" style="border-bottom: none; border-left: none;"
                class="@if(isset($lang) && $lang === 'ar') text-left @else text-right @endif"><b>{{ trans('custom.grand_total') }}:</b></td>
            <td style="text-align: right"><b>{{ number_format($grandTotal, $currencyDecimalPlace) }}</b></td>
        </tr>
        </tfoot>
    </table>
</div>
</body>
</html>
