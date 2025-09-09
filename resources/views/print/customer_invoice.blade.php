<style type="text/css">
    <!--
    @page {
        margin: 20px 30px 220px;
    }

    /* RTL Support for Arabic */
    @if(app()->getLocale() == 'ar')
    body {
        direction: rtl;
        text-align: right;
    }
    
    .rtl-text-left {
        text-align: right !important;
    }
    
    .rtl-text-right {
        text-align: left !important;
    }
    
    .rtl-float-left {
        float: right !important;
    }
    
    .rtl-float-right {
        float: left !important;
    }
    
    .rtl-margin-left {
        margin-right: 0 !important;
        margin-left: auto !important;
    }
    
    .rtl-margin-right {
        margin-left: 0 !important;
        margin-right: auto !important;
    }
    
    .rtl-padding-left {
        padding-right: 0 !important;
        padding-left: auto !important;
    }
    
    .rtl-padding-right {
        padding-left: 0 !important;
        padding-right: auto !important;
    }
    
    table {
        direction: rtl;
    }
    
    .table th, .table td {
        text-align: right;
    }
    
    .text-right {
        text-align: left !important;
    }
    
    .text-left {
        text-align: right !important;
    }
    @endif

    #footer {
        position: fixed;
        left: 0px;
        bottom: 0px;
        right: 0px;
        height: 0px;
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
                            {{$request->CompanyName}}
                        </h3>

                        <h3 class="font-weight-bold">
                            @if($request->is_pdo_vendor)
                                VAT
                            @endif
                            Invoice
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
            <tr>
            <td style="width: 40%">
                <fieldset class="scheduler-border" style="background-color: #f1f1f1">
                    <legend class="scheduler-border" style="background-color: white;border: 1px solid black">Customer
                        Details
                    </legend>
                    <br>

                    <table style="width: 100%; !important">
                        <tr>
                            <td><b>Name Of Customer </b></td>
                            <td>:@if($request->line_customerShortCode)
                                    {{$request->customer->CutomerCode}} -
                                @endif
                                {{$request->customer->ReportTitle}}</td>
                        </tr>


                        <tr>
                            <td><b>Customer Address </b></td>
                            <td>:
                                {{$request->customer->customerAddress1}}</td>
                        </tr>
                        <tr>
                            <td><b>Customer telephone </b></td>
                            <td>: {{isset($request->CustomerContactDetails->contactPersonTelephone)?$request->CustomerContactDetails->contactPersonTelephone:' '}}</td>
                        </tr>
                        <tr>
                            <td><b>Customer Fax </b></td>
                            <td>: {{isset($request->CustomerContactDetails->contactPersonFax)?$request->CustomerContactDetails->contactPersonFax:' '}}</td>
                        </tr>
                        <tr>
                            <td><b>Customer VATIN</b></td>
                            <td>:
                                {{$request->vatNumber}}</td>
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
                            <td width="120px"><span class="font-weight-bold">Date Of Supply</span></td>
                            <td width="10px"><span class="font-weight-bold">-</span></td>
                            <td><span>
                                 @if(!empty($request->date_of_supply))
                                        {{\App\helper\Helper::dateFormat($request->date_of_supply) }}
                                    @endif
                            </span></td>
                        </tr>
                        @if($request->line_performaCode)
                            <tr>
                                <td width="120px"><span class="font-weight-bold">Proforma Invoice No</span></td>
                                <td width="10px"><span class="font-weight-bold">-</span></td>
                                <td><span>{{$request->invoicedetail->performadetails->performaCode}}</span></td>
                            </tr>
                        @endif
                        @if($request->line_seNo)
                            <tr>
                                <td width="120px"><span class="font-weight-bold">SE No</span></td>
                                <td width="10px"><span class="font-weight-bold">-</span></td>
                                <td><span>{{$request->wanNO}}</span></td>
                            </tr>
                        @endif
                        @if($request->line_dueDate)
                            <tr>
                                <td width="120px"><span class="font-weight-bold">Due Date</span></td>
                                <td width="10px"><span class="font-weight-bold">-</span></td>
                                <td><span>
                                     @if(!empty($request->invoiceDueDate))
                                            {{\App\helper\Helper::dateFormat($request->invoiceDueDate)}}
                                        @endif
                            </span></td>
                            </tr>
                        @endif
                        @if ($request->line_contractNo)
                            <tr>
                                <td width="120px"><span
                                            class="font-weight-bold">Contract @if($request->line_paymentTerms) Ref
                                        No @endif </span></td>
                                <td width="10px"><span class="font-weight-bold">-</span></td>

                                @if (isset($request->invoicedetails[0]->clientContractID))
                                    <td><span>{{$request->invoicedetails[0]->clientContractID}}</span></td>
                                @else
                                    <td><span></span></td>
                                @endif
                            </tr>
                        @endif

                        @if ($request->line_poNumber)
                            <tr>
                                <td width="120px"><span class="font-weight-bold">PO Number</span></td>
                                <td width="10px"><span class="font-weight-bold">-</span></td>
                                <td>{{$request->PONumber}}</td>

                            </tr>
                        @endif
                        @if($request->line_paymentTerms)
                            <tr>
                                <td width="120px"><span class="font-weight-bold">Payment Terms</span></td>
                                <td width="10px"><span class="font-weight-bold">-</span></td>
                                <td>{{$request->paymentInDaysForJob}} Days</td>

                            </tr>
                        @endif
                        @if ($request->line_unit)
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
                        @endif
                        @if ($request->is_pdo_vendor)
                            <tr>
                                <td width="120px"><span class="font-weight-bold">VAT Number</span></td>
                                <td width="10px"><span class="font-weight-bold">-</span></td>
                                <td><span>{{$request->vatNumber}}</span></td>
                            </tr>
                        @endif
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
            <b><span>{{isset($request->invoicedetail->billmaster->ticketmaster->rig->RigDescription)?$request->invoicedetail->billmaster->ticketmaster->rig->RigDescription:''}}</span> |
                <span> {{isset($request->invoicedetail->billmaster->ticketmaster->regNo)?$request->invoicedetail->billmaster->ticketmaster->regNo:''}}</span></b>
        </div>
    @else
        <div class="row" style="">
            <b>Comments : </b> {!! nl2br($request->comments) !!}
        </div>
    @endif
    <div class="row">
        <div style="text-align: right"><b>Currency
                : {{empty($request->currency) ? '' : $request->currency->CurrencyCode}} </b></div>
    </div>
    <div class="row">

        @if($request->linePdoinvoiceDetails)

            <table class="table table-bordered table-striped table-sm" style="width: 100%;">
                <thead>
                <tr class="">

                    <th style="text-align: center">Well</th>
                    <th style="text-align: center">Network</th>
                    <th style="text-align: center">SE</th>
                    <th style="text-align: right">Amount</th>
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
                    <th style="text-align: center">Client Ref</th>
                    @if($request->is_po_in_line)
                        <th style="text-align: center">PO Line Item</th>
                    @endif
                    <th style="text-align: center">Details</th>
                    <th style="text-align: center">Qty</th>
                    <th style="text-align: center">Unit Rate</th>
                    <th style="text-align: right">Amount</th>
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
                        <td style="width: 8%;text-align: center">{{number_format($item->qty,2)}}</td>
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
                    <th style=" text-align: center">Details</th>


                    <th style="width:140px;text-align: right">Total Amount</th>
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

        @php
            $currencyCode = empty($request->currency) ? '' : $request->currency->CurrencyCode;
            $decimalPlaces = empty($request->currency) ? 2 : $request->currency->DecimalPlaces;
        @endphp

        @if(in_array($request->isPerforma, [2, 3, 4, 5]))
            @if ($request)
                    <table class="table table-bordered" style="width: 100%;">
                        <thead>
                            <tr>
                                <th colspan="5" style="text-align: center">Item Details</th>
                                @if($request->salesType == 3)
                                    <th colspan="9" style="text-align: center">Price ({{ $currencyCode }})</th>
                                @else
                                    <th colspan="8" style="text-align: center">Price ({{ $currencyCode }})</th>
                                @endif
                            </tr>
                            <tr class="theme-tr-head">
                                <th style="text-align: center">#</th>
                                <th style="text-align: center">Description</th>
                                <th style="text-align: center">Project</th>
                                <th style="text-align: center">Ref No</th>
                                <th style="text-align: center">UOM</th>
                                <th style="text-align: center">QTY</th>
                                @if($request->salesType == 3)
                                    <th style="text-align: center">User QTY</th>
                                @endif
                                <th style="text-align: center">Sales Price</th>
                                <th style="text-align: center">Dis %</th>
                                <th style="text-align: center">Discount Amount</th>
                                <th style="text-align: center">Selling Unit Price</th>
                                <th style="text-align: center">Taxable Amount</th>
                                <th style="text-align: center">VAT</th>
                                <th style="text-align: center">Net Amount ({{ $currencyCode }})</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{$x=1}}
                            {{$directTraSubTotal=0}}
                        @foreach ($request->issue_item_details as $item)
                            {{$directTraSubTotal +=$item->sellingTotal}}
                            <tr style="border-top: 2px solid #333 !important;border-bottom: 2px solid #333 !important;">
                                <td>{{$x}}</td>
                                <td class="text-left">{{$item->itemPrimaryCode .' - '.$item->itemDescription}}</td>
                                <td class="text-left">  @if($item->project)
                                    {{$item->project->projectCode.' - '.$item->project->description}} @else - @endif
                                </td>
                                <td class="text-left">{{$item->part_no}}</td>
                                <td class="text-left">{{$item->uom_issuing->UnitShortCode}}</td>
                                <td class="text-right">{{$item->qtyIssuedDefaultMeasure}}</td>
                                @if($request->salesType == 3)
                                    <td class="text-right">{{$item->userQty}}</td>
                                @endif
                                <td class="text-right">{{number_format($item->salesPrice, $decimalPlaces)}}</td>
                                <td class="text-right">{{$item->discountPercentage}}</td>
                                <td class="text-right">{{number_format($item->discountAmount, $decimalPlaces)}}</td>
                                <td class="text-right">{{number_format($item->sellingCostAfterMargin, $decimalPlaces)}}</td>
                                <td class="text-right">{{number_format($item->taxable_amount, $decimalPlaces)}}</td>
                                <td class="text-right">{{number_format($item->VATAmount, $decimalPlaces)}}</td>
                                <td class="text-right">{{number_format($item->sellingTotal, $decimalPlaces)}}</td>
                            </tr>
                            {{ $x++ }}
                        @endforeach
                        </tbody>

                    </table>
                @endif
        @else
            @if ($request->template <> 1 && !$request->line_invoiceDetails)
                    <table class="table table-bordered" style="width: 100%;">
                        <thead>
                        <tr class="theme-tr-head">
                            <th style="width:3%"></th>
                            <th style="width:10%;text-align: center">GL Code</th>
                            <th style="width:40%;text-align: center">GL Code Description</th>
                            <th style="width:20%;text-align: center">Segment</th>
                            <th style="width:10%;text-align: center">UoM</th>
                            <th style="width:10%;text-align: center">QTY</th>
                            <th style="width:10%;text-align: center">Unit Rate</th>
                            <th style="width:10%;text-align: center">Total Amount</th>
                        </tr>
                        </thead>
                        <tbody>
                        {{$x=1}}
                        {{$directTraSubTotal=0}}
                        @foreach ($request->invoicedetails as $item)
                            {{$directTraSubTotal +=$item->invoiceAmount}}
                            <tr style="border-top: 2px solid #333 !important;border-bottom: 2px solid #333 !important;">
                                <td>{{$x}}</td>
                                <td class="text-left">{{$item->glCode}}</td>
                                <td class="text-left">{{$item->glCodeDes}}</td>
                                <td class="text-left">{{isset($item->department->ServiceLineDes)?$item->department->ServiceLineDes:''}}</td>
                                <td class="text-left">{{$item->unit->UnitShortCode}}</td>
                                <td class="text-right">{{number_format($item->invoiceQty,2)}}</td>
                                <td class="text-right">{{number_format($item->unitCost, $decimalPlaces)}}</td>
                                <td class="text-right">{{number_format($item->invoiceAmount, $decimalPlaces)}}</td>
                            </tr>
                            {{ $x++ }}
                        @endforeach
                        </tbody>

                    </table>
                @endif
        @endif
    </div>

    <div class="row">
        <table style="width:100%;" class="table table-bordered">
            <tbody>
                <tr>
                    <td style="width: 60%;border:none !important;"></td>
                    <td class="text-left" style="border:none !important;">
                        <span class="font-weight-bold" style="border-bottom: none !important; font-size: 11.5px;">Total:</span>
                    </td>
                    <td class="text-right" style="font-size: 11.5px; border-left: 1px solid #EBEBEB !important; border-right: 1px solid #EBEBEB !important;">
                        <span class="font-weight-bold">
                            @if ($request->invoicedetails)
                                {{ number_format($directTraSubTotal, $decimalPlaces) }}
                            @endif
                        </span>
                    </td>
                </tr>

            @if ($request->tax)
                {{$directTraSubTotal += $request->tax->amount}}
                <tr>
                    <td style="width: 60%;border:none !important;"></td>
                    <td class="text-left" style="border:none !important;">
                        <span class="font-weight-bold" style="font-size: 11.5px;">
                            VAT Amount ({{ number_format($request->tax->taxPercent, $decimalPlaces) }}%)
                        </span>
                    </td>
                    <td class="text-right" style="font-size: 11.5px; border-left: 1px solid #EBEBEB !important; border-right: 1px solid #EBEBEB !important;">
                        <span class="font-weight-bold">
                            {{ number_format($request->tax->amount, $decimalPlaces) }}
                        </span>
                    </td>
                </tr>

                <tr>
                    <td style="width: 60%;border:none !important;"></td>
                    <td class="text-left" style="border:none !important;">
                        <span class="font-weight-bold" style="font-size: 11.5px;">Net Amount</span>
                    </td>
                    <td class="text-right" style="font-size: 11.5px; border-left: 1px solid #EBEBEB !important; border-right: 1px solid #EBEBEB !important; background-color: #EBEBEB;">
                        <span class="font-weight-bold">
                            {{ number_format($directTraSubTotal, $decimalPlaces) }}
                        </span>
                    </td>
                </tr>

                <tr>
                    <td style="width: 60%;border:none !important;"></td>
                    <td class="text-left" style="border:none !important;">
                        <span class="font-weight-bold" style="font-size: 11.5px;">Net Amount in Word</span>
                    </td>
                    <td class="text-right" style="font-size: 11.5px; border-left: 1px solid #EBEBEB !important; border-right: 1px solid #EBEBEB !important; background-color: #EBEBEB;">
                        <span class="font-weight-bold">
                            {{$request->amount_word}}
                            @if ($request->floatAmt > 0)
                                and {{ $request->floatAmt }} / {{ $decimalPlaces == 3 ? '1000' : '100' }}
                            @endif
                            only
                        </span>
                    </td>
                </tr>
            @endif
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
                            <td width="100px"><span class="font-weight-bold">Remittance Details </span></td>
                            <td>-</td>
                        </tr>
                        <tr>
                            <td width="100px"><span class="font-weight-bold">Bank Name </span></td>
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
                            <td width="100px"><span class="font-weight-bold">Ac Num </span></td>
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
                                {{-- <td width="15%">
                                    <span class="font-weight-bold">Prepared By :</span>
                                </td>
                                <td width="35%">
                                    @if($request->createduser)
                                        {{$request->createduser->empName}}
                                    @endif
                                </td> --}}
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









