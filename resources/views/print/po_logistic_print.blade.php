<html @if(isset($lang) && $lang === 'ar') dir="rtl" @endif>
<head>
<style type="text/css">
    @if(isset($lang) && $lang === 'ar')
        body {
        direction: rtl;
        text-align: right;
    }

    .text-left {
        text-align: right !important;
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
    @endif
    <!--
    @page {
        margin-left: 5%;
        margin-right: 3%;
        margin-top: 1%;
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
        border-spacing: 5px 12px;
        padding: 0 8px 8px 0;
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
        font-size: 12px;
    }

    .titlebolt {
        font-size: 12px;
        font-weight: 700;
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

    .spacing {
        border-collapse: collapse;
        border-spacing: 5px 12px;
        padding: 0 8px 8px 0;
    }


</style>
</head>
<body>
<div class="content">
    <div class="row">
        <table style="width:100%">
            <tr>
                <td width="35%">
                    <table>
                        <tr>
                            <td><img src="{{$podata->company->logo_url}}" width="180px" height="60px"></td>
                        </tr>
                    </table>
                </td>
                <td width="65%">
                    <table>
                        <tr>
                            <td>
                                <h3 class="font-weight-bold" style="text-align: center; font-size: 14px">
                                    {{ __('custom.advance_payment_request') }} <br>
                                    @if ($podata->company)
                                        {{$podata->company->CompanyName}}
                                    @endif
                                </h3>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
    <table style="width:100%">
        <tr>
            <td width="60%">
                <table style="width:100%">
                    <tr>
                        <td width="20%"><span class="titlebolt">{{ __('custom.po_code') }}</span></td>
                        <td width="10%"><span class="titlebolt">:</span></td>
                        <td width="70%"><span class="titlebolt">
                            @if ($podata->poCode)
                                    {{$podata->poCode}}
                                @endif
                            </span>
                        </td>
                    </tr>
                </table>
            </td>
            <td width="40%">
                <table style="width:100%">
                    <tr>
                        <td width="26%"><span class="titlebolt">{{ __('custom.req_date') }} </span></td>
                        <td width="4%"><span class="titlebolt">:</span></td>
                        <td width="70%"><span class="titlebolt">{{ \App\helper\Helper::dateFormat($podata->reqDate)}}</span></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <table style="width:43%">
        <tr>
            <td width="28%"><span class="titlebolt">{{ __('custom.doc_control') }}</span></td>
            <td width="15%"><span class="titlebolt">:</span></td>
            <td width="60%"><span class="titlebolt">{{$docRef}}</span></td>
        </tr>
    </table>
    <table style="width:100%">
        <tr >
            <td height="20" width="14%"><span class="title">{{ __('custom.supplier') }}</span></td>
            <td width="3%"><span class="title">:</span></td>
            <td width="83%"> &nbsp;
                @if ($podata->supplier_by)
                    {{$podata->supplier_by->supplierName}}
                @endif
            </td>
        </tr>
        <tr>
            <td height="20" width="14%"><span class="title">{{ __('custom.narration') }} </span></td>
            <td width="3%"><span class="title">:</span></td>
            <td width="83%"><span class="title"> &nbsp;
                    @if ($podata->narration)
                        {{$podata->narration}}
                    @endif
                            </span>
            </td>
        </tr>
        <tr>
            <td height="20" width="14%"><span class="titlebolt">{{ __('custom.amount') }}</span></td>
            <td width="3%"><span class="titlebolt">:</span></td>
            <td width="83%"><span class="titlebolt"> &nbsp;
                    @if ($podata->currency)
                        {{$podata->currency->CurrencyCode}} &nbsp;
                        &nbsp; {{number_format($podata->reqAmount, $podata->currency->DecimalPlaces)}}
                    @endif
                </span>
            </td>
        </tr>
    </table>
    {{--    <table style="width:100%">
            <tr>
                <td width="70%">
                </td>
                <td width="30%">
                    <table>
                        <tr>
                            <td><span>Printed By</span></td>
                            <td><span>:</span></td>
                            <td>{{ $currentuser }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>--}}
</div>
</body>
