<html>
<center>
    <tr>
       <td colspan="6"> Image</td>
        <td colspan="8">    
            <span style="font-size:35px;">        
                <h3 class="font-weight-bold">
                 {{$request->CompanyName}}<br>
                 <span style="font-size: 14px">
                                {{$request->CompanyAddress}}<br>
                                Telephone: {{$request->CompanyTelephone}}
                 </span>  
                </h3>
            </span>
        </td>

    <tr>

    <tr>
    <td colspan="6"> </td>
    <td colspan="1">
    <b>Tax Invoice</b>
                    </td>
    </tr>
</center>
<br>
<br>

<div class="row">










    <tr>
    <td colspan="10">Customer Details</td>
    <td colspan="8"> Invoice  Details</td>
    </tr>

    <tr>
    <td colspan="10"></td>
    <td colspan="8"> </td>
    </tr>
    <tr>
    <td colspan="10">  
       Name of Customer :  {{$request->customer->ReportTitle}}
    </td>
    <td colspan="2"> Invoice Number : </td>
    <td colspan="2">{{$request->bookingInvCode}}</td>
    </tr>


    <tr>
    <td colspan="10">   
       Address Line 1 - {{$request->customer->customerAddress1}}
    </td>
         <td colspan="2"> Invoice Date : </td>
    <td colspan="2"><span>
                                 @if(!empty($request->bookingDate))
                                        {{\App\helper\Helper::dateFormat($request->bookingDate) }}
                                    @endif
                    </span>
                        </td>
    </tr>

    <tr>
    <td colspan="10">   
    Address Line 2 - 
    @if($request->lineSecondAddress)
        {{$request->customer->customerAddress2}}
    @else
            {{$request->customer->customerCity}}
    @endif

    </td>

      <td colspan="2">
      PO Number :
    </td>
    <td colspan="2">
    @if ($request->PONumber)
                                        {{$request->PONumber}}
                                    @endif
    </td>
   </tr>



    <tr>
    <td colspan="10">   
    @if ($request->is_pdo_vendor) 
    Vendor Code :{{$request->vendorCode}}   @endif
    </td>
    
    <td colspan="2">
    Contract No :
   </td>
   <td colspan="2">
   @if (isset($request->invoicedetails[0]->clientContractID))
                                        {{$request->invoicedetails[0]->clientContractID}}
                                    @endif
        </td>

    </tr>

    <tr>
        <td colspan="10">
        Customer VATIN :    @if (isset($request->customer->vatNumber) && !is_null($request->customer->vatNumber))
                                        {{$request->customer->vatNumber}}
                                    @endif
        </td>
        <td colspan="2">
        Payment Terms :
        </td>
        <td colspan="2">
        @if($request->paymentInDaysForJob)
                                        {{$request->paymentInDaysForJob}} Days

                                    @endif
        </td>
   </tr>



   <tr>
    <td colspan="10"> </td>
    <td colspan="2">
    Invoice Due Date : 
        </td>
    <td colspan="2">    @if(!empty($request->invoiceDueDate))
                                        {{\App\helper\Helper::dateFormat($request->invoiceDueDate)}}
                                    @endif </td>
   </tr>


   <tr>
    <td colspan="10"> </td>
    <td colspan="2">SE No : </td>

    <td colspan="2">
    @if($request->wanNO)
    {{$request->wanNO}}
    @endif
    </td>

   </tr>


   <tr>
    <td colspan="10"> </td>
    <td colspan="2">
    Date of Supply/Service :
        </td>

    <td colspan="2">  @if($request->serviceStartDate)
                            {{\App\helper\Helper::dateFormat($request->serviceStartDate) }}
                        @endif
                        -
                            @if($request->serviceEndDate)
                            {{\App\helper\Helper::dateFormat($request->serviceEndDate) }}
                        @endif
                                </td>
   </tr>


   <tr>
    <td colspan="10"> </td>
    <td colspan="2">Invoice Currency :</td>
    <td colspan="2">{{empty($request->currency) ? '' : $request->currency->CurrencyCode}}</td>
   </tr>


   <tr>
    <td colspan="10"> </td>
    <td colspan="2">VATIN : </td>
    <td colspan="2">   @if(isset($request->company->vatRegistratonNumber))
        {{$request->company->vatRegistratonNumber}}
    @endif()</td>

   </tr>

   <tr>
    <td colspan="10"> </td>
    <td colspan="2">JSRS No :</td>

     <td colspan="2">@if(isset($request->company->jsrsNumber))
                                        {{$request->company->jsrsNumber}}
                                    @endif()</td>

   </tr>



   <tr>
    <td colspan="10"> </td>
    <td colspan="2">Tax Card No :</td>
    <td colspan="2"> @if(isset($request->company->taxCardNo))
                                        {{$request->company->taxCardNo}}
                                    @endif()</td>

   </tr>


     
