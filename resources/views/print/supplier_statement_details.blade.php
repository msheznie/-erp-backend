<style type="text/css">
    @page {
        margin: 110px 30px 40px;
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

    h3 {
        font-size: 1.53125rem;
    }

    h6 {
        font-size: 0.875rem;
    }

    h6, h3 {
        margin-bottom: 0.1rem;
        font-weight: 500;
        line-height: 1.2;
        color: inherit;
    }

    table > tbody > th > tr > td {
        font-size: 10px;
    }

    table > thead > th {
        font-size: 10px;
    }

    .theme-tr-head {
        background-color: #EBEBEB !important;
    }

    .text-left {
        text-align: left;
    }

    table {
        border-collapse: collapse;
    }

    .font-weight-bold {
        font-weight: 700 !important;
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

    tfoot > tr > td {
        border: 1px solid rgb(127, 127, 127);
    }

    .text-right {
        text-align: right !important;
    }

    .font-weight-bold {
        font-weight: 700 !important;
    }

    hr {
        border: 0;
        border-top: 1px solid rgba(0, 0, 0, 0.1);
    }

    th {
        text-align: inherit;
        font-weight: bold;
    }

    .table-striped tbody tr:nth-of-type(odd) {
        background-color: #f9f9f9;
    }

    .white-space-pre-line {
        word-wrap: break-word;
    }

    p {
        margin-top: 0 !important;
    }

    .pagenum:after {
        content: counter(page);
    }

    .content {
        margin-bottom: 45px;
    }

</style>
<div id="footer">
    <table style="width:100%;">
        <tr>
            <td style="width:50%;font-size: 10px;vertical-align: bottom;">
                <span>Printed Date : {{date("d-M-y", strtotime(now()))}}</span>
            </td>
            <td style="width:50%; text-align: center;font-size: 10px;vertical-align: bottom;">
                <span style="float: right;">Page <span class="pagenum"></span></span><br>
            </td>
        </tr>
    </table>
</div>
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
                        <span class="font-weight-bold">{{$companyName}}</span><br>
                        <span class="font-weight-bold">Supplier Statement Details</span><br>
                        <span class="font-weight-bold">&nbsp;&nbsp;&nbsp;As of {{ $fromDate }}</span>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
<br><br>
<div id="content">
    <table style="width:100%;border:1px solid #9fcdff" class="table">

        <tr style="width:100%">
            <th>Payable Account</th>
            <th>Prepayment Account</th>
            <th>Currency</th>
            <th>Supplier Name</th>
            <th>Open Supplier Invoices</th>
            <th>Open Advance to Suppliers</th>
            <th>Open Debit Notes</th>
            <th>Total Payable</th>
            <th>Total Prepayment</th>
            <th>Net Outstanding</th>
        </tr>
        <tbody>
        @foreach ($reportData as $key => $supplier)
            <tr style="width:100%">
                <td>{{ $supplier['payable_account'] }}</td>
                <td>{{ $supplier['prePayment_account'] }}</td>
                <td>{{ $supplier['supplier_currency']}}</td>
                <td>{{ $key }}</td>
                <td>{{ $supplier['open_invoices'] }}</td>
                <td>{{ $supplier['open_advances'] }}</td>
                <td>{{ $supplier['open_debit_notes'] }}</td>
                <td class="text-right">{{ $supplier['open_invoices'] }}</td>
                <td class="text-right">{{ number_format(($supplier['open_advances'] + $supplier['open_debit_notes']) , $currencyDecimalPlace) }}</td>
                <td class="text-right">{{ number_format($supplier['open_invoices'] - ($supplier['open_advances'] + $supplier['open_debit_notes']), $currencyDecimalPlace) }}</td>
            </tr>
        @endforeach
        <tr width="100%">
                <td colspan="4" style="border-bottom-color:white !important;border-left-color:white !important"
                    class="text-right"><b>Total:</b></td>

                <td style="text-align: right"><b>{{ number_format($totalArray['totalInvoices'], $currencyDecimalPlace) }}</b></td>
                <td style="text-align: right"><b>{{ number_format($totalArray['totalAdvances'], $currencyDecimalPlace) }}</b></td>
                <td style="text-align: right"><b>{{ number_format($totalArray['totalDebitNotes'], $currencyDecimalPlace) }}</b></td>
                <td style="text-align: right"><b>{{ number_format($totalArray['totalInvoices'], $currencyDecimalPlace) }}</b></td>
                <td style="text-align: right"><b>{{ number_format($totalArray['totalPrepayment'], $currencyDecimalPlace) }}</b></td>
                <td style="text-align: right"><b>{{ number_format($totalArray['totalNetOutstanding'], $currencyDecimalPlace) }}</b></td>
        </tr>
        </tbody>
        <tfoot>
        </tfoot>
    </table>
</div>
