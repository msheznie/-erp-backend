<style type="text/css">
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
        font-size: 11px;
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
    }

    .table th {
        background-color: #D7E4BD !important;
    }

    table > tbody > th > tr > td {
        font-size: 10px;
    }

    table > thead > th {
        font-size: 10px;
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

    .pagenum:after {
        content: counter(page);
    }



</style>
<div id="footer">
    <table style="width:100%;">
        <tr>
            <td colspan="2" style="width:100%">
                <span class="font-weight-bold"></span>
            </td>
        </tr>
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
                    <td valign="top" style="width: 40%">
                        <img src="{{$companyLogo}}" width="180px" height="60px"><br>
                    </td>
                    <td valign="top" style="width: 60%">
                        <br><br>
                        <span class="font-weight-bold">Customer Ledger for as of date {{ $fromDate }}</span>
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
<div id="content">
    <table style="width:100%;border:1px solid #9fcdff" class="table">
        @foreach ($reportData as $key => $val)
            <tr style="width:100%">
                <td colspan="11"><span style="font-size: 11px; font-weight: bold">{{$key}}</span></td>
            </tr>
            @foreach ($val as $key2 => $val2)
                <tr style="width:100%">
                    <th>Document Code</th>
                    <th>Posted Date</th>
                    <th>Account</th>
                    <th>Invoice Number</th>
                    <th>Invoice Date</th>
                    <th>Contract</th>
                    <th>PO Number</th>
                    <th>Narration</th>
                    <th>Currency</th>
                    <th>Invoice Amount</th>
                    <th>Received Amount</th>
                    <th>Balance Amount</th>
                    <th>Age Days</th>
                </tr>
                <tbody>
                {{ $lineTotal1 = 0 }}
                {{ $lineTotal2 = 0 }}
                {{ $lineTotal3 = 0 }}
                @foreach ($val2 as $det2)
                    <tr style="width:100%">
                        <td>{{ $det2->DocumentCode }}</td>
                        <td>{{ \App\helper\Helper::dateFormat($det2->PostedDate)}}</td>
                        <td>{{ $det2->AccountDescription }}</td>
                        <td class="white-space-pre-line">{{ $det2->invoiceNumber }}</td>
                        <td> {{ \App\helper\Helper::dateFormat($det2->InvoiceDate)}}</td>
                        <td>{{ $det2->Contract }}</td>
                        <td>{{ $det2->PONumber }}</td>
                        <td>{{ $det2->DocumentNarration }}</td>
                        <td>{{ $det2->documentCurrency }}</td>
                        <td class="text-right">{{ number_format($det2->invoiceAmount, $currencyDecimalPlace) }}</td>
                        <td class="text-right">{{ number_format($det2->paidAmount, $currencyDecimalPlace) }}</td>
                        <td class="text-right">{{ number_format($det2->balanceAmount, $currencyDecimalPlace) }}</td>
                        <td>{{ $det2->ageDays }}</td>
                    </tr>
                    {{$lineTotal1 += $det2->invoiceAmount}}
                    {{$lineTotal2 += $det2->paidAmount}}
                    {{$lineTotal3 += $det2->balanceAmount}}
                @endforeach
                <tr width="100%">
                    <td colspan="8" style="border-bottom-color:white !important;border-left-color:white !important"
                            class="text-right"><b>Total:</b></td>
                  
                    <td style="text-align: right"><b>{{ number_format($lineTotal1, $currencyDecimalPlace) }}</b></td>
                    <td style="text-align: right"><b>{{ number_format($lineTotal2, $currencyDecimalPlace) }}</b></td>
                    <td style="text-align: right"><b>{{ number_format($lineTotal3, $currencyDecimalPlace) }}</b></td>
                    <td></td>
                </tr>
                </tbody>
            @endforeach
        @endforeach
    </table>
</div>
