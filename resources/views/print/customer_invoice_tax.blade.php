<html @if(isset($lang) && $lang === 'ar') dir="rtl" @endif>
<style type="text/css">
    <!--
    body {
        font-size: 11.5px;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
        color: black;
    }

    @if(isset($lang) && $lang === 'ar')
        body {
        direction: rtl;
        text-align: right;
        font-family: 'Noto Sans Arabic', sans-serif;
    }

    .text-left {
        text-align: right !important;
    }

    .text-right {
        text-align: left !important;
    }

    table {
        direction: rtl;
    }

    .table th, .table td {
        text-align: right;
    }
    @endif
    @page {
        margin: 20px 30px 220px;
    }

    #footer {
        position: fixed;
        left: 0px;
        top: 750px;
        bottom: 10px;
        right: 0px;
        height: 0px;
        /*font-size: 10px;*/
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
        font-weight: bold !important;
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
        margin-bottom: 30px;
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
        <table style="width:100%">
            <tr>
                <td width="30%">
                    @if($request->logo)
                           @if($type == 1)
                            <img src="{{$request->companyLogo}}"
                            class="container">
                          @else
                            {{__('custom.image_not_found')}}
                          @endif

                    @endif
                </td>


                <td width="50%" style="text-align: center;white-space: nowrap">
                    <div class="text-center">

                        <h3 class="font-weight-bold">
                            {{__('custom.tax_invoice')}}
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
        <table style="width: 100%">
            <tr>
                <td style="width: 50%; vertical-align: top; @if(isset($lang) && $lang === 'ar') text-align: right; @else text-align: left; @endif">
                    <b>{{$request->CompanyName}}</b><br>
                    {{$request->CompanyAddress}}<br>
                    <b> {{__('custom.tel')}}: </b> {{$request->CompanyTelephone}}<br>
                    <b> {{__('custom.fax')}}: </b> {{$request->CompanyFax}}<br>
                    <b>{{__('custom.vat_no')}}: </b>{{$request->vatRegistratonNumber}}
                </td>

                <td style="width: 50%; vertical-align: top; @if(isset($lang) && $lang === 'ar') text-align: right; @else text-align: left; @endif">
                    <table style="width: 100%">
                        <tr>
                            <td><b>{{__('custom.invoice_no')}} </b></td>
                            <td>: {{$request->bookingInvCode}}</td>
                        </tr>
                        <tr>
                            <td><b>{{__('custom.invoice_date')}} </b></td>
                            <td>: @if(!empty($request->bookingDate))
                                    {{\App\helper\Helper::dateFormat($request->bookingDate) }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><b>{{__('custom.date_of_supply')}} </b></td>
                            <td>: @if(!empty($request->date_of_supply))
                                    {{\App\helper\Helper::dateFormat($request->date_of_supply) }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><b>{{__('custom.name_of_customer')}} </b></td>
                            <td>:@if($request->line_customerShortCode)
                                    {{$request->customer->CutomerCode}} -
                                @endif
                                {{$request->customer->ReportTitle}}</td>
                        </tr>


                        <tr>
                            <td><b>{{__('custom.customer_address')}} </b></td>
                            <td>:
                                {{$request->customer->customerAddress1}}</td>
                        </tr>
                        <tr>
                            <td><b>{{__('custom.customer_telephone')}} </b></td>
                            <td>: {{isset($request->CustomerContactDetails->contactPersonTelephone)?$request->CustomerContactDetails->contactPersonTelephone:' '}}</td>
                        </tr>
                        <tr>
                            <td><b>{{__('custom.customer_fax')}} </b></td>
                            <td>: {{isset($request->CustomerContactDetails->contactPersonFax)?$request->CustomerContactDetails->contactPersonFax:' '}}</td>
                        </tr>
                        <tr>
                            <td><b>{{__('custom.customer_vatin')}}</b></td>
                            <td>:
                                {{$request->vatNumber}}</td>
                        </tr>

                    </table>
                </td>
            <tr>
        </table>
    </div>

    <br>
    <div class="row">
        <table style="width: 100%">
            <tr>
                <td style="width: 10%; vertical-align: top; @if(isset($lang) && $lang === 'ar') text-align: right; @else text-align: left; @endif"><b>{{__('custom.comments')}}: </b></td>
                <td style="width: 90%; @if(isset($lang) && $lang === 'ar') text-align: right; @else text-align: left; @endif">@if(!empty($request->comments))
                        {{$request->comments}}
                    @endif
                </td>
            <tr>
        </table>
    </div>
    <br>

    <div class="row">
        @if($request->linePdoinvoiceDetails)

            <table class="table table-bordered table-striped table-sm" style="width: 100%;">
                <thead>
                <tr class="">

                    <th style="text-align: center">{{__('custom.well')}}</th>
                    <th style="text-align: center">{{__('custom.network')}}</th>
                    <th style="text-align: center">{{__('custom.se')}}</th>
                    <th style="text-align: center">{{__('custom.total_amount')}}({{empty($request->currency) ? '' : $request->currency->CurrencyCode}})</th>
                </tr>
                </thead>
                <tbody>
                {{$decimal = 2}}
                {{$x=1}}
                {{$directTraSubTotal=0}}
                {{$numberFormatting=empty($request->currency) ? 2 : $request->currency->DecimalPlaces}}
                @foreach ($request->linePdoinvoiceDetails as $item)
                    {{$directTraSubTotal +=$item->wellAmount}}
                    <tr style="border-top: 2px solid #333 !important;border-bottom: 2px solid #333 !important;background-color: white">

                        <td style="width: 25%">{{$item->wellNo}}</td>
                        <td style="width: 25%">{{$item->netWorkNo}}</td>
                        <td style="width: 25%">{{$item->SEno}}</td>
                        <td style="width: 25%;text-align: right">{{number_format($item->wellAmount,$numberFormatting)}}</td>

                    </tr>
                    {{ $x++ }}
                @endforeach
                </tbody>

            </table>
        @endif

        @if($request->line_invoiceDetails)
            <table class="table table-bordered table-striped table-sm" style="width: 100%;">
                <thead>
                <tr class="">
                    <th style="width:1%"></th>
                    <th style="text-align: center">{{__('custom.client_ref')}}</th>
                    @if($request->is_po_in_line)
                        <th style="text-align: center">{{__('custom.po_line_item')}}</th>
                    @endif
                    <th style="text-align: center">{{__('custom.details')}}</th>
                    <th style="text-align: center">{{__('custom.uom')}}</th>
                    <th style="text-align: center">{{__('custom.qty')}}</th>
                    <th style="text-align: center">{{__('custom.unit_rate')}}</th>
                    <th style="text-align: center">{{__('custom.total_amount')}} ({{empty($request->currency) ? '' : $request->currency->CurrencyCode}})</th>
                </tr>
                </thead>
                <tbody>
                {{$decimal = 2}}
                {{$x=1}}
                {{$directTraSubTotal=0}}
                {{$numberFormatting=empty($request->currency) ? 2 : $request->currency->DecimalPlaces}}
                @foreach ($request->line_invoiceDetails as $item)
                    {{$directTraSubTotal +=$item->amount}}
                    <tr style="border-top: 2px solid #333 !important;border-bottom: 2px solid #333 !important;background-color: white">
                        <td>{{$x}}</td>
                        <td style="width: 12%">{{$item->ClientRef}}</td>
                        @if($request->is_po_in_line)
                            <td style="width: 12%">{{$item->pl3}}</td>
                        @endif
                        <td>{{$item->assetDescription}}</td>
                        <td style="text-align: left">{{isset($item->uom_issuing->UnitShortCode)?$item->uom_issuing->UnitShortCode:''}}</td>
                        <td style="width: 8%;text-align: right">{{number_format($item->qty,2)}}</td>
                        <td style="width: 10%;text-align: right">{{number_format($item->rate,$numberFormatting)}}</td>

                        <td style="width: 10%"
                            class="text-right">{{number_format($item->amount,$numberFormatting)}}</td>
                    </tr>
                    {{ $x++ }}
                @endforeach
                </tbody>

            </table>

        @endif

        @if ($request->template==1 && !$request->line_invoiceDetails && !$request->linePdoinvoiceDetails)

            <table class="table table-bordered table-striped table-sm" style="width: 100%;">
                <thead>
                <tr class="theme-tr-head">
                    <th style="width:1%"></th>
                    <th style=" text-align: center">{{__('custom.details')}}</th>


                    <th style="width:140px;text-align: center">{{__('custom.total_amount')}} ({{empty($request->currency) ? '' : $request->currency->CurrencyCode}})</th>
                </tr>
                </thead>

                <tbody>
                {{$decimal = 2}}
                {{$x=1}}
                {{$directTraSubTotal=0}}
                {{$numberFormatting=empty($request->currency) ? 2 : $request->currency->DecimalPlaces}}


                @foreach ($request->temp as $item)

                    {{$directTraSubTotal +=$item->sumofsumofStandbyAmount}}
                    <tr style="border-top: 2px solid #333 !important;border-bottom: 2px solid #333 !important;">
                        <td>{{$x}}</td>
                        <td>{{$item->myStdTitle}}</td>


                        <td style="width: 100px"
                            class="text-right">{{number_format($item->sumofsumofStandbyAmount,$numberFormatting)}}</td>
                    </tr>
                    {{ $x++ }}
                @endforeach
                </tbody>

            </table>
        @endif

        @if ($request->template <> 1 && !$request->line_invoiceDetails && isset($request->invoicedetails) && sizeof($request->invoicedetails) > 0)
            <table class="table table-bordered" style="width: 100%;">
                <thead>
                <tr class="theme-tr-head">
                    <th style="width:3%"></th>
                    <th style="text-align: center">{{__('custom.gl_code')}}</th>
                    <th style="text-align: center">{{__('custom.gl_code_description')}}</th>
                    @if($request->isProjectBase && $request->isPerforma == 0)
                        <th style="text-align: center">{{__('custom.project')}}</th>
                    @endif
                    <th style="text-align: center">{{__('custom.segment')}}</th>
                    <th style="text-align: center">{{__('custom.uom')}}</th>
                    <th style="text-align: center">{{__('custom.qty')}}</th>
                    <th style="text-align: center">{{__('custom.sales_price')}}</th>
                    <th style="text-align: center">{{__('custom.dis_percent')}}</th>
                    <th style="text-align: center">{{__('custom.discount_amount')}}</th>
                    <th style="text-align: center">{{__('custom.selling_unit_price')}}</th>
                    <th style="text-align: center">{{__('custom.vat_per_unit')}}</th>
                    <th style="text-align: center">{{__('custom.total_amount')}} ({{empty($request->currency) ? '' : $request->currency->CurrencyCode}})</th>
                </tr>
                </thead>
                <tbody>
                {{$decimal = 2}}
                {{$x=1}}
                {{$directTraSubTotal=0}}
                {{$numberFormatting=empty($request->currency) ? 2 : $request->currency->DecimalPlaces}}
                @foreach ($request->invoicedetails as $item)
                    {{$directTraSubTotal +=$item->invoiceAmount}}
                    <tr style="border-top: 2px solid #333 !important;border-bottom: 2px solid #333 !important;">
                        <td>{{$x}}</td>
                        <td>{{$item->glCode}}</td>
                        <td>{{$item->glCodeDes}}</td>
                        @if($request->isProjectBase && $request->isPerforma == 0)
                            <td>
                                @if(isset($item->project) && $item->project != null)
                                    {{$item->project->projectCode}} - {{$item->project->description}}
                                @endif
                            </td>
                        @endif
                        <td class="text-left">{{isset($item->department->ServiceLineDes)?$item->department->ServiceLineDes:''}}</td>
                        <td style="text-align: left">{{isset($item->unit->UnitShortCode)?$item->unit->UnitShortCode:''}}</td>
                        <td class="text-center" style="text-align: right">{{number_format($item->invoiceQty,2)}}</td>
                        <td class="text-right">{{number_format($item->salesPrice,$numberFormatting)}}</td>
                        <td class="text-right">{{number_format($item->discountPercentage,2)}}</td>
                        <td class="text-right">{{number_format($item->discountAmountLine,$numberFormatting)}}</td>
                        <td class="text-right">{{number_format($item->unitCost,$numberFormatting)}}</td>
                        <td class="text-right">{{number_format($item->VATAmountLocal,$numberFormatting)}}</td>
                        <td class="text-right">{{number_format($item->invoiceAmount,$numberFormatting)}}</td>
                    </tr>
                    {{ $x++ }}
                @endforeach
                </tbody>

            </table>
        @endif

        @if ($request->template == 2 && isset($request->item_invoice) && $request->item_invoice)
            <table class="table table-bordered" style="width: 100%;">
                <thead>
                <tr class="theme-tr-head">
                    <th style="width:2%"></th>
                    <th style="text-align: center">{{__('custom.description')}}</th>
                    @if($request->isProjectBase && $request->isPerforma == 2)
                        <th style="text-align: center">{{__('custom.project')}}</th>
                    @endif
                    <th style="text-align: center">{{__('custom.part_no_ref_number')}}</th>
                    <th style="text-align: center">{{__('custom.uom')}}</th>
                    <th style="text-align: center">{{__('custom.quantity')}}</th>
                    <th style="text-align: center">{{__('custom.sales_price')}}</th>
                    <th style="text-align: center">{{__('custom.dis_percent')}}</th>
                    <th style="text-align: center">{{__('custom.discount_amount')}}</th>
                    <th style="text-align: center">{{__('custom.selling_unit_price')}}</th>
                    <th style="text-align: center">{{__('custom.taxable_amount')}} ({{empty($request->currency) ? '' : $request->currency->CurrencyCode}})</th>
                    <th style="text-align: center">{{__('custom.taxable_rate')}}</th>
                    <th style="text-align: center">{{__('custom.tax')}} ({{empty($request->currency) ? '' : $request->currency->CurrencyCode}})</th>
                    <th style="text-align: center">{{__('custom.total_amount')}} ({{empty($request->currency) ? '' : $request->currency->CurrencyCode}})</th>
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
                            <tr style="border-top: 2px solid #333 !important;border-bottom: 2px solid #333 !important;">
                                <td>{{$x}}</td>
                                <td>{{$item->itemPrimaryCode.' - '.$item->itemDescription}}</td>
                                @if($request->isProjectBase && $request->isPerforma == 2)
                                    <td>
                                        @if($request->isProjectBase && $request->isPerforma == 2 && isset($item->project) && $item->project != null)
                                            {{$item->project->projectCode}} - {{$item->project->description}}
                                        @endif
                                    </td>
                                @endif
                                <td class="text-center" style="text-align: center">{{$item->part_no}}</td>
                                <td style="text-align: left">{{isset($item->uom_issuing->UnitShortCode)?$item->uom_issuing->UnitShortCode:''}}</td>
                                <td class="text-right" style="text-align: right">{{$item->qtyIssued}}</td>
                                <td class="text-right">{{number_format($item->salesPrice,$numberFormatting)}}</td>
                                <td class="text-right">{{number_format($item->discountPercentage,2)}}</td>
                                <td class="text-right">{{number_format($item->discountAmount,$numberFormatting)}}</td>
                                <td class="text-right">{{number_format($item->sellingCostAfterMargin,$numberFormatting)}}</td>
                                <td  class="text-right">{{number_format($item->VATAmount,$numberFormatting)}}</td>
                                <td  class="text-right">{{$item->VATPercentage}}</td>
                                <td  class="text-right">{{number_format(($item->VATAmount * $item->qtyIssued),$numberFormatting)}}</td>
                                <td class="text-right">{{number_format($item->sellingTotal,$numberFormatting)}}</td>
                            </tr>
                            {{ $x++ }}
                        @endif
                    @endforeach
                @endif
                </tbody>

            </table>
        @endif
    </div>
    <div class="row">
        <table style="width:100%;" class="table table-bordered">
            <tbody>
            <tr>
                <td style="border:none !important; width: 40%">
                    &nbsp;&nbsp;&nbsp;
                </td>
                <td class="text-left" style="border:none !important;width: 30%">
                        <span class="font-weight-bold" style="font-size: 11.5px">
                            {{__('custom.subtotal_excluding_vat')}}
                        </span>
                </td>

                <td class="text-right"
                    style="font-size: 11.5px;border-left: 1px #EBEBEB !important;border-right: 1px #EBEBEB !important;width: 30%"><span
                            class="font-weight-bold">@if ($request->invoicedetails){{number_format($directTraSubTotal, $numberFormatting)}}@endif</span>
                </td>
            </tr>

            {{$totalVATAmount = (($request->tax && $request->tax->amount) ? $request->tax->amount : 0)}}
            {{$directTraSubTotal+= $totalVATAmount}}
            <tr>
                <td style="border:none !important;width: 40%">
                    &nbsp;
                </td>
                <td class="text-left" style="border:none !important;width: 30%">
                        <span class="font-weight-bold" style="font-size: 11.5px">
                            {{__('custom.total_vat')}} ({{empty($request->currency) ? '' : $request->currency->CurrencyCode}}) ({{round( ( ($request->tax && $request->tax->taxPercent ) ? $request->tax->taxPercent : 0 ), 2)}} %)
                        </span>
                </td>
                <td class="text-right"
                    style="font-size: 11.5px;border-left: 1px #EBEBEB !important;border-right: 1px #EBEBEB !important;width: 30%"><span class="font-weight-bold">{{number_format($totalVATAmount, $numberFormatting)}}</span>
                </td>
            </tr>

            <tr>
                <td  style="border:none !important;width: 40%">
                    &nbsp;
                </td>
                <td class="text-left" style="border:none !important;width: 30%">
                        <span class="font-weight-bold" style="font-size: 11.5px">
                            {{__('custom.total_amount_payable')}}
                        </span>
                </td>
                <td class="text-right"
                    style="font-size: 11.5px;border-left: 1px #EBEBEB !important;border-right: 1px #EBEBEB !important;width: 30%"><span
                            class="font-weight-bold">{{number_format($directTraSubTotal, $numberFormatting)}}</span>
                </td>
            </tr>
            <tr>
                <td  style="border:none !important;width: 40%">
                    &nbsp;
                </td>
                <td class="text-left" style="border:none !important;width: 30%">
                        <span class="font-weight-bold" style="font-size: 11.5px">
                            {{__('custom.total_amount_payable_in_word')}}
                        </span>
                </td>
                <td class="text-right"
                    style="font-size: 11.5px;border-left: 1px #EBEBEB !important;border-right: 1px #EBEBEB !important;width: 30%">
                    <span
                            class="font-weight-bold">                                
                            {{$request->amount_word}}
                            @if ($request->floatAmt > 0)
                            {{__('custom.and')}}
                            {{$request->floatAmt}}/@if($request->currency->DecimalPlaces == 3)1000 @else 100 @endif
                            @endif

                            {{__('custom.only')}}
                    </span>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

<div id="footer">
    @if($request->line_invoiceDetails)
        <div class="" style="">
            @else
                <div class="" style="">
                    @endif
                    <table>
                        <tr>
                            <td width="100%" colspan="2"><span class="font-weight-bold" style="text-decoration: underline;">{{__('custom.remittance_details')}}  </span></td>
                        </tr>
                        <tr>
                            <td width="100%"><span class="font-weight-bold">{{__('custom.bank')}}: </span>
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
                            <td width="100%"><span class="font-weight-bold">{{__('custom.branch')}}: </span>
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
                            <td width="100%"><span class="font-weight-bold">{{__('custom.account_no')}}: </span>
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
                            <td width="100%"><span class="font-weight-bold">{{__('custom.swift_code')}}: </span>
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
                                <td width="50%" style="vertical-align: top;">
                                    <span class="font-weight-bold">{{__('custom.approved_by')}}:</span><br>
                                    @foreach ($request->approved_by as $det)
                                        @if($det->employee)
                                            {{$det->employee->empFullName }}
                                            <br>
                                            @if($det->employee)
                                                {{ \App\helper\Helper::convertDateWithTime($det->approvedDate)}}
                                            @endif
                                        @endif
                                        <br>
                                        <br>
                                    @endforeach
                                </td>
                            </tr>
                        </table>
                    </div>
            @endif

            <!--    <table style="width:100%;">

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
                    </table> -->
        </div>
</div>








