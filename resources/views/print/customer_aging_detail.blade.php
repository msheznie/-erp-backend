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
                        <span class="font-weight-bold">Customer Invoice Aging Report</span><br>
                        <span class="font-weight-bold">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;As of {{ $fromDate }}</span>
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
<div class="content">
    <table style="width:100%;border:1px solid #9fcdff" class="table">
        <?php $ageRangeGrandTotal = [] ?>
        {{ $grandTotal = 0 }}
        @foreach ($reportData as $key => $val)
            <tr>
                <td colspan="14"><b>{{$key}}</b></td>
            </tr>
            <tr>
                <td><b>Credit Days</b></td>
                <td colspan="13"><b>{{$customerCreditDays[$key]}}</b></td>
            </tr>
            <tr>
                <th width="10%">Document Code</th>
                <th width="10%">Posted Date</th>
                <th width="5%">Invoice Number</th>
                <th width="5%">PO Number</th>
                <th width="5%">Invoice Date</th>
                <th width="5%">Aged Days</th>
                <th width="5%">Currency</th>
                <th width="10%">Invoice Amount</th>
                <th width="10%">Balance Amount</th>
                @foreach ($agingRange as $age)
                    <th>{{$age}}</th>
                @endforeach
            </tr>
            <tbody>
            {{ $ageTotal = 0 }}
            {{ $ageSubTotal = 0 }}
            {{ $invoiceTotal = 0 }}
            @foreach ($val as $det)
                <?php $ageRangeSubTotal = [] ?>
                @foreach ($det as $det2)
                    {{ $ageTotal = 0 }}
                    @foreach ($agingRange as $age)
                        {{ $ageTotal += $det2->$age }}
                        {{$ageRangeSubTotal[$age][] = $det2->$age}}
                        {{$ageRangeGrandTotal[$age][] = $det2->$age}}
                    @endforeach
                    <tr>
                        <td>{{ $det2->DocumentCode }}</td>
                        <td> {{ \App\helper\Helper::dateFormat($det2->PostedDate)}}</td>
                        <td>{{ $det2->invoiceNumber }}</td>
                        <td>{{ $det2->PONumber }}</td>
                        <td> {{ \App\helper\Helper::dateFormat($det2->InvoiceDate)}}</td>
                        <td style="text-align: right">{{ $det2->age }}</td>
                        <td>{{ $det2->documentCurrency }}</td>
                        <td style="text-align: right">{{ number_format($det2->invoiceAmount) }}</td>
                        <td style="text-align: right">{{ number_format($ageTotal) }}</td>
                        @foreach ($agingRange as $age)
                            <td style="text-align: right">{{ number_format($det2->$age) }}</td>
                        @endforeach
                    </tr>
                    {{$ageSubTotal += $ageTotal}}
                    {{$invoiceTotal += $det2->invoiceAmount}}
                @endforeach
                <tr>
                    <td colspan="7" style="border-bottom-color:white !important;border-left-color:white !important"
                        class="text-right"><b>Sub Total:</b></td>
                    <td style="text-align: right"><b>{{ number_format($invoiceTotal) }}</b></td>
                    <td style="text-align: right">
                        <b>{{ number_format($ageSubTotal) }}</b></td>

                    @foreach ($agingRange as $age)
                        <td style="text-align: right">
                            <b>{{ number_format(array_sum($ageRangeSubTotal[$age])) }}</b>
                        </td>
                    @endforeach
                </tr>
                {{$grandTotal += $ageSubTotal}}
            @endforeach
            </tbody>
        @endforeach
        <tfoot>
        <tr>
            <td colspan="7" style="border-bottom-color:white !important;border-left-color:white !important"
                class="text-right"><b>Grand Total:</b></td>
            <td style="text-align: right"><b>{{ number_format($invoiceAmountTotal) }}</b></td>
            <td style="text-align: right"><b>{{ number_format($grandTotal) }}</b></td>
            @foreach ($agingRange as $age)
                <td style="text-align: right">
                    <b>{{ number_format(array_sum($ageRangeGrandTotal[$age])) }}</b></td>
            @endforeach
        </tr>
        </tfoot>
    </table>
</div>
