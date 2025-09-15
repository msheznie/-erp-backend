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
        background-color: #EBEBEB  !important;
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
<div class="header">
    <table style="width:100%;">
        <tr>
            <td style="width:100%;text-align: center;">
                <span class="font-weight-bold" style="font-size: 14px">{{ __('custom.financial_trial_balance') }}</span>
            </td>
        </tr>
    </table>
    <table style="width:100%;">
        <tr>
            <td colspan="2" style="width:100%;text-align: center;">
                <span class="font-weight-bold">{{ __('custom.period_from') }} :{{ $fromDate }} |
                    {{ __('custom.period_to') }} : {{ $toDate }}</span>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="width:100%;text-align: center;">
                <span class="font-weight-bold">{{$companyName}}</span>
            </td>
        </tr>
        <br>
    </table>
</div>
<div id="footer">
    <table style="width:100%;">
        <tr>
            <td style="width:50%;font-size: 10px;vertical-align: bottom;">
                <span>{{ __('custom.printed_date_time') }} : {{date("d-M-y, h:i:s A")}}</span><br>
                <span>{{ __('custom.printed_by') }} : {{$employeeData->empName}}</span>
            </td>
            <td style="width:50%; text-align: center;font-size: 10px;vertical-align: bottom;">
                <span style="float: right;">{{ __('custom.page') }} <span class="pagenum"></span></span><br>
            </td>
        </tr>
    </table>
</div>
<div class="content">
    <table style="width:100%;border:1px solid #9fcdff" class="table">
        <thead>
            <tr>
                <th>{{ __('custom.account_code') }}</th>
                <th>{{ __('custom.account_description') }}</th>
                <th>{{ __('custom.type') }}</th>

                @if ($currencyId ==1 || $currencyId ==3)
                    <th>{{ __('custom.opening_balance_local_currency') }} - {{$requestCurrencyLocal->CurrencyCode}})</th>
                    <th>{{ __('custom.debit_local_currency') }} - {{$requestCurrencyLocal->CurrencyCode}})</th>
                    <th>{{ __('custom.credit_local_currency') }} - {{$requestCurrencyLocal->CurrencyCode}})</th>
                    <th>{{ __('custom.closing_balance_local_currency') }} - {{$requestCurrencyLocal->CurrencyCode}}) </th>
                @endif

                @if ($currencyId ==2 || $currencyId ==3)
                    <th>{{ __('custom.opening_balance_reporting_currency') }} - {{$requestCurrencyRpt->CurrencyCode}})</th>
                    <th>{{ __('custom.debit_reporting_currency') }} - {{$requestCurrencyRpt->CurrencyCode}})</th>
                    <th>{{ __('custom.credit_reporting_currency') }} - {{$requestCurrencyRpt->CurrencyCode}})</th>
                    <th>{{ __('custom.closing_balance_reporting_currency') }} - {{$requestCurrencyRpt->CurrencyCode}}) </th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach ($output as $val)
                <tr>
                    <td>{{$val->glCode}}</td>
                    <td>{{$val->AccountDescription}}</td>
                    <td>{{$val->glAccountType}}</td>

                    @if ($currencyId ==1 || $currencyId ==3)
                        <td style="text-align: right;">{{number_format($val->openingBalLocal, $decimalPlaceLocal)}}</td>
                        <td style="text-align: right;">{{number_format($val->documentLocalAmountDebit, $decimalPlaceLocal) }}</td>
                        <td style="text-align: right;">{{number_format($val->documentLocalAmountCredit, $decimalPlaceLocal) }}</td>
                        <td style="text-align: right;">{{number_format(($val->documentLocalAmountDebit + $val->openingBalLocal)-($val->documentLocalAmountCredit), $decimalPlaceLocal) }}</td>
                    @endif

                    @if ($currencyId ==2 || $currencyId ==3)
                        <td style="text-align: right;">{{number_format($val->openingBalRpt, $decimalPlaceRpt)}}</td>
                        <td style="text-align: right;">{{number_format($val->documentRptAmountDebit, $decimalPlaceRpt)}}</td>
                        <td style="text-align: right;">{{number_format($val->documentRptAmountCredit, $decimalPlaceRpt)}}</td>
                        <td style="text-align: right;">{{number_format(($val->documentRptAmountDebit + $val->openingBalRpt)-($val->documentRptAmountCredit), $decimalPlaceRpt)}}</td>
                    @endif
                </tr>
            @endforeach
            <tr>
                <th style="text-align: right;" colspan="3">{{ __('custom.grand_total') }}</th>
                @if ($currencyId ==1 || $currencyId ==3)
                    <th style="text-align: right;">{{number_format($totalOpeningBalanceLocal, $decimalPlaceLocal)}}</th>
                    <th style="text-align: right;">{{number_format($totaldocumentLocalAmountDebit, $decimalPlaceLocal)}}</th>
                    <th style="text-align: right;">{{number_format($totaldocumentLocalAmountCredit, $decimalPlaceLocal)}}</th>
                    <th style="text-align: right;">{{number_format($totalClosingBalanceLocal, $decimalPlaceLocal)}}</th>
                @endif

                @if ($currencyId ==2 || $currencyId ==3)
                    <th style="text-align: right;">{{number_format($totalOpeningBalanceRpt, $decimalPlaceRpt)}}</th>
                    <th style="text-align: right;">{{number_format($totaldocumentRptAmountDebit, $decimalPlaceRpt)}}</th>
                    <th style="text-align: right;">{{number_format($totaldocumentRptAmountCredit, $decimalPlaceRpt)}}</th>
                    <th style="text-align: right;">{{number_format($totalClosingBalanceRpt, $decimalPlaceRpt)}}</th>
                @endif
            </tr>
        </tbody>
    </table>
</div>
