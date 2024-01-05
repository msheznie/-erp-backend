<style type="text/css">
    <!--
    @page {
        margin: 20px 30px 220px;
    }

    #footer {
        position: fixed;
        left: 0px;
        bottom: 10px;
        right: 0px;
        height: 0px;
        /*font-size: 10px;*/
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
        /* background-color: #EBEBEB !important; */
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
        font-size: 9px;
        font-family: Arial, Helvetica, sans-serif;
    }

    .table th {
        border: 1px solid  !important;
    }

    .table th, .table td {
        padding: 3px !important;
        vertical-align: top;
        border-bottom: 1px solid  !important;
    }

    .table th {
        background-color:  !important;
    }

    tfoot > tr > td {
        border: 1px solid ;
    }

    .text-right {
        text-align: right !important;
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

    .underline {
    flex-grow: 1;
    border-bottom: 1px solid black;
    margin-left: 5px;
    }

    .head_font {
        font-size: 9px;
        font-family: Arial, Helvetica, sans-serif;
    }

    .normal_font {
        font-size: 9px;
        font-family: Arial, Helvetica, sans-serif;
    }


</style>

<div class="content">
    <div class="row">
        <table class="table_height head_font" style="width:100%">
            <tr>
                <td width="20%">
                    @if($request->logo)
                           @if($type == 1)
                            <img style="height: 130px" src="{{$request->companyLogo}}">
                          @else
                            image not found
                          @endif

                    @endif
                </td>

                <td width="40%">
                </td>
                <td width="40%" style="text-align: left;white-space: nowrap">
                    <table style="width: 100%">
                        <tr>
                            <td style="font-weight: bold">{{$request->CompanyName}}</th>
                        <tr>
                            <td>{{$request->CompanyAddress}},</td>
                        </tr>
                        <tr>
                            <td>{{$request->CompanyCountry}}</td>
                        </tr>
                        <tr>
                            <td><b>Tel</b>&nbsp;&nbsp;:&nbsp;{{$request->CompanyTelephone}}, <b>Fax</b>&nbsp;:&nbsp;{{$request->CompanyFax}}</td>
                        </tr>
                    </table>
                    <br>
                </td>
            </tr>
        </table>
    </div>
    <div class="row">
        <br>
    </div>
    <div class="row underline">

    </div>
    <div class="row">
        <table  style="width: 100%; margin-top: -10px; margin-bottom: -15px!important;">
            <tr>
                <td width="100%" style="text-align: center;white-space: nowrap">
                        <h4 class="text-center" style="margin-top: 1px">
                            <br/>
                            {{ __('custom.tax_invoice') }}
                        </h4>
                </td>
            </tr>
        </table>

        <table class="head_font" style="width: 100%">
            <tr>
                <td style="width: 65%; text-align: left;vertical-align: top;">
                    <table  style="width: 100%">
                        <tr>
                            <td style="width: 23% !important;"><b>{{ __('custom.customer_name') }} </b></td>
                            <td style="width: 1% !important; vertical-align: top;">:</td>
                            <td>@if($request->line_customerShortCode)
                                    {{$request->customer->CutomerCode}} -
                                @endif
                                {{$request->customer->ReportTitle}}</td>
                        </tr>
                        <tr>
                            <td style="width: 23% !important; vertical-align: top;"><b>{{ __('custom.customer_address') }} </b></td>
                            <td style="width: 2% !important; vertical-align: top;">:</td>
                            <td>
                                {{$request->customer->customerAddress1}}</td>
                        </tr>
                        <tr>
                            <td style="width: 23% !important;"><b>{{ __('custom.contact_person') }} </b></td>
                            <td style="width: 2% !important; vertical-align: top;">:</td>
                            <td>{{isset($request->CustomerContactDetails->contactPersonName)?$request->CustomerContactDetails->contactPersonName:' '}}</td>
                        </tr>
                        <tr>
                            <td style="width: 23% !important;"><b>{{ __('custom.contact_vatin') }}</b></td>
                            <td style="width: 2% !important; vertical-align: top;">:</td>
                            <td>
                                {{$request->vatNumber}}</td>
                        </tr>
                        <tr>
                            <td style="width: 23% !important;"><b>{{ __('custom.contact_person_tel') }}</b></td>
                            <td style="width: 2% !important; vertical-align: top;">:</td>
                            <td>{{isset($request->CustomerContactDetails->contactPersonTelephone)?$request->CustomerContactDetails->contactPersonTelephone:' '}}</td>
                        </tr>

                        <tr>
                            <td style="width: 23% !important;"><b>{{ __('custom.invoice_due_date') }}</b></td>
                            <td style="width: 2% !important; vertical-align: top;">:</td>

                            <td> @if(!empty($request->invoiceDueDate))
                                    {{\App\helper\Helper::dateFormat($request->invoiceDueDate) }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 23% !important;"><b>{{ __('custom.segment') }} </b></td>
                            <td style="width: 2% !important; vertical-align: top;">:</td>

                            <td>@if(!empty($request->segment->ServiceLineDes))
                                    {{$request->segment->ServiceLineDes}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 23% !important;"><b>{{ __('custom.narration') }} </b></td>
                            <td style="width: 2% !important; vertical-align: top;">:</td>

                            <td>@if(!empty($request->comments))
                                    {{$request->comments}}
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>

                <td style="width: 35%; text-align: left;vertical-align: top;">
                    <table class="head_font" style="width: 100%">
                        <tr>
                            <td style="width: 38% !important;"><b>{{ __('custom.invoice_number') }} </b></td>
                            <td>: {{$request->bookingInvCode}}</td>
                        </tr>
                        <tr>
                            <td style="width: 38% !important;"><b>{{ __('custom.document_date') }} </b></td>
                            <td>: @if(!empty($request->bookingDate))
                                    {{\App\helper\Helper::dateFormat($request->bookingDate) }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 38% !important;"><b>{{ __('custom.reference_number') }} </b></td>
                            <td>: @if(!empty($request->customerInvoiceNo))
                                {{$request->customerInvoiceNo}}
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <td style="width: 38% !important;"><b>{{ __('custom.currency') }} </b></td>
                            <td>: @if(!empty($request->currency->CurrencyName))
                                    {{$request->currency->CurrencyName}} ({{$request->currency->CurrencyCode}})
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 38% !important;"><b>{{ __('custom.invoice_date') }} </b></td>
                            <td>: @if(!empty($request->customerInvoiceDate))
                                    {{\App\helper\Helper::dateFormat($request->customerInvoiceDate) }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 38% !important;"><b>{{ __('custom.date_of_supply') }} </b></td>
                            <td>: @if(!empty($request->date_of_supply))
                                    {{\App\helper\Helper::dateFormat($request->date_of_supply) }}
                                @endif
                            </td>
                        </tr>

                    </table>
                </td>
            <tr>
        </table>
    </div>

    <br>

    <div class="row">
        @if($request->linePdoinvoiceDetails)

            <table class="table table-bordered table-striped table-sm normal_font" style="width: 100%;">
                <thead>
                <tr class="">

                    <th style="text-align: center">{{ __('custom.well') }}</th>
                    <th style="text-align: center">{{ __('custom.network') }}</th>
                    <th style="text-align: center">{{ __('custom.se') }}</th>
                    <th style="text-align: center">{{ __('custom.total_amount') }}({{empty($request->currency) ? '' : $request->currency->CurrencyCode}})</th>
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
            <table class="table table-bordered table-striped table-sm normal_font" style="width: 100%;">
                <thead>
                <tr class="">
                    <th style="width:1%"></th>
                    <th style="text-align: center">{{ __('custom.client_ref') }}</th>
                    @if($request->is_po_in_line)
                        <th style="text-align: center">{{ __('custom.po_line_item') }}</th>
                    @endif
                    <th style="text-align: center">{{ __('custom.details') }}</th>
                    <th style="text-align: center">{{ __('custom.uom') }}</th>
                    <th style="text-align: center">{{ __('custom.qty') }}</th>
                    <th style="text-align: center">{{ __('custom.unit_rate') }}</th>
                    <th style="text-align: center">{{ __('custom.total_amount') }} ({{empty($request->currency) ? '' : $request->currency->CurrencyCode}})</th>
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

            <table class="table table-bordered table-striped table-sm normal_font" style="width: 100%;">
                <thead>
                <tr class="theme-tr-head">
                    <th style="width:1%"></th>
                    <th style=" text-align: center">{{ __('custom.details') }}</th>


                    <th style="width:140px;text-align: center">{{ __('custom.total_amount') }}({{empty($request->currency) ? '' : $request->currency->CurrencyCode}})</th>
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
            <table class="table table-bordered normal_font" style="width: 100%;">
                <thead>
                    <tr class="theme-tr-head">
                        @if($request->isProjectBase && $request->isPerforma == 0)
                            <th style="text-align: center" colspan="6">{{ __('custom.item_details') }}</th>
                        @else
                            <th style="text-align: center" colspan="5">{{ __('custom.item_details') }}</th>
                        @endif
                        <th style="text-align: center" colspan="8">{{ __('custom.price') }}
                            @if(!empty($request->currency->CurrencyCode))
                                ({{$request->currency->CurrencyCode}})
                            @endif
                        </th>
                    </tr>
                </thead>
                <thead>
                <tr class="theme-tr-head">
                    <th style="width:3%">#</th>
                    <th style="text-align: center">{{ __('custom.gl_code') }}</th>
                    <th style="text-align: center">{{ __('custom.description') }}</th>
                    @if($request->isProjectBase && $request->isPerforma == 0)
                        <th style="text-align: center">{{ __('custom.project') }}</th>
                    @endif
                    <th style="text-align: center">{{ __('custom.uom') }}</th>
                    <th style="text-align: center">{{ __('custom.qty') }}</th>
                    <th style="text-align: center">{{ __('custom.sales_price') }}</th>
                    <th style="text-align: center">{{ __('custom.dis') }} <br/>%</th>
                    <th style="text-align: center">{{ __('custom.discount_amount') }}</th>
                    <th style="text-align: center">{{ __('custom.selling_unit_price') }}</th>
                    <th style="text-align: center">{{ __('custom.taxable_amount') }}</th>
                    <th style="text-align: center">{{ __('custom.vat') }}</th>
                    <th style="text-align: center">{{ __('custom.vat_amount') }}</th>
                    <th style="text-align: center">{{ __('custom.net_amount') }} @if(!empty($request->currency->CurrencyCode))
                                                                            ({{$request->currency->CurrencyCode}})
                                                                        @endif
                    </th>
                </tr>
                </thead>
                <tbody>
                {{$decimal = 2}}
                {{$x=1}}
                {{$directTraSubTotal=0}}
                {{$netAmount=0}}
                {{$numberFormatting=empty($request->currency) ? 2 : $request->currency->DecimalPlaces}}
                @foreach ($request->invoicedetails as $item)
                    {{$directTraSubTotal +=$item->invoiceAmount}}
                    {{$amountIncludingVAT = $item->invoiceAmount+$item->VATAmountLocal}}
                    <tr style="border-top: 2px solid #333 !important;border-bottom: 2px solid #333 !important;">
                        <td>{{$x}}</td>
                        <td>{{$item->glCode}}</td>
                        <td>{{$item->comments}}</td>
                        @if($request->isProjectBase && $request->isPerforma == 0)
                            <td>
                                @if(isset($item->project) && $item->project != null)
                                    {{$item->project->projectCode}} - {{$item->project->description}}
                                @endif
                            </td>
                        @endif
                        <td style="text-align: left">{{isset($item->unit->UnitShortCode)?$item->unit->UnitShortCode:''}}</td>
                        <td class="text-center" style="text-align: right">{{number_format($item->invoiceQty,2)}}</td>
                        <td class="text-right">{{number_format($item->salesPrice,$numberFormatting)}}</td>
                        <td class="text-right">{{number_format($item->discountPercentage,2)}}</td>
                        <td class="text-right">{{number_format($item->discountAmountLine,$numberFormatting)}}</td>
                        <td class="text-right">{{number_format($item->unitCost,$numberFormatting)}}</td>
                        <td class="text-right">{{number_format($item->invoiceAmount,$numberFormatting)}}</td>
                        <td class="text-right">{{$item->VATPercentage}}%</td>
                        <td class="text-right">{{number_format(($item->VATAmount * $item->invoiceQty),$numberFormatting)}}</td>
                        <td class="text-right">{{number_format(($item->invoiceAmount + ($item->VATAmount * $item->invoiceQty)),$numberFormatting)}}</td>
                    </tr>
                    {{ $x++ }}
                @endforeach
                <?php
                $totalVATAmount = (($request->tax && $request->tax->amount) ? $request->tax->amount : 0);

                $company = \App\Models\Company::with(['localcurrency'])->find($request->companySystemID);
                ?>

                @if(!empty($company->localcurrency->currencyID) && !empty($request->currency->currencyID) && $company->localcurrency->currencyID != $request->currency->currencyID && $totalVATAmount > 0)
                <tr style="border-top: 2px solid #333 !important;border-bottom: 2px solid #333 !important;">
                    <td class="text-center" colspan="6" style="text-align: center"></td>
                    <td class="text-center" colspan="3" style="text-align: center"><B>{{ __('custom.grand_total') }} @if(!empty($request->currency->CurrencyCode))({{$request->currency->CurrencyCode}}) @endif</B></td>
                    <td class="text-center" style="text-align: right"><B>@if ($request->invoicedetails){{number_format($directTraSubTotal, $numberFormatting)}}@endif</B></td>
                    {{$totalVATAmount = (($request->tax && $request->tax->amount) ? $request->tax->amount : 0)}}
                    {{$netAmount = $totalVATAmount + $directTraSubTotal}}

                    <td class="text-center" style="text-align: center"></td>

                    <td class="text-center" style="text-align: right"><B>{{number_format($totalVATAmount, $numberFormatting)}}</B></td>
                    <td class="text-center" style="text-align: right"><B>{{number_format($netAmount, $numberFormatting)}}</B></td>
                </tr>
                @endif
                </tbody>

            </table>
        @endif

        @if ($request->template == 2 && isset($request->item_invoice) && $request->item_invoice)
            <table class="table table-bordered normal_font" style="width: 100%;">
                <thead>
                    <tr class="theme-tr-head">
                        @if($request->isProjectBase && $request->isPerforma == 2)
                            <th style="text-align: center" colspan="6">{{ __('custom.item_details') }}</th>
                        @else
                            <th style="text-align: center" colspan="5">{{ __('custom.item_details') }}</th>
                        @endif
                        <th style="text-align: center" colspan="8">{{ __('custom.price') }}
                            @if(!empty($request->currency->CurrencyCode))
                                ({{$request->currency->CurrencyCode}})
                            @endif
                        </th>
                    </tr>
                </thead>
                <thead>
                <tr class="theme-tr-head">
                    <th style="width:2%">#</th>
                    <th style="text-align: center">{{ __('custom.description') }}</th>
                    @if($request->isProjectBase && $request->isPerforma == 2)
                        <th style="text-align: center">{{ __('custom.project') }}</th>
                    @endif
                    <th style="text-align: center">{{ __('custom.ref_no') }}</th>
                    <th style="text-align: center">{{ __('custom.uom') }}</th>
                    <th style="text-align: center">{{ __('custom.qty') }}</th>
                    <th style="text-align: center">{{ __('custom.sales_price') }}</th>
                    <th style="text-align: center">{{ __('custom.dis') }} %</th>
                    <th style="text-align: center">{{ __('custom.discount_amount') }}</th>
                    <th style="text-align: center">{{ __('custom.selling_unit_price') }}</th>
                    <th style="text-align: center">{{ __('custom.taxable_amount') }}</th>
                    <th style="text-align: center">{{ __('custom.vat') }}</th>
                    <th style="text-align: center">{{ __('custom.vat_amount') }}</th>
                    <th style="text-align: center">{{ __('custom.net_amount') }} @if(!empty($request->currency->CurrencyCode))
                                                                            ({{$request->currency->CurrencyCode}})
                                                                        @endif</th>
                </tr>
                </thead>
                <tbody>
                {{$decimal = 2}}
                {{$x=1}}
                {{$directTraSubTotal=0}}
                {{$netAmount=0}}
                {{$numberFormatting=empty($request->currency) ? 2 : $request->currency->DecimalPlaces}}
                @if(!empty($request->issue_item_details))
                    @foreach ($request->issue_item_details as $item)
                        @if ($item->sellingTotal != 0)
                            {{$directTraSubTotal +=$item->sellingTotal}}
                            <tr style="border-top: 2px solid #333 !important;border-bottom: 2px solid #333 !important;">
                                <td>{{$x}}</td>
                                <td>{{$item->itemPrimaryCode.' - '.$item->itemDescription}}<br>
                                    {{$item->comments}}
                                </td>
                                @if($request->isProjectBase && $request->isPerforma == 2)
                                    <td>
                                        @if(isset($item->project) && $item->project != null)
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
                                <td class="text-right">{{number_format($item->sellingTotal,$numberFormatting)}}</td>
                                <td class="text-right">{{$item->VATPercentage}}%</td>
                                <td class="text-right">{{number_format(($item->VATAmount * $item->qtyIssued),$numberFormatting)}}</td>
                                <td class="text-right">{{number_format(($item->sellingTotal + ($item->VATAmount * $item->qtyIssued)),$numberFormatting)}}</td>
                            </tr>
                            {{ $x++ }}
                        @endif
                    @endforeach
                    <?php
                    $totalVATAmount = (($request->tax && $request->tax->amount) ? $request->tax->amount : 0);

                    $company = \App\Models\Company::with(['localcurrency'])->find($request->companySystemID);
                    ?>

                    @if(!empty($company->localcurrency->currencyID) && !empty($request->currency->currencyID) && $company->localcurrency->currencyID != $request->currency->currencyID && $totalVATAmount > 0)
                        <tr style="border-top: 2px solid #333 !important;border-bottom: 2px solid #333 !important;">
                            <td class="text-center" colspan="6" style="text-align: center"></td>
                            <td class="text-center" colspan="3" style="text-align: center"><B>{{ __('custom.grand_total') }} @if(!empty($request->currency->CurrencyCode))({{$request->currency->CurrencyCode}}) @endif</B></td>
                            <td class="text-center" style="text-align: right"><B>@if ($request->invoicedetails){{number_format($directTraSubTotal, $numberFormatting)}}@endif</B></td>
                            {{$totalVATAmount = (($request->tax && $request->tax->amount) ? $request->tax->amount : 0)}}
                            {{$netAmount = $totalVATAmount + $directTraSubTotal}}

                            <td class="text-right" style="text-align: right"></td>

                            <td class="text-right" style="text-align: right"><B>{{number_format($totalVATAmount, $numberFormatting)}}</B></td>
                            <td class="text-right" style="text-align: right"><B>{{number_format($netAmount, $numberFormatting)}}</B></td>
                        </tr>
                    @endif
                @endif
                </tbody>

            </table>
        @endif
    </div>


    <div class="row">
        <br/>
        <br/>
        <?php
        $totalVATAmount = (($request->tax && $request->tax->amount) ? $request->tax->amount : 0);

        $company = \App\Models\Company::with(['localcurrency'])->find($request->companySystemID);
        ?>

    @if(!empty($company->localcurrency->currencyID) && !empty($request->currency->currencyID) && $company->localcurrency->currencyID != $request->currency->currencyID && $totalVATAmount > 0)
        <p class="normal_font"><B>({{ __('custom.grand_total_in') }} @if(!empty($request->currency->CurrencyCode)){{$request->currency->CurrencyCode}} @endif :   {{$request->amount_word}}
                @if ($request->floatAmt > 0)
                    {{ __('custom.and') }}
                    {{$request->floatAmt}} /@if($request->currency->DecimalPlaces == 3)1000 @else 100 @endif
                @endif
                {{ __('custom.only') }})</B></p>
        @endif


        @php $totalVATAmount = (($request->tax && $request->tax->amount) ? $request->tax->amount : 0) @endphp

        @if(!empty($company->localcurrency->currencyID) && !empty($request->currency->currencyID) && $company->localcurrency->currencyID != $request->currency->currencyID && $totalVATAmount > 0)
        <table class="table table-bordered table-striped table-sm normal_font" style="width: 100%;">
            <thead>
            <tr class="">
                <th style="text-align: center">{{ __('custom.conversion_rate') }}</th>
                <th style="text-align: center">{{ __('custom.currency') }}</th>
                <th style="text-align: center">{{ __('custom.taxable_amount') }}</th>
                <th style="text-align: center">{{ __('custom.vat_amount') }}</th>
                <th style="text-align: center">{{ __('custom.grand_total') }}</th>
            </tr>
            </thead>
            <tbody>
                <tr style="border-top: 2px solid #333 !important;border-bottom: 2px solid #333 !important;background-color: white">


                    <td style="width: 15%" class="text-left">{{ __('custom.conversion_rate') }}
                        @if(!empty($company->localcurrency->CurrencyCode))
                            ({{ $company->localcurrency->CurrencyCode }}) :
                        @endif
                        @if(!empty($request->localCurrencyER))
                            {{ round(1 / $request->localCurrencyER,4)}}
                        @endif  </td>
                    <td style="width: 5%" class="text-right">
                        @if(!empty($request->currency->CurrencyCode))
                            {{$request->currency->CurrencyCode}}
                        @endif
                    </td>
                    <td style="width: 10%" class="text-right">@if ($request->invoicedetails){{number_format($directTraSubTotal, $numberFormatting)}}@endif</td>

                    {{$totalVATAmountCurrency = (($request->tax && $request->tax->amount) ? $request->tax->amount : 0)}}
                    {{$directTraSubTotalCurrency = $directTraSubTotal}}
                    {{$directTraSubTotalCurrency += $totalVATAmountCurrency}}
                    <td style="width: 10%" class="text-right">{{ number_format($totalVATAmountCurrency, $numberFormatting) }}</td>
                    <td style="width: 10%" class="text-right">{{ number_format($directTraSubTotalCurrency, $numberFormatting) }}</td>
                </tr>
                <tr style="border-top: 2px solid #333 !important;border-bottom: 2px solid #333 !important;background-color: white">


                    <td style="width: 15%" class="text-left"> </td>
                    <td style="width: 5%" class="text-right">
                        @if(!empty($company->localcurrency->CurrencyCode))
                            {{ $company->localcurrency->CurrencyCode }}
                        @endif
                    </td>
                    <td style="width: 10%" class="text-right">@if ($request->invoicedetails)
                            @if(!empty($request->localCurrencyER) && !empty($company->localcurrency->DecimalPlaces))
                            {{number_format($directTraSubTotal / $request->localCurrencyER, $company->localcurrency->DecimalPlaces)}}
                            @endif
                        @endif
                    </td>
                    {{$totalVATAmountLocal = (($request->tax && $request->tax->amount) ? $request->tax->amount : 0)}}
                    {{$directTraSubTotalLocal = $directTraSubTotal}}
                    {{$directTraSubTotalLocal+= $totalVATAmountLocal}}
                    <td style="width: 10%" class="text-right">
                        @if(!empty($request->localCurrencyER) && !empty($company->localcurrency->DecimalPlaces))
                        {{ number_format($totalVATAmountLocal / $request->localCurrencyER, $company->localcurrency->DecimalPlaces) }}
                        @endif
                    </td>
                    <td style="width: 10%" class="text-right">
                        @if(!empty($request->localCurrencyER) && !empty($company->localcurrency->DecimalPlaces))
                        {{ number_format($directTraSubTotalLocal / $request->localCurrencyER, $company->localcurrency->DecimalPlaces) }}
                        @endif
                    </td>
                </tr>
            </tbody>

        </table>
        @endif
    </div>



                @php $totalVATAmount = (($request->tax && $request->tax->amount) ? $request->tax->amount : 0) @endphp

    @if(!empty($company->localcurrency->currencyID) && !empty($request->currency->currencyID) && $company->localcurrency->currencyID != $request->currency->currencyID && $totalVATAmount > 0)
    <div class="row">
        <br/>
        <table style="width:100%;" class="table table-bordered normal_font">
            <tbody>
            <tr>
                <td class="text-left" style="border:none !important; width: 15%">
                        <span class="font-weight-bold">
                            {{ __('custom.sub_total') }} @if(!empty($company->localcurrency->CurrencyCode))
                                ({{ $company->localcurrency->CurrencyCode }})
                            @endif
                        </span>
                </td>

                <td class="text-left" style="border:none !important">
                    <span class="font-weight-bold">: @if ($request->invoicedetails)
                            @if(!empty($request->localCurrencyER) && !empty($company->localcurrency->DecimalPlaces))
                            {{number_format($directTraSubTotal / $request->localCurrencyER, $company->localcurrency->DecimalPlaces)}}
                            @endif
                        @endif
                    </span>
                </td>
                <td style="border:none !important; width: 40%">
                    &nbsp;
                </td>
                <td class="text-left" style="border:none !important">
                        <span class="font-weight-bold">
                            {{ __('custom.sub_total') }} ({{empty($request->currency) ? '' : $request->currency->CurrencyCode}})
                        </span>
                </td>

                <td class="text-left" style="border:none !important">
                    <span class="font-weight-bold">: @if ($request->invoicedetails){{number_format($directTraSubTotal, $numberFormatting)}}@endif</span>
                </td>
            </tr>

            {{$totalVATAmount = (($request->tax && $request->tax->amount) ? $request->tax->amount : 0)}}
            {{$directTraSubTotal+= $totalVATAmount}}
            <tr>
                <td class="text-left" style="border:none !important;width: 15%">
                        <span class="font-weight-bold">
                            {{ __('custom.vat') }}  @if(!empty($company->localcurrency->CurrencyCode))
                                ({{ $company->localcurrency->CurrencyCode }})
                            @endif
                        </span>
                </td>
                <td class="text-left"
                    style="border:none !important"><span class="font-weight-bold">:
                        @if(!empty($request->localCurrencyER) && !empty($company->localcurrency->DecimalPlaces))
                        {{number_format($totalVATAmount / $request->localCurrencyER, $company->localcurrency->DecimalPlaces)}}
                        @endif
                    </span>
                </td>
                <td style="border:none !important;width: 40%">
                    &nbsp;
                </td>
                <td class="text-left" style="border:none !important;width: 15%">
                        <span class="font-weight-bold">
                            {{ __('custom.vat') }}  ({{empty($request->currency) ? '' : $request->currency->CurrencyCode}})
                        </span>
                </td>
                <td class="text-left"
                    style="border:none !important"><span class="font-weight-bold">:
                        {{number_format($totalVATAmount, $numberFormatting)}}</span>
                </td>
            </tr>

            <tr>
                <td class="text-left" style="border:none !important;width: 15%">
                        <span class="font-weight-bold">
                            {{ __('custom.total') }}   @if(!empty($company->localcurrency->CurrencyCode))
                                ({{ $company->localcurrency->CurrencyCode }})
                            @endif
                        </span>
                </td>
                <td class="text-left"
                    style="border:none !important"><span
                            class="font-weight-bold">:
                          @if(!empty($request->localCurrencyER) && !empty($company->localcurrency->DecimalPlaces))
                        {{number_format($directTraSubTotal / $request->localCurrencyER, $company->localcurrency->DecimalPlaces)}}
                           @endif
                    </span>
                </td>
                <td  style="border:none !important;width: 40%">
                    &nbsp;
                </td>
                <td class="text-left" style="border:none !important;width: 15%">
                        <span class="font-weight-bold">
                            {{ __('custom.total') }}  ({{empty($request->currency) ? '' : $request->currency->CurrencyCode}})
                        </span>
                </td>
                <td class="text-left"
                    style="border:none !important"><span
                            class="font-weight-bold">: {{number_format($directTraSubTotal, $numberFormatting)}}</span>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    @else
            <div class="row">
                <br/>
                <table style="width:100%;" class="table table-bordered normal_font">
                    <tbody>
                    <tr>
                        <td class="font-weight-bold" style="border:none !important; width: 10%">
                            <B>{{ __('custom.bank') }} </B>
                        </td>
                        <td style="border:none !important; width: 35%"> : 
                            @if($request->secondaryLogoCompanySystemID)
                                @if($secondaryBankAccount->contract && $secondaryBankAccount->contract->secondary_bank_account)
                                    {{$secondaryBankAccount->contract->secondary_bank_account->bankName}}
                                @endif
                            @else
                                {{($request->bankaccount) ? $request->bankaccount->bankName : ''}}
                            @endif
                        </td>
                        <td class="text-left" style="border:none !important;width: 25%">
                                <span class="font-weight-bold">
                                    <B>{{ __('custom.sub_total') }}  ({{ __('custom.excluding_vat') }} )</B>
                                </span>
                        </td>

                        <td class="text-right"
                            style="border-left: 1px #EBEBEB !important;border-right: 1px #EBEBEB !important;width: 30%"><span
                                    class="font-weight-bold">@if ($request->invoicedetails)<B>{{number_format($directTraSubTotal, $numberFormatting)}}</B>@endif</span>
                        </td>
                    </tr>

                    {{$totalVATAmount = (($request->tax && $request->tax->amount) ? $request->tax->amount : 0)}}
                    {{$directTraSubTotal+= $totalVATAmount}}
                    <tr>
                        <td class="font-weight-bold" style="border:none !important; width: 10%" >
                            <B>{{ __('custom.branch') }}</B>
                        </td>
                        <td style="border:none !important; width: 35%"> : 
                            @if($request->secondaryLogoCompanySystemID)
                                @if($secondaryBankAccount->contract && $secondaryBankAccount->contract->secondary_bank_account)
                                    {{$secondaryBankAccount->contract->secondary_bank_account->bankBranch}}
                                @endif
                            @else
                                {{($request->bankaccount) ? $request->bankaccount->bankBranch : ''}}
                               @endif
                        </td>
                        <td class="text-left" style="border:none !important;width: 25%">
                                <span class="font-weight-bold">
                                    <B>{{ __('custom.total_vat') }} ({{empty($request->currency) ? '' : $request->currency->CurrencyCode}}) ({{round( ( ($request->tax && $request->tax->taxPercent ) ? $request->tax->taxPercent : 0 ), 2)}} %)</B>
                                </span>
                        </td>
                        <td class="text-right"
                            style="border-left: 1px #EBEBEB !important;border-right: 1px #EBEBEB !important;width: 30%"><span class="font-weight-bold"><B>{{number_format($totalVATAmount, $numberFormatting)}}</B></span>
                        </td>
                    </tr>

                    <tr>
                        <td class="font-weight-bold" style="border:none !important; width: 10%">
                            <B>{{ __('custom.account_no') }}</B>
                        </td>
                        <td style="border:none !important; width: 35%"> : 
                            @if($request->secondaryLogoCompanySystemID)
                                @if($secondaryBankAccount->contract && $secondaryBankAccount->contract->secondary_bank_account)
                                    {{$secondaryBankAccount->contract->secondary_bank_account->AccountNo}}
                                @endif
                            @else
                                {{($request->bankaccount) ? $request->bankaccount->AccountNo : ''}}
                            @endif
                        </td>
                        <td class="text-left" style="border:none !important;width: 25%">
                                <span class="font-weight-bold">
                                    <B>{{ __('custom.total_amount_payable') }}</B>
                                </span>
                        </td>
                        <td class="text-right"
                            style="border-left: 1px #EBEBEB !important;border-right: 1px #EBEBEB !important;width: 30%"><span
                                    class="font-weight-bold"><B>{{number_format($directTraSubTotal, $numberFormatting)}}</B></span>
                        </td>
                    </tr>
                    <tr>
                        <td style="border:none !important; width: 10%">
                            &nbsp;&nbsp;&nbsp;
                        </td>
                        <td style="border:none !important; width: 35%">
                            &nbsp;&nbsp;&nbsp;
                        </td>
                        <td class="text-left" style="border:none !important;width: 25%">
                                <span class="font-weight-bold">
                                    <B>{{ __('custom.total_amount_payable_in_word') }}</B>
                                </span>
                        </td>
                        <td class="text-right"
                            style="border-left: 1px #EBEBEB !important;border-right: 1px #EBEBEB !important;width: 30%">
                            <span class="font-weight-bold">
                                <B>{{$request->amount_word}}</B>
                                @if ($request->floatAmt > 0)
                                    <B>and</B>
                                    <B>{{$request->floatAmt}}</B>/@if($request->currency->DecimalPlaces == 3)<B>1000</B> @else <B>100</B> @endif
                                @endif

                                <B>only</B>
                            </span>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
    @endif
    <br/>
</div>







