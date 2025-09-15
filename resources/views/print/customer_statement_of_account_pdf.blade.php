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
                <span class="font-weight-bold">{{ trans('custom.kindly_confirm_the_balance_and_settle_the_pending_invoices_at_the_earliest') }}</span>
            </td>
        </tr>
        <tr>
            <td style="width:50%;font-size: 10px;vertical-align: bottom;">
                <span>{{ trans('custom.printed_date') }} : {{date("d-M-y", strtotime(now()))}}</span>
            </td>
            <td style="width:50%; text-align: center;font-size: 10px;vertical-align: bottom;">
                <span style="float: right;">{{ trans('custom.page') }} <span class="pagenum"></span></span><br>
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
                        <img src="{{$companylogo}}" width="180px" height="60px"><br>
                    </td>
                    <td valign="top" style="width: 60%">
                        <br><br>
                        <span class="font-weight-bold">{{ trans('custom.statement_of_account_for_the_period') }} {{ $fromDate }}
                            {{ trans('custom.to') }} {{ $toDate }}</span>
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
    <table style="width:100%;">

        <tr>
            <td style="width:50%;font-size: 10px;vertical-align: bottom;">

            </td>
            <td style="width:50%; text-align: center;font-size: 10px;vertical-align: bottom;">
                <span style="float: right;"><b>{{ $currency }}</b></span><br>
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
            <table style="width:95%;border:1px solid #9fcdff" class="table">
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
                        <td colspan="7" class="text-right"
                            style=""><b>{{ trans('custom.sub_total') }}:</b>
                        </td>
                        <td class="text-right">
                            <b>{{number_format($subInvoiceAmount, $val[$key1][0]->balanceDecimalPlaces)}}</b></td>
                        <td colspan="2" style=""
                            class="text-right"></td>
                        <td class="text-right">
                            <b>{{number_format($subReceiptAmount, $val[$key1][0]->balanceDecimalPlaces)}}</b></td>
                        <td class="text-right">
                            <b>{{number_format($subBalanceAmount, $val[$key1][0]->balanceDecimalPlaces)}}</b></td>
                    </tr>
            @if($currencyID != 1)
                <tr style="background-color: #E7E7E7">
                    <td colspan="7" class="text-right"
                        style=""><b>{{ trans('custom.grand_total') }}:</b></td>
                    <td class="text-right"><b>{{number_format($invoiceAmount, $currencyDecimalPlace)}}</b></td>
                    <td colspan="2" style=""
                        class="text-right"></td>
                    <td class="text-right"><b>{{number_format($receiptAmount, $currencyDecimalPlace)}}</b></td>
                    <td class="text-right"><b>{{number_format($balanceAmount, $currencyDecimalPlace)}}</b></td>
                </tr>
            @endif
    </table>
        @endforeach

    @endforeach

</div>
