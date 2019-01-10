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
        font-size: 9px;
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
        font-size: 9px;
    }

    table > thead > th {
        font-size: 9px;
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
                <span class="font-weight-bold">{{$companyName}}</span>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="width:100%;text-align: center;">
                <span class="font-weight-bold">Revenue Report - {{ $year }}</span>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="width:100%;text-align: center;">
                <span class="font-weight-bold">As of {{ $fromDate }}</span>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="width:100%;text-align: center;">
                <span class="font-weight-bold">Currency : {{ $currency }}</span>
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
                <td colspan="14"><b>{{$key}}</b></td>
            </tr>
            <tr>
                <th width="20%" style="background-color: #D7E4BD">Customer Name</th>
                <th width="6%">Jan</th>
                <th width="6%">Feb</th>
                <th width="6%">March</th>
                <th width="6%">April</th>
                <th width="6%">May</th>
                <th width="6%">Jun</th>
                <th width="6%">July</th>
                <th width="6%">Aug</th>
                <th width="6%">Sept</th>
                <th width="6%">Oct</th>
                <th width="6%">Nov</th>
                <th width="6%">Dec</th>
                <th width="8%">Total</th>
            </tr>
            </thead>
            <tbody>
            {{ $janTotal = 0 }}
            {{ $febTotal = 0 }}
            {{ $marTotal = 0 }}
            {{ $aprTotal = 0 }}
            {{ $mayTotal = 0 }}
            {{ $junTotal = 0 }}
            {{ $julTotal = 0 }}
            {{ $augTotal = 0 }}
            {{ $sepTotal = 0 }}
            {{ $octTotal = 0 }}
            {{ $novTotal = 0 }}
            {{ $decTotal = 0 }}
            {{ $gTotal = 0 }}
            @foreach ($val as $det)
                {{ $janTotal += $det->Jan }}
                {{ $febTotal += $det->Feb }}
                {{ $marTotal += $det->March }}
                {{ $aprTotal += $det->April }}
                {{ $mayTotal += $det->May }}
                {{ $junTotal += $det->May }}
                {{ $julTotal += $det->July }}
                {{ $augTotal += $det->Aug }}
                {{ $sepTotal += $det->Sept }}
                {{ $octTotal += $det->Oct }}
                {{ $novTotal += $det->Nov }}
                {{ $decTotal += $det->Dece }}
                {{ $gTotal += $det->Total }}
                <tr>
                    <td>{{ $det->CustomerName  }}</td>
                    <td style="text-align: right">{{ number_format($det->Jan) }}</td>
                    <td style="text-align: right">{{ number_format($det->Feb) }}</td>
                    <td style="text-align: right">{{ number_format($det->March) }}</td>
                    <td style="text-align: right">{{ number_format($det->April) }}</td>
                    <td style="text-align: right">{{ number_format($det->May) }}</td>
                    <td style="text-align: right">{{ number_format($det->June) }}</td>
                    <td style="text-align: right">{{ number_format($det->July) }}</td>
                    <td style="text-align: right">{{ number_format($det->Aug) }}</td>
                    <td style="text-align: right">{{ number_format($det->Sept) }}</td>
                    <td style="text-align: right">{{ number_format($det->Oct) }}</td>
                    <td style="text-align: right">{{ number_format($det->Nov) }}</td>
                    <td style="text-align: right">{{ number_format($det->Dece) }}</td>
                    <td style="text-align: right">{{ number_format($det->Total) }}</td>
                </tr>
            @endforeach
            <tr>
                <td class="text-right"
                    style=""><b>Total:</b>
                </td>
                <td class="text-right"><b>{{number_format($janTotal)}}</b></td>
                <td class="text-right"><b>{{number_format($febTotal)}}</b></td>
                <td class="text-right"><b>{{number_format($marTotal)}}</b></td>
                <td class="text-right"><b>{{number_format($aprTotal)}}</b></td>
                <td class="text-right"><b>{{number_format($mayTotal)}}</b></td>
                <td class="text-right"><b>{{number_format($junTotal)}}</b></td>
                <td class="text-right"><b>{{number_format($julTotal)}}</b></td>
                <td class="text-right"><b>{{number_format($augTotal)}}</b></td>
                <td class="text-right"><b>{{number_format($sepTotal)}}</b></td>
                <td class="text-right"><b>{{number_format($octTotal)}}</b></td>
                <td class="text-right"><b>{{number_format($novTotal)}}</b></td>
                <td class="text-right"><b>{{number_format($decTotal)}}</b></td>
                <td class="text-right"><b>{{number_format($gTotal)}}</b></td>
            </tr>
            </tbody>
        @endforeach
        <tfoot>
        <tr>
            <td class="text-right"
                style=""><b>Grand Total:</b>
            </td>
            <td class="text-right"><b>{{number_format($total['Jan'])}}</b></td>
            <td class="text-right"><b>{{number_format($total['Feb'])}}</b></td>
            <td class="text-right"><b>{{number_format($total['March'])}}</b></td>
            <td class="text-right"><b>{{number_format($total['April'])}}</b></td>
            <td class="text-right"><b>{{number_format($total['May'])}}</b></td>
            <td class="text-right"><b>{{number_format($total['June'])}}</b></td>
            <td class="text-right"><b>{{number_format($total['July'])}}</b></td>
            <td class="text-right"><b>{{number_format($total['Aug'])}}</b></td>
            <td class="text-right"><b>{{number_format($total['Sept'])}}</b></td>
            <td class="text-right"><b>{{number_format($total['Oct'])}}</b></td>
            <td class="text-right"><b>{{number_format($total['Nov'])}}</b></td>
            <td class="text-right"><b>{{number_format($total['Dece'])}}</b></td>
            <td class="text-right"><b>{{number_format($total['Total'])}}</b></td>
        </tr>
        </tfoot>
    </table>
</div>
