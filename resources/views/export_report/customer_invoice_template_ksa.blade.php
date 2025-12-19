<html>
<center>
    <tr>
       <td colspan="8"></td>
        <td colspan="8">    
            <b>TAX INVOICE</b>
        </td>

    <tr>

    <tr>
    <td colspan="8"> </td>
    <td colspan="1">
    <b> فاتورة ضريبية</b>
                    </td>
    </tr>
</center>
<br>
<br>
    <tr>
        <td colspan="1"></td>
        <td colspan="16"> {{$request->CompanyAddress}}</td>
        <td colspan="8"> {{$request->CompanyAddressSecondaryLanguage}}</td>
    </tr>
    <tr>
        <td colspan="1"></td>
        <td colspan="16"> Tel:  {{$request->CompanyTelephone}}</td>
        <td colspan="8"> هاتف : {{$request->CompanyTelephone}}</td>
    </tr>
    <tr>
        <td colspan="1"></td>
        <td colspan="16"> Fax: {{$request->CompanyFax}}</td>
        <td colspan="8"> فاكس : {{$request->CompanyFax}}</td>
    </tr>
    <tr>
        <td colspan="1"></td>
        <td colspan="16"> <b>VAT NO: {{$request->vatRegistratonNumber}}</b></td>
        <td colspan="8"> الضريبي : {{$request->vatRegistratonNumber}}</td>
    </tr>
    <br>
    <br>


    <tr>
        <td colspan="1"></td>
        <td colspan="16"><b>INVOICE NO : {{$request->bookingInvCode}}</b></td>
        <td colspan="8"> <b>رقم الفاتورة : {{$request->bookingInvCode}}</b></td>
    </tr>
    <tr>
        <td colspan="1"></td>
        <td colspan="16"> <b>INVOICE DATE : @if(!empty($request->bookingDate))
                                        {{\App\helper\Helper::dateFormat($request->bookingDate) }}
                                    @endif</td>
        <td colspan="8"> <b>تاريخ الفاتورة : @if(!empty($request->bookingDate))
                                        {{\App\helper\Helper::dateFormat($request->bookingDate) }}
                                    @endif</b></td>
    </tr>
    <tr>
        <td colspan="1"></td>
        <td colspan="16"> <b>Date Of Supply  : @if(!empty($request->date_of_supply))
                                        {{\App\helper\Helper::dateFormat($request->date_of_supply) }}
                                    @endif</td>
        <td colspan="8"> <b>تاريخ التوريد : @if(!empty($request->date_of_supply))
                                        {{\App\helper\Helper::dateFormat($request->date_of_supply) }}
                                    @endif</b></td>
    </tr>
    @if(preg_match('/[^\p{L}\p{N}]/u', $request->PONumber))
        <tr>
            <td colspan="1"></td>
            <td colspan="14"> <b>Contract / PO No : 
                                    @if($request->line_poNumber)
                                        {{$request->PONumber}}
                                    @endif
                                </b></td>
            <td colspan="8"> <b>
                    @if($request->line_poNumber)
                        {{$request->PONumber}}
                    @endif
                </b> : <b>رقم العقد/أمر الشراء 
                                </b></td>
        </tr>
    @else
        <tr>
            <td colspan="1"></td>
            <td colspan="16"> <b>Contract / PO No : 
                                    @if($request->line_poNumber)
                                        {{$request->PONumber}}
                                    @endif
                                </b></td>
            <td colspan="8"> <b>رقم العقد/أمر الشراء : 
                                    @if($request->line_poNumber)
                                        {{$request->PONumber}}
                                    @endif
                                </b></td>
        </tr>
    @endif
    <br>
    <br>

    <tr>
        <td colspan="1"></td>
        <td colspan="16"> <b>CUSTOMER NAME : {{$request->customer->ReportTitle}}</b></td>
        <td colspan="8"><b>أسم العميل : {{$request->customer->customerSecondLanguage}}</b></td>
    </tr>
    <tr>
        <td colspan="1"></td>
        <td colspan="16"> <b>ADDRESS : {{$request->customer->customerAddress1}}</b></td>
        <td colspan="8"> <b>عنوان العميل : {{$request->customer->addressOneSecondLanguage}}</b></td>
    </tr>
   <tr>
        <td colspan="1"></td>
        <td colspan="16"><b>CUSTOMER TELEPHONE : {{isset($request->CustomerContactDetails->contactPersonTelephone)?$request->CustomerContactDetails->contactPersonTelephone:' '}}</b></td>
        <td colspan="8"> <b>رقم العميل : {{isset($request->CustomerContactDetails->contactPersonTelephone)?$request->CustomerContactDetails->contactPersonTelephone:' '}}</b></td>
    </tr>

    <tr>
        <td colspan="1"></td>
        <td colspan="16"><b>CUSTOMER FAX : {{isset($request->CustomerContactDetails->contactPersonFax)?$request->CustomerContactDetails->contactPersonFax:' '}}</b></td>
        <td colspan="8"> <b>فاكس العميل : {{isset($request->CustomerContactDetails->contactPersonFax)?$request->CustomerContactDetails->contactPersonFax:' '}}</b></td>
    </tr>

    <tr>
        <td colspan="1"></td>
        <td colspan="16"><b>VAT NO : {{$request->vatNumber}}</b></td>
        <td colspan="8"> <b>الرقم الضريبي : {{$request->vatNumber}}</b></td>
    </tr>

    <br>
    <br>

    @if ($request->isPerforma == 0)
        <div class="row">
            <table style="width:100%">
                <tr>
                    <td colspan="1"></td>
                    <td colspan="2"><b>Comments :</td>
                    <td colspan="3"> <b>{{$request->comments}}</b></td>
                </tr>
            </table>
        </div>
    @endif
    @if ($request->isPerforma == 0)
        @if ($request->template <> 1 && !$request->line_invoiceDetails && !$request->item_invoice)
            <table class="table table-sm table-striped hover table-bordered" style="width: 100%;">
                <thead>
                    <tr style="background-color: #6798da">
                        <th colspan="1"></th>
                        <th colspan="4">Line item <br>رقم المنتج</th>
                        <th colspan="2">UOM <br>وحدة القياس</th>
                        <th colspan="2">Qty <br>الكمية</th>
                        <th colspan="2">Unit Price <br>سعر الوحده</th>
                        <th colspan="3">Total Taxable Amount <br>إجمالي المبلغ الخاضع للضريبة </th>
                        <th colspan="3">VAT % <br>ضريبة القيمة المضافة %</th>
                        <th colspan="3">VAT Amount <br>مبلغ ضريبة القيمة المضافة</th>
                        <th colspan="3">Total Amount inclusive of VAT <br>القيمة الكلية متضمنة ضريبة القيمة المضافة</th>
                    </tr>
                </thead>

                <tbody>
                    {{$decimal = 2}}
                    {{$x=1}}
                    {{$directTraSubTotal=0}}
                    {{$localDirectTraSubTotal=0}}
                    {{$numberFormatting=empty($request->currency) ? 2 : $request->currency->DecimalPlaces}}
                    @foreach ($request->invoicedetails as $item)
                        {{$directTraSubTotal +=$item->invoiceAmount}}
                        {{$localDirectTraSubTotal +=$item->localAmount}}
                        {{$totalVatamount = ($item->VATPercentage/100)*$item->invoiceAmount}}

                        <tr style="border: 1px solid !important;">
                            <td colspan="1">{{$x}}</td>
                            <td colspan="4">{{isset($item->comments)?$item->comments:''}}</td>
                            <td colspan="2">{{isset($item->unit)? $item->unit->UnitShortCode:''}}</td>
                            <td colspan="2">{{isset($item->invoiceQty)? $item->invoiceQty:0}}</td>
                            <td colspan="2">{{number_format($item->salesPrice,$numberFormatting)}}</td>
                            <td colspan="3">{{number_format($item->invoiceAmount,$numberFormatting)}}</td>
                            <td colspan="3">{{number_format($item->VATPercentage, 0)}}</td>
                            <td colspan="3">{{number_format(($totalVatamount),$numberFormatting)}}</td>
                            <td colspan="3">{{number_format($item->invoiceAmount+$totalVatamount,$numberFormatting)}}</td>
                        </tr>
                        {{ $x++ }}
                    @endforeach

                </tbody>

                <tbody>
                    <tr>
                        <td colspan="1"></td>
                        <td colspan="16" style="text-align: left; border-right: none !important;"><b>Total Before VAT ( الاجمالي قبل الضريبة )</b></td>
                        <td colspan="3" style="text-align: center; border-left: none !important"><b>{{empty($request->currency) ? '' : $request->currency->CurrencyCode}}</b></td>
                        <td colspan="3" class="text-right">@if ($request->invoicedetails)
                        {{number_format($directTraSubTotal, $numberFormatting)}}
                    @endif</td>
                    </tr>
                    {{$directTraSubTotal+= ($request->tax) ? $request->tax->amount : 0}}
                    {{$taxAmount = ($request->tax) ? $request->tax->amount : 0}}
                    {{$taxPercent = ($request->tax) ? $request->tax->taxPercent : 0}}
                    <tr>
                        <td colspan="1"></td>
                        <td colspan="16" style="text-align: left; border-right: none !important;"><b>Value Added Tax {{number_format($taxPercent, 0)}}% (ضريبة القيمة المضافة )</b></td>
                        <td colspan="3" style="text-align: center; border-left: none !important"><b>{{empty($request->currency) ? '' : $request->currency->CurrencyCode}}</b></td>
                        <td colspan="3" class="text-right">{{number_format($taxAmount, $numberFormatting)}}</td>
                    </tr>
                    <tr>
                        <td colspan="1"></td>
                        <td colspan="16" style="text-align: left; border-right: none !important;"><b>Total Amount Including VAT(القيمة الكلية متضمنة ضريبة القيمة المضافة)</b></td>
                        <td colspan="3" style="text-align: center; border-left: none !important"><b>{{empty($request->currency) ? '' : $request->currency->CurrencyCode}}</b></td>
                        <td colspan="3" class="text-right">{{number_format($directTraSubTotal, $numberFormatting)}}</td>
                    </tr>
                </tbody>

                @if ($request->currency && $request->local_currency && $request->currency->CurrencyCode != $request->local_currency->CurrencyCode)
                    <tbody>
                        <tr>
                            <td colspan="23"><br> </td>
                        </tr>

                        <tr>
                            <td colspan="1"></td>
                            <td colspan="16" style="text-align: left; border-right: none !important;"><b>Total Before VAT ( الاجمالي قبل الضريبة )</b></td>
                            <td colspan="3" style="text-align: center; border-left: none !important"><b>{{empty($request->local_currency) ? '' : $request->local_currency->CurrencyCode}}</b></td>
                            <td colspan="3" class="text-right">@if ($request->invoicedetails)
                            {{number_format($localDirectTraSubTotal, $numberFormatting)}}
                        @endif</td>
                        </tr>
                        {{$localDirectTraSubTotal+= ($request->tax) ? $request->tax->localAmount : 0}}
                        {{$taxAmount = ($request->tax) ? $request->tax->localAmount : 0}}
                        {{$taxPercent = ($request->tax) ? $request->tax->taxPercent : 0}}
                        <tr>
                            <td colspan="1"></td>
                            <td colspan="16" style="text-align: left; border-right: none !important;"><b>Value Added Tax {{number_format($taxPercent, 0)}}% (ضريبة القيمة المضافة )</b></td>
                            <td colspan="3" style="text-align: center; border-left: none !important"><b>{{empty($request->local_currency) ? '' : $request->local_currency->CurrencyCode}}</b></td>
                            <td colspan="3" class="text-right">{{number_format($taxAmount, $numberFormatting)}}</td>
                        </tr>
                        <tr>
                            <td colspan="1"></td>
                            <td colspan="16" style="text-align: left; border-right: none !important;"><b>Total Amount Including VAT(القيمة الكلية متضمنة ضريبة القيمة المضافة)</b></td>
                            <td colspan="3" style="text-align: center; border-left: none !important"><b>{{empty($request->local_currency) ? '' : $request->local_currency->CurrencyCode}}</b></td>
                            <td colspan="3" class="text-right">{{number_format($localDirectTraSubTotal, $numberFormatting)}}</td>
                        </tr>
                    </tbody>
                @endif

                <tbody>
                    <tr>
                        <td colspan="1"></td>
                        <td colspan="23">PLEASE ISSUE ALL PAYMENT ON BELOW BANK ACCOUNT DETAILS : </td>
                    </tr>
                </tbody>
                
            </table>
        @endif
    @endif

    @if ($request->isPerforma != 0)
        <div class="row">
            @if ($request->template==1 && !$request->line_invoiceDetails && !$request->linePdoinvoiceDetails)
                <table class="table">
                    <thead>
                    <tr style="background-color: #6798da;">
                        <th colspan="1" style="width:6%;">Item<br>رقم المنتج</th>
                        <th colspan="4" style="width:25%; text-align: center">Description<br>الوصف</th>
                        <th colspan="2" style="width:6%;text-align: center">QTY<br>الكمية</th>
                        <th colspan="2" style="width:10%;text-align: center">Days(OP)<br>الايام عمل</th>
                        <th colspan="2" style="width:10%;text-align: center">Price(OP)<br>سعر العمل</th>
                        <th colspan="2" style="width:10%;text-align: center">Days(STB)<br>الايام الانتظار</th>
                        <th colspan="2" style="width:10%;text-align: center">Price(STB)<br>سعر الانتظار</th>
                        <th colspan="2" style="width:13%;text-align: center">Total Amount<br>القيمة الكلية</th>
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
                                <td colspan="1">{{$x}}</td>
                                <td colspan="4" style="word-wrap:break-word;">{{$item->description}}</td>
                                <td colspan="2"  style="text-align: right;">{{$item->Qty}}</td>
                                <td colspan="2" style="text-align: right;">{{$item->Days_OP}}</td>
                                <td colspan="2" style="text-align: right;">{{number_format($item->Price_OP,$numberFormatting)}}</td>
                                <td colspan="2" style="text-align: right;">{{$item->Days_STB}}</td>
                                <td colspan="2" style="text-align: right;">{{number_format($item->Price_STB,$numberFormatting)}}</td>
                                <td colspan="2" class="text-right">{{number_format($item->total,$numberFormatting)}}</td>
                            </tr>
                            {{ $x++ }}
                        @endif
                    @endforeach
                    </tbody>

                    <tbody>
                        <tr>
                            <td></td>
                            <td colspan="12" style="text-align: left; border-right: none !important;"><b>Total Before VAT ( الاجمالي قبل الضريبة )</b></td>
                            <td colspan="2" style="text-align: center; border-left: none !important"><b>{{empty($request->currency) ? '' : $request->currency->CurrencyCode}}</b></td>
                            <td class="text-right">@if ($request->invoicedetails)
                            {{number_format($directTraSubTotal, $numberFormatting)}}
                        @endif</td>
                        </tr>
                        {{$directTraSubTotal+= ($request->tax) ? $request->tax->amount : 0}}
                        {{$taxAmount = ($request->tax) ? $request->tax->amount : 0}}
                        {{$taxPercent = ($request->tax) ? $request->tax->taxPercent : 0}}
                        <tr>
                            <td></td>
                            <td colspan="12" style="text-align: left; border-right: none !important;"><b>Value Added Tax {{$taxPercent}}% (ضريبة القيمة المضافة )</b></td>
                            <td colspan="2" style="text-align: center; border-left: none !important"><b>{{empty($request->currency) ? '' : $request->currency->CurrencyCode}}</b></td>
                            <td class="text-right">{{number_format($taxAmount, $numberFormatting)}}</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td colspan="12" style="text-align: left; border-right: none !important;"><b>Total Amount Including VAT(القيمة الكلية متضمنة ضريبة القيمة المضافة)</b></td>
                            <td colspan="2" style="text-align: center; border-left: none !important"><b>{{empty($request->currency) ? '' : $request->currency->CurrencyCode}}</b></td>
                            <td class="text-right">{{number_format($directTraSubTotal, $numberFormatting)}}</td>
                        </tr>
                    </tbody>
                <!--  <tbody>
                        <tr>
                            <td colspan="7" style="background-color: #8db3e2; text-align: right;">({{$request->amountInWords}})</td>
                        </tr>
                    </tbody> -->
                    <tbody>
                        <tr>
                            <td colspan="8">PLEASE ISSUE ALL PAYMENT ON BELOW BANK ACCOUNT DETAILS : </td>
                        </tr>
                    </tbody>
                    
                </table>
            @endif

            @if ($request->template <> 1 && !$request->line_invoiceDetails && !$request->item_invoice)
                <table class="table table-sm table-striped hover table-bordered" style="width: 100%;">
                    <thead>
                    <tr style="background-color: #6798da">
                        <th colspan="1">Item<br>رقم المنتج</th>
                        <th colspan="4">GL Code<br>رمز جل</th>
                        <th colspan="6">Description<br>الوصف</th>
                        <th colspan="2">QTY<br>الكمية</th>
                        <th colspan="3">Unit Rate<br> سعر الوحده</th>
                        <th colspan="4">Total Amount<br>القيمة الكلية</th>
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
                            <td colspan="1">{{$x}}</td>
                            <td colspan="4">{{$item->glCode}}</td>
                            <td colspan="6">{{$item->glCodeDes}}</td>
                            <td colspan="2">{{number_format($item->invoiceQty,2)}}</td>
                            <td colspan="3">{{number_format($item->unitCost,$numberFormatting)}}</td>
                            <td colspan="4">{{number_format($item->invoiceAmount,$numberFormatting)}}</td>
                        </tr>
                        {{ $x++ }}
                    @endforeach
                    </tbody>

                    <tbody>
                        <tr>
                            <td></td>
                            <td colspan="12" style="text-align: left; border-right: none !important;"><b>Total Before VAT ( الاجمالي قبل الضريبة )</b></td>
                            <td  colspan="3" style="text-align: center; border-left: none !important"><b>{{empty($request->currency) ? '' : $request->currency->CurrencyCode}}</b></td>
                            <td colspan="5" class="text-right">@if ($request->invoicedetails)
                            {{number_format($directTraSubTotal, $numberFormatting)}}
                        @endif</td>
                        </tr>
                        @if ($request->tax)
                        {{$directTraSubTotal+=$request->tax->amount}}
                            <tr>
                                <td></td>
                                <td colspan="12" style="text-align: left; border-right: none !important;"><b>Value Added Tax {{$request->tax->taxPercent}}% (ضريبة القيمة المضافة )</b></td>
                                <td colspan="3" style="text-align: center; border-left: none !important"><b>{{empty($request->currency) ? '' : $request->currency->CurrencyCode}}</b></td>
                                <td colspan="5" class="text-right">{{number_format($request->tax->amount, $numberFormatting)}}</td>
                            </tr>

                        <tr>
                            <td></td>
                            <td colspan="12" style="text-align: left; border-right: none !important;"><b>Total Amount Including VAT(القيمة الكلية متضمنة ضريبة القيمة المضافة)</b></td>
                            <td colspan="3" style="text-align: center; border-left: none !important"><b>{{empty($request->currency) ? '' : $request->currency->CurrencyCode}}</b></td>
                            <td colspan="5" class="text-right">{{number_format($directTraSubTotal, $numberFormatting)}}</td>
                        </tr>
                        @endif
                    </tbody>
                <!--  <tbody>
                        <tr>
                            <td colspan="7" style="background-color: #8db3e2; text-align: right;">({{$request->amountInWords}})</td>
                        </tr>
                    </tbody> -->
                    <tbody>
                        <tr>
                            <td colspan="6">PLEASE ISSUE ALL PAYMENT ON BELOW BANK ACCOUNT DETAILS : </td>
                        </tr>
                    </tbody>
                    
                </table>
            @endif

            @if ($request->template == 2 && isset($request->item_invoice) && $request->item_invoice)

                    <table class="table">
                        <thead>
                        <tr style="background-color: #6798da;">
                            <th colspan="1" style="width:5%;"></th>
                            <th colspan="4" style="width:40%;">Item<br>رقم المنتج</th>
                            <th colspan="2" style="width:10%;text-align: center">UOM<br>وحدة القياس</th>
                            <th colspan="2" style="width:15%;text-align: center">QTY<br>الكمية</th>
                            <th colspan="2" style="width:15%;text-align: center">unit Cost<br>تكلفة الوحدة</th>
                            <th colspan="2" style="width:15%;text-align: center">Total Amount<br>القيمة الكلية</th>
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
                                        <td colspan="1">{{$x}}</td>
                                        <td colspan="4" style="word-wrap:break-word;">{{$item->itemPrimaryCode.' - '.$item->itemDescription}}</td>
                                        <td colspan="2" style="text-align: right;">{{isset($item->uom_issuing->UnitShortCode)?$item->uom_issuing->UnitShortCode:''}}</td>
                                        <td colspan="2" style="text-align: right;">{{$item->qtyIssued}}</td>
                                        <td colspan="2" style="text-align: right;">{{number_format($item->sellingCostAfterMargin,$numberFormatting)}}</td>
                                        <td colspan="2" class="text-right">{{number_format($item->sellingTotal,$numberFormatting)}}</td>
                                    </tr>
                                    {{ $x++ }}
                                @endif
                            @endforeach
                        @endif

                        </tbody>
                        <tbody>
                        <tr>
                            <td colspan="7"></td>
                            <td colspan="3" style="text-align: left; border-right: none !important;"><b>Total Before VAT ( الاجمالي قبل الضريبة )</b></td>
                            <td style="text-align: center; border-left: none !important"><b>{{empty($request->currency) ? '' : $request->currency->CurrencyCode}}</b></td>
                            <td class="text-right">@if ($request->invoicedetails)
                                    {{number_format($directTraSubTotal, $numberFormatting)}}
                                @endif</td>
                        </tr>
                        @if ($request->isVATEligible)
                            {{$totalVATAmount = (($request->tax && $request->tax->amount) ? $request->tax->amount : 0)}}
                            {{$directTraSubTotal+=$totalVATAmount}}
                            <tr>
                            <td colspan="7"></td>
                                <td colspan="3" style="text-align: left; border-right: none !important;"><b>Value Added Tax {{round( ( ($request->tax && $request->tax->taxPercent ) ? $request->tax->taxPercent : 0 ), 2)}}% (ضريبة القيمة المضافة )</b></td>
                                <td style="text-align: center; border-left: none !important"><b>{{empty($request->currency) ? '' : $request->currency->CurrencyCode}}</b></td>
                                <td class="text-right">{{number_format($totalVATAmount, $numberFormatting)}}</td>
                            </tr>

                            <tr>
                            <td colspan="7"></td>
                                <td colspan="3" style="text-align: left; border-right: none !important;"><b>Total Amount Including VAT(القيمة الكلية متضمنة ضريبة القيمة المضافة)</b></td>
                                <td style="text-align: center; border-left: none !important"><b>{{empty($request->currency) ? '' : $request->currency->CurrencyCode}}</b></td>
                                <td class="text-right">{{number_format($directTraSubTotal, $numberFormatting)}}</td>
                            </tr>
                        @endif
                        </tbody>
                    <!--  <tbody>
                        <tr>
                            <td colspan="7" style="background-color: #8db3e2; text-align: right;">({{$request->amountInWords}})</td>
                        </tr>
                    </tbody> -->
                        <tbody>
                        <tr>
                            <td colspan="6">PLEASE ISSUE ALL PAYMENT ON BELOW BANK ACCOUNT DETAILS : </td>
                        </tr>
                        </tbody>
                    </table>

            @endif
        </div>
    @endif






    <br>
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
                            <td colspan="1"></td>
                            <td colspan="3" width="100px"><span class="font-weight-bold"><b>BANK NAME</b></span></td>
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
                            <td colspan="1"></td>
                            <td colspan="3" width="100px"><span class="font-weight-bold"><b>ACCOUNT NAME</b></span></td>
                            <td><b> :
                                @if($request->secondaryLogoCompanySystemID)
                                    @if($secondaryBankAccount->contract && $secondaryBankAccount->contract->secondary_bank_account)
                                        {{$secondaryBankAccount->contract->secondary_bank_account->AccountName}}
                                    @endif
                                @else
                                    {{($request->bankaccount) ? $request->bankaccount->AccountName : ''}}
                                @endif
                                </b>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="1"></td>
                            <td colspan="3" width="100px"><span class="font-weight-bold"><b>ACCOUNT NO</b></span></td>
                            <td><b> :
                                @if($request->secondaryLogoCompanySystemID)
                                    @if($secondaryBankAccount->contract && $secondaryBankAccount->contract->secondary_bank_account)
                                        {{$secondaryBankAccount->contract->secondary_bank_account->AccountNo}}
                                        @if ($request->isPerforma == 0 && $secondaryBankAccount->contract->secondary_bank_account)
                                            ({{($secondaryBankAccount->contract->secondary_bank_account->currency->CurrencyCode) ? $secondaryBankAccount->contract->secondary_bank_account->currency->CurrencyCode : ''}})
                                        @endif
                                    @endif
                                @else
                                    {{($request->bankaccount) ? $request->bankaccount->AccountNo : ''}}
                                    @if ($request->isPerforma == 0 && $request->bankaccount)
                                        ({{($request->bankaccount->currency->CurrencyCode) ? $request->bankaccount->currency->CurrencyCode : ''}})
                                    @endif
                                @endif

                                </b>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="1"></td>
                            <td colspan="3" width="100px"><span class="font-weight-bold"><b>IBAN NO</b></span></td>
                            <td><b> :
                                @if($request->secondaryLogoCompanySystemID)
                                    @if($secondaryBankAccount->contract && $secondaryBankAccount->contract->secondary_bank_account)
                                        {{$request->accountIBANSecondary}}
                                    @endif
                                @else
                                    {{($request->bankaccount) ? $request->accountIBAN : ''}}
                                @endif
                                </b>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="1"></td>
                            <td colspan="3" width="100px"><span class="font-weight-bold"><b>SWIFT Code</b> </span></td>
                            <td><b> :
                                @if($request->secondaryLogoCompanySystemID)
                                    @if($secondaryBankAccount->contract && $secondaryBankAccount->contract->secondary_bank_account)
                                        {{$secondaryBankAccount->contract->secondary_bank_account->accountSwiftCode}}
                                    @endif
                                @else
                                    {{($request->bankaccount) ? $request->bankaccount->accountSwiftCode : ''}}
                                @endif
                                </b>
                            </td>
                        </tr>
                    </table>
                </div>

                @if(!$request->line_rentalPeriod)
                    <div class="" style="margin-top: 10px">
                        <table style="width: 100%">
                            <tr>
                                <td colspan="1"></td>
                                <td colspan="3" >
                                    <span class="font-weight-bold"><b>Approved By :</b></span>
                                </td>
                            </tr>
                            <tr>
                                @foreach ($request->approved_by as $det)
                                    <td colspan="1"></td>
                                    <td colspan="3" >
                                        <b>
                                        @if($det->employee)
                                            {{$det->employee->empFullName }}
                                            <br>

                                            @if($det->employee->details)
                                                @if($det->employee->details->designation)
                                                    {{$det->employee->details->designation->designation}}
                                                @endif
                                            @endif
                                            <br>
                                            @if($det->employee)
                                                {{ \App\helper\Helper::dateFormat($det->approvedDate)}}
                                            @endif
                                        @endif
                                        </b>
                                    </td>
                                @endforeach
                            </tr>

                            @if ($request->isPerforma == 0)
                                <tr>
                                    <td>
                                        <br>
                                        <br>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="1"></td>
                                    <td colspan="3" width="100px"><span class="font-weight-bold"><b>Created By</b> </span></td>
                                    <td><b> :
                                            @if ($request->createduser)
                                                {{($request->createduser) ? $request->createduser->empFullName : ''}}
                                            @endif
                                        </b>
                                    </td>
                                </tr>
                            @endif


                        </table>
                    </div>
                @endif
        </div>
</div>






</html>
