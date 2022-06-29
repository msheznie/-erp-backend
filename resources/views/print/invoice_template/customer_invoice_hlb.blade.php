<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<style type="text/css">
    <!--
    @page {
        margin: 20px 30px 220px !important;
    }

    #footer {
        position: fixed;
        bottom: 0px;
        font-size: 12px;
    }

    body {
        font-size: 11.5px;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
        color: black;
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
    }

    table > tbody > th > tr > td {
        font-size: 11.5px;
    }

    .theme-tr-head {
        background-color: #EBEBEB !important;
    }

    .text-left {
        text-align: left;
    }

    td {
        padding: 3px;
    }

    table {
        border-collapse: collapse;
        color: black;
    }

    .font-weight-bold {
        font-weight: 700 !important;
    }

    .table th {
        border: 1px solid !important;
    }

    .table tr {
        /*border: 1px solid !important;*/
    }

    .foot-amount tr {
        border: 1px solid !important;
    }

    .body-amount tr {
        border-left: 1px solid !important;
        border-right: 1px solid !important;
        border-top: none !important;
        border-bottom: none !important;
    }

    .table th {
        background-color: #EBEBEB !important;
    }

    tfoot > tr > td {
        /*border: 1px solid rgb(127, 127, 127);*/
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

    .pagenum:after {
        content: counter(page);
    }

    /*.content {
        margin-bottom: 30px;
    }
*/
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

    fieldset.scheduler-border {
        border: 1px solid #ddd !important;
        /*padding: 0 1.4em 1.4em 1.4em !important;*/
        padding: 0 0.5em 0em 0.8em !important;
        /*margin: 0 0 1.5em 0 !important;*/
        -webkit-box-shadow: 0px 0px 0px 0px #000;
        box-shadow: 0px 0px 0px 0px #000;
    }

    legend.scheduler-border {

        text-align: left !important;
        width: auto;
        padding: 5px;
        border-bottom: none;
    }

    legend {
        margin-top: -15px;
        font-size: 11.5px;
        color: black;
    }


</style>

<div class="content">
    <div class="row">
        <table style="width:100%">
            <tr>
                <td width="30%">
                    @if($request->logoExists)
                          @if($type == 1)
                            <img src="{{$request->companyLogo}}"
                                width="180px" height="60px">
                          @else
                            image not found
                          @endif
                    @endif
                </td>


                <td width="50%" style="text-align: center;white-space: nowrap">
                    <div class="text-center">
                    </div>
                </td>
                <td style="width: 30%; text-align: right;">
                    <div style="display: flex;">
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <div class="row">
        <br>
    </div>
    <div class="row">
        <table style="width:100%">
            <tr>
                <td width="30%">
                </td>


                <td width="50%" style="text-align: center;white-space: nowrap">
                    <div class="text-center">

                        <h3>
                            <b style="text-decoration: underline;">TAX INVOICE</b>
                        </h3>
                    </div>

                </td>
                <td style="width: 30%; text-align: right;">
                    <div style="display: flex;">
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <div class="row">
        <br>
    </div>

    <div class="row">
        <table style="width:100%">
            <tr>
                <td style="width: 50%; text-align:left;vertical-align: top;">
                    <b>{{$request->customer->CustomerName}}</b><br>
                    <b>{{$request->customer->customerAddress1}}</b><br>
                    <b>{{$request->customer->customerAddress2}}</b><br>
                    <b>{{$request->vatNumber}}</b><br>
                </td>

                <td style="width: 50%; text-align:right;vertical-align: top;">
                    <b>
                        @if(!empty($request->bookingDate))
                            {{\App\helper\Helper::dateFormat($request->bookingDate) }}
                        @endif
                    </b>
                    <br>
                    <b>INVOICE NO : {{$request->bookingInvCode}}</b><br>
                    <b>VAT NO : {{$request->vatNumber}}</b>
                </td>
            </tr>
        </table>
    </div>
    <div class="row">
        <br>
    </div>
    <br>
    <div class="row">
        @if ($request->template == 2 && isset($request->item_invoice) && $request->item_invoice)
            <table class="table" style="width: 100%">
                <thead>
                    <tr class="theme-tr-head">
                        <th style="width:80%;">Description</th>
                        <th style="width:20%;text-align: center">Amount<br>({{empty($request->currency) ? '' : $request->currency->CurrencyCode}})</th>
                    </tr>
                </thead>

                <tbody class="body-amount">
                {{$decimal = 2}}
                {{$x=1}}
                {{$directTraSubTotal=0}}
                {{$numberFormatting=empty($request->currency) ? 2 : $request->currency->DecimalPlaces}}

                @if(!empty($request->issue_item_details))
                    @foreach ($request->issue_item_details as $item)

                        @if ($item->sellingTotal != 0)
                            {{$directTraSubTotal +=$item->sellingTotal}}

                            <tr style="border-bottom: none !important;">
                                <td style="word-wrap:break-word;border-bottom: none !important;">{{$item->itemDescription}}</td>
                                <td class="text-right" style="border-left: 1px solid !important">{{number_format($item->sellingTotal,$numberFormatting)}}</td>
                            </tr>
                            {{ $x++ }}
                        @endif
                    @endforeach
                @endif

                </tbody>
                <tbody class="foot-amount">
                    <tr>
                        <td style="text-align: left; border-right: none !important;"><b>Total</b></td>
                        <td class="text-right" style="border-left: 1px solid !important">@if ($request->invoicedetails)
                                {{number_format($directTraSubTotal, $numberFormatting)}}
                            @endif</td>
                    </tr>
                    @if ($request->isVATEligible)
                        {{$totalVATAmount = (($request->tax && $request->tax->amount) ? $request->tax->amount : 0)}}
                        {{$directTraSubTotal+=$totalVATAmount}}
                        <tr>
                            <td style="text-align: left; border-right: none !important;"><b>VAT @ {{round( ( ($request->tax && $request->tax->taxPercent ) ? $request->tax->taxPercent : 0 ), 2)}}% </b></td>
                            <td class="text-right" style="border-left: 1px solid !important">{{number_format($totalVATAmount, $numberFormatting)}}</td>
                        </tr>

                        <tr>
                            <td  style="text-align: left; border-right: none !important;"><b>Total Payable: ({{$request->amountInWordsEnglish}})</b></td>
                            <td class="text-right" style="border-left: 1px solid !important">{{number_format($directTraSubTotal, $numberFormatting)}}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        @endif
    </div>
    <br>
    <div class="row">
         <table style="width: 100%">
            <tr>
                <td width="100px" colspan="3"><span class="font-weight-bold" style="text-decoration: underline;"><b>Bank Details </b></span></td>
            </tr>
            <tr>
                <td width="100px"><span class="font-weight-bold">Bank Name</span></td>
                <td> :
                    @if($request->secondaryLogoCompanySystemID)
                         @if($secondaryBankAccount->contract && $secondaryBankAccount->contract->secondary_bank_account)
                            {{$secondaryBankAccount->contract->secondary_bank_account->bankName}}
                          @endif
                        @else
                        {{($request->bankaccount) ? $request->bankaccount->bankName : ''}}
                    @endif
                </td>
            </tr>
            <tr>
                <td width="100px"><span class="font-weight-bold">Account Name</span></td>
                <td>:
                    @if($request->secondaryLogoCompanySystemID)
                        @if($secondaryBankAccount->contract && $secondaryBankAccount->contract->secondary_bank_account)
                            {{$secondaryBankAccount->contract->secondary_bank_account->AccountName}}
                        @endif
                    @else
                        {{($request->bankaccount) ? $request->bankaccount->AccountName : ''}}
                    @endif
                </td>
            </tr>
            <tr>
                <td width="100px"><span class="font-weight-bold">Account No</td>
                <td>:
                    @if($request->secondaryLogoCompanySystemID)
                        @if($secondaryBankAccount->contract && $secondaryBankAccount->contract->secondary_bank_account)
                            {{$secondaryBankAccount->contract->secondary_bank_account->AccountNo}}
                        @endif
                    @else
                        {{($request->bankaccount) ? $request->bankaccount->AccountNo : ''}}
                    @endif
                </td>
            </tr>
        </table>
    </div>
</div>










