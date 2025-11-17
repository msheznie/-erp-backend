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

    @if(isset($lang) && $lang === 'ar')
        body {
        font-family: 'Noto Sans Arabic', sans-serif !important;
    }
    @endif

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


                <td width="50%" style="text-align: center;white-space: nowrap">
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
                    <!-- {{$request->CompanyCountry}}<br> -->
                    Tel: {{$request->CompanyTelephone}}<br>
                    Fax: {{$request->CompanyFax}}<br>
                    <b>VAT NO: {{$request->vatRegistratonNumber}}</b>
                </td>
                <td style="width: 50%; text-align: right;direction: rtl;">
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
                    
                        <b>QUOTE/DO TUE : 
                            @if($request->line_poNumber)
                                {{$request->PONumber}}
                            @endif
                        </b><br>
                        <b>Date Of Supply : @if(!empty($request->date_of_supply))
                            {{\App\helper\Helper::dateFormat($request->date_of_supply) }}
                            @endif
                        </b><br>
                        <b>Contract / PO No : 
                             @if(!empty($request->invoicedetails) )
                                {{isset($request->invoicedetails[0]->clientContractID)?$request->invoicedetails[0]->clientContractID:''}}
                            @endif
                        </b>
                </td>
                <td style="width: 50%; text-align:right; direction: rtl;">
                    <b>رقم الفاتورة : {{$request->bookingInvCode}}</b><br>
                    <b>تاريخ الفاتورة : @if(!empty($request->bookingDate))
                                    {{\App\helper\Helper::dateFormat($request->bookingDate) }}
                                @endif</b><br>
                        <b>رقم التسعيرة : @if($request->line_poNumber)
                                {{$request->PONumber}}
                            @endif 
                            
                        </b><br>
                        <b>رقم العقد/أمر الشراء : @if(!empty($request->invoicedetails) )
                                {{isset($request->invoicedetails[0]->clientContractID)?$request->invoicedetails[0]->clientContractID:''}}
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
                    <b>أسم العميل : {{$request->customer->reportTitleSecondLanguage}}</b><br>
                    <b>عنوان العميل : {{$request->customer->addressOneSecondLanguage}}</b><br>
                    <b>الرقم الضريبي : {{$request->vatNumber}}</b>
                </td>
            </tr>
        </table>
    </div>
    <br>
    <div class="row">
        @if ($request->template==1)
            <table class="table">
                <thead>
                <tr style="background-color: #6798da;">
                    <th style="width:5%;">Item<br>رقم المنتج</th>
                    <th style="width:20%; text-align: center">Our Reference<br>المرجع</th>
                    <th style="width:15%;text-align: center">Client Reference<br>مرجع العميل</th>
                    <th style="width:25%;text-align: center">Item Description<br>وصف السلعة</th>
                    <th style="width:10%;text-align: center">UOM<br>وحدة القياس</th>
                    <th style="width:5%;text-align: center">QTY<br>الكمية</th>
                    <th style="width:10%;text-align: center">Unit Rate<br> سعر الوحده</th>
                    <th style="width:10%;text-align: center">Total Amount<br>القيمة الكلية</th>
                </tr>
                </thead>

                <tbody>
                {{$decimal = 2}}
                {{$x=1}}
                {{$directTraSubTotal=0}}
                {{$numberFormatting=empty($request->currency) ? 2 : $request->currency->DecimalPlaces}}

                    @foreach ($request->profomaDetailData as $item)
                        @if ($item->amount != 0)
                            {{$directTraSubTotal +=$item->amount}}
                            <tr style="border: 1px solid !important;">
                                <td>{{$x}}</td>
                                <td style="word-wrap:break-word;">{{$item->OurRef}}</td>
                                <td style="word-wrap:break-word;">{{$item->ClientRef}}</td>
                                <td style="word-wrap:break-word;">{{$item->assetDescription}}</td>
                                <td style="text-align: left;">{{isset($item->uom_issuing->UnitShortCode)?$item->uom_issuing->UnitShortCode:''}}</td>
                                <td style="text-align: right;">{{$item->qty}}</td>
                                <td style="text-align: right;">{{number_format($item->rate,$numberFormatting)}}</td>
                                <td style="text-align: right;">{{number_format($item->amount,$numberFormatting)}}</td>
                            </tr>
                            {{ $x++ }}
                        @endif
                    @endforeach
                </tbody>
                <tbody>
                    <tr>
                        <td></td>
                        <td colspan="4" style="text-align: left; border-right: none !important;"><b>Total Before VAT ( الاجمالي قبل الضريبة )</b></td>
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
                        <td colspan="4" style="text-align: left; border-right: none !important;"><b>Value Added Tax {{$taxPercent}}% (ضريبة القيمة المضافة )</b></td>
                        <td style="text-align: center; border-left: none !important"><b>{{empty($request->currency) ? '' : $request->currency->CurrencyCode}}</b></td>
                        <td class="text-right">{{number_format($taxAmount, $numberFormatting)}}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td colspan="4" style="text-align: left; border-right: none !important;"><b>Total Amount Including VAT(القيمة الكلية متضمنة ضريبة القيمة المضافة)</b></td>
                        <td style="text-align: center; border-left: none !important"><b>{{empty($request->currency) ? '' : $request->currency->CurrencyCode}}</b></td>
                        <td class="text-right">{{number_format($directTraSubTotal, $numberFormatting)}}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td colspan="6" style="text-align: left; border-right: none !important;"><b>Total Amount in Word ({{empty($request->currency) ? '' : $request->currency->CurrencyCode}}): ({{$request->amount_word}}
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
                        <td colspan="7">PLEASE ISSUE ALL PAYMENT ON BELOW BANK ACCOUNT DETAILS : </td>
                    </tr>
                </tbody>
                
            </table>
        @endif
    </div>
</div>










