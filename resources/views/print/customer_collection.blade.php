<style type="text/css">
    @if(isset($lang) && $lang === 'ar')
    body {
        direction: rtl;
        text-align: right;
    }

    .text-right {
        text-align: left !important;
    }

    table {
        direction: rtl;
    }

    .table th, .table td {
        text-align: right;
    }

    .table .total{
        text-align: left !important;
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
        font-size: 10px;
    }

    table > tbody > th {
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

    .text-left-footer {
        text-align: left !important;
    }

    .text-right-footer {
        text-align: right !important;
    }

</style>
<div style="width: 100%; text-align: center; font-size: 10px; margin-bottom: 20px;">
    <table style="width: 100%">
        <tr>
            <td valign="top" style="width: 45%; @if(isset($lang) && $lang === 'ar') text-align: right; @endif">
                <img src="{{$companylogo}}" width="180px" height="60px"><br>
            </td>
            <td valign="top" style="width: 55%; @if(isset($lang) && $lang === 'ar') text-align: right; @endif">
                <br><br>
                <span style="font-weight: bold; font-size: 12px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ trans('custom.collection_report') }}</span><br>
                <span style="font-weight: bold; font-size: 12px;">{{ trans('custom.collection_for_the_period') }} : {{ $fromDate }}
                    {{ trans('custom.to') }} {{ $toDate }}</span><br>
                <span style="font-weight: bold; font-size: 12px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ trans('custom.currency') }} {{ $selectedCurrency }}</span>
            </td>
        </tr>
        <tr>
            <td colspan="2" valign="top" style="width: 45%; @if(isset($lang) && $lang === 'ar') text-align: right; @endif">
                <span style="font-weight: bold;"> &nbsp;&nbsp;&nbsp;{{$companyName}}</span>
            </td>
            <td>
            </td>
        </tr>
    </table>
</div>
<div class="content">
    <table style="width:100%;border:1px solid #9fcdff" class="table">
        @foreach ($reportData as $key => $val)
            <tr>
                <td colspan="5"><b>{{$key}}</b></td>
            </tr>
            <tr>
                <th width="20%" style="text-align: center">{{ trans('custom.customer_code') }}</th>
                <th width="50%" style="text-align: center">{{ trans('custom.customer_name') }}</th>
                <th width="10%" style="text-align: center">{{ trans('custom.bank_payment') }}</th>
                <th width="10%" style="text-align: center">{{ trans('custom.credit_note_issued') }}</th>
                <th width="10%" style="text-align: center">{{ trans('custom.total') }}</th>
            </tr>
            <tbody>
            {{ $lineTotalBank = 0 }}
            {{ $lineTotalCredit = 0 }}
            @foreach ($val as $det)
                {{ $lineTotalBank = 0  }}
                {{ $lineTotalCredit = 0  }}
                @foreach ($det as $det2)
                    <tr>
                        <td>{{ $det2->CutomerCode }}</td>
                        <td>{{ $det2->CustomerName }}</td>
                        <td style="text-align: right"> {{ number_format($det2->BRVDocumentAmount, $decimalPlaces) }} </td>
                        <td style="text-align: right"> {{ number_format($det2->CNDocumentAmount, $decimalPlaces) }} </td>
                        <td style="text-align: right"> {{ number_format(($det2->BRVDocumentAmount + $det2->CNDocumentAmount), $decimalPlaces) }} </td>
                    </tr>
                    {{ $lineTotalBank += $det2->BRVDocumentAmount }}
                    {{ $lineTotalCredit += $det2->CNDocumentAmount }}
                @endforeach
                <tr>
                    <td colspan="2" style="border-inline-start:white !important;border-block-end-color:white !important;"
                        class="text-right total"><b>{{ trans('custom.total') }}:</b></td>
                    <td style="text-align: right"><b>{{ number_format($lineTotalBank, $decimalPlaces) }}</b></td>
                    <td style="text-align: right"><b>{{ number_format($lineTotalCredit, $decimalPlaces) }}</b></td>
                    <td style="text-align: right">
                        <b>{{ number_format(($lineTotalBank + $lineTotalCredit), $decimalPlaces) }}</b></td>
                </tr>

        @endforeach
        <tbody>
        @endforeach
        <tfoot>
        <tr>
            <td colspan="2" style="border-inline-start:white !important;border-block-end-color:white !important;"
                class="text-right total"><b>{{ trans('custom.grand_total') }}:</b></td>
            <td style="text-align: right"><b>{{ number_format($bankPaymentTotal, $decimalPlaces) }}</b></td>
            <td style="text-align: right"><b>{{ number_format($creditNoteTotal, $decimalPlaces) }}</b></td>
            <td style="text-align: right">
                <b>{{ number_format(($bankPaymentTotal + $creditNoteTotal), $decimalPlaces) }}</b></td>
        </tr>
        </tfoot>
    </table>
</div>

<div style="width: 100%; text-align: center; font-size: 10px; padding-top: 20px; margin-top: 20px;">
    <table style="width:100%;">
        <tr>
            <td style="width:50%;font-size: 10px;vertical-align: bottom; @if(isset($lang) && $lang === 'ar') text-align: right; @endif">
                <span>{{ trans('custom.printed_date') }} : {{date("d-M-y", strtotime(now()))}}</span>
            </td>
            <td class="@if(isset($lang) && $lang === 'ar') text-left-footer @else text-right-footer @endif" style="width:50%; font-size: 10px;vertical-align: bottom;">
                <span style="@if(isset($lang) && $lang === 'ar') float: left !important; @else float: right !important; @endif">{{ trans('custom.page') }} <span>{PAGENO}</span></span><br>
            </td>
        </tr>
    </table>
</div>
