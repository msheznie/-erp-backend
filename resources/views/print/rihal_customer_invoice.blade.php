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
        font-size: 9px;
        font-family: Arial, Helvetica, sans-serif;
    }

    .table th {
        border: 1px solid rgb(253, 254, 255) !important;
    }

    .table th, .table td {
        padding: 3px !important;
        vertical-align: top;
        border-bottom: 1px solid rgb(253, 254, 255) !important;
    }

    .table th {
        background-color: #EBEBEB !important;
    }

    tfoot > tr > td {
        border: 1px solid rgb(253, 254, 255);
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
                <td width="40%" style="text-align: right;white-space: nowrap">
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
    <div class="row underline">

    </div>
    <div class="row">
        <table  style="width: 100%; margin-top: -15px; margin-bottom: -15px!important;">
            <tr>
                <td width="100%" style="text-align: center;white-space: nowrap">
                        <h4 class="text-center">
                            Tax Invoice
                        </h4>
                </td>
            </tr>
        </table>

        <table class="head_font" style="width: 100%">
            <tr>
                <td style="width: 65%; text-align: left;vertical-align: top;">
                    <table  style="width: 100%">
                        <tr>
                            <td style="width: 23% !important;"><b>Customer Name </b></td>
                            <td style="width: 1% !important; vertical-align: top;">:</td>
                            <td>@if($request->line_customerShortCode)
                                    {{$request->customer->CutomerCode}} -
                                @endif
                                {{$request->customer->ReportTitle}}</td>
                        </tr>
                        <tr>
                            <td style="width: 23% !important; vertical-align: top;"><b>Customer Address </b></td>
                            <td style="width: 2% !important; vertical-align: top;">:</td>
                            <td>
                                {{$request->customer->customerAddress1}}</td>
                        </tr>
                        <tr>
                            <td style="width: 23% !important;"><b>Contact Person </b></td>
                            <td style="width: 2% !important; vertical-align: top;">:</td>
                            <td>{{isset($request->CustomerContactDetails->contactPersonName)?$request->CustomerContactDetails->contactPersonName:' '}}</td>
                        </tr>
                        <tr>
                            <td style="width: 23% !important;"><b>Customer VATIN</b></td>
                            <td style="width: 2% !important; vertical-align: top;">:</td>
                            <td>
                                {{$request->vatNumber}}</td>
                        </tr>
                        <tr>
                            <td style="width: 23% !important;"><b>Contact Person Tel</b></td>
                            <td style="width: 2% !important; vertical-align: top;">:</td>
                            <td>{{isset($request->CustomerContactDetails->contactPersonTelephone)?$request->CustomerContactDetails->contactPersonTelephone:' '}}</td>
                        </tr>

                        <tr>
                            <td style="width: 23% !important;"><b>Invoice Due Date </b></td>
                            <td style="width: 2% !important; vertical-align: top;">:</td>

                            <td> @if(!empty($request->invoiceDueDate))
                                    {{\App\helper\Helper::dateFormat($request->invoiceDueDate) }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 23% !important;"><b>Segment </b></td>
                            <td style="width: 2% !important; vertical-align: top;">:</td>

                            <td>@if(!empty($request->segment->ServiceLineDes))
                                    {{$request->segment->ServiceLineDes}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 23% !important;"><b>Narration </b></td>
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
                            <td style="width: 38% !important;"><b>Invoice number </b></td>
                            <td>: {{$request->bookingInvCode}}</td>
                        </tr>
                        <tr>
                            <td style="width: 38% !important;"><b>Document Date </b></td>
                            <td>: @if(!empty($request->bookingDate))
                                    {{\App\helper\Helper::dateFormat($request->bookingDate) }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 38% !important;"><b>Reference Number </b></td>
                            <td>: @if(!empty($request->customerInvoiceNo))
                                {{$request->customerInvoiceNo}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 38% !important;"><b>PO Number </b></td>
                            <td>: @if(!empty($request->PONumber))
                                    {{$request->PONumber}}
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <td style="width: 38% !important;"><b>Currency </b></td>
                            <td>: @if(!empty($request->currency->CurrencyName))
                                    {{$request->currency->CurrencyName}} ({{$request->currency->CurrencyCode}})
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 38% !important;"><b>VATIN </b></td>
                            <td>: @if(!empty($request->vatRegistratonNumber))
                                {{$request->vatRegistratonNumber}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 38% !important;"><b>Invoice Date </b></td>
                            <td>: @if(!empty($request->customerInvoiceDate))
                                    {{\App\helper\Helper::dateFormat($request->customerInvoiceDate) }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 38% !important;"><b>Date Of Supply </b></td>
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

    {{$directTraSubTotal=0}}
    {{$numberFormatting=empty($request->currency) ? 2 : $request->currency->DecimalPlaces}}

    <div class="row">
        @if($request->linePdoinvoiceDetails)

            <table class="table table-bordered table-striped table-sm normal_font" style="width: 100%;">
                <thead>
                <tr class="">

                    <th style="text-align: center">Well</th>
                    <th style="text-align: center">Network</th>
                    <th style="text-align: center">SE</th>
                    <th style="text-align: center">Total Amount({{empty($request->currency) ? '' : $request->currency->CurrencyCode}})</th>
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
                    <th style="text-align: center">Client Ref</th>
                    @if($request->is_po_in_line)
                        <th style="text-align: center">PO Line Item</th>
                    @endif
                    <th style="text-align: center">Details</th>
                    <th style="text-align: center">UOM</th>
                    <th style="text-align: center">Qty</th>
                    <th style="text-align: center">Unit Rate</th>
                    <th style="text-align: center">Total Amount ({{empty($request->currency) ? '' : $request->currency->CurrencyCode}})</th>
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
                    <th style=" text-align: center">Details</th>


                    <th style="width:140px;text-align: center">Total Amount ({{empty($request->currency) ? '' : $request->currency->CurrencyCode}})</th>
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
                            <th style="text-align: center" colspan="6">Item Details</th>
                        @else
                            <th style="text-align: center" colspan="5">Item Details</th>
                        @endif
                        <th style="text-align: center" colspan="8">Price 
                            @if(!empty($request->currency->CurrencyCode))
                                ({{$request->currency->CurrencyCode}})
                            @endif
                        </th>
                    </tr>
                </thead>
                <thead>
                <tr class="theme-tr-head">
                    <th style="width:3%">#</th>
                    <th style="text-align: center">GL Code</th>
                    <th style="text-align: center">Description</th>
                    @if($request->isProjectBase && $request->isPerforma == 0)
                        <th style="text-align: center">Project</th>
                    @endif
                    <th style="text-align: center">UOM</th>
                    <th style="text-align: center">QTY</th>
                    <th style="text-align: center">Sales Price</th>
                    <th style="text-align: center">Dis <br/>%</th>
                    <th style="text-align: center">Discount Amount</th>
                    <th style="text-align: center">Selling Unit Price</th>
                    <th style="text-align: center">Taxable Amount</th>
                    <th style="text-align: center">VAT</th>
                    <th style="text-align: center">VAT Amount</th>
                    <th style="text-align: center">Net Amount @if(!empty($request->currency->CurrencyCode))
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

                @if(!empty($company->localcurrency->currencyID) && !empty($request->currency->currencyID) && $company->localcurrency->currencyID != $request->currency->currencyID)
                <tr style="border-top: 2px solid #333 !important;border-bottom: 2px solid #333 !important;">
                    <td class="text-center" colspan="6" style="text-align: center"></td>
                    <td class="text-center" colspan="3" style="text-align: center"><B>Grand Total @if(!empty($request->currency->CurrencyCode))({{$request->currency->CurrencyCode}}) @endif</B></td>
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
                            <th style="text-align: center" colspan="6">Item Details</th>
                        @else
                            <th style="text-align: center" colspan="5">Item Details</th>
                        @endif
                        <th style="text-align: center" colspan="8">Price 
                            @if(!empty($request->currency->CurrencyCode))
                                ({{$request->currency->CurrencyCode}})
                            @endif
                        </th>
                    </tr>
                </thead>
                <thead>
                <tr class="theme-tr-head">
                    <th style="width:2%">#</th>
                    <th style="text-align: center">Description</th>
                    @if($request->isProjectBase && $request->isPerforma == 2)
                        <th style="text-align: center">Project</th>
                    @endif
                    <th style="text-align: center">Ref. No</th>
                    <th style="text-align: center">UOM</th>
                    <th style="text-align: center">QTY</th>
                    <th style="text-align: center">Sales Price</th>
                    <th style="text-align: center">Dis %</th>
                    <th style="text-align: center">Discount Amount</th>
                    <th style="text-align: center">Selling Unit Price</th>
                    <th style="text-align: center">Taxable Amount</th>
                    <th style="text-align: center">VAT</th>
                    <th style="text-align: center">VAT Amount</th>
                    <th style="text-align: center">Net Amount @if(!empty($request->currency->CurrencyCode))
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

                    @if(!empty($company->localcurrency->currencyID) && !empty($request->currency->currencyID) && $company->localcurrency->currencyID != $request->currency->currencyID)
                        <tr style="border-top: 2px solid #333 !important;border-bottom: 2px solid #333 !important;">
                            <td class="text-center" colspan="6" style="text-align: center"></td>
                            <td class="text-center" colspan="3" style="text-align: center"><B>Grand Total @if(!empty($request->currency->CurrencyCode))({{$request->currency->CurrencyCode}}) @endif</B></td>
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

    @if(!empty($company->localcurrency->currencyID) && !empty($request->currency->currencyID) && $company->localcurrency->currencyID != $request->currency->currencyID)
        <p class="normal_font"><B>(Grand Total in @if(!empty($request->currency->CurrencyCode)){{$request->currency->CurrencyCode}} @endif :   {{$request->amount_word}}
                @if ($request->floatAmt > 0)
                    and
                    {{$request->floatAmt}} /@if($request->currency->DecimalPlaces == 3)1000 @else 100 @endif
                @endif
                only)</B></p>
        @endif


        @php $totalVATAmount = (($request->tax && $request->tax->amount) ? $request->tax->amount : 0) @endphp

        @if(!empty($company->localcurrency->currencyID) && !empty($request->currency->currencyID) && $company->localcurrency->currencyID != $request->currency->currencyID)
        <table class="table table-bordered table-striped table-sm normal_font" style="width: 100%;">
            <thead>
            <tr class="">
                <th style="text-align: center">Conversion Rate</th>
                <th style="text-align: center">Currency</th>
                <th style="text-align: center">Taxable Amount</th>
                <th style="text-align: center">VAT Amount</th>
                <th style="text-align: center">Grand Total</th>
            </tr>
            </thead>
            <tbody>                
                <tr style="border-top: 2px solid #333 !important;border-bottom: 2px solid #333 !important;background-color: white">


                    <td style="width: 15%" class="text-left">Conversion Rate
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
                    @if ($request->isPerforma == 1)
                        <td style="width: 10%" class="text-right">@if ($request->invoicedetails){{number_format($directTraSubTotal-$totalVATAmount, $numberFormatting)}}@endif</td>
                    @else
                        <td style="width: 10%" class="text-right">@if ($request->invoicedetails){{number_format($directTraSubTotal, $numberFormatting)}}@endif</td>
                    @endif

                    {{$totalVATAmountCurrency = (($request->tax && $request->tax->amount) ? $request->tax->amount : 0)}}
                    {{$directTraSubTotalCurrency = $directTraSubTotal}}
                    {{$directTraSubTotalCurrency += $totalVATAmountCurrency}}
                    <td style="width: 10%" class="text-right">{{ number_format($totalVATAmountCurrency, $numberFormatting) }}</td>

                    @if ($request->isPerforma == 1)
                        <td style="width: 10%" class="text-right">@if ($request->invoicedetails){{number_format($directTraSubTotal, $numberFormatting)}}@endif</td>
                    @else
                        <td style="width: 10%" class="text-right">@if ($request->invoicedetails){{number_format($directTraSubTotalCurrency, $numberFormatting)}}@endif</td>
                    @endif

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
                                @if ($request->isPerforma == 1)
                                    {{number_format(($directTraSubTotal-$totalVATAmount) / $request->localCurrencyER, $company->localcurrency->DecimalPlaces)}}
                                @else
                                    {{number_format($directTraSubTotal / $request->localCurrencyER, $company->localcurrency->DecimalPlaces)}}
                                @endif
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
                            @if ($request->isPerforma == 1)
                                {{ number_format($directTraSubTotal / $request->localCurrencyER, $company->localcurrency->DecimalPlaces) }}
                            @else
                                {{ number_format($directTraSubTotalLocal / $request->localCurrencyER, $company->localcurrency->DecimalPlaces) }}
                            @endif
                        @endif
                    </td>
                </tr>
            </tbody>

        </table>
        @endif
    </div>



                @php $totalVATAmount = (($request->tax && $request->tax->amount) ? $request->tax->amount : 0) @endphp

    @if(!empty($company->localcurrency->currencyID) && !empty($request->currency->currencyID) && $company->localcurrency->currencyID != $request->currency->currencyID)
    <div class="row">
        <br/>
        <table style="width:100%;" class="table table-bordered normal_font">
            <tbody>
            <tr>
                <td class="text-left" style="border:none !important; width: 15%">
                        <span class="font-weight-bold">
                            Sub Total @if(!empty($company->localcurrency->CurrencyCode))
                                ({{ $company->localcurrency->CurrencyCode }})
                            @endif
                        </span>
                </td>

                <td class="text-left" style="border:none !important">
                    <span class="font-weight-bold">: @if ($request->invoicedetails)
                            @if(!empty($request->localCurrencyER) && !empty($company->localcurrency->DecimalPlaces))
                                @if ($request->isPerforma == 1)
                                    {{number_format(($directTraSubTotal-$totalVATAmount) / $request->localCurrencyER, $company->localcurrency->DecimalPlaces)}}
                                @else
                                    {{number_format($directTraSubTotal / $request->localCurrencyER, $company->localcurrency->DecimalPlaces)}}
                                @endif
                            @endif
                        @endif
                    </span>
                </td>
                <td style="border:none !important; width: 40%">
                    &nbsp;
                </td>
                <td class="text-left" style="border:none !important">
                        <span class="font-weight-bold">
                            Sub Total ({{empty($request->currency) ? '' : $request->currency->CurrencyCode}})
                        </span>
                </td>

                <td class="text-left" style="border:none !important">
                    @if ($request->isPerforma == 1)
                        <span class="font-weight-bold">: @if ($request->invoicedetails){{number_format($directTraSubTotal-$totalVATAmount, $numberFormatting)}}@endif</span>
                    @else
                        <span class="font-weight-bold">: @if ($request->invoicedetails){{number_format($directTraSubTotal, $numberFormatting)}}@endif</span>
                    @endif
                </td>
            </tr>

            {{$totalVATAmount = (($request->tax && $request->tax->amount) ? $request->tax->amount : 0)}}
            {{$directTraSubTotal+= $totalVATAmount}}
            <tr>
                <td class="text-left" style="border:none !important;width: 15%">
                        <span class="font-weight-bold">
                            VAT  @if(!empty($company->localcurrency->CurrencyCode))
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
                            VAT ({{empty($request->currency) ? '' : $request->currency->CurrencyCode}})
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
                            Total  @if(!empty($company->localcurrency->CurrencyCode))
                                ({{ $company->localcurrency->CurrencyCode }})
                            @endif
                        </span>
                </td>
                <td class="text-left"
                    style="border:none !important"><span
                            class="font-weight-bold">:
                          @if(!empty($request->localCurrencyER) && !empty($company->localcurrency->DecimalPlaces))
                            @if ($request->isPerforma == 1)
                                {{number_format(($directTraSubTotal - $totalVATAmount) / $request->localCurrencyER, $company->localcurrency->DecimalPlaces)}}
                            @else
                                {{number_format($directTraSubTotal / $request->localCurrencyER, $company->localcurrency->DecimalPlaces)}}
                            @endif
                           @endif
                    </span>
                </td>
                <td  style="border:none !important;width: 40%">
                    &nbsp;
                </td>
                <td class="text-left" style="border:none !important;width: 15%">
                        <span class="font-weight-bold">
                            Total ({{empty($request->currency) ? '' : $request->currency->CurrencyCode}})
                        </span>
                </td>
                <td class="text-left" style="border:none !important">
                    @if ($request->isPerforma == 1)
                        <span class="font-weight-bold">: {{number_format($directTraSubTotal - $totalVATAmount, $numberFormatting)}}</span>
                    @else
                        <span class="font-weight-bold">: {{number_format($directTraSubTotal, $numberFormatting)}}</span>
                    @endif
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
                        <td style="border:none !important; width: 40%">
                            &nbsp;&nbsp;&nbsp;
                        </td>
                        <td class="text-left" style="border:none !important;width: 30%">
                                <span class="font-weight-bold">
                                    Sub Total (Excluding VAT)
                                </span>
                        </td>

                        <td class="text-right"
                            style="border-left: 1px #EBEBEB !important;border-right: 1px #EBEBEB !important;width: 30%"><span
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
                                <span class="font-weight-bold">
                                    Total VAT ({{empty($request->currency) ? '' : $request->currency->CurrencyCode}}) ({{round( ( ($request->tax && $request->tax->taxPercent ) ? $request->tax->taxPercent : 0 ), 2)}} %)
                                </span>
                        </td>
                        <td class="text-right"
                            style="border-left: 1px #EBEBEB !important;border-right: 1px #EBEBEB !important;width: 30%"><span class="font-weight-bold">{{number_format($totalVATAmount, $numberFormatting)}}</span>
                        </td>
                    </tr>

                    <tr>
                        <td  style="border:none !important;width: 40%">
                            &nbsp;
                        </td>
                        <td class="text-left" style="border:none !important;width: 30%">
                                <span class="font-weight-bold">
                                    Total Amount Payable
                                </span>
                        </td>
                        <td class="text-right"
                            style="border-left: 1px #EBEBEB !important;border-right: 1px #EBEBEB !important;width: 30%"><span
                                    class="font-weight-bold">{{number_format($directTraSubTotal, $numberFormatting)}}</span>
                        </td>
                    </tr>
                    <tr>
                        <td  style="border:none !important;width: 40%">
                            &nbsp;
                        </td>
                        <td class="text-left" style="border:none !important;width: 30%">
                                <span class="font-weight-bold">
                                    Total Amount Payable in word
                                </span>
                        </td>
                        <td class="text-right"
                            style="border-left: 1px #EBEBEB !important;border-right: 1px #EBEBEB !important;width: 30%">
                            <span
                                    class="font-weight-bold">
                                    {{$request->amount_word}}
                                @if ($request->floatAmt > 0)
                                    and
                                    {{$request->floatAmt}}/@if($request->currency->DecimalPlaces == 3)1000 @else 100 @endif
                                @endif

                                    only
                            </span>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
    @endif
    <br/>
</div>

<div id="footer">
    @if($request->line_invoiceDetails)
        <div class="">
            @else
                <div class=""  style="margin-top: 20px;">
                    @endif
                    <table class="normal_font">

                        <tr>
                            <td width="100px"><span class="font-weight-bold">Bank : </span>
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
                            <td width="100px"><span class="font-weight-bold">Branch : </span>
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
                            <td width="100px"><span class="font-weight-bold">Account No : </span>
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
                            <td width="100px"><span class="font-weight-bold">SWIFT Code : </span>
                                @if($request->secondaryLogoCompanySystemID)
                                    @if($secondaryBankAccount->contract && $secondaryBankAccount->contract->secondary_bank_account)
                                        {{$secondaryBankAccount->contract->secondary_bank_account->accountSwiftCode}}
                                    @endif
                                @else
                                    {{($request->bankaccount) ? $request->bankaccount->accountSwiftCode : ''}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td width="600px"><span class="font-weight-bold">Amount in words : </span>
                                {{$request->amount_word}}@if ($request->floatAmt > 0) and {{$request->floatAmt}}/@if($request->currency->DecimalPlaces == 3)1000 @else 100 @endif @endif only
                            </td>
                        </tr>
                    </table>
                </div>
                <br>
                @if(!$request->line_rentalPeriod)
                    <div >
                        <table class="normal_font" width="100%">
                            <tr style="width: 100%">
                                <td width="50%" style="vertical-align: top;">
                                    <span class="font-weight-bold">Electronically Approved By</span>
                                </td>
                                <td width="50%" style="vertical-align: top;">
                                    <span class="font-weight-bold">:
                                        @php
                                            $employee = \App\Models\Employee::find($request->approvedByUserSystemID);
                                        @endphp
                                        @if($employee)
                                        {{ $employee->empName }}
                                        @endif
                                    </span>
                                </td>
                            </tr>
                            <tr  style="width: 100%">
                                <td width="50%" style="vertical-align: top;">
                                    <span class="font-weight-bold">Electronically Approved Date</span>
                                </td>
                                <td width="50%" style="vertical-align: top;">
                                    <span class="font-weight-bold">:


                                    {{ \App\helper\Helper::convertDateWithTime($request->approvedDate)}}

                                    </span>
                                </td>
                            </tr>
                        </table>
                        <hr>
                        <table class="normal_font" width="100%">
                            <tr  style="width: 100%">
                                <td width="33%" style="vertical-align: top;">
                                    <span class="font-weight-bold"></span>
                                </td>
                                <td width="33%" style="vertical-align: top; text-align:center;">
                                    <span class="font-weight-bold">This is a computer generated document and does not require signature</span>
                                </td>
                                <td width="5%" style="vertical-align: top;">
                                </td>
                                <td width="27%" style="vertical-align: top;">
                                    <span class="font-weight-bold">{{date('l jS \of F Y h:i:s A')}}</span>
                                </td>
                            </tr>
                        </table>
                    </div>
            @endif
        </div>
</div>








