<html>
<head>
    <style>
        @page {
            margin: 100px 30px 40px;
        }

        #header {
            position: fixed;
            left: 0px;
            top: -100px;
            right: 0px;
            height: 50px;
            text-align: center;
        }

        #footer {
            position: fixed;
            left: 0px;
            bottom: 0px;
            right: 0px;
            height: 0px;
            font-size: 10px;
        }

        #footer .page:after {
            content: counter(page, upper-roman);
        }

        body {
            font-size: 10px;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol"
        }

        .pagenum:after {
            content: counter(page);
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

    </style>

<body>
<div id="header">
    <div class="row">
        <div class="col-md-12">
            <table style="width: 100%">
                <tr>
                    <td valign="top" style="width: 45%">
                        <img src="{{$companylogo}}" width="180px" height="60px"><br>
                    </td>
                    <td valign="top" style="width: 55%">
                        <br><br>
                        <span class="font-weight-bold">{{ trans('custom.customer_balance_statement') }}</span><br>
                        <span class="font-weight-bold">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ trans('custom.as_of') }} {{ $fromDate }}</span>
                    </td>
                </tr>
                <tr>
                    <td valign="top" style="width: 45%">
                        <span class="font-weight-bold"> {{$companyName}}</span>
                    </td>
                    <td>

                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
<div id="footer">
    <table style="width:100%;">
        <tr>
            <td style="width:50%;font-size: 10px;vertical-align: bottom;">
                <span>{{ trans('custom.printed_date') }} : {{date("d-M-y", strtotime(now()))}}</span>
            </td>
            <td style="width:50%; text-align: center;font-size: 10px;vertical-align: bottom;">
                <span style="float: right;">{{ trans('custom.page') }} <span class="pagenum"></span></span><br>
            </td>
        </tr>
    </table>
</div>
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
                    <td colspan="8" style="border-bottom-color:white !important;border-left-color:white !important"
                        class="text-right"><b>{{ trans('custom.total') }}:</b></td>
                    <td style="text-align: right"><b>{{ number_format($lineTotal, $currencyDecimalPlace) }}</b></td>
                </tr>
            @endforeach
            </tbody>
        @endforeach
        <tfoot>
        <tr>
            <td colspan="8" style="border-bottom-color:white !important;border-left-color:white !important"
                class="text-right"><b>{{ trans('custom.grand_total') }}:</b></td>
            <td style="text-align: right"><b>{{ number_format($grandTotal, $currencyDecimalPlace) }}</b></td>
        </tr>
        </tfoot>
    </table>
</div>
</body>
</html>
