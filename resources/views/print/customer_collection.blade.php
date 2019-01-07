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

    .table th, .table td {
        padding: 0.4rem !important;
        vertical-align: top;
        border: 1px solid #dee2e6 !important;
        /* border-bottom: 1px solid rgb(127, 127, 127) !important;*/
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
    <table style="width:100%;">
        <tr>
            <td colspan="2" style="width:100%;text-align: center;">
                <span class="font-weight-bold">Collection Report</span>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="width:100%;text-align: center;">
                <span class="font-weight-bold">Collection for the Period {{ $fromDate }}
                    to {{ $toDate }}</span>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="width:100%;text-align: center;">
                <span class="font-weight-bold">Currency : {{ $selectedCurrency }}</span>
            </td>
        </tr>
    </table>
</div>
<br><br>
<div class="content">
    <table style="width:100%;border:1px solid #9fcdff" class="table">
        @foreach ($reportData as $key => $val)
            <thead>
            <tr>
                <td colspan="5"><b>{{$key}}</b></td>
            </tr>
            <tr>
                <th width="20%">Customer Code</th>
                <th width="50%">Customer Name</th>
                <th width="10%">Bank Payment</th>
                <th width="10%">Credit Note Issued</th>
                <th width="10%">Total</th>
            </tr>
            </thead>
            <tbody>
            {{ $grandTotalBank = 0 }}
            {{ $grandTotalCredit = 0 }}
            @foreach ($val as $det)
                @foreach ($det as $det2)
                    <tr>
                        <td>{{ $det2->CutomerCode }}</td>
                        <td>{{ $det2->CustomerName }}</td>
                        <td style="text-align: right"> {{ number_format($det2->BRVDocumentAmount, $decimalPlaces) }} </td>
                        <td style="text-align: right"> {{ number_format($det2->CNDocumentAmount, $decimalPlaces) }} </td>
                        <td style="text-align: right"> {{ number_format(($det2->BRVDocumentAmount + $det2->CNDocumentAmount), $decimalPlaces) }} </td>

                    </tr>
                @endforeach
                <tr>
                    <td colspan="2" style="border-bottom-color:white !important;border-left-color:white !important"
                        class="text-right"><b>Total:</b></td>
                    <td style="text-align: right"><b>{{ number_format($bankPaymentTotal, $decimalPlaces) }}</b></td>
                    <td style="text-align: right"><b>{{ number_format($creditNoteTotal, $decimalPlaces) }}</b></td>
                    <td style="text-align: right"><b>{{ number_format(($bankPaymentTotal + $creditNoteTotal), $decimalPlaces) }}</b></td>
                </tr>
                {{ $grandTotalBank += $bankPaymentTotal  }}
                {{ $grandTotalCredit += $creditNoteTotal  }}
            @endforeach
            <tr>
                <td colspan="2" style="border-bottom-color:white !important;border-left-color:white !important"
                    class="text-right"><b>Grand Total:</b></td>
                <td style="text-align: right"><b>{{ number_format($grandTotalBank, $decimalPlaces) }}</b></td>
                <td style="text-align: right"><b>{{ number_format($grandTotalCredit, $decimalPlaces) }}</b></td>
                <td style="text-align: right"><b>{{ number_format(($grandTotalBank + $grandTotalCredit), $decimalPlaces) }}</b></td>
            </tr>
            <tbody>
        @endforeach
    </table>
</div>

