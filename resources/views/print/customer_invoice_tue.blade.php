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


</style>

<div class="content">
    <div class="row">
        <table style="width:100%">
            <tr>
                <td width="30%">
                    @if($request->logoExists)
                        <img src="logos/{{$request->companyLogo}}"
                             width="180px" height="60px">
                    @endif
                </td>


                <td width="50%" style="text-align: center;white-space: nowrap">
                    <div class="text-center">

                        <h3>
                            <b>TAX INVOICE</b>
                        </h3>

                        <h3>
                           <b> فاتورة ضريبية</b>
                        </h3>
                    </div>

                </td>
                <td style="width: 30%; text-align: right;">
                    <div style="display: flex;">
                        <h3 style="color: #3f67c8; font-size: 15px"><b>شركة الهدف المتحدة للطاقة</b></h3>
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
                    <!-- {{$request->CompanyCountry}}<br> -->
                    Tel: {{$request->CompanyTelephone}}<br>
                    Fax: {{$request->CompanyFax}}<br>
                    <b>VAT NO: {{$request->vatRegistratonNumber}}</b>
                </td>
                <td style="width: 50%; text-align: right;">
                    {{$request->CompanyAddressSecondaryLanguage}}<br>
                    <!-- سلطنة عمان<br> -->
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
                    
                        <b>QUOTE TUE : 
                            @if($request->line_poNumber)
                                {{$request->PONumber}}
                            @endif
                        </b><br>
                        <b>Contract / PO No : 
                             @if(!empty($request->invoicedetails) )
                                {{$request->invoicedetails[0]->clientContractID}}
                            @endif
                        </b>
                </td>
                <td style="width: 50%; text-align:right;">
                    <b> {{$request->bookingInvCode}} : رقم الفاتورة</b><br>
                    <b>تاريخ الفاتورة : @if(!empty($request->bookingDate))
                                    {{\App\helper\Helper::dateFormat($request->bookingDate) }}
                                @endif</b><br>
                        <b>@if($request->line_poNumber)
                                {{$request->PONumber}}
                            @endif : تاريخ الفاتورة 
                            
                        </b><br>
                        <b>@if(!empty($request->invoicedetails) )
                                {{$request->invoicedetails[0]->clientContractID}}
                            @endif : رقم العقد/أمر الشراء
                             
                        </b>

                </td>
            </tr>
        </table>
    </div>
    <div class="row">
        <br>
    </div>
    @if(!empty($request->serviceStartDate) && !empty($request->serviceEndDate))
    <div class="row" style="text-decoration: underline; text-align: center;">
        <b>RENTAL INVOICE FOR THE PERIOD FROM  
                                    {{\App\helper\Helper::dateFormat($request->serviceStartDate) }}
                                 TO 
                                    {{\App\helper\Helper::dateFormat($request->serviceEndDate) }}
                            </b>
    @endif
    </div>
    <div class="row">
        <br>
    </div>
    <div class="row">
        <table style="width:100%">
            <tr>
                <td style="width: 50%; text-align: left">
                    <b>CUSTOMER NAME : {{$request->customer->ReportTitle}}</b><br>
                    <b>ADDRESS : {{$request->customer->customerAddress1}}</b><br>
                    <b>VAT NO : {{$request->vatNumber}}</b>
                </td>
                <td style="width: 50%; text-align: right;">
                    <b>أسم العميل : {{$request->customer->reportTitleSecondLanguage}}</b><br>
                    <b>عنوان العميل : {{$request->customer->addressOneSecondLanguage}}</b><br>
                    <b>الرقم الضريبي : {{$request->vatNumber}}</b>
                </td>
            </tr>
        </table>
    </div>
    <br>
    <div class="row">
        @if ($request->template==1 && !$request->line_invoiceDetails && !$request->linePdoinvoiceDetails)
            <table class="table" style="width: 100%;">
                <thead>
                <tr style="background-color: #6798da">
                    <th style="width:6%">Item<br>رقم المنتج</th>
                    <th style="width:29%; text-align: center">Description<br>الوصف</th>
                    <th style="width:5%;text-align: center">QTY<br>الكمية</th>
                    <th style="width:6%;text-align: center">Days<br>عدد الايام</th>
                    <th style="width:10%;text-align: center">Price<br>السعر</th>
                    <th style="width:15%;text-align: center">Net Price<br>السعر الصافي</th>
                    <!-- <th style="width:14%;text-align: center">Rental Period<br></th> -->
                    <th style="width:15%;text-align: center">Total Amount<br>القيمة الكلية</th>
                </tr>
                </thead>

                <tbody>
                {{$decimal = 2}}
                {{$x=1}}
                {{$directTraSubTotal=0}}
                {{$numberFormatting=empty($request->currency) ? 2 : $request->currency->DecimalPlaces}}

                @foreach ($request->temp as $item)

                    {{$directTraSubTotal +=$item->sumofsumofStandbyAmount}}
                    <tr style="border: 1px solid !important;">
                        <td>{{$x}}</td>
                        <td>{{$item->myStdTitle}}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <!-- <td>{{$request->monthOfInvoice}}</td> -->
                        <td class="text-right">{{number_format($item->sumofsumofStandbyAmount,$numberFormatting)}}</td>
                    </tr>
                    {{ $x++ }}
                @endforeach
                </tbody>

                <tbody>
                    <tr>
                        <td></td>
                        <td colspan="4" style="text-align: left; border-right: none !important;"><b>Total Before VAT ( الاجمالي قبل الضريبة )</b></td>
                        <td style="text-align: center; border-left: none !important"><b>SAR</b></td>
                        <td class="text-right">@if ($request->invoicedetails)
                        {{number_format($directTraSubTotal, $numberFormatting)}}
                    @endif</td>
                    </tr>
                    @if ($request->tax)
                        {{$directTraSubTotal+=$request->tax->amount}}
                        <tr>
                            <td></td>
                            <td colspan="4" style="text-align: left; border-right: none !important;"><b>Value Added Tax {{$request->tax->taxPercent}}% (ضريبة القيمة المضافة )</b></td>
                            <td style="text-align: center; border-left: none !important"><b>SAR</b></td>
                            <td class="text-right">{{number_format($request->tax->amount, $numberFormatting)}}</td>
                        </tr>

                    <tr>
                        <td></td>
                        <td colspan="4" style="text-align: left; border-right: none !important;"><b>Total Amount Including VAT(القيمة الكلية متضمنة ضريبة القيمة المضافة)</b></td>
                        <td style="text-align: center; border-left: none !important"><b>SAR</b></td>
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
                        <td colspan="7">PLEASE ISSUE ALL PAYMENT ON BELOW BANK ACCOUNT DETAILS : </td>
                    </tr>
                </tbody>
                
            </table>
        @endif

         @if ($request->template <> 1 && !$request->line_invoiceDetails)
            <table class="table" style="width: 100%;">
                <thead>
                <tr style="background-color: #6798da">
                    <th style="width:6%">Item<br>رقم المنتج</th>
                    <th style="width:29%; text-align: center">Description<br>الوصف</th>
                    <th style="width:5%;text-align: center">QTY<br>الكمية</th>
                    <th style="width:6%;text-align: center">Days<br>عدد الايام</th>
                    <th style="width:10%;text-align: center">Price<br>السعر</th>
                    <th style="width:15%;text-align: center">Net Price<br>السعر الصافي</th>
                    <!-- <th style="width:14%;text-align: center">Rental Period<br></th> -->
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
                        <td>{{$item->glCodeDes}}</td>
                        <td>{{number_format($item->invoiceQty,2)}}</td>
                        <td></td>
                        <td style="text-align: right;">{{number_format($item->unitCost,$numberFormatting)}}</td>
                        <td style="text-align: right;">{{number_format($item->invoiceAmount,$numberFormatting)}}</td>
                        <!-- <td style="text-align: right;">{{$request->monthOfInvoice}}</td> -->
                        <td class="text-right">{{number_format($item->invoiceAmount,$numberFormatting)}}</td>
                    </tr>
                    {{ $x++ }}
                @endforeach
                </tbody>

                <tbody>
                    <tr>
                        <td></td>
                        <td colspan="4" style="text-align: left; border-right: none !important;"><b>Total Before VAT ( الاجمالي قبل الضريبة )</b></td>
                        <td style="text-align: center; border-left: none !important"><b>SAR</b></td>
                        <td class="text-right">@if ($request->invoicedetails)
                        {{number_format($directTraSubTotal, $numberFormatting)}}
                    @endif</td>
                    </tr>
                    @if ($request->tax)
                    {{$directTraSubTotal+=$request->tax->amount}}
                        <tr>
                            <td></td>
                            <td colspan="4" style="text-align: left; border-right: none !important;"><b>Value Added Tax {{$request->tax->taxPercent}}% (ضريبة القيمة المضافة )</b></td>
                            <td style="text-align: center; border-left: none !important"><b>SAR</b></td>
                            <td class="text-right">{{number_format($request->tax->amount, $numberFormatting)}}</td>
                        </tr>

                    <tr>
                        <td></td>
                        <td colspan="4" style="text-align: left; border-right: none !important;"><b>Total Amount Including VAT(القيمة الكلية متضمنة ضريبة القيمة المضافة)</b></td>
                        <td style="text-align: center; border-left: none !important"><b>SAR</b></td>
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
                        <td colspan="7">PLEASE ISSUE ALL PAYMENT ON BELOW BANK ACCOUNT DETAILS : </td>
                    </tr>
                </tbody>
                
            </table>
        @endif
    </div>
</div>










