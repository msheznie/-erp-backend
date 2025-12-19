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
            <td style="width:100%;text-align: center;">
                <span class="font-weight-bold" style="font-size: 14px">Financial General Ledger</span>
            </td>
        </tr>
    </table>
    <table style="width:100%;">
        <tr>
            <td colspan="2" style="width:100%;text-align: center;">
                <span class="font-weight-bold">Period From :{{ $fromDate }} |
                    Period To : {{ $toDate }}</span>
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
                 @if($isGroup == 0)
                    <th colspan="3" style="text-align: center">Local Currency ({{$currencyLocal}})</th>
                @endif
                <th colspan="3" style="text-align: center">Reporting Currency ({{$currencyRpt}})</th>
            </tr>
            <tr>
                <th>Document Number</th>
                <th>Date</th>
                <th>Document Narration</th>
                <th>Segment</th>
                <th>Contract</th>
                <th>Party Name</th>
                @if(in_array('confi_name', $extraColumns))
                    <th>Confirmed By</th>
                @endif
                @if(in_array('confi_date', $extraColumns))
                    <th>Confirmed Date</th>
                @endif
                @if(in_array('app_name', $extraColumns))
                    <th>Approved By</th>
                @endif
                @if(in_array('app_date', $extraColumns))
                    <th>Approved Date</th>
                @endif
                @if($isGroup == 0)
                    <th style="text-align: center">Debit</th>
                    <th style="text-align: center">Credit</th>
                    <th style="text-align: center">Balance</th>
                @endif
                <th style="text-align: center">Debit</th>
                <th style="text-align: center">Credit</th>
                <th style="text-align: center">Balance</th>
            </tr>
            {{$acLocalDebitTotal = 0}}
            {{$acLocalCreditTotal = 0}}
            {{$acRptDebitTotal = 0}}
            {{$acRptCreditTotal = 0}}
            {{$localBalanceAmount = 0}}
            {{$rptBalanceAmount = 0}}
            @foreach ($det as $key2 => $val)
                {{$localBalanceAmount += $val->localBalanceAmount}}
                {{$rptBalanceAmount += $val->rptBalanceAmount}}
                <tr>
                    <td>{{ $val->documentCode  }}</td>
                    <td>{{\Helper::dateFormat($val->documentDate)}}</td>
                    <td>{{ $val->documentNarration  }}</td>
                    <td>{{ $val->serviceLineCode  }}</td>
                    <td>{{ $val->clientContractID  }}</td>
                    <td>{{ $val->isCustomer  }}</td>
                    @if(in_array('confi_name', $extraColumns))
                        <td>{{ $val->confirmedBy  }}</td>
                    @endif
                    @if(in_array('confi_date', $extraColumns))
                        <td>{{\Helper::dateFormat($val->documentConfirmedDate)}}</td>
                    @endif
                    @if(in_array('app_name', $extraColumns))
                        <td>{{ $val->approvedBy  }}</td>
                    @endif
                    @if(in_array('app_date', $extraColumns))
                        <td>{{\Helper::dateFormat($val->documentFinalApprovedDate)}}</td>
                    @endif
                    @if($isGroup == 0)
                        <td class="text-right">{{number_format($val->localDebit, $decimalPlaceLocal)}}</td>
                        <td class="text-right">{{number_format($val->localCredit, $decimalPlaceLocal)}}</td>
                        <td class="text-right">{{number_format($localBalanceAmount, $decimalPlaceLocal)}}</td>
                    @endif
                    <td class="text-right">{{number_format($val->rptDebit, $decimalPlaceRpt)}}</td>
                    <td class="text-right">{{number_format($val->rptCredit, $decimalPlaceRpt)}}</td>
                    <td class="text-right">{{number_format($rptBalanceAmount, $decimalPlaceRpt)}}</td>
                </tr>
                {{$acLocalDebitTotal += $val->localDebit}}
                {{$acLocalCreditTotal += $val->localCredit}}
                {{$acRptDebitTotal += $val->rptDebit}}
                {{$acRptCreditTotal += $val->rptCredit}}
            @endforeach
            <tr style="background-color: #E7E7E7">
                <td colspan="{{6 + count($extraColumns)}}" class="text-right"
                    style=""><b>Total Amount:</b>
                </td>
                @if($isGroup == 0)
                    <td class="text-right">
                        <b>{{number_format($acLocalDebitTotal, $decimalPlaceLocal)}}</b>
                    </td>
                @endif
                @if($isGroup == 0)
                    <td class="text-right">
                        <b>{{number_format($acLocalCreditTotal, $decimalPlaceLocal)}}</b>
                    </td>
                    <td>&nbsp;</td>
                @endif
                <td class="text-right">
                    <b>{{number_format($acRptDebitTotal, $decimalPlaceRpt)}}</b>
                </td>
                <td class="text-right">
                    <b>{{number_format($acRptCreditTotal, $decimalPlaceRpt)}}</b>
                </td>
                <td>&nbsp;</td>
            </tr>
            <tr style="background-color: #E7E7E7">
                <td colspan="{{6 + count($extraColumns)}}" class="text-right"
                    style="">
                    <b>Balance</b>
                </td>
                @if($isGroup == 0)
                    <td colspan="2" class="text-right">
                        <b>{{number_format(($acLocalDebitTotal - $acLocalCreditTotal ), $decimalPlaceLocal)}}</b>
                    </td>
                    <td>&nbsp;</td>
                @endif
                <td colspan="2" class="text-right">
                    <b>{{number_format(($acRptDebitTotal - $acRptCreditTotal), $decimalPlaceRpt)}}</b>
                </td>
                <td>&nbsp;</td>
            </tr>
        @endforeach
        <tr style="background-color: #E7E7E7">
            <td colspan="{{6 + count($extraColumns)}}" class="text-right"
                style=""><b>Grand Total:</b>
            </td>
            @if($isGroup == 0)
                <td class="text-right">
                    <b>{{number_format($totaldocumentLocalAmountDebit, $decimalPlaceLocal)}}</b>
                </td>
            @endif
            @if($isGroup == 0)
                <td class="text-right">
                    <b>{{number_format($totaldocumentLocalAmountCredit, $decimalPlaceLocal)}}</b>
                </td>
                <td>&nbsp;</td>
            @endif
            <td class="text-right">
                <b>{{number_format($totaldocumentRptAmountDebit, $decimalPlaceRpt)}}</b>
            </td>
            <td class="text-right">
                <b>{{number_format($totaldocumentRptAmountCredit, $decimalPlaceRpt)}}</b>
            </td>
            <td>&nbsp;</td>
        </tr>
        <tr style="background-color: #E7E7E7">
            <td colspan="{{6 + count($extraColumns)}}" class="text-right"
                style="">
            </td>
            @if($isGroup == 0)
                <td colspan="2" class="text-right">
                    <b>{{number_format(($totaldocumentLocalAmountDebit - $totaldocumentLocalAmountCredit ), $decimalPlaceLocal)}}</b>
                </td>
                <td>&nbsp;</td>
            @endif
            <td colspan="2" class="text-right">
                <b>{{number_format(($totaldocumentRptAmountDebit - $totaldocumentRptAmountCredit), $decimalPlaceRpt)}}</b>
            </td>
            <td>&nbsp;</td>
        </tr>
    </table>
</div>
