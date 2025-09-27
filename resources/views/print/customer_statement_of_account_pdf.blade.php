<style type="text/css">
    @if(isset($lang) && $lang === 'ar')
    body {
        direction: rtl;
        text-align: right;
    }

    table {
        direction: rtl;
    }

    .table th, .table td {
        text-align: right;
    }

    .table .text-left {
        text-align: left !important;
    }

    .text-float-right {
        float: left !important;
    }
    @endif
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
        text-align: left !important;
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

    .text-float-right {
        float: right;
    }

</style>
<div class="content">
    <table style="width:100%;">

        <tr>
{{--            <td style="width:50%;font-size: 10px;vertical-align: bottom;">--}}

{{--            </td>--}}
            <td style="width:100%; font-size: 10px;vertical-align: bottom;" class="@if(isset($lang) && $lang === 'ar') text-left @else text-right @endif">
                <span style="@if(isset($lang) && $lang === 'ar') float: left !important; @else float: right; @endif"><b>{{ $currency }}</b></span><br>
            </td>
        </tr>
    </table>

    @foreach ($reportData as $key => $val)
        <h4>{{$key}}</h4>
            @foreach ($val as $key1=>$res)
                @php
                    $subInvoiceAmount = 0;
                    $subReceiptAmount = 0;
                    $subBalanceAmount = 0;
                @endphp
            <table style="width:100%;border:1px solid #9fcdff" class="table">
                <thead>
                <tr>
                    <th width="10%">{{ trans('custom.document_code') }}</th>
                    <th width="6%">{{ trans('custom.posted_date') }}</th>
                    <th width="5%">{{ trans('custom.contract') }}</th>
                    <th width="5%">{{ trans('custom.po_number') }}</th>
                    <th width="7%">{{ trans('custom.invoice_date') }}</th>
                    <th width="10%">{{ trans('custom.narration') }}</th>
                    <th width="5%">{{ trans('custom.currency') }}</th>
                    <th width="10%">{{ trans('custom.invoice_amount') }}</th>
                    <th width="5%">{{ trans('custom.receipt_cn_code') }}</th>
                    <th width="5%">{{ trans('custom.receipt_cn_date') }}</th>
                    <th width="10%">{{ trans('custom.receipt_amount') }}</th>
                    <th width="10%">{{ trans('custom.balance_amount') }}</th>
                </tr>
                </thead>

                @foreach($res as $det)
                    {{ $subInvoiceAmount += $det->invoiceAmount }}
                    {{ $subReceiptAmount += $det->receiptAmount }}
                    {{ $subBalanceAmount += $det->balanceAmount }}
                    <tr>
                        <td>{{ $det->documentCode  }}</td>
                        <td>{{\Helper::dateFormat($det->postedDate)}}</td>
                        <td>{{$det->clientContractID}}</td>
                        <td>{{$det->PONumber}}</td>
                        <td>{{\Helper::dateFormat($det->invoiceDate)}}</td>
                        <td style="word-break: break-all;white-space: normal;">{{$det->documentNarration}}</td>
                        <td>{{$det->documentCurrency}}</td>
                        <td class="text-right">{{number_format($det->invoiceAmount, $det->balanceDecimalPlaces)}}</td>
                        <td><p style="width: 80px;">{{$det->ReceiptCode}}</p></td>
                        <td>{{\Helper::dateFormat($det->ReceiptDate)}}</td>
                        <td class="text-right">{{number_format($det->receiptAmount, $det->balanceDecimalPlaces)}}</td>
                        <td class="text-right">{{number_format($det->balanceAmount, $det->balanceDecimalPlaces)}}</td>
                    </tr>
                @endforeach
                <tr style="background-color: #E7E7E7">
                    <td colspan="7" class="@if(isset($lang) && $lang === 'ar') text-left @else text-right @endif"><b>{{ trans('custom.sub_total') }}:</b></td>
                    <td class="text-right">
                        <b>{{number_format($subInvoiceAmount, $val[$key1][0]->balanceDecimalPlaces)}}</b></td>
                    <td colspan="2" class="text-right"></td>
                    <td class="text-right">
                        <b>{{number_format($subReceiptAmount, $val[$key1][0]->balanceDecimalPlaces)}}</b></td>
                    <td class="text-right">
                        <b>{{number_format($subBalanceAmount, $val[$key1][0]->balanceDecimalPlaces)}}</b></td>
                </tr>
                @if($currencyID != 1)
                    <tr style="background-color: #E7E7E7">
                        <td colspan="7" class="@if(isset($lang) && $lang === 'ar') text-left @else text-right @endif"><b>{{ trans('custom.grand_total') }}:</b></td>
                        <td class="text-right"><b>{{number_format($invoiceAmount, $currencyDecimalPlace)}}</b></td>
                        <td colspan="2" class="text-right"></td>
                        <td class="text-right"><b>{{number_format($receiptAmount, $currencyDecimalPlace)}}</b></td>
                        <td class="text-right"><b>{{number_format($balanceAmount, $currencyDecimalPlace)}}</b></td>
                    </tr>
                @endif
            </table>
        @endforeach

    @endforeach

</div>
