<style type="text/css">
    @page {
        margin-left: 2%;
        margin-right: 2%;
        margin-top: 4%;
    }

    .footer {
        position: absolute;
    }

    body {
        font-size: 8px;
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
        font-size: 20px;
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

    .title {
        font-size: 13px;
        font-weight: 600;
    }

    .footer {
        bottom: 0;
        height: 20px;
    }

    .footer {
        width: 100%;
        text-align: center;
        position: fixed;
        font-size: 10px;
        padding-top: -10px;
    }

    .pagenum:after {
        content: counter(page);
    }

    .content {
        margin-bottom: 45px;
    }

</style>
<!-- <div class="footer">
    <table style="width:100%;">
        <tr>
            <td style="width:50%;font-size: 10px;vertical-align: bottom;">
                <span>{{ __('custom.printed_date') }} : {{date("d-M-y", strtotime(now()))}}</span>
            </td>
            <td style="width:50%; text-align: center;font-size: 10px;vertical-align: bottom;">
                <span style="float: right;">{{ __('custom.page') }} <span class="pagenum"></span></span><br>
            </td>
        </tr>
    </table>
</div> -->
<div class="header">
    <table style="width:100%;">
        <tr>
            <td style="width:100%;text-align: center;">
                <span class="font-weight-bold" style="font-size: 14px">{{ __('custom.bank_ledger') }}</span>
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

    </table>
</div>
<div class="content">
    <table style="border:1px solid #9fcdff;width: 100%" class="table">
        @foreach ($reportData as $key => $det)
            <tr>
                <th colspan="{{6 + count($extraColumns)}}">{{ $key  }}</th>
                 @if($isGroup == 0 && $currencyID == 3)
                    @if($accBalanceShow)
                        <th colspan="3" style="text-align: center">{{ __('custom.local_currency') }} ({{$currencyCode}})</th>
                    @else
                        <th colspan="2" style="text-align: center">{{ __('custom.local_currency') }} ({{$currencyCode}})</th>
                    @endif
                @endif
                @if($currencyID == 2)
                    @if($accBalanceShow)
                        <th colspan="3" style="text-align: center">{{ __('custom.reporting_currency') }} ({{$currencyCode}})</th>
                    @else
                        <th colspan="2" style="text-align: center">{{ __('custom.reporting_currency') }} ({{$currencyCode}})</th>
                    @endif
                @endif
                @if($currencyID == 1)
                    @if($accBalanceShow)
                        <th colspan="3" style="text-align: center">{{ __('custom.bank_currency') }} ({{ isset($det[0]->bankCurrency) ? $det[0]->bankCurrency : ""}})</th>
                    @else
                        <th colspan="2" style="text-align: center">{{ __('custom.bank_currency') }} ({{ isset($det[0]->bankCurrency) ? $det[0]->bankCurrency : ""}})</th>
                    @endif
                @endif
            </tr>
            <tr>
                <th>{{ __('custom.account_description') }}</th>
                <th>{{ __('custom.document_number') }}</th>
                <th>{{ __('custom.document_type') }}</th>
                <th>{{ __('custom.date') }}</th>
                <th>{{ __('custom.document_narration') }}</th>
                <th>{{ __('custom.supplier_customer') }}</th>
                @if(in_array('confi_name', $extraColumns))
                    <th>{{ __('custom.confirmed_by') }}</th>
                @endif
                @if(in_array('confi_date', $extraColumns))
                    <th>{{ __('custom.confirmed_date') }}</th>
                @endif
                @if(in_array('app_name', $extraColumns))
                    <th>{{ __('custom.approved_by') }}</th>
                @endif
                @if(in_array('app_date', $extraColumns))
                    <th>{{ __('custom.approved_date') }}</th>
                @endif
                @if(($isGroup == 0 && $currencyID == 3) || $currencyID == 2 || $currencyID == 1)
                    <th style="text-align: center">{{ __('custom.debit') }}</th>
                    <th style="text-align: center">{{ __('custom.credit') }}</th>
                @endif
                @if($accBalanceShow)
                    <th style="text-align: center">{{ __('custom.account_balance') }}</th>
                @endif
            </tr>
            {{$acLocalDebitTotal = 0}}
            {{$acLocalCreditTotal = 0}}
            {{$acRptDebitTotal = 0}}
            {{$acRptCreditTotal = 0}}
            {{$acBankDebitTotal = 0}}
            {{$acBankCreditTotal = 0}}
            {{$bankDecimal = 2}}
            {{$balanceDecimal = 2}}
            @foreach ($det as $key2 => $val)
                <tr>
                    <td>{{ $val->AccountDescription  }}</td>
                    <td>{{ $val->documentCode  }}</td>
                    <td>{{ $val->documentID  }}</td>
                    <td>{{\Helper::dateFormat($val->documentDate)}}</td>
                    <td>{{ $val->documentNarration  }}</td>
                    <td>{{ $val->partyName  }}</td>
                    @if(in_array('confi_name', $extraColumns))
                        <td>{{ $val->confirmBy  }}</td>
                    @endif
                    @if(in_array('confi_date', $extraColumns))
                        <td>{{\Helper::dateFormat($val->confirmDate)}}</td>
                    @endif
                    @if(in_array('app_name', $extraColumns))
                        <td>{{ $val->approvedBy  }}</td>
                    @endif
                    @if(in_array('app_date', $extraColumns))
                        <td>{{\Helper::dateFormat($val->approvedDate)}}</td>
                    @endif
                    @if($isGroup == 0 && $currencyID == 3)
                        <td class="text-right">{{number_format($val->localDebit, $currencyDecimalPlace)}}</td>
                        <td class="text-right">{{number_format($val->localCredit, $currencyDecimalPlace)}}</td>
                        {{$balanceDecimal = $currencyDecimalPlace}}
                    @endif
                    @if($currencyID == 2)
                        <td class="text-right">{{number_format($val->rptDebit, $currencyDecimalPlace)}}</td>
                        <td class="text-right">{{number_format($val->rptCredit, $currencyDecimalPlace)}}</td>
                        {{$balanceDecimal = $currencyDecimalPlace}}
                    @endif
                    @if($currencyID == 1)
                        <td class="text-right">{{number_format($val->bankDebit, $val->bankCurrencyDecimal)}}</td>
                        <td class="text-right">{{number_format($val->bankCredit, $val->bankCurrencyDecimal)}}</td>
                        {{$balanceDecimal = $val->bankCurrencyDecimal}}
                    @endif
                    @if($accBalanceShow)
                        @if(isset($val->accountBalance))
                            <td class="text-right">{{number_format($val->accountBalance, $balanceDecimal)}}</td>
                        @endif
                    @endif
                </tr>
                {{$acLocalDebitTotal += $val->localDebit}}
                {{$acLocalCreditTotal += $val->localCredit}}
                {{$acRptDebitTotal += $val->rptDebit}}
                {{$acRptCreditTotal += $val->rptCredit}}
                {{$acBankDebitTotal += $val->bankDebit}}
                {{$acBankCreditTotal += $val->bankCredit}}
                {{$bankDecimal = $val->bankCurrencyDecimal}}
            @endforeach
            @if(($currencyID == 1 && $accBalanceShow) || ($isGroup == 0 && $currencyID == 3) || $currencyID == 2)
                <tr style="background-color: #E7E7E7">
                    <td colspan="{{6 + count($extraColumns)}}" class="text-right"
                        style=""><b>{{ __('custom.total_amount') }}:</b>
                    </td>
                    @if($isGroup == 0 && $currencyID == 3)
                        <td class="text-right">
                            <b>{{number_format($acLocalDebitTotal, $currencyDecimalPlace)}}</b>
                        </td>
                    @endif
                    @if($isGroup == 0 && $currencyID == 3)
                        <td class="text-right">
                            <b>{{number_format($acLocalCreditTotal, $currencyDecimalPlace)}}</b>
                        </td>
                    @endif
                    @if($currencyID == 2)
                        <td class="text-right">
                            <b>{{number_format($acRptDebitTotal, $currencyDecimalPlace)}}</b>
                        </td>
                        <td class="text-right">
                            <b>{{number_format($acRptCreditTotal, $currencyDecimalPlace)}}</b>
                        </td>
                    @endif

                    @if($currencyID == 1 && $accBalanceShow)
                        <td class="text-right">
                            <b>{{number_format($acBankDebitTotal, $bankDecimal)}}</b>
                        </td>
                        <td class="text-right">
                            <b>{{number_format($acBankCreditTotal, $bankDecimal)}}</b>
                        </td>
                    @endif
                </tr>
            @endif
            @if(($currencyID == 1 && $accBalanceShow) || ($isGroup == 0 && $currencyID == 3) || $currencyID == 2)
                <tr style="background-color: #E7E7E7">
                    <td colspan="{{6 + count($extraColumns)}}" class="text-right"
                        style="">
                        <b>{{ __('custom.net_amount') }}</b>
                    </td>
                    @if($isGroup == 0 && $currencyID == 3)

                        @if(($acLocalDebitTotal - $acLocalCreditTotal) > 0)
                            <td class="text-right">
                                <b>{{number_format(($acLocalDebitTotal - $acLocalCreditTotal ), $currencyDecimalPlace)}}</b>
                            </td>
                            <td></td>
                        @endif
                        @if(($acLocalDebitTotal - $acLocalCreditTotal) < 0)
                            <td></td>
                            <td class="text-right">
                                <b>{{number_format((($acLocalDebitTotal - $acLocalCreditTotal) * -1), $currencyDecimalPlace)}}</b>
                            </td>
                        @endif
                    @endif
                    @if($currencyID == 2)
                        @if(($acRptDebitTotal - $acRptCreditTotal) > 0)
                            <td class="text-right">
                                <b>{{number_format(($acRptDebitTotal - $acRptCreditTotal ), $currencyDecimalPlace)}}</b>
                            </td>
                            <td></td>
                        @endif
                        @if(($acRptDebitTotal - $acRptCreditTotal) < 0)
                            <td></td>
                            <td class="text-right">
                                <b>{{number_format((($acRptDebitTotal - $acRptCreditTotal) * -1), $currencyDecimalPlace)}}</b>
                            </td>
                        @endif
                    @endif
                    @if($currencyID == 1 && $accBalanceShow)
                        @if(($acBankDebitTotal - $acBankCreditTotal) > 0)
                            <td class="text-right">
                                <b>{{number_format(($acBankDebitTotal - $acBankCreditTotal ), $bankDecimal)}}</b>
                            </td>
                            <td></td>
                        @endif
                        @if(($acBankDebitTotal - $acBankCreditTotal) < 0)
                            <td></td>
                            <td class="text-right">
                                <b>{{number_format((($acBankDebitTotal - $acBankCreditTotal) * -1), $bankDecimal)}}</b>
                            </td>
                        @endif
                    @endif
                </tr>
            @endif
        @endforeach
    </table>
</div>