</div>
<br>
<br>
<br>






            <br>
    @if($request->line_rentalPeriod)
    <tr> 
    <td colspan="6">
        <div class="row" style="text-align: center">
            <b>Rental Period From
                {{\App\helper\Helper::dateFormat($request->invoicedetail->billmaster->rentalStartDate)}} -
                {{\App\helper\Helper::dateFormat($request->invoicedetail->billmaster->rentalEndDate)}}</b>
        </div>
    </td>
    </tr>

    <tr> 
    <td colspan="6">
        <div class="row" style="">
            <b><span>{{$request->invoicedetail->billmaster->ticketmaster->rig->RigDescription}}</span> |
                <span> {{$request->invoicedetail->billmaster->ticketmaster->regNo}}</span></b>
        </div>
        </td>
    </tr>
    @else
    <tr> 
    <td colspan="6">
        <div class="row" style="">
            <b>Comments : </b> {!! nl2br($request->comments) !!}
        </div>
        </td>
    </tr>
    @endif
    @if($request->linePdoinvoiceDetails)
        <div class="row">
            <table class="table table-bordered table-striped table-sm" style="width: 100%;">
                <thead>
                <tr class="">
                    <th colspan="1" style="width:1%"></th>
                    <th colspan="3" style="text-align: center">Client Ref No</th>
                    <th colspan="3" style="text-align: center">PO Line Item No</th>
                    <th colspan="3" style="text-align: center">Description of Goods/ Services</th>
                    <th colspan="2" style="text-align: right">Quantity</th>
                    <th colspan="2" style="text-align: right">Unit Price (Excluding Tax)</th>
                    <th colspan="2"  style="text-align: right">Taxable Amount after excluding Tax</th>
                    <th colspan="2"  style="text-align: right">VAT Rate %</th>
                    <th colspan="2"  style="text-align: right">VAT Amount</th>
                    <th colspan="2"  style="text-align: right">Total Amount Inclusive of VAT</th>
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
                        <td colspan="1" >{{$x}}</td>
                        <td colspan="3"  >{{$item->client_referance}}</td>
                        <td colspan="3" >{{$item->po_detail_id}}</td>
                        <td colspan="3" >{{$item->item_description}}</td>
                        <td colspan="2"  style="text-align: right">{{number_format($item->qty,2)}}</td>
                        <td colspan="2" style="text-align: right">{{number_format(($item->unit_price - $item->vatAmount),$numberFormatting)}}</td>
                        <td colspan="2" style="text-align: right">{{number_format(($item->amount - $vatAmount),$numberFormatting)}}</td>
                        <td colspan="2" style="text-align: right">{{number_format($vatPecentage,2)}}</td>
                        <td colspan="2" style="text-align: right">{{number_format($vatAmount,$numberFormatting)}}</td>
                        <td colspan="2" style="text-align: right" class="text-right">{{number_format(($item->amount),$numberFormatting)}}</td>
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
                <td colspan="8">
                </td>
                    <td colspan="2" style="text-align: right;">
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
                    <td colspan="18" style="border-bottom: none !important;border-left: none !important;width: 60%;">&nbsp;</td>
                    <td colspan="2" class="text-right" style="width: 20%;border-bottom: none !important"><span
                                class="font-weight-bold"
                                style="border-bottom: none !important;font-size: 11.5px">Sub Total  ({{empty($request->currency) ? '' : $request->currency->CurrencyCode}}) </span>
                    </td>
                    <td class="text-right"
                        style="font-size: 11.5px;width: 20%;border-left: 1px #EBEBEB !important;border-right: 1px #EBEBEB !important;">
                        <span class="font-weight-bold">@if ($request->linePdoinvoiceDetails){{number_format($directTraSubTotal, $numberFormatting)}}@endif</span>
                    </td>
                </tr>


                <tr>
                    <td colspan="18" style="border:none !important;">
                        &nbsp;
                    </td>
                    <td colspan="2" class="text-right" style="border:none !important;"><span
                                class="font-weight-bold"
                                style="font-size: 11.5px">VAT ({{empty($request->currency) ? '' : $request->currency->CurrencyCode}})
                            </span></td>
                    <td class="text-right"
                        style="font-size: 11.5px;border-left: 1px #EBEBEB !important;border-right: 1px #EBEBEB !important;"><span
                                class="font-weight-bold">{{number_format($vatAmountSubTotal, $numberFormatting)}}</span>
                    </td>
                </tr>

                <tr>
                    <td colspan="18" style="border-bottom: none !important;border-top: none !important;border-left: none !important;">
                        &nbsp;
                    </td>
                    <td colspan="2" class="text-right" style="border:none !important;"><span
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
                        <td colspan="18" style="border-bottom: none !important;border-left: none !important;width: 60%;">&nbsp;</td>
                        <td colspan="2" class="text-right" style="width: 20%;border-bottom: none !important"><span
                                    class="font-weight-bold"
                                    style="border-bottom: none !important;font-size: 11.5px">Sub Total  ({{empty($request->local_currency) ? '' : $request->local_currency->CurrencyCode}}) </span>
                        </td>
                        <td class="text-right"
                            style="font-size: 11.5px;width: 20%;border-left: 1px #EBEBEB !important;border-right: 1px #EBEBEB !important;">
                            <span class="font-weight-bold">@if ($request->linePdoinvoiceDetails){{number_format(($directTraSubTotal/$request->localCurrencyER), $numberFormatting)}}@endif</span>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="18" style="border:none !important;">
                            &nbsp;
                        </td>
                        <td colspan="2" class="text-right" style="border:none !important;"><span
                                    class="font-weight-bold"
                                    style="font-size: 11.5px">VAT ({{empty($request->local_currency) ? '' : $request->local_currency->CurrencyCode}})
                                </span></td>
                        <td class="text-right"
                            style="font-size: 11.5px;border-left: 1px #EBEBEB !important;border-right: 1px #EBEBEB !important;"><span
                                    class="font-weight-bold">{{number_format(($vatAmountSubTotal/$request->localCurrencyER), $numberFormatting)}}</span>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="18" style="border-bottom: none !important;border-top: none !important;border-left: none !important;">
                            &nbsp;
                        </td>
                        <td colspan="2" class="text-right" style="border:none !important;"><span
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
                <th colspan="1" style="width:3%"></th>
                <th colspan="2" style="width:10%;text-align: center">GL Code</th>
                <th colspan="4" style="width:60%;text-align: center">GL Description</th>
                <th colspan="2" style="width:10%;text-align: center">QTY</th>
                <th colspan="2"  style="width:10%;text-align: center">Unit Price</th>
                <th colspan="2"  style="width:10%;text-align: center">Total Taxable Amount</th>
                <th colspan="2" style="width:10%;text-align: center">VAT %</th>
                <th colspan="2" style="width:10%;text-align: center">VAT Amount</th>
                <th colspan="2" style="width:10%;text-align: right">Total Amount Inclusive of VAT</th>
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
                    <td colspan="1" >{{$x}}</td>
                    <td colspan="2" >{{$item->glCode}}</td>
                    <td colspan="4" >{{$item->glCodeDes}}</td>
                    <td colspan="2" class="text-center" style="text-align: center">{{number_format($item->invoiceQty,2)}}</td>
                    <td colspan="2" class="text-right">{{number_format(($item->invoiceAmount -$item->VATAmount),$numberFormatting)}}</td>
                    <td colspan="2" class="text-right">{{number_format(($item->invoiceAmount -$item->VATAmount),$numberFormatting)}}</td>
                    <td colspan="2" class="text-right">{{number_format($item->VATPercentage,$numberFormatting)}}</td>
                    <td colspan="2" class="text-right">{{number_format($item->VATAmount,$numberFormatting)}}</td>
                    <td colspan="2" class="text-right">{{number_format($item->invoiceAmount,$numberFormatting)}}</td>
                </tr>
                {{ $x++ }}
            @endforeach
            <tr>
                <td colspan="8">
                </td>
                <td colspan="2" style="text-align: right;">
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
                    <td colspan="15" style="border-bottom: none !important;border-left: none !important;width: 70%;">.</td>
                    <td colspan="2" class="text-right" style="width: 15%;border-bottom: none !important"><span
                                class="font-weight-bold"
                                style="border-bottom: none !important;font-size: 11.5px">Sub Total  ({{empty($request->currency) ? '' : $request->currency->CurrencyCode}}) </span>
                    </td>
                    <td class="text-right"
                        style="font-size: 11.5px;width: 15%;border-left: 1px #EBEBEB !important;border-right: 1px #EBEBEB !important;">
                        <span class="font-weight-bold">@if ($request->invoicedetails){{number_format(($directTraSubTotal - $vatTraSubTotal), $numberFormatting)}}@endif</span>
                    </td>
                </tr>


                <tr>
                    <td colspan="15" style="border:none !important;">
                        &nbsp;
                    </td>
                    <td colspan="2" class="text-right" style="border:none !important;"><span
                                class="font-weight-bold"
                                style="font-size: 11.5px">VAT ({{empty($request->currency) ? '' : $request->currency->CurrencyCode}})
                            </span></td>
                    <td class="text-right"
                        style="font-size: 11.5px;border-left: 1px #EBEBEB !important;border-right: 1px #EBEBEB !important;"><span
                                class="font-weight-bold">{{number_format($vatTraSubTotal, $numberFormatting)}}</span>
                    </td>
                </tr>

                <tr>
                    <td colspan="15" style="border-bottom: none !important;border-top: none !important;border-left: none !important;">
                        &nbsp;
                    </td>
                    <td colspan="2" class="text-right" style="border:none !important;"><span
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
                        <td colspan="15" style="border-bottom: none !important;border-left: none !important;width: 70%;">&nbsp;</td>
                        <td colspan="2" class="text-right" style="width: 15%;border-bottom: none !important"><span
                                    class="font-weight-bold"
                                    style="border-bottom: none !important;font-size: 11.5px">Sub Total  ({{empty($request->local_currency) ? '' : $request->local_currency->CurrencyCode}}) </span>
                        </td>
                        <td class="text-right"
                            style="font-size: 11.5px;width: 15%;border-left: 1px #EBEBEB !important;border-right: 1px #EBEBEB !important;">
                            <span class="font-weight-bold">@if ($request->invoicedetails){{number_format((($directTraSubTotal - $vatTraSubTotal)/$request->localCurrencyER), $numberFormatting)}}@endif</span>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="15" style="border:none !important;">
                            &nbsp;
                        </td>
                        <td colspan="2" class="text-right" style="border:none !important;"><span
                                    class="font-weight-bold"
                                    style="font-size: 11.5px">VAT ({{empty($request->local_currency) ? '' : $request->local_currency->CurrencyCode}})
                                </span></td>
                        <td class="text-right"
                            style="font-size: 11.5px;border-left: 1px #EBEBEB !important;border-right: 1px #EBEBEB !important;"><span
                                    class="font-weight-bold">{{number_format(($vatTraSubTotal/$request->localCurrencyER), $numberFormatting)}}</span>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="15" style="border-bottom: none !important;border-top: none !important;border-left: none !important;">
                            &nbsp;
                        </td>
                        <td colspan="2" class="text-right" style="border:none !important;"><span
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
                <th colspan="1" style="width:3%"></th>
                <th colspan="2" style="width:10%;text-align: center">GL Code</th>
                <th colspan="3" style="width:60%;text-align: center">GL Description</th>
                <th colspan="2" style="width:10%;text-align: center">QTY</th>
                <th colspan="2" style="width:10%;text-align: center">Unit Price</th>
                <th colspan="2" style="width:10%;text-align: center">Total Taxable Amount</th>
                <th colspan="2" style="width:10%;text-align: center">VAT %</th>
                <th colspan="2" style="width:10%;text-align: center">VAT Amount</th>
                <th colspan="2" style="width:10%;text-align: right">Total Amount Inclusive of VAT</th>
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
                    <td colspan="1" >{{$x}}</td>
                    <td colspan="2" >{{$item->glCode}}</td>
                    <td colspan="3" >{{$item->glCodeDes}}</td>
                    <td colspan="2" class="text-center" style="text-align: center">{{number_format($item->invoiceQty,2)}}</td>
                    <td colspan="2" class="text-right">{{number_format(($item->unitCost),$numberFormatting)}}</td>
                    <td colspan="2" class="text-right">{{number_format(($item->unitCost * $item->invoiceQty),$numberFormatting)}}</td>
                    <td colspan="2" class="text-right">{{number_format($item->VATPercentage,$numberFormatting)}}</td>
                    <td colspan="2" class="text-right">{{number_format(($item->VATAmount * $item->invoiceQty),$numberFormatting)}}</td>
                    <td colspan="2" class="text-right">{{number_format(($item->invoiceAmount + ($item->VATAmount * $item->invoiceQty)),$numberFormatting)}}</td>
                </tr>
                {{ $x++ }}
            @endforeach
            <tr>
                <td colspan="8">
                </td>
                <td colspan="2" style="text-align: right;">
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
                    <td colspan="14" style="border-bottom: none !important;border-left: none !important;width: 70%;">.</td>
                    <td colspan="2" class="text-right" style="width: 15%;border-bottom: none !important"><span
                                class="font-weight-bold"
                                style="border-bottom: none !important;font-size: 11.5px">Sub Total  ({{empty($request->currency) ? '' : $request->currency->CurrencyCode}}) </span>
                    </td>
                    <td class="text-right"
                        style="font-size: 11.5px;width: 15%;border-left: 1px #EBEBEB !important;border-right: 1px #EBEBEB !important;">
                        <span class="font-weight-bold">@if ($request->invoicedetails){{number_format(($directTraSubTotal), $numberFormatting)}}@endif</span>
                    </td>
                </tr>


                <tr>
                    <td colspan="14" style="border:none !important;">
                        &nbsp;
                    </td>
                    <td colspan="2" class="text-right" style="border:none !important;"><span
                                class="font-weight-bold"
                                style="font-size: 11.5px">VAT ({{empty($request->currency) ? '' : $request->currency->CurrencyCode}}) 
                            </span></td>
                    <td class="text-right"
                        style="font-size: 11.5px;border-left: 1px #EBEBEB !important;border-right: 1px #EBEBEB !important;"><span
                                class="font-weight-bold">{{number_format($vatTraSubTotal, $numberFormatting)}}</span>
                    </td>
                </tr>

                <tr>
                    <td colspan="14" style="border-bottom: none !important;border-top: none !important;border-left: none !important;">
                        &nbsp;
                    </td>
                    <td colspan="2" class="text-right" style="border:none !important;"><span
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
                        <td colspan="14" style="border-bottom: none !important;border-left: none !important;width: 70%;">&nbsp;</td>
                        <td colspan="2" class="text-right" style="width: 15%;border-bottom: none !important"><span
                                    class="font-weight-bold"
                                    style="border-bottom: none !important;font-size: 11.5px">Sub Total  ({{empty($request->local_currency) ? '' : $request->local_currency->CurrencyCode}}) </span>
                        </td>
                        <td class="text-right"
                            style="font-size: 11.5px;width: 15%;border-left: 1px #EBEBEB !important;border-right: 1px #EBEBEB !important;">
                            <span class="font-weight-bold">@if ($request->invoicedetails){{number_format(($directTraSubTotal/$request->localCurrencyER), $numberFormatting)}}@endif</span>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="14" style="border:none !important;">
                            &nbsp;
                        </td>
                        <td colspan="2" class="text-right" style="border:none !important;"><span
                                    class="font-weight-bold"
                                    style="font-size: 11.5px">VAT ({{empty($request->local_currency) ? '' : $request->local_currency->CurrencyCode}}) 
                                </span></td>
                        <td class="text-right"
                            style="font-size: 11.5px;border-left: 1px #EBEBEB !important;border-right: 1px #EBEBEB !important;"><span
                                    class="font-weight-bold">{{number_format(($vatTraSubTotal/$request->localCurrencyER), $numberFormatting)}}</span>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="14" style="border-bottom: none !important;border-top: none !important;border-left: none !important;">
                            &nbsp;
                        </td>
                        <td colspan="2" class="text-right" style="border:none !important;"><span
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
    <br>
    <br>
    <br>

    <div id="footer">
    @if($request->line_invoiceDetails)
        <div class="" style="">
            @else
                <div class="" style="">
                    @endif
                    <table>
                        <tr>
                            <td colspan="4" width="100px"><span class="font-weight-bold" style="text-decoration: underline;">For Wire Transfer Instructions </span></td>
                            <td colspan="4">-</td>
                        </tr>
                        <tr>
                            <td colspan="4" width="100px"><span class="font-weight-bold">Bank</span></td>
                            <td colspan="4"> -
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
                            <td colspan="4" width="100px"><span class="font-weight-bold">Branch </span></td>
                            <td colspan="4"> -
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
                            <td colspan="4" width="100px"><span class="font-weight-bold">Account No </span></td>
                            <td colspan="4"> -
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
                            <td colspan="4" width="100px"><span class="font-weight-bold">SWIFT Code </span></td>
                            <td colspan="4">  -
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
                                <td colspan="4" width="15%">
                                    <span class="font-weight-bold">Prepared By :</span>
                                </td>
                                <td colspan="4" width="35%">
                                    @if($request->createduser)
                                        {{$request->createduser->empName}}
                                    @endif
                                </td>
                                @if(!$request->is_pdo_vendor)
                                    <td colspan="4" width="15%">
                                        <span class="font-weight-bold">Checked By :</span>
                                    </td>
                                    <td colspan="4" width="15%">
                                        <div style="border-bottom: 1px solid black;width: 90px;margin-top: 7px;"></div>
                                    </td>
                                @endif
                                @if($request->lineApprovedBy && !$request->is_pdo_vendor)
                                    <td colspan="4" width="15%">
                                        <span class="font-weight-bold">Approved By :</span>
                                    </td>
                                    <td colspan="4" width="15%">
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
                                                {{ \App\helper\Helper::dateFormat($det->approvedDate)}}
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
                                <td colspan="4" width="15%">
                                    <span class="font-weight-bold">Prepared By :</span>
                                </td>
                                <td colspan="4" width="35%">
                                    @if($request->createduser)
                                        {{$request->createduser->empName}}
                                    @endif
                                </td>
                                <td colspan="4" width="30%" style="">

                                </td>
                                <td colspan="4" width="20%" style="text-align:center; border-top: 1px solid black;margin-top: 7px;">
                                    <span class="font-weight-bold">Authorized  Signatory :</span>
                                </td>


                            </tr>

                        </table>
                    </div>
                @endif

                <table style="width:100%;">

                    <tr>
                        @if($request->footerDate)
                            <td colspan="4" style="width:33%;font-size: 10px;">
                                <span style="font-weight: bold; font-size: 12px ">  {{date("d/m/Y", strtotime(now()))}}</span>
                            </td>
                        @endif

                        @if($request->linePageNo)
                            <td colspan="4" style="width:33%; text-align: right;font-size: 12px;vertical-align: top;">
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

    </html>