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
        font-size: 11px;
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

    .table th {
        border: 1px solid rgb(127, 127, 127) !important;
    }

    .table th, .table td {
        padding: 0.4rem !important;
        vertical-align: top;
        border-bottom: 1px solid rgb(127, 127, 127) !important;
    }

    .table th {
        background-color: #EBEBEB !important;
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

    #watermark {
        position: fixed;
        width: 100%;
        height: 100%;
        padding-top: 31%;
    }

    .watermarkText {
        color: #dedede !important;
        font-size: 30px;
        font-weight: 700 !important;
        text-align: center !important;
        font-family: fantasy !important;
    }

    #watermark {
        height: 1000px;
        opacity: 0.6;
        left: 0;
        transform-origin: 20% 20%;
        z-index: 1000;
    }

</style>
<div class="footer">
    <table style="width:100%;">
        <tr>
            <td colspan="2" style="width:100%">
                <span class="font-weight-bold">Kindly confirm the balance and settle the pending invoices at the earliest.</span>
            </td>
        </tr>
        <tr>
            <td style="width:50%;font-size: 10px;vertical-align: top;">
                <span style="">Printed Date : {{date("d-M-y", strtotime(now()))}}</span>
            </td>
            <td style="width:50%; text-align: center;font-size: 10px;vertical-align: top;">
                <span style="text-align: center">Page <span class="pagenum"></span></span><br>
            </td>
        </tr>
    </table>
</div>
<div class="content">
    <div class="row">
        <table style="width:100%;" class="table table-bordered table-striped table-sm">
            @foreach ($reportData as $key => $val)
                <tr style="border-top: 1px solid black;">
                    <th width="10%">Document Code</th>
                    <th width="10%">Posted Date</th>
                    <th width="5%">Contract</th>
                    <th width="5%">PO Number</th>
                    <th width="10%">Invoice Date</th>
                    <th width="15%">Narration</th>
                    <th width="5%">Currency</th>
                    <th width="10%">Invoice Amount</th>
                    <th width="5%">Receipt/CN Code</th>
                    <th width="5%">Receipt/CN Date</th>
                    <th width="10%">Receipt Amount</th>
                    <th width="10%">Balance Amount</th>
                </tr>
                {{ $subInvoiceAmount = 0 }}
                {{ $subReceiptAmount = 0 }}
                {{ $subBalanceAmount = 0 }}
                @foreach ($val as $det)
                    {{ $subInvoiceAmount += $det->invoiceAmount }}
                    {{ $subReceiptAmount += $det->receiptAmount }}
                    {{ $subBalanceAmount += $det->balanceAmount }}
                    <tr style="border-bottom: 1px solid black;">
                        <td>{{ $det->documentCode  }}</td>
                        <td>{{$det->postedDate}}</td>
                        <td>{{$det->clientContractID}}</td>
                        <td>{{$det->invoiceDate}}</td>
                        <td>{{$det->documentNarration}}</td>
                        <td>{{$det->documentCurrency}}</td>
                        <td class="text-right">{{number_format($det->invoiceAmount, $det->balanceDecimalPlaces)}}</td>
                        <td>{{$det->ReceiptCode}}</td>
                        <td>{{$det->ReceiptDate}}</td>
                        <td class="text-right">{{number_format($det->receiptAmount, $det->balanceDecimalPlaces)}}</td>
                        <td class="text-right">{{number_format($det->balanceAmount, $det->balanceDecimalPlaces)}}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="7" class="text-right"><b>Sub Total:</b></td>
                    <td class="text-right">{{number_format($subInvoiceAmount, $reportData->$key[0]->balanceDecimalPlaces)}}</td>
                    <td colspan="2" style="border-bottom-color:white !important;border-left-color:white !important"
                        class="text-right"></td>
                    <td class="text-right">{{number_format($subReceiptAmount, $reportData->$key[0]->balanceDecimalPlaces)}}</td>
                    <td class="text-right">{{number_format($subBalanceAmount, $reportData->$key[0]->balanceDecimalPlaces)}}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="7" class="text-right"><b>Grand Total:</b></td>
                <td class="text-right">{{number_format($invoiceAmount, $currencyDecimalPlace)}}</td>
                <td colspan="2" style="border-bottom-color:white !important;border-left-color:white !important"
                    class="text-right"></td>
                <td class="text-right">{{number_format($receiptAmount, $currencyDecimalPlace)}}</td>
                <td class="text-right">{{number_format($balanceAmount, $currencyDecimalPlace)}}</td>
            </tr>
        </table>
    </div>
</div>
