
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<style type="text/css">
    
    @page {
        margin: 20px 30px 220px !important;
    }

    #footer {
        position: fixed;
        bottom: 0px;
        font-size: 10px;
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

    .table td {
        border: 1px solid !important;
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
    .container
          {
            display: block;
            max-width:230px;
            max-height:95px;
            width: auto;
            height: auto;
            }

    .table_height
    {
        max-height: 60px !important;
    }

    .head_font {
        font-size: 13px;
        font-family:Arial, Helvetica, sans-serif;
    }
    .normal_font {
        font-size: 10px;
        font-family: Arial, Helvetica, sans-serif;
    }
    
    .text_align {
        text-align: left;vertical-align: top;
    } 


</style>

<div class="content">
    <div class="row">
        <table style="width:100%" class="table_height">
            <tr>
                <td width="30%">
                </td>


                <td width="40%" style="text-align: center;white-space: nowrap">
                    <div class="text-center">

                        <h3>
                            <b>TAX INVOICE</b>
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
        <br>
    </div>

    <div class="row">
        <table style="width:100%">
            <tr>
                <td style="width: 50%; text-align: left;vertical-align: top;">
                    <table class="head_font"  style="width: 100%">
                        <tr>
                            <td class="text_align" style="width:30%;"><b>INVOICE NO </b></td>
                            <td class="text_align" style="width:2%;">:</td>
                            <td class="text_align" style="width:68%;">
                                {{$request->bookingInvCode}}</td>
                        </tr>
                        <tr>
                            <td class="text_align" style="width:30%;"><b>INVOICE DATE </b></td>
                            <td class="text_align" style="width:2%;">:</td>
                            <td class="text_align" style="width:68%;">
                                @if(!empty($request->bookingDate))
                                    {{\App\helper\Helper::dateFormat($request->bookingDate) }}
                                @endif</td>
                        </tr>

                        <tr><td></td></tr>
                        <tr>
                            <td class="text_align" style="width:30%;"><b>CUSTOMER NAME </b></td>
                            <td class="text_align" style="width:2%;">:</td>
                            <td class="text_align" style="width:68%;">
                                {{$request->customer->ReportTitle}}</td>
                        </tr>
                        <tr>
                            <td class="text_align" style="width:30%;"><b>CUSTOMER ADDRESS </b></td>
                            <td class="text_align" style="width:2%;">:</td>
                            <td class="text_align" style="width:68%;">
                                {{$request->customer->customerAddress1}}</td>
                        </tr>
                    </table>
                </td>
                <td style="width: 50%; text-align: left;vertical-align: top;">
                    <table class="head_font">
                        <tr>
                            <td width="100px" colspan="2"  style="text-decoration: underline;"><b> Remittance Details  </b></td>
                        </tr>
                        <tr>
                            <td width="100px"><span class="font-weight-bold"><b>BANK NAME</b></span></td>
                            <td><b> :
                                @if($request->secondaryLogoCompanySystemID)
                                        @if($secondaryBankAccount->contract && $secondaryBankAccount->contract->secondary_bank_account)
                                        {{$secondaryBankAccount->contract->secondary_bank_account->bankName}}
                                        @endif
                                    @else
                                    {{($request->bankaccount) ? $request->bankaccount->bankName : ''}}
                                @endif
                                </b>
                            </td>
                        </tr>
                        <tr>
                            <td width="100px"><span class="font-weight-bold"><b>ACCOUNT NO</b></span></td>
                            <td><b> :
                                @if($request->secondaryLogoCompanySystemID)
                                    @if($secondaryBankAccount->contract && $secondaryBankAccount->contract->secondary_bank_account)
                                        {{$secondaryBankAccount->contract->secondary_bank_account->AccountNo}}
                                    @endif
                                @else
                                    {{($request->bankaccount) ? $request->bankaccount->AccountNo : ''}}
                                @endif

                                </b>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
    <div class="row">
        <br>
    </div>

    <div class="row">
        <br>
    </div>
    <br>
    <div class="row">
        @if ($request->template==1 && !$request->line_invoiceDetails && !$request->linePdoinvoiceDetails)
            <table class="table" style="width: 100%;">
                <thead>
                <tr>
                    <th style="width:6%;">Item</th>
                    <th style="width:25%; text-align: center">Description</th>
                    <th style="width:10%;text-align: center">UOM</th>
                    <th style="width:6%;text-align: center">QTY</th>
                    <th style="width:10%;text-align: center">Days(OP)</th>
                    <th style="width:10%;text-align: center">Price(OP)</th>
                    <th style="width:10%;text-align: center">Days(STB)</th>
                    <th style="width:10%;text-align: center">Price(STB)</th>
                    <th style="width:13%;text-align: center">Total Amount</th>
                </tr>
                </thead>

                <tbody>
                {{$decimal = 2}}
                {{$x=1}}
                {{$directTraSubTotal=0}}
                {{$numberFormatting=empty($request->currency) ? 2 : $request->currency->DecimalPlaces}}

                @foreach ($request->profomaDetailData as $item)
                    @if ($item->total != 0)
                        {{$directTraSubTotal +=$item->total}}
                        <tr style="border: 1px solid !important;">
                            <td>{{$x}}</td>
                            <td style="word-wrap:break-word;">{{$item->description}}</td>
                            <td style="text-align: left;">{{isset($item->uom_issuing->UnitShortCode)?$item->uom_issuing->UnitShortCode:''}}</td>
                            <td style="text-align: right;">{{$item->Qty}}</td>
                            <td style="text-align: right;">{{$item->Days_OP}}</td>
                            <td style="text-align: right;">{{number_format($item->Price_OP,$numberFormatting)}}</td>
                            <td style="text-align: right;">{{$item->Days_STB}}</td>
                            <td style="text-align: right;">{{number_format($item->Price_STB,$numberFormatting)}}</td>
                            <td class="text-right">{{number_format($item->total,$numberFormatting)}}</td>
                        </tr>
                        {{ $x++ }}
                    @endif
                @endforeach
                </tbody>

                <tbody>
                    <tr>
                        <td></td>
                        <td colspan="5" style="text-align: left; border-right: none !important;"><b>Total Before VAT</b></td>
                        <td style="text-align: center; border-left: none !important"><b>{{empty($request->currency) ? '' : $request->currency->CurrencyCode}}</b></td>
                        <td class="text-right">@if ($request->invoicedetails)
                        {{number_format($directTraSubTotal, $numberFormatting)}}
                    @endif</td>
                    </tr>
                    {{$directTraSubTotal+= ($request->tax) ? $request->tax->amount : 0}}
                    {{$taxAmount = ($request->tax) ? $request->tax->amount : 0}}
                    {{$taxPercent = ($request->tax) ? $request->tax->taxPercent : 0}}
                    <tr>
                        <td></td>
                        <td colspan="5" style="text-align: left; border-right: none !important;"><b>Value Added Tax {{$taxPercent}}%</b></td>
                        <td style="text-align: center; border-left: none !important"><b>{{empty($request->currency) ? '' : $request->currency->CurrencyCode}}</b></td>
                        <td class="text-right">{{number_format($taxAmount, $numberFormatting)}}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td colspan="5" style="text-align: left; border-right: none !important;"><b>Total Amount Including VAT</b></td>
                        <td style="text-align: center; border-left: none !important"><b>{{empty($request->currency) ? '' : $request->currency->CurrencyCode}}</b></td>
                        <td class="text-right">{{number_format($directTraSubTotal, $numberFormatting)}}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td colspan="7" style="text-align: left; border-right: none !important;"><b>Total Amount in Word ({{empty($request->currency) ? '' : $request->currency->CurrencyCode}}): ({{$request->amount_word}}
                            @if ($request->floatAmt > 0)
                            and
                            {{$request->floatAmt}}/@if($request->currency->DecimalPlaces == 3)1000 @else 100 @endif
                            @endif
                            
                            only)</b>
                        </td>
                    </tr>
                </tbody>
                <tbody>
                    <tr>
                        <td colspan="8">PLEASE ISSUE ALL PAYMENT ON ABOVE BANK ACCOUNT DETAILS</td>
                    </tr>
                </tbody>
                
            </table>
        @endif

         @if ($request->template <> 1 && !$request->line_invoiceDetails && !$request->item_invoice)
            <table class="table" style="width: 100%;">
                <thead>
                <tr>
                    <th style="width:6%">Item</th>
                    <th style="width:10%; text-align: center">GL Code</th>
                    <th style="width:25%; text-align: center">Description</th>
                    <th style="width:10%;text-align: center">UOM</th>
                    <th style="width:5%;text-align: center">QTY</th>
                    <th style="width:10%;text-align: center">Unit Rate</th>
                    <th style="width:15%;text-align: center">Total Amount</th>
                </tr>
                </thead>

                <tbody>
                {{$decimal = 2}}
                {{$x=1}}
                {{$directTraSubTotal=0}}
                {{$numberFormatting=empty($request->currency) ? 2 : $request->currency->DecimalPlaces}}
                @foreach ($request->invoicedetails as $item)
                    {{$directTraSubTotal +=$item->invoiceAmount}}
                    <tr style="border: 1px solid !important;">
                        <td>{{$x}}</td>
                        <td>{{$item->glCode}}</td>
                        <td>{{$item->glCodeDes}}</td>
                        <td style="text-align: left;">{{isset($item->unit->UnitShortCode)?$item->unit->UnitShortCode:''}}</td>
                        <td style="text-align: right;">{{number_format($item->invoiceQty,2)}}</td>
                        <td style="text-align: right;">{{number_format($item->unitCost,$numberFormatting)}}</td>
                        <td class="text-right">{{number_format($item->invoiceAmount,$numberFormatting)}}</td>
                    </tr>
                    {{ $x++ }}
                @endforeach
                </tbody>

                <tbody>
                    <tr>
                        <td></td>
                        <td colspan="3" style="text-align: left; border-right: none !important;"><b>Total Before VAT</b></td>
                        <td style="text-align: center; border-left: none !important"><b>{{empty($request->currency) ? '' : $request->currency->CurrencyCode}}</b></td>
                        <td colspan="2" class="text-right">@if ($request->invoicedetails)
                        {{number_format($directTraSubTotal, $numberFormatting)}}
                    @endif</td>
                    </tr>
                    @if ($request->tax)
                    {{$directTraSubTotal+=$request->tax->amount}}
                        <tr>
                            <td></td>
                            <td colspan="3" style="text-align: left; border-right: none !important;"><b>Value Added Tax {{$request->tax->taxPercent}}%</b></td>
                            <td style="text-align: center; border-left: none !important"><b>{{empty($request->currency) ? '' : $request->currency->CurrencyCode}}</b></td>
                            <td colspan="2" class="text-right">{{number_format($request->tax->amount, $numberFormatting)}}</td>
                        </tr>

                    <tr>
                        <td></td>
                        <td colspan="3" style="text-align: left; border-right: none !important;"><b>Total Amount Including VAT</b></td>
                        <td style="text-align: center; border-left: none !important"><b>{{empty($request->currency) ? '' : $request->currency->CurrencyCode}}</b></td>
                        <td colspan="2" class="text-right">{{number_format($directTraSubTotal, $numberFormatting)}}</td>
                    </tr>
                    @endif
                    <tr>
                        <td></td>
                        <td colspan="4" style="text-align: left;"><b>Total Amount in Word ({{empty($request->currency) ? '' : $request->currency->CurrencyCode}})</b>
                        </td>
                        <td colspan="2" style="text-align: right;">
                            {{$request->amount_word}}
                            @if ($request->floatAmt > 0)
                            and
                            {{$request->floatAmt}}/@if($request->currency->DecimalPlaces == 3)1000 @else 100 @endif
                            @endif
                            only
                        </td>
                    </tr>
                </tbody>
                <tbody>
                    <tr>
                        <td colspan="7">PLEASE ISSUE ALL PAYMENT ON ABOVE BANK ACCOUNT DETAILS</td>
                    </tr>
                </tbody>
                
            </table>
        @endif

        @if ($request->template == 2 && isset($request->item_invoice) && $request->item_invoice)

                <table class="table" style="width: 100%;">
                    <thead>
                    <tr>
                        <th style="width:5%;"></th>
                        <th style="width:40%;">Item</th>
                        <th style="width:10%;text-align: center">UOM</th>
                        <th style="width:15%;text-align: center">QTY</th>
                        <th style="width:15%;text-align: center">Unit Cost</th>
                        <th style="width:15%;text-align: center">Total Amount</th>
                    </tr>
                    </thead>

                    <tbody>
                    {{$decimal = 2}}
                    {{$x=1}}
                    {{$directTraSubTotal=0}}
                    {{$numberFormatting=empty($request->currency) ? 2 : $request->currency->DecimalPlaces}}

                    @if(!empty($request->issue_item_details))
                        @foreach ($request->issue_item_details as $item)

                            @if ($item->sellingTotal != 0)
                                {{$directTraSubTotal +=$item->sellingTotal}}

                                <tr style="border: 1px solid !important;">
                                    <td>{{$x}}</td>
                                    <td style="word-wrap:break-word;">{{$item->itemPrimaryCode.' - '.$item->itemDescription}}</td>
                                    <td style="text-align: left;">{{isset($item->uom_issuing->UnitShortCode)?$item->uom_issuing->UnitShortCode:''}}</td>
                                    <td style="text-align: right;">{{$item->qtyIssued}}</td>
                                    <td style="text-align: right;">{{number_format($item->sellingCostAfterMargin,$numberFormatting)}}</td>
                                    <td class="text-right">{{number_format($item->sellingTotal,$numberFormatting)}}</td>
                                </tr>
                                {{ $x++ }}
                            @endif
                        @endforeach
                    @endif

                    </tbody>
                    <tbody>
                    <tr>
                        <td></td>
                        <td colspan="3" style="text-align: left; border-right: none !important;"><b>Total Before VAT</b></td>
                        <td style="text-align: left; border-left: none !important"><b>{{empty($request->currency) ? '' : $request->currency->CurrencyCode}}</b></td>
                        <td class="text-right">@if ($request->invoicedetails)
                                {{number_format($directTraSubTotal, $numberFormatting)}}
                            @endif</td>
                    </tr>
                    @if ($request->isVATEligible)
                        {{$totalVATAmount = (($request->tax && $request->tax->amount) ? $request->tax->amount : 0)}}
                        {{$directTraSubTotal+=$totalVATAmount}}
                        <tr>
                            <td></td>
                            <td colspan="3" style="text-align: left; border-right: none !important;"><b>Value Added Tax {{round( ( ($request->tax && $request->tax->taxPercent ) ? $request->tax->taxPercent : 0 ), 2)}}%</b></td>
                            <td style="text-align: left; border-left: none !important"><b>{{empty($request->currency) ? '' : $request->currency->CurrencyCode}}</b></td>
                            <td class="text-right">{{number_format($totalVATAmount, $numberFormatting)}}</td>
                        </tr>

                        <tr>
                            <td></td>
                            <td colspan="3" style="text-align: left; border-right: none !important;"><b>Total Amount Including VAT</b></td>
                            <td style="text-align: left; border-left: none !important"><b>{{empty($request->currency) ? '' : $request->currency->CurrencyCode}}</b></td>
                            <td class="text-right">{{number_format($directTraSubTotal, $numberFormatting)}}</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td colspan="3" style="text-align: left; border-right: none !important;"><b>Total Amount in Word ({{empty($request->currency) ? '' : $request->currency->CurrencyCode}})</b>
                            </td>
                            <td colspan="2" style="text-align: left; border-left: none !important;">
                                <b>
                                    {{$request->amount_word}}
                                    @if ($request->floatAmt > 0)
                                    and
                                    {{$request->floatAmt}}/@if($request->currency->DecimalPlaces == 3)1000 @else 100 @endif
                                    @endif
                                    only
                                </b>
                            </td>
                        </tr>
                    @endif
                    </tbody>
                    <tbody>
                    <tr>
                        <td colspan="6">PLEASE ISSUE ALL PAYMENT ON ABOVE BANK ACCOUNT DETAILS</td>
                    </tr>
                    </tbody>
                </table>

        @endif
    </div>
</div>










