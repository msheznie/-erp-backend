<style type="text/css">
    <!--
    @page {
        margin: 20px 30px 0px;
    }

    #footer {
        /*bottom: 0;*/
        /*position: absolute;*/
        /*bottom: -190px;*/
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
        color: inherit;
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
        border: 1px solid rgb(174, 174, 174) !important;
    }

    .table th, .table td {
        padding: 3px !important;
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
        font-size: 13px;
        font-weight: 600;
    }

    .pagenum:after {
        content: counter(page);
    }

    .content {
        margin-bottom: 10px;
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
</style>

{{--<div id="watermark">
         <span class="watermarkText">
           <h3 class="text-muted">
               @if($request->confirmedYN == 0 && $request->approved == 0)
                   Not Confirmed & Not Approved <br> Draft Copy
               @endif
               @if($request->confirmedYN == 1 && $request->approved == 0)
                   Confirmed & Not Approved <br> Draft Copy
               @endif

           </h3>
         </span>
</div>--}}

<div class="content">
    <div class="row">
        <table style="width:100%" class="table_height">
            <tr>
                <td width="30%">
                   @if($request->logo)
                           @if($type == 1)
                            <img src="{{$request->companyLogo}}"
                            class="container">
                          @else
                            image not found
                          @endif

                    @endif
                </td>


                <td width="50%" style="text-align: center;white-space: nowrap">
                    <div class="text-center">

                        <h3 class="font-weight-bold">
                            {{$request->CompanyName}}<br>
                            <span style="font-size: 14px">
                                {{$request->CompanyAddress}}<br>
                               Telephone: {{$request->CompanyTelephone}}
                            </span>
                        </h3>

                        <h3 class="font-weight-bold">
                                Tax Invoice
                        </h3>
                    </div>

                </td>
                <td style="width: 30%"></td>
            </tr>
        </table>
    </div>
    <div class="row">
        <br>
    </div>

    <div class="row">
        <table style="width:100%">
            <tr style="vertical-align: top;">
            <td style="width: 40%" style="vertical-align: top;">
                <fieldset class="scheduler-border" style="background-color: #f1f1f1">
                    <legend class="scheduler-border" style="background-color: white;border: 1px solid black">Customer
                        Details
                    </legend>
                    <br>

                    <table style="width: 100%; !important">
                        <tr>
                            <td width="110px"><span class="font-weight-bold">Name of Customer</span></td>
                            <td width="10px"><span class="font-weight-bold">-</span></td>
                            <td>
                                <span>
                                {{$request->customer->ReportTitle}}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td width="110px"><span class="font-weight-bold">Address Line 1</span></td>
                            <td width="10px"><span class="font-weight-bold">-</span></td>
                            <td>
                                <span>
                                {{$request->customer->customerAddress1}}
                                </span>
                            </td>
                        </tr>
                        @if($request->lineSecondAddress)
                        <tr>
                            <td width="110px"><span class="font-weight-bold">Address Line 2</span></td>
                            <td width="10px"><span class="font-weight-bold">-</span></td>
                            <td>
                                <span>
                                {{$request->customer->customerAddress2}}
                                </span>
                            </td>
                        </tr>
                       @else
                            <tr>
                                <td>{{$request->customer->customerCity}}</td>
                            </tr>
                            <tr>
                                <td></td>
                            </tr>
                            <tr>
                                <td></td>
                            </tr>
                            <tr>
                                <td></td>
                            </tr>
                        @endif

                        @if ($request->is_pdo_vendor)
                        <tr>
                            <td width="110px"><span class="font-weight-bold">Vendor Code</span></td>
                            <td width="10px"><span class="font-weight-bold">-</span></td>
                            <td>
                                <span>
                                {{$request->vendorCode}}
                                </span>
                            </td>
                        </tr>
                        @endif
                        <tr>
                            <td width="110px"><span class="font-weight-bold">Customer VATIN</span></td>
                            <td width="10px"><span class="font-weight-bold">-</span></td>
                            <td>
                                <span>
                                    @if (isset($request->customer->vatNumber) && !is_null($request->customer->vatNumber))
                                            {{$request->customer->vatNumber}}
                                    @endif
                                </span>
                            </td>
                        </tr>
                    </table>
                </fieldset>
            </td>

            <td style="width: 10%"></td>
            <td style="width: 40%">
                <fieldset class="scheduler-border" style="background-color: #f1f1f1">
                    <legend class="scheduler-border" style="background-color: white;border: 1px solid black">Invoice
                        Details
                    </legend>
                    <br>
                    <table style="width: 100%">
                        <tr>
                            <td width="120px"><span class="font-weight-bold">Invoice Number</span></td>
                            <td width="10px"><span class="font-weight-bold">-</span></td>
                            <td><span>{{$request->bookingInvCode}}</span></td>
                        </tr>
                        <tr>
                            <td width="120px"><span class="font-weight-bold">Invoice Date</span></td>
                            <td width="10px"><span class="font-weight-bold">-</span></td>
                            <td><span>
                                 @if(!empty($request->bookingDate))
                                        {{\App\helper\Helper::dateFormat($request->bookingDate) }}
                                    @endif
                            </span></td>
                        </tr>
                        <tr>
                            <td width="120px"><span class="font-weight-bold">PO Number</span></td>
                            <td width="10px"><span class="font-weight-bold">-</span></td>
                            <td>
                                @if ($request->PONumber)
                                    {{$request->PONumber}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td width="120px"><span
                                        class="font-weight-bold">Contract No</span></td>
                            <td width="10px"><span class="font-weight-bold">-</span></td>
                            <td>
                                <span>
                                    @if (isset($request->invoicedetails[0]->clientContractID))
                                        {{$request->invoicedetails[0]->clientContractID}}
                                    @endif
                                 </span>
                            </td>
                        </tr>
                        <tr>
                            <td width="120px"><span class="font-weight-bold">Payment Terms</span></td>
                            <td width="10px"><span class="font-weight-bold">-</span></td>
                            <td>

                                @if($request->paymentInDaysForJob)
                                    {{$request->paymentInDaysForJob}} Days

                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td width="120px"><span class="font-weight-bold">Invoice Due Date</span></td>
                            <td width="10px"><span class="font-weight-bold">-</span></td>
                            <td>
                                <span>
                                 @if(!empty($request->invoiceDueDate))
                                        {{\App\helper\Helper::dateFormat($request->invoiceDueDate)}}
                                    @endif
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td width="120px"><span class="font-weight-bold">SE No</span></td>
                            <td width="10px"><span class="font-weight-bold">-</span></td>
                            <td>
                                <span>
                                    @if($request->wanNO)
                                        {{$request->wanNO}}
                                    @endif
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td width="120px"><span class="font-weight-bold">Date of Supply/Service</span></td>
                            <td width="10px"><span class="font-weight-bold">-</span></td>
                            <td>
                                <span>
                                    @if($request->serviceStartDate)
                                         {{\App\helper\Helper::dateFormat($request->serviceStartDate) }}
                                    @endif
                                    -
                                     @if($request->serviceEndDate)
                                        {{\App\helper\Helper::dateFormat($request->serviceEndDate) }}
                                    @endif
                                </span>
                            </td>
                        </tr>
                      <!--  
                        @if($request->line_performaCode)
                            <tr>
                                <td width="120px"><span class="font-weight-bold">Proforma Invoice No</span></td>
                                <td width="10px"><span class="font-weight-bold">-</span></td>
                                <td><span>{{$request->invoicedetail->performadetails->performaCode}}</span></td>
                            </tr>
                        @endif

                        -->
                      
                        <!-- @if ($request->line_unit)
                            <tr>
                                <td width="120px"><span class="font-weight-bold">Unit</span></td>
                                <td width="10px"><span class="font-weight-bold">-</span></td>
                                {{--<td><span>{{$request->rigNo}}</span></td> --}}
                                <td>

                                    <span>{{isset($request->invoicedetail->billmaster->ticketmaster->rig->RigDescription)?$request->invoicedetail->billmaster->ticketmaster->rig->RigDescription:''}}</span>
                                    | <span>{{isset($request->invoicedetail->billmaster->ticketmaster->regNo)?$request->invoicedetail->billmaster->ticketmaster->regNo:''}}</span></td>
                            </tr>
                        @endif
                        @if ($request->line_jobNo)
                            <tr>
                                <td width="120px"><span class="font-weight-bold">Job No</span></td>
                                <td width="10px"><span class="font-weight-bold">-</span></td>
                                <td><span>{{isset($request->invoicedetail->billmaster->ticketmaster->ticketNo)?$request->invoicedetail->billmaster->ticketmaster->ticketNo:''}}


                                    </span></td>
                            </tr>
                        @endif
                        @if ($request->is_pdo_vendor)
                            <tr>
                                <td width="120px"><span class="font-weight-bold">TRN</span></td>
                                <td width="10px"><span class="font-weight-bold">-</span></td>
                                <td><span>-</span></td>
                            </tr>
                        @endif -->
                        <tr>
                            <td width="120px"><span class="font-weight-bold">Invoice Currency</span></td>
                            <td width="10px"><span class="font-weight-bold">-</span></td>
                            <td><span>{{empty($request->currency) ? '' : $request->currency->CurrencyCode}}</span></td>
                        </tr>
                        <tr>
                            <td width="120px"><span class="font-weight-bold">VATIN</span></td>
                            <td width="10px"><span class="font-weight-bold">-</span></td>
                            <td>
                                <span>
                                @if(isset($request->company->vatRegistratonNumber))
                                     {{$request->company->vatRegistratonNumber}}
                                @endif()
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td width="120px"><span class="font-weight-bold">JSRS No</span></td>
                            <td width="10px"><span class="font-weight-bold">-</span></td>
                            <td>
                                <span>
                                    @if(isset($request->company->jsrsNumber))
                                    {{$request->company->jsrsNumber}}
                                    @endif()
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td width="120px"><span class="font-weight-bold">Tax Card No</span></td>
                            <td width="10px"><span class="font-weight-bold">-</span></td>
                            <td>
                                <span>
                                    @if(isset($request->company->taxCardNo))
                                    {{$request->company->taxCardNo}}
                                    @endif()
                                </span>
                            </td>
                        </tr>
                    </table>
                </fieldset>
            </td>
            </tr>
        </table>

    </div>

    <br>
    @if($request->line_rentalPeriod)

        <div class="row" style="text-align: center">
            <b>Rental Period From
                {{\App\helper\Helper::dateFormat($request->invoicedetail->billmaster->rentalStartDate)}} -
                {{\App\helper\Helper::dateFormat($request->invoicedetail->billmaster->rentalEndDate)}}</b>
        </div>
        <div class="row" style="">
            <b><span>{{$request->invoicedetail->billmaster->ticketmaster->rig->RigDescription}}</span> |
                <span> {{$request->invoicedetail->billmaster->ticketmaster->regNo}}</span></b>
        </div>
    @else
        <div class="row" style="">
            <b>Comments : </b> {!! nl2br($request->comments) !!}
        </div>
    @endif
    @if($request->linePdoinvoiceDetails)
        <div class="row">
                <table class="table table-bordered table-striped table-sm" style="width: 100%;">
                    <thead>
                    <tr class="">
                        <th style="width:1%"></th>
                        <th style="text-align: center">Client Ref No</th>
                        <th style="text-align: center">PO Line Item No</th>
                        <th style="text-align: center">Description of Goods/ Services</th>
                        <th style="text-align: right">Quantity</th>
                        <th style="text-align: right">Unit Price (Excluding Tax)</th>
                        <th style="text-align: right">Taxable Amount after excluding Tax</th>
                        <th style="text-align: right">VAT Rate %</th>
                        <th style="text-align: right">VAT Amount</th>
                        <th style="text-align: right">Total Amount Inclusive of VAT</th>
                    </tr>
                    </thead>
                    <tbody>
                    {{$decimal = 2}}
                    {{$x=1}}
                    {{$directTraSubTotal=0}}
                    {{$vatAmountSubTotal=0}}
                    {{$numberFormatting=empty($request->currency) ? 2 : $request->currency->DecimalPlaces}}
                    @foreach ($request->linePdoinvoiceDetails as $item)
                        {{$vatPecentage = $item->percentage}}
                        {{$vatAmount = $item->vatAmount * $item->qty}}
                        {{$directTraSubTotal +=($item->amount - $vatAmount)}}
                        {{$vatAmountSubTotal +=$vatAmount}}
                        <tr style="border-top: 2px solid #333 !important;border-bottom: 2px solid #333 !important;background-color: white">
                            <td>{{$x}}</td>
                            <td >{{$item->client_referance}}</td>
                            <td>{{$item->po_detail_id}}</td>
                            <td>{{$item->item_description}}</td>
                            <td style="text-align: right">{{number_format($item->qty,2)}}</td>
                            <td style="text-align: right">{{number_format(($item->unit_price - $item->vatAmount),$numberFormatting)}}</td>
                            <td style="text-align: right">{{number_format(($item->amount - $vatAmount),$numberFormatting)}}</td>
                            <td style="text-align: right">{{number_format($vatPecentage,2)}}</td>
                            <td style="text-align: right">{{number_format($vatAmount,$numberFormatting)}}</td>
                            <td style="text-align: right" class="text-right">{{number_format(($item->amount),$numberFormatting)}}</td>
                        </tr>
                        {{ $x++ }}
                    @endforeach

                        <tr style="background-color: #EBEBEB">
                            <td colspan="4" style="text-align: right">
                                 <span class="font-weight-bold">
                                    Total (Currency in {{empty($request->currency) ? '' : $request->currency->CurrencyCode}})
                                 </span>
                            </td>
                            <td colspan="2">

                            </td>
                            <td style="text-align: right">
                                 <span class="font-weight-bold">
                                    @if ($request->linePdoinvoiceDetails)
                                            {{number_format($directTraSubTotal, $numberFormatting)}}@endif</span>
                            </td>
                            <td>
                            </td>
                            <td style="text-align: right">
                                 <span class="font-weight-bold">
                                     @if ($request->linePdoinvoiceDetails)
                                            {{number_format($vatAmountSubTotal, $numberFormatting)}}@endif</span>
                            </td>
                            <td style="text-align: right">
                                 <span class="font-weight-bold">
                                    @if ($request->linePdoinvoiceDetails)
                                            {{number_format(($vatAmountSubTotal + $directTraSubTotal), $numberFormatting)}}@endif</span>
                            </td>
                        </tr>
                        @if ($request->linePdoinvoiceDetails)
                        <tr>
                            <td colspan="10">
                                (Total Amount in {{empty($request->currency) ? '' : $request->currency->CurrencyCode}} : {{\App\helper\Helper::amountInWords(round(($directTraSubTotal + $vatAmountSubTotal), $numberFormatting))}} Only)
                            </td>
                        </tr>
                        @endif
                        <tr>
                            <td colspan="7" style="text-align: right;">
                                 <span class="font-weight-bold">
                                    Conversion Rate
                                 </span>
                            </td>
                            <td style="text-align: right;">
                                @if($request->localCurrencyER != 0)
                                    {{ round(1/$request->localCurrencyER,4)}}
                                @endif
                            </td>
                            <td colspan="2">
                            </td>
                        </tr>

                         <tr style="background-color: #EBEBEB">
                            <td colspan="4" style="text-align: right">
                                 <span class="font-weight-bold">
                                    Grand Total ({{empty($request->local_currency) ? '' : $request->local_currency->CurrencyCode}})
                                 </span>
                            </td>
                            <td colspan="2">

                            </td>
                            <td style="text-align: right">
                                 <span class="font-weight-bold">
                                    @if ($request->linePdoinvoiceDetails)
                                            {{number_format(($directTraSubTotal/$request->localCurrencyER), $numberFormatting)}}@endif</span>
                            </td>
                            <td>
                            </td>
                            <td style="text-align: right">
                                 <span class="font-weight-bold">
                                    @if ($request->linePdoinvoiceDetails)
                                            {{number_format(($vatAmountSubTotal/$request->localCurrencyER), $numberFormatting)}}@endif</span>
                            </td>
                            <td style="text-align: right">
                                 <span class="font-weight-bold">
                                    @if ($request->linePdoinvoiceDetails)
                                            {{number_format((($directTraSubTotal/$request->localCurrencyER) + ($vatAmountSubTotal/$request->localCurrencyER)), $numberFormatting)}}@endif</span>
                            </td>
                        </tr>
                    </tbody>

                </table>
        </div>
        <br>
        <div class="row">
            <table style="width:100%;" class="table table-bordered">
                <tbody>
                <tr>
                    <td style="border-bottom: none !important;border-left: none !important;width: 60%;">&nbsp;</td>
                    <td class="text-right" style="width: 20%;border-bottom: none !important"><span
                                class="font-weight-bold"
                                style="border-bottom: none !important;font-size: 11.5px">Sub Total  ({{empty($request->currency) ? '' : $request->currency->CurrencyCode}}) </span>
                    </td>
                    <td class="text-right"
                        style="font-size: 11.5px;width: 20%;border-left: 1px #EBEBEB !important;border-right: 1px #EBEBEB !important;">
                    <span class="font-weight-bold">@if ($request->linePdoinvoiceDetails){{number_format($directTraSubTotal, $numberFormatting)}}@endif</span>
                    </td>
                </tr>

               
                <tr>
                    <td style="border:none !important;">
                        &nbsp;
                    </td>
                    <td class="text-right" style="border:none !important;"><span
                                class="font-weight-bold"
                                style="font-size: 11.5px">VAT ({{empty($request->currency) ? '' : $request->currency->CurrencyCode}}) 
                            </span></td>
                    <td class="text-right"
                        style="font-size: 11.5px;border-left: 1px #EBEBEB !important;border-right: 1px #EBEBEB !important;"><span
                                class="font-weight-bold">{{number_format($vatAmountSubTotal, $numberFormatting)}}</span>
                    </td>
                </tr>

                <tr>
                    <td style="border-bottom: none !important;border-top: none !important;border-left: none !important;">
                        &nbsp;
                    </td>
                    <td class="text-right" style="border:none !important;"><span
                                class="font-weight-bold"
                                style="font-size: 11.5px">Total  ({{empty($request->currency) ? '' : $request->currency->CurrencyCode}})   </span>
                    </td>
                    <td class="text-right"
                        style="font-size: 11.5px;border-left: 1px #EBEBEB !important;border-right: 1px #EBEBEB !important;background-color: #EBEBEB">
                            <span class="font-weight-bold">

                                    {{number_format(($directTraSubTotal + $vatAmountSubTotal), $numberFormatting)}}</span>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <br>
        @if($request->custTransactionCurrencyID != $request->localCurrencyID)
            <br>
            <div class="row">
                <table style="width:100%;" class="table table-bordered">
                    <tbody>
                    <tr>
                        <td style="border-bottom: none !important;border-left: none !important;width: 60%;">&nbsp;</td>
                        <td class="text-right" style="width: 20%;border-bottom: none !important"><span
                                    class="font-weight-bold"
                                    style="border-bottom: none !important;font-size: 11.5px">Sub Total  ({{empty($request->local_currency) ? '' : $request->local_currency->CurrencyCode}}) </span>
                        </td>
                        <td class="text-right"
                            style="font-size: 11.5px;width: 20%;border-left: 1px #EBEBEB !important;border-right: 1px #EBEBEB !important;">
                        <span class="font-weight-bold">@if ($request->linePdoinvoiceDetails){{number_format(($directTraSubTotal/$request->localCurrencyER), $numberFormatting)}}@endif</span>
                        </td>
                    </tr>

                    <tr>
                        <td style="border:none !important;">
                            &nbsp;
                        </td>
                        <td class="text-right" style="border:none !important;"><span
                                    class="font-weight-bold"
                                    style="font-size: 11.5px">VAT ({{empty($request->local_currency) ? '' : $request->local_currency->CurrencyCode}}) 
                                </span></td>
                        <td class="text-right"
                            style="font-size: 11.5px;border-left: 1px #EBEBEB !important;border-right: 1px #EBEBEB !important;"><span
                                    class="font-weight-bold">{{number_format(($vatAmountSubTotal/$request->localCurrencyER), $numberFormatting)}}</span>
                        </td>
                    </tr>

                    <tr>
                        <td style="border-bottom: none !important;border-top: none !important;border-left: none !important;">
                            &nbsp;
                        </td>
                        <td class="text-right" style="border:none !important;"><span
                                    class="font-weight-bold"
                                    style="font-size: 11.5px">Total  ({{empty($request->local_currency) ? '' : $request->local_currency->CurrencyCode}})   </span>
                        </td>
                        <td class="text-right"
                            style="font-size: 11.5px;border-left: 1px #EBEBEB !important;border-right: 1px #EBEBEB !important;background-color: #EBEBEB">
                                <span class="font-weight-bold">

                                        {{number_format((($directTraSubTotal/$request->localCurrencyER) + ($vatAmountSubTotal/$request->localCurrencyER)), $numberFormatting)}}</span>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        @endif
    @endif

    @if (!$request->linePdoinvoiceDetails && $request->invoicedetails && $request->isPerforma == 1)
        <table class="table table-bordered" style="width: 100%;">
            <thead>
            <tr class="theme-tr-head">
                <th style="width:3%"></th>
                <th style="width:10%;text-align: center">GL Code</th>
                <th style="width:40%;text-align: center">GL Description</th>
                <th style="width:10%;text-align: center">QTY</th>
                <th style="width:10%;text-align: center">Unit Price</th>
                <th style="width:10%;text-align: center">Total Taxable Amount</th>
                <th style="width:10%;text-align: center">VAT %</th>
                <th style="width:10%;text-align: center">VAT Amount</th>
                <th style="width:10%;text-align: right">Total Amount Inclusive of VAT</th>
            </tr>
            </thead>
            <tbody>
            {{$decimal = 2}}
            {{$x=1}}
            {{$directTraSubTotal=0}}
            {{$vatTraSubTotal = 0}}
            {{$numberFormatting=empty($request->currency) ? 2 : $request->currency->DecimalPlaces}}
            @foreach ($request->invoicedetails as $item)
                {{$directTraSubTotal += $item->invoiceAmount}}
                {{$vatTraSubTotal += $item->VATAmount}}
                <tr style="border-top: 2px solid #333 !important;border-bottom: 2px solid #333 !important;">
                    <td>{{$x}}</td>
                    <td>{{$item->glCode}}</td>
                    <td>{{$item->glCodeDes}}</td>
                    <td class="text-center" style="text-align: center">{{number_format($item->invoiceQty,2)}}</td>
                    <td class="text-right">{{number_format(($item->invoiceAmount -$item->VATAmount),$numberFormatting)}}</td>
                    <td class="text-right">{{number_format(($item->invoiceAmount -$item->VATAmount),$numberFormatting)}}</td>
                    <td class="text-right">{{number_format($item->VATPercentage,$numberFormatting)}}</td>
                    <td class="text-right">{{number_format($item->VATAmount,$numberFormatting)}}</td>
                    <td class="text-right">{{number_format($item->invoiceAmount,$numberFormatting)}}</td>
                </tr>
                {{ $x++ }}
            @endforeach
                <tr>
                    <td colspan="5" style="text-align: right;">
                         <span class="font-weight-bold">
                            Conversion Rate
                         </span>
                    </td>
                    <td style="text-align: right;">
                        @if($request->localCurrencyER != 0)
                           {{ round(1/$request->localCurrencyER,4)}}
                        @endif
                    </td>
                    <td colspan="3">
                    </td>
                </tr>
            </tbody>

        </table>
        <div class="row">
            <table style="width:100%;" class="table table-bordered">
                <tbody>
                <tr>
                    <td style="border-bottom: none !important;border-left: none !important;width: 70%;">&nbsp;</td>
                    <td class="text-right" style="width: 15%;border-bottom: none !important"><span
                                class="font-weight-bold"
                                style="border-bottom: none !important;font-size: 11.5px">Sub Total  ({{empty($request->currency) ? '' : $request->currency->CurrencyCode}}) </span>
                    </td>
                    <td class="text-right"
                        style="font-size: 11.5px;width: 15%;border-left: 1px #EBEBEB !important;border-right: 1px #EBEBEB !important;">
                    <span class="font-weight-bold">@if ($request->invoicedetails){{number_format(($directTraSubTotal - $vatTraSubTotal), $numberFormatting)}}@endif</span>
                    </td>
                </tr>

               
                <tr>
                    <td style="border:none !important;">
                        &nbsp;
                    </td>
                    <td class="text-right" style="border:none !important;"><span
                                class="font-weight-bold"
                                style="font-size: 11.5px">VAT ({{empty($request->currency) ? '' : $request->currency->CurrencyCode}}) 
                            </span></td>
                    <td class="text-right"
                        style="font-size: 11.5px;border-left: 1px #EBEBEB !important;border-right: 1px #EBEBEB !important;"><span
                                class="font-weight-bold">{{number_format($vatTraSubTotal, $numberFormatting)}}</span>
                    </td>
                </tr>

                <tr>
                    <td style="border-bottom: none !important;border-top: none !important;border-left: none !important;">
                        &nbsp;
                    </td>
                    <td class="text-right" style="border:none !important;"><span
                                class="font-weight-bold"
                                style="font-size: 11.5px">Total  ({{empty($request->currency) ? '' : $request->currency->CurrencyCode}})   </span>
                    </td>
                    <td class="text-right"
                        style="font-size: 11.5px;border-left: 1px #EBEBEB !important;border-right: 1px #EBEBEB !important;background-color: #EBEBEB">
                            <span class="font-weight-bold">

                                    {{number_format(($directTraSubTotal), $numberFormatting)}}</span>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <br>
        @if($request->custTransactionCurrencyID != $request->localCurrencyID)
            <br>
            <div class="row">
                <table style="width:100%;" class="table table-bordered">
                    <tbody>
                    <tr>
                        <td style="border-bottom: none !important;border-left: none !important;width: 70%;">&nbsp;</td>
                        <td class="text-right" style="width: 15%;border-bottom: none !important"><span
                                    class="font-weight-bold"
                                    style="border-bottom: none !important;font-size: 11.5px">Sub Total  ({{empty($request->local_currency) ? '' : $request->local_currency->CurrencyCode}}) </span>
                        </td>
                        <td class="text-right"
                            style="font-size: 11.5px;width: 15%;border-left: 1px #EBEBEB !important;border-right: 1px #EBEBEB !important;">
                        <span class="font-weight-bold">@if ($request->invoicedetails){{number_format((($directTraSubTotal - $vatTraSubTotal)/$request->localCurrencyER), $numberFormatting)}}@endif</span>
                        </td>
                    </tr>

                    <tr>
                        <td style="border:none !important;">
                            &nbsp;
                        </td>
                        <td class="text-right" style="border:none !important;"><span
                                    class="font-weight-bold"
                                    style="font-size: 11.5px">VAT ({{empty($request->local_currency) ? '' : $request->local_currency->CurrencyCode}}) 
                                </span></td>
                        <td class="text-right"
                            style="font-size: 11.5px;border-left: 1px #EBEBEB !important;border-right: 1px #EBEBEB !important;"><span
                                    class="font-weight-bold">{{number_format(($vatTraSubTotal/$request->localCurrencyER), $numberFormatting)}}</span>
                        </td>
                    </tr>

                    <tr>
                        <td style="border-bottom: none !important;border-top: none !important;border-left: none !important;">
                            &nbsp;
                        </td>
                        <td class="text-right" style="border:none !important;"><span
                                    class="font-weight-bold"
                                    style="font-size: 11.5px">Total  ({{empty($request->local_currency) ? '' : $request->local_currency->CurrencyCode}})   </span>
                        </td>
                        <td class="text-right"
                            style="font-size: 11.5px;border-left: 1px #EBEBEB !important;border-right: 1px #EBEBEB !important;background-color: #EBEBEB">
                                <span class="font-weight-bold">

                                        {{number_format(($directTraSubTotal/$request->localCurrencyER), $numberFormatting)}}</span>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        @endif
    @endif

    @if (!$request->linePdoinvoiceDetails && $request->invoicedetails && $request->isPerforma == 0)
        <table class="table table-bordered" style="width: 100%;">
            <thead>
            <tr class="theme-tr-head">
                <th style="width:3%"></th>
                <th style="width:10%;text-align: center">GL Code</th>
                <th style="width:40%;text-align: center">GL Description</th>
                <th style="width:10%;text-align: center">QTY</th>
                <th style="width:10%;text-align: center">Unit Price</th>
                <th style="width:10%;text-align: center">Total Taxable Amount</th>
                <th style="width:10%;text-align: center">VAT %</th>
                <th style="width:10%;text-align: center">VAT Amount</th>
                <th style="width:10%;text-align: right">Total Amount Inclusive of VAT</th>
            </tr>
            </thead>
            <tbody>
            {{$decimal = 2}}
            {{$x=1}}
            {{$directTraSubTotal=0}}
            {{$vatTraSubTotal=0}}
            {{$numberFormatting=empty($request->currency) ? 2 : $request->currency->DecimalPlaces}}
            @foreach ($request->invoicedetails as $item)
                {{$directTraSubTotal +=$item->invoiceAmount}}
                {{$vatTraSubTotal +=($item->VATAmount * $item->invoiceQty)}}
                <tr style="border-top: 2px solid #333 !important;border-bottom: 2px solid #333 !important;">
                    <td>{{$x}}</td>
                    <td>{{$item->glCode}}</td>
                    <td>{{$item->glCodeDes}}</td>
                    <td class="text-center" style="text-align: center">{{number_format($item->invoiceQty,2)}}</td>
                    <td class="text-right">{{number_format(($item->unitCost),$numberFormatting)}}</td>
                    <td class="text-right">{{number_format(($item->unitCost * $item->invoiceQty),$numberFormatting)}}</td>
                    <td class="text-right">{{number_format($item->VATPercentage,$numberFormatting)}}</td>
                    <td class="text-right">{{number_format(($item->VATAmount * $item->invoiceQty),$numberFormatting)}}</td>
                    <td class="text-right">{{number_format(($item->invoiceAmount + ($item->VATAmount * $item->invoiceQty)),$numberFormatting)}}</td>
                </tr>
                {{ $x++ }}
            @endforeach
                <tr>
                    <td colspan="5" style="text-align: right;">
                         <span class="font-weight-bold">
                            Conversion Rate
                         </span>
                    </td>
                    <td style="text-align: right;">
                        @if($request->localCurrencyER != 0)
                            {{ round(1/$request->localCurrencyER,4)}}
                        @endif
                    </td>
                    <td colspan="3">
                    </td>
                </tr>
            </tbody>

        </table>
        <div class="row">
            <table style="width:100%;" class="table table-bordered">
                <tbody>
                <tr>
                    <td style="border-bottom: none !important;border-left: none !important;width: 70%;">&nbsp;</td>
                    <td class="text-right" style="width: 15%;border-bottom: none !important"><span
                                class="font-weight-bold"
                                style="border-bottom: none !important;font-size: 11.5px">Sub Total  ({{empty($request->currency) ? '' : $request->currency->CurrencyCode}}) </span>
                    </td>
                    <td class="text-right"
                        style="font-size: 11.5px;width: 15%;border-left: 1px #EBEBEB !important;border-right: 1px #EBEBEB !important;">
                    <span class="font-weight-bold">@if ($request->invoicedetails){{number_format(($directTraSubTotal), $numberFormatting)}}@endif</span>
                    </td>
                </tr>

               
                <tr>
                    <td style="border:none !important;">
                        &nbsp;
                    </td>
                    <td class="text-right" style="border:none !important;"><span
                                class="font-weight-bold"
                                style="font-size: 11.5px">VAT ({{empty($request->currency) ? '' : $request->currency->CurrencyCode}}) 
                            </span></td>
                    <td class="text-right"
                        style="font-size: 11.5px;border-left: 1px #EBEBEB !important;border-right: 1px #EBEBEB !important;"><span
                                class="font-weight-bold">{{number_format($vatTraSubTotal, $numberFormatting)}}</span>
                    </td>
                </tr>

                <tr>
                    <td style="border-bottom: none !important;border-top: none !important;border-left: none !important;">
                        &nbsp;
                    </td>
                    <td class="text-right" style="border:none !important;"><span
                                class="font-weight-bold"
                                style="font-size: 11.5px">Total  ({{empty($request->currency) ? '' : $request->currency->CurrencyCode}})   </span>
                    </td>
                    <td class="text-right"
                        style="font-size: 11.5px;border-left: 1px #EBEBEB !important;border-right: 1px #EBEBEB !important;background-color: #EBEBEB">
                            <span class="font-weight-bold">

                                    {{number_format(($directTraSubTotal + $vatTraSubTotal), $numberFormatting)}}</span>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <br>
        @if($request->custTransactionCurrencyID != $request->localCurrencyID)
            <br>
            <div class="row">
                <table style="width:100%;" class="table table-bordered">
                    <tbody>
                    <tr>
                        <td style="border-bottom: none !important;border-left: none !important;width: 70%;">&nbsp;</td>
                        <td class="text-right" style="width: 15%;border-bottom: none !important"><span
                                    class="font-weight-bold"
                                    style="border-bottom: none !important;font-size: 11.5px">Sub Total  ({{empty($request->local_currency) ? '' : $request->local_currency->CurrencyCode}}) </span>
                        </td>
                        <td class="text-right"
                            style="font-size: 11.5px;width: 15%;border-left: 1px #EBEBEB !important;border-right: 1px #EBEBEB !important;">
                        <span class="font-weight-bold">@if ($request->invoicedetails){{number_format(($directTraSubTotal/$request->localCurrencyER), $numberFormatting)}}@endif</span>
                        </td>
                    </tr>

                    <tr>
                        <td style="border:none !important;">
                            &nbsp;
                        </td>
                        <td class="text-right" style="border:none !important;"><span
                                    class="font-weight-bold"
                                    style="font-size: 11.5px">VAT ({{empty($request->local_currency) ? '' : $request->local_currency->CurrencyCode}}) 
                                </span></td>
                        <td class="text-right"
                            style="font-size: 11.5px;border-left: 1px #EBEBEB !important;border-right: 1px #EBEBEB !important;"><span
                                    class="font-weight-bold">{{number_format(($vatTraSubTotal/$request->localCurrencyER), $numberFormatting)}}</span>
                        </td>
                    </tr>

                    <tr>
                        <td style="border-bottom: none !important;border-top: none !important;border-left: none !important;">
                            &nbsp;
                        </td>
                        <td class="text-right" style="border:none !important;"><span
                                    class="font-weight-bold"
                                    style="font-size: 11.5px">Total  ({{empty($request->local_currency) ? '' : $request->local_currency->CurrencyCode}})   </span>
                        </td>
                        <td class="text-right"
                            style="font-size: 11.5px;border-left: 1px #EBEBEB !important;border-right: 1px #EBEBEB !important;background-color: #EBEBEB">
                                <span class="font-weight-bold">

                                        {{number_format((($directTraSubTotal/$request->localCurrencyER) + ($vatTraSubTotal/$request->localCurrencyER)), $numberFormatting)}}</span>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        @endif
    @endif

</div>
<div id="footer">
    @if($request->line_invoiceDetails)
        <div class="" style="">
            @else
                <div class="" style="">
                    @endif
                    <table>
                        <tr>
                            <td width="100px"><span class="font-weight-bold" style="text-decoration: underline;">For Wire Transfer Instructions </span></td>
                            <td>-</td>
                        </tr>
                        <tr>
                            <td width="100px"><span class="font-weight-bold">Bank</span></td>
                            <td> -
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
                            <td width="100px"><span class="font-weight-bold">Branch </span></td>
                            <td> -
                                @if($request->secondaryLogoCompanySystemID)
                                    @if($secondaryBankAccount->contract && $secondaryBankAccount->contract->secondary_bank_account)
                                        {{$secondaryBankAccount->contract->secondary_bank_account->bankBranch}}
                                    @endif
                                @else
                                    {{($request->bankaccount) ? $request->bankaccount->bankBranch : ''}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td width="100px"><span class="font-weight-bold">Account No </span></td>
                            <td> -
                                @if($request->secondaryLogoCompanySystemID)
                                    @if($secondaryBankAccount->contract && $secondaryBankAccount->contract->secondary_bank_account)
                                        {{$secondaryBankAccount->contract->secondary_bank_account->AccountNo}}
                                    @endif
                                @else
                                    {{($request->bankaccount) ? $request->bankaccount->AccountNo : ''}}
                                @endif

                            </td>
                        </tr>
                        <tr>
                            <td width="100px"><span class="font-weight-bold">SWIFT Code </span></td>
                            <td> -
                                @if($request->secondaryLogoCompanySystemID)
                                    @if($secondaryBankAccount->contract && $secondaryBankAccount->contract->secondary_bank_account)
                                        {{$secondaryBankAccount->contract->secondary_bank_account->accountSwiftCode}}
                                    @endif
                                @else
                                    {{($request->bankaccount) ? $request->bankaccount->accountSwiftCode : ''}}
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>

                @if(!$request->line_rentalPeriod)
                    <div class="" style="margin-top: 20px">
                        <table width="100%">

                            <tr>
                                <td width="15%">
                                    <span class="font-weight-bold">Prepared By :</span>
                                </td>
                                <td width="35%">
                                    @if($request->createduser)
                                        {{$request->createduser->empName}}
                                    @endif
                                </td>
                                @if(!$request->is_pdo_vendor)
                                    <td width="15%">
                                        <span class="font-weight-bold">Checked By :</span>
                                    </td>
                                    <td width="15%">
                                        <div style="border-bottom: 1px solid black;width: 90px;margin-top: 7px;"></div>
                                    </td>
                                @endif
                                @if($request->lineApprovedBy && !$request->is_pdo_vendor)
                                    <td width="15%">
                                        <span class="font-weight-bold">Approved By :</span>
                                    </td>
                                    <td width="15%">
                                        <div style="border-bottom: 1px solid black;width: 90px;margin-top: 7px;"></div>
                                    </td>
                                @endif
                            </tr>
                        </table>
                    </div>


                    <div class="" style="margin-top: 10px">
                        <table style="width: 100%">
                            <tr>
                                <td>
                                    <span class="font-weight-bold">Electronically Approved By :</span>
                                </td>
                            </tr>
                            <tr>

                                @foreach ($request->approved_by as $det)
                                    <td style="padding-right: 25px" class="text-center">
                                        @if($det->employee)
                                            {{$det->employee->empFullName }}
                                            <br>

                                            @if($det->employee->details)
                                                @if($det->employee->details->designation)
                                                    {{$det->employee->details->designation->designation}}
                                                @endif
                                            @endif
                                            <br><br>
                                            @if($det->employee)
                                                {{ \App\helper\Helper::convertDateWithTime($det->approvedDate)}}
                                            @endif
                                        @endif


                                    </td>
                                @endforeach
                            </tr>
                        </table>
                    </div>
                @else
                    {{--SGG PDO ONLY--}}
                    <div class="" style="">
                        <table width="100%">
                            <tr>
                                <td width="15%">
                                    <span class="font-weight-bold">Prepared By :</span>
                                </td>
                                <td width="35%">
                                    @if($request->createduser)
                                        {{$request->createduser->empName}}
                                    @endif
                                </td>
                                <td width="30%" style="">

                                </td>
                                <td width="20%" style="text-align:center; border-top: 1px solid black;margin-top: 7px;">
                                    <span class="font-weight-bold">Authorized  Signatory :</span>
                                </td>


                            </tr>

                        </table>
                    </div>
                @endif

                <table style="width:100%;">

                    <tr>
                        @if($request->footerDate)
                            <td style="width:33%;font-size: 10px;">
                                <span style="font-weight: bold; font-size: 12px ">  {{date("d/m/Y", strtotime(now()))}}</span>
                            </td>
                        @endif

                        @if($request->linePageNo)
                            <td style="width:33%; text-align: right;font-size: 12px;vertical-align: top;">
                                <span style="text-align: right;font-weight: bold;">Page <span
                                            class="pagenum"></span> <span class="pagecount"></span></span><br>

                            </td>
                        @endif
                    </tr>
                    @if($request->linefooterAddress)
                        <tr>
                            <td colspan="2"
                                style="font-size: 11px;font-style: italic">{{$request->company->CompanyAddress}} Tel
                                : {{$request->company->CompanyTelephone}} , Fax : {{$request->company->CompanyFax}} ,
                                E-mail : {{$request->company->CompanyEmail}}  </td>
                        </tr>
                    @endif
                </table>
        </div>
</div>













