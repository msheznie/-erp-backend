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
                <span>{{trans('custom.printed_date')}} : {{date("d-M-y", strtotime(now()))}}</span>
            </td>
            <td style="width:50%; text-align: center;font-size: 10px;vertical-align: bottom;">
                <span style="float: right;">{{trans('custom.page')}} <span class="pagenum"></span></span><br>
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
                        <span class="font-weight-bold">{{trans('custom.supplier_ledger')}}</span><br>
                        <span class="font-weight-bold">{{trans('custom.period')}} : {{ $fromDate }} - {{ $toDate }}</span>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
<br><br>
<div id="content">
    <table style="width:100%;border:1px solid #9fcdff" class="table">
        @foreach ($reportData as $key => $val)
            <tr style="width:100%">
                <td colspan="7"><span style="font-size: 11px; font-weight: bold">{{$key}}</span></td>
            </tr>
            @foreach ($val as $key2 => $val2)
                <tr style="width:100%">
                    <th>{{trans('custom.document_code')}}</th>
                    <th>{{trans('custom.posted_date')}}</th>
                    <th>{{trans('custom.account')}}</th>
                    <th>{{trans('custom.invoice_number')}}</th>
                    <th>{{trans('custom.invoice_date')}}</th>
                    <th>{{trans('custom.document_narration')}}</th>
                    <th>{{trans('custom.currency')}}</th>
                    <th>{{trans('custom.document_amount')}}</th>
                </tr>
                <tbody>
                {{ $lineTotal = 0 }}
                @foreach ($val2 as $det2)
                    <tr style="width:100%">
                        <td>{{ $det2->documentCode }}</td>
                        <td>
                            @if($det2->documentSystemCode != '1970-01-01')
                                {{ \App\helper\Helper::dateFormat($det2->documentDate)}}
                            @endif
                        </td>
                        <td>{{ $det2->glCode }} - {{ $det2->AccountDescription }}</td>
                        <td class="white-space-pre-line">{{ $det2->invoiceNumber }}</td>
                        <td> {{ \App\helper\Helper::dateFormat($det2->invoiceDate)}}</td>
                        <td>{{ $det2->documentNarration }}</td>
                        <td>{{ $det2->documentCurrency }}</td>
                        <td class="text-right">{{ number_format($det2->invoiceAmount, $currencyDecimalPlace) }}</td>
                    </tr>
                    {{$lineTotal += $det2->invoiceAmount}}
                @endforeach
                <tr width="100%">
                    <td colspan="6" style="border-bottom-color:white !important;border-left-color:white !important"
                            class="text-right"><b>{{trans('custom.total')}}:</b></td>
                  
                    <td style="text-align: right"><b>{{ number_format($lineTotal, $currencyDecimalPlace) }}</b></td>
                </tr>
                </tbody>
            @endforeach
        @endforeach
        <tfoot>
            <tr width="100%">
                <td colspan="6" style="border-bottom-color:white !important;border-left-color:white !important"
                        class="text-right"><b>{{trans('custom.grand_total')}}:</b></td>
                <td style="text-align: right"><b>{{ number_format($invoiceAmount, $currencyDecimalPlace) }}</b></td>
            </tr>
        </tfoot>
    </table>
</div>
