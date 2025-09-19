<style type="text/css">
    @page {
        margin: 110px 30px 40px;
    }
    font-family: 'dejavusans', DejaVu Sans, sans-serif;
    direction: rtl;
    unicode-bidi: bidi-override;
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
                        <span class="font-weight-bold">{{ __('custom.supplier_statement') }}</span><br>
                        <span class="font-weight-bold">&nbsp;&nbsp;&nbsp;{{ __('custom.as_of') }} {{ $fromDate }}</span>
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
                @if(!$sentEmail)
                    <td colspan="10"><span style="font-size: 11px; font-weight: bold">{{$key}}</span></td>
                @endif
                @if($sentEmail)
                    <td colspan="9"><span style="font-size: 11px; font-weight: bold">{{$key}}</span></td>
                @endif
            </tr>
            @foreach ($val as $key2 => $val2)
                <tr style="width:100%">
                    @if(!$sentEmail)
                        <td colspan="10"><span style="font-size: 9px; font-weight: bold">{{$key2}}</span></td>
                    @endif
                    @if($sentEmail)
                        <td colspan="9"><span style="font-size: 9px; font-weight: bold">{{$key2}}</span></td>
                    @endif
                </tr>
                @foreach ($val2 as $key3 => $val3)
                    <tr style="width:100%">
                        @if(!$sentEmail)
                            <td colspan="10"><span style="font-size: 9px; font-weight: bold">{{ __('custom.supplier_group') }}: {{$key3}}</span></td>
                        @endif
                        @if($sentEmail)
                            <td colspan="9"><span style="font-size: 9px; font-weight: bold">{{ __('custom.supplier_group') }}: {{$key3}}</span></td>
                        @endif
                    </tr>
                    <tr style="width:100%">
                        <th>{{ __('custom.document_id') }}</th>
                        <th>{{ __('custom.document_code') }}</th>
                        <th>{{ __('custom.document_date') }}</th>
                        <th>{{ __('custom.account') }}</th>
                        @if(!$sentEmail)
                            <th>{{ __('custom.narration') }}</th>
                        @endif
                        <th>{{ __('custom.invoice_number') }}</th>
                        <th>{{ __('custom.invoice_date') }}</th>
                        <th>{{ __('custom.currency') }}</th>
                        <th>{{ __('custom.age_days') }}</th>
                        <th>{{ __('custom.doc_amount') }}</th>
                        <th>{{ __('custom.balance_amount') }}</th>
                    </tr>
                    <tbody>
                    {{ $lineTotal = 0 }}
                    @foreach ($val3 as $det2)
                        <tr style="width:100%">
                            <td>{{ $det2->documentID }}</td>
                            <td>{{ $det2->documentCode }}</td>
                            <td>{{ \App\helper\Helper::dateFormat($det2->documentDate)}}</td>
                            <td>{{ $det2->glCode }} - {{ $det2->AccountDescription }}</td>
                            @if(!$sentEmail)
                                <td class="white-space-pre-line">{{ $det2->documentNarration }}</td>
                            @endif
                            <td class="white-space-pre-line">{{ $det2->invoiceNumber }}</td>
                            <td> {{ \App\helper\Helper::dateFormat($det2->invoiceDate)}}</td>
                            <td>{{ $det2->documentCurrency }}</td>
                            <td class="text-right">{{ $det2->ageDays }}</td>
                            <td class="text-right">{{ number_format($det2->invoiceAmount, $currencyDecimalPlace) }}</td>
                            <td class="text-right">{{ number_format($det2->balanceAmount, $currencyDecimalPlace) }}</td>
                        </tr>
                        {{$lineTotal += $det2->balanceAmount}}
                    @endforeach
                    <tr width="100%">
                        @if(!$sentEmail)
                            <td colspan="10" style="border-bottom-color:white !important;border-left-color:white !important"
                                class="text-right"><b>Total:</b></td>
                        @endif
                        @if($sentEmail)
                            <td colspan="9" style="border-bottom-color:white !important;border-left-color:white !important"
                                class="text-right"><b>Total:</b></td>
                        @endif
                        <td style="text-align: right"><b>{{ number_format($lineTotal, $currencyDecimalPlace) }}</b></td>
                    </tr>
                    </tbody>
                @endforeach
            @endforeach
        @endforeach
        <tfoot>
        <tr width="100%">
            @if(!$sentEmail)
                <td colspan="10" style="border-bottom-color:white !important;border-left-color:white !important"
                    class="text-right"><b>Grand Total:</b></td>
            @endif
            @if($sentEmail)
                <td colspan="9" style="border-bottom-color:white !important;border-left-color:white !important"
                    class="text-right"><b>Grand Total:</b></td>
            @endif
            <td style="text-align: right"><b>{{ number_format($grandTotal, $currencyDecimalPlace) }}</b></td>
        </tr>
        </tfoot>
    </table>
</div>
