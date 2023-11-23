
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

    .table th {
        background-color: #8db3e2 !important;
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


</style>

<div class="content">
    <div class="row">
        <table style="width:100%" class="table_height">
            <tr>
                <td width="30%">
                    @if($request->logoExists)
                          @if($type == 1)
                            <img src="{{$request->companyLogo}}"
                            class="container">
                          @else
                            image not found
                          @endif
                    @endif
                </td>


                <td width="40%" style="text-align: center;white-space: nowrap">
                    <div class="text-center">

                        <h3>
                            <b>TAX INVOICE</b>
                            <br>
                            <b> فاتورة ضريبية</b>
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
        <table style="width: 100%">
            <tr>
                <td style="width: 50%; text-align: left;">
                    {{$request->CompanyAddress}}<br>
                    Tel: {{$request->CompanyTelephone}}<br>
                    Fax: {{$request->CompanyFax}}<br>
                    <b>VAT NO: {{$request->vatRegistratonNumber}}</b>
                </td>
                <td style="width: 50%; text-align: right;direction: rtl;">
                    {{$request->CompanyAddressSecondaryLanguage}}<br>
                    هاتف : {{$request->CompanyTelephone}}<br>
                          فاكس : {{$request->CompanyFax}}<br>
                   <b>الرقم الضريبي : {{$request->vatRegistratonNumber}}</b>
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
                <td style="width: 50%; text-align:left;">
                    <b>INVOICE NO : {{$request->bookingInvCode}}</b><br>

                    <b>INVOICE DATE : @if(!empty($request->bookingDate))
                                    {{\App\helper\Helper::dateFormat($request->bookingDate) }}
                                @endif</b><br>

                    <b>Date Of Supply : @if(!empty($request->date_of_supply))
                        {{\App\helper\Helper::dateFormat($request->date_of_supply) }}
                        @endif
                    </b><br>
                    
                    <b>Contract / PO No : 
                        @if($request->line_poNumber)
                            {{$request->PONumber}}
                        @endif
                    </b>
                </td>
                <td style="width: 50%; text-align:right; direction: rtl;">
                    <b>رقم الفاتورة : {{$request->bookingInvCode}}</b><br>
                    <b>تاريخ الفاتورة : @if(!empty($request->bookingDate))
                                    {{\App\helper\Helper::dateFormat($request->bookingDate) }}
                                @endif</b><br>

                    <b>تاريخ التوريد : @if(!empty($request->date_of_supply))
                        {{\App\helper\Helper::dateFormat($request->date_of_supply) }}
                        @endif
                    </b><br>

                    <b>رقم العقد/أمر الشراء : 
                        @if($request->line_poNumber)
                            <span dir="ltr">{{$request->PONumber}}</span>
                        @endif
                        
                    </b>

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
                <td style="width: 50%; text-align: left">
                    <b>CUSTOMER NAME : {{$request->customer->ReportTitle}}</b><br>
                    <b>CUSTOMER ADDRESS : {{$request->customer->customerAddress1}}</b><br>
                    <b>CUSTOMER TELEPHONE : {{isset($request->CustomerContactDetails->contactPersonTelephone)?$request->CustomerContactDetails->contactPersonTelephone:' '}}</b><br>
                    <b>CUSTOMER FAX : {{isset($request->CustomerContactDetails->contactPersonFax)?$request->CustomerContactDetails->contactPersonFax:' '}}</b><br>
                    <b>CUSTOMER VATIN : {{$request->vatNumber}}</b>
                </td>
                <td style="width: 50%; text-align: right;direction: rtl;">
                    <b>أسم العميل : {{$request->customer->ReportTitle}}</b><br>
                    <b>عنوان العميل : {{$request->customer->customerAddress1}}</b><br>
                    <b>رقم العميل : {{isset($request->CustomerContactDetails->contactPersonTelephone)?$request->CustomerContactDetails->contactPersonTelephone:' '}}</b><br>
                    <b>فاكس العميل : {{isset($request->CustomerContactDetails->contactPersonFax)?$request->CustomerContactDetails->contactPersonFax:' '}}</b><br>
                    <b>الرقم الضريبي : {{$request->vatNumber}}</b>
                </td>
            </tr>
        </table>
    </div>
    <br>

    @if ($request->isPerforma == 0)
        <div class="row">
            <table style="width:100%">
                <tr>
                    <td style="width: 100%; text-align: left">
                        <b>Comments  : {{$request->comments}}</b><br>
                    </td>
                </tr>
            </table>
        </div>

        @if ($request->template <> 1 && !$request->line_invoiceDetails && !$request->item_invoice)
            <table class="table" style="width: 100%;">
                <thead>
                <tr style="background-color: #6798da">
                    <th style="width:2%"></th>
                    <th style="width:24%; text-align: center">Line item<br>رقم المنتج</th>
                    <th style="width:6%; text-align: center">UOM<br>وحدة القياس</th>
                    <th style="width:6%; text-align: center">Qty<br>الكمية</th>
                    <th style="width:12%;text-align: center">Unit Price<br>سعر الوحده</th>
                    <th style="width:12%;text-align: center">Total Taxable Amount<br>إجمالي المبلغ الخاضع للضريبة </th>
                    <th style="width:10%;text-align: center">VAT %<br>ضريبة القيمة المضافة %</th>
                    <th style="width:12%;text-align: center">VAT Amount<br>مبلغ ضريبة القيمة المضافة</th>
                    <th style="width:15%;text-align: center">Total Amount inclusive of VAT<br>القيمة الكلية متضمنة ضريبة القيمة المضافة</th>
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
                        <tr style="border: 1px solid !important;">
                            <td>{{$x}}</td>
                            <td>{{isset($item->comments)?$item->comments:''}}</td>
                            <td>{{isset($item->unit)? $item->unit->UnitShortCode:''}}</td>
                            <td style="text-align: right;">{{isset($item->invoiceQty)? $item->invoiceQty:0}}</td>
                            <td style="text-align: right;">{{number_format($item->salesPrice,$numberFormatting)}}</td>
                            <td style="text-align: right;">{{number_format($item->invoiceAmount,$numberFormatting)}}</td>
                            <td style="text-align: right;">{{$item->VATPercentage}}</td>
                            {{$totalVatamount = ($item->VATPercentage/100)*$item->invoiceAmount}}
                            <td style="text-align: right;">{{number_format(($totalVatamount),$numberFormatting)}}</td>
                            <td style="text-align: right;">{{number_format($item->invoiceAmount+$totalVatamount,$numberFormatting)}}</td>
                        </tr>
                        {{ $x++ }}
                    @endforeach
                </tbody>
                <tbody>
                    <tr>
                        <td></td>
                        <td colspan="6" style="text-align: left; border-right: none !important;"><b>Total Before VAT ( الاجمالي قبل الضريبة )</b></td>
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
                        <td colspan="6" style="text-align: left; border-right: none !important;"><b>Value Added Tax {{$taxPercent}}% (ضريبة القيمة المضافة )</b></td>
                        <td style="text-align: center; border-left: none !important"><b>{{empty($request->currency) ? '' : $request->currency->CurrencyCode}}</b></td>
                        <td class="text-right">{{number_format($taxAmount, $numberFormatting)}}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td colspan="6" style="text-align: left; border-right: none !important;"><b>Total Amount Including VAT(القيمة الكلية متضمنة ضريبة القيمة المضافة)</b></td>
                        <td style="text-align: center; border-left: none !important"><b>{{empty($request->currency) ? '' : $request->currency->CurrencyCode}}</b></td>
                        <td class="text-right">{{number_format($directTraSubTotal, $numberFormatting)}}</td>
                    </tr>
                </tbody>
                @if ($request->currency && $request->local_currency && $request->currency->CurrencyCode != $request->local_currency->CurrencyCode)
                    <tbody>
                        <tr>
                            <td colspan="9"><br> </td>
                        </tr>

                        <tr>
                            <td></td>
                            <td colspan="6" style="text-align: left; border-right: none !important;"><b>Total Before VAT ( الاجمالي قبل الضريبة )</b></td>
                            <td style="text-align: center; border-left: none !important"><b>{{empty($request->local_currency) ? '' : $request->local_currency->CurrencyCode}}</b></td>
                            <td class="text-right">@if ($request->invoicedetails)
                            {{number_format($localDirectTraSubTotal, $numberFormatting)}}
                        @endif</td>
                        </tr>
                        {{$localDirectTraSubTotal+= ($request->tax) ? $request->tax->localAmount : 0}}
                        {{$taxAmount = ($request->tax) ? $request->tax->localAmount : 0}}
                        {{$taxPercent = ($request->tax) ? $request->tax->taxPercent : 0}}
                        <tr>
                            <td></td>
                            <td colspan="6" style="text-align: left; border-right: none !important;"><b>Value Added Tax {{$taxPercent}}% (ضريبة القيمة المضافة )</b></td>
                            <td style="text-align: center; border-left: none !important"><b>{{empty($request->local_currency) ? '' : $request->local_currency->CurrencyCode}}</b></td>
                            <td class="text-right">{{number_format($taxAmount, $numberFormatting)}}</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td colspan="6" style="text-align: left; border-right: none !important;"><b>Total Amount Including VAT(القيمة الكلية متضمنة ضريبة القيمة المضافة)</b></td>
                            <td style="text-align: center; border-left: none !important"><b>{{empty($request->local_currency) ? '' : $request->local_currency->CurrencyCode}}</b></td>
                            <td class="text-right">{{number_format($localDirectTraSubTotal, $numberFormatting)}}</td>
                        </tr>
                    </tbody>
                @endif
                <tbody>
                    <tr>
                        <td colspan="9">PLEASE ISSUE ALL PAYMENT ON BELOW BANK ACCOUNT DETAILS : </td>
                    </tr>
                </tbody>
                
            </table>

            <br>
            <br>
            @php
                $qrcode=QrCode::encoding('UTF-8')->size(75)->generate(URL::full());
                $qrcode=str_replace('<?xml version="1.0" encoding="UTF-8"?>',"",$qrcode); //replace to empty
            @endphp
            {!! $qrcode !!}
            <table>
                <tbody>
                    <tr>
                        <td style="width: 50%; text-align: left">This QR code is encoded as per ZATCA e-invoicing requirement / رمز الإستجابة السريعة مشفَر بحسب متطلبات هيئة الزكاة والضريبة والجمارك للفوترة الإلكترونية</td>
                        <td></td>
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
                        <th style="width:6%;">Item<br>رقم المنتج</th>
                        <th style="width:25%; text-align: center">Description<br>الوصف</th>
                        <th style="width:10%;text-align: center">UOM<br>وحدة القياس</th>
                        <th style="width:6%;text-align: center">QTY<br>الكمية</th>
                        <th style="width:10%;text-align: center">Days(OP)<br>الايام عمل</th>
                        <th style="width:10%;text-align: center">Price(OP)<br>سعر العمل</th>
                        <th style="width:10%;text-align: center">Days(STB)<br>الايام الانتظار</th>
                        <th style="width:10%;text-align: center">Price(STB)<br>سعر الانتظار</th>
                        <th style="width:13%;text-align: center">Total Amount<br>القيمة الكلية</th>
                    </tr>
                    </thead>

                    <tbody>
                    {{$decimal = 2}}
                    {{$x=1}}
                    {{$directTraSubTotal=0}}
                    {{$numberFormatting=empty($request->currency) ? 2 : $request->currency->DecimalPlaces}}
                    @if (isset($request->profomaDetailData))
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
                    @endif
                    </tbody>

                    <tbody>
                        <tr>
                            <td></td>
                            <td colspan="5" style="text-align: left; border-right: none !important;"><b>Total Before VAT ( الاجمالي قبل الضريبة )</b></td>
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
                            <td colspan="5" style="text-align: left; border-right: none !important;"><b>Value Added Tax {{$taxPercent}}% (ضريبة القيمة المضافة )</b></td>
                            <td style="text-align: center; border-left: none !important"><b>{{empty($request->currency) ? '' : $request->currency->CurrencyCode}}</b></td>
                            <td class="text-right">{{number_format($taxAmount, $numberFormatting)}}</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td colspan="5" style="text-align: left; border-right: none !important;"><b>Total Amount Including VAT(القيمة الكلية متضمنة ضريبة القيمة المضافة)</b></td>
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
                <!--  <tbody>
                        <tr>
                            <td colspan="7" style="background-color: #8db3e2; text-align: right;">({{$request->amountInWords}})</td>
                        </tr>
                    </tbody> -->
                    <tbody>
                        <tr>
                            <td colspan="8">PLEASE ISSUE ALL PAYMENT ON BELOW BANK ACCOUNT DETAILS :  </td>
                        </tr>
                    </tbody>
                    
                </table>
            @endif

            @if ($request->template <> 1 && !$request->line_invoiceDetails && !$request->item_invoice)
                <table class="table" style="width: 100%;">
                    <thead>
                    <tr style="background-color: #6798da">
                        <th style="width:6%">Item<br>رقم المنتج</th>
                        <th style="width:10%; text-align: center">GL Code<br>رمز جل</th>
                        <th style="width:25%; text-align: center">Description<br>الوصف</th>
                        <th style="width:10%;text-align: center">UOM<br>وحدة القياس</th>
                        <th style="width:5%;text-align: center">QTY<br>الكمية</th>
                        <th style="width:10%;text-align: center">Unit Rate<br> سعر الوحده</th>
                        <th style="width:15%;text-align: center">Total Amount<br>القيمة الكلية</th>
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
                            <td colspan="3" style="text-align: left; border-right: none !important;"><b>Total Before VAT ( الاجمالي قبل الضريبة )</b></td>
                            <td style="text-align: center; border-left: none !important"><b>{{empty($request->currency) ? '' : $request->currency->CurrencyCode}}</b></td>
                            <td colspan="2" class="text-right">@if ($request->invoicedetails)
                            {{number_format($directTraSubTotal, $numberFormatting)}}
                        @endif</td>
                        </tr>
                        @if ($request->tax)
                        {{$directTraSubTotal+=$request->tax->amount}}
                            <tr>
                                <td></td>
                                <td colspan="3" style="text-align: left; border-right: none !important;"><b>Value Added Tax {{$request->tax->taxPercent}}% (ضريبة القيمة المضافة )</b></td>
                                <td style="text-align: center; border-left: none !important"><b>{{empty($request->currency) ? '' : $request->currency->CurrencyCode}}</b></td>
                                <td colspan="2" class="text-right">{{number_format($request->tax->amount, $numberFormatting)}}</td>
                            </tr>

                        <tr>
                            <td></td>
                            <td colspan="3" style="text-align: left; border-right: none !important;"><b>Total Amount Including VAT(القيمة الكلية متضمنة ضريبة القيمة المضافة)</b></td>
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
                <!--  <tbody>
                        <tr>
                            <td colspan="7" style="background-color: #8db3e2; text-align: right;">({{$request->amountInWords}})</td>
                        </tr>
                    </tbody> -->
                    <tbody>
                        <tr>
                            <td colspan="7">PLEASE ISSUE ALL PAYMENT ON BELOW BANK ACCOUNT DETAILS : </td>
                        </tr>
                    </tbody>
                    
                </table>
            @endif

            @if ($request->template == 2 && isset($request->item_invoice) && $request->item_invoice)

                    <table class="table">
                        <thead>
                        <tr style="background-color: #6798da;">
                            <th style="width:5%;"></th>
                            <th style="width:40%;">Item<br>رقم المنتج</th>
                            <th style="width:10%;text-align: center">UOM<br>وحدة القياس</th>
                            <th style="width:15%;text-align: center">QTY<br>الكمية</th>
                            <th style="width:15%;text-align: center">unit Cost<br>تكلفة الوحدة</th>
                            <th style="width:15%;text-align: center">Total Amount<br>القيمة الكلية</th>
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
                            <td colspan="3" style="text-align: left; border-right: none !important;"><b>Total Before VAT ( الاجمالي قبل الضريبة )</b></td>
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
                                <td colspan="3" style="text-align: left; border-right: none !important;"><b>Value Added Tax {{round( ( ($request->tax && $request->tax->taxPercent ) ? $request->tax->taxPercent : 0 ), 2)}}% (ضريبة القيمة المضافة )</b></td>
                                <td style="text-align: left; border-left: none !important"><b>{{empty($request->currency) ? '' : $request->currency->CurrencyCode}}</b></td>
                                <td class="text-right">{{number_format($totalVATAmount, $numberFormatting)}}</td>
                            </tr>

                            <tr>
                                <td></td>
                                <td colspan="3" style="text-align: left; border-right: none !important;"><b>Total Amount Including VAT(القيمة الكلية متضمنة ضريبة القيمة المضافة)</b></td>
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
</div>
<br>
<br>
    @if($request->line_invoiceDetails)
        <div class="" style="">
            @else
                <div class="" style="">
                    @endif
                    <table>
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
                            <td width="100px"><span class="font-weight-bold"><b>ACCOUNT NAME</b></span></td>
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
                            <td width="100px"><span class="font-weight-bold"><b>ACCOUNT NO</b></span></td>
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
                            <td width="100px"><span class="font-weight-bold"><b>IBAN NO</b></span></td>
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
                            <td width="100px"><span class="font-weight-bold"><b>SWIFT Code</b> </span></td>
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
                                <td>
                                    <span class="font-weight-bold"><b>Approved By :</b></span>
                                </td>
                            </tr>
                            <tr>
                                @foreach ($request->approved_by as $det)
                                    <td style="padding-right: 25px" class="text-center">
                                        <b>
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
                                        </b>
                                    </td>
                                @endforeach
                            </tr>

                            @if ($request->isPerforma == 0)
                                <tr>
                                    <td width="100px"><span class="font-weight-bold"><b>Created By</b> </span></td>
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










