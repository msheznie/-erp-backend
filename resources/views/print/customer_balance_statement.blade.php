<style type="text/css">
    @page {
        margin-left: 3%;
        margin-right: 3%;
        margin-top: 4%;
    }

    .footer {
        position: absolute;
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
        white-space: pre-line;
    }

    p {
        margin-top: 0 !important;
    }

    .title {
        font-size: 13px;
        font-weight: 600;
    }

    .footer {
        bottom: 0;
        height: 40px;
    }

    .footer {
        width: 100%;
        text-align: center;
        position: fixed;
        font-size: 10px;
        padding-top: -20px;
    }

    .pagenum:after {
        content: counter(page);
    }

    .content {
        margin-bottom: 45px;
    }

    header {
        position: fixed;
        top: -60px;
        left: 0px;
        right: 0px;
        height: 50px;

        /** Extra personal styles **/
        background-color: #03a9f4;
        color: white;
        text-align: center;
        line-height: 35px;
    }

</style>
<div class="footer">
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
<div class="header">
    <div class="row">
        <div class="col-md-12">
            <table style="width: 100%">
                <tr>
                    <td valign="top" style="width: 45%">
                        <img src="logos/{{$companylogo}}" width="180px" height="60px"><br>
                    </td>
                    <td valign="top" style="width: 55%">
                        <span class="font-weight-bold" style="font-size: 12px; text-align: center;">Customer Balance Statement</span><br>
                        <span class="font-weight-bold" style="font-size: 12px; text-align: center;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;As of {{ $fromDate }}</span>
                    </td>
                </tr>
                <tr>
                    <td valign="top" style="width: 45%">
                        <span style="font-size: 12px;font-weight: 400"> {{$companyName}}</span>
                    </td>
                    <td>

                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
<br><br>
<div class="content">
    <table style="width:100%;border:1px solid #9fcdff" class="table">
        @foreach ($reportData as $key => $val)
            <tr>
                <td colspan="9"><b>{{$key}}</b></td>
            </tr>
            <tr>
                <th width="10%">Document Code</th>
                <th width="10%">Posted Date</th>
                <th width="25%">Narration</th>
                <th width="10%">Contract</th>
                <th width="10%">PO Number</th>
                <th width="10%">Invoice Number</th>
                <th width="10%">Invoice Date</th>
                <th width="5%">Currency</th>
                <th width="10%">Balance Amount</th>
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
                        class="text-right"><b>Total:</b></td>
                    <td style="text-align: right"><b>{{ number_format($lineTotal, $currencyDecimalPlace) }}</b></td>
                </tr>
            @endforeach
            </tbody>
        @endforeach
        <tfoot>
        <tr>
            <td colspan="8" style="border-bottom-color:white !important;border-left-color:white !important"
                class="text-right"><b>Grand Total:</b></td>
            <td style="text-align: right"><b>{{ number_format($grandTotal, $currencyDecimalPlace) }}</b></td>
        </tr>
        </tfoot>
    </table>
</div>
