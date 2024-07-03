

<style type="text/css">
    <!--
    @page {
        margin-left: 3%;
        margin-right: 3%;
        margin-top: 4%;
    }

    .footer {
        position: absolute;
    }

    body {
        font-size: 11px;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol"
    }

    #watermark h3, .content h3 {
        font-size: 1.53125rem;
    }

     #watermark h6, .content h6 {
        font-size: 0.875rem;
    }

     #watermark h6, .content h6 ,#watermark h3,  .content h3 {
        margin-bottom: 0.1rem;
        font-weight: 500;
        line-height: 1.2;
        color: inherit;
    }

    table > tbody > th > tr > td {
        font-size: 11px;
    }

    .theme-tr-head {
        background-color: #EBEBEB !important;
    }

    .text-left {
        text-align: left;
    }

    table {
        border-collapse: collapse;
    }

    .font-weight-bold {
        font-weight: 700 !important;
    }

    .table th {
        border: 1px solid rgb(127, 127, 127) !important;
    }

    .table th, .table td {
        padding: 0.4rem !important;
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

    .footer {
        bottom: 0;
        height: 40px;
    }

    .footer {
        width: 100%;
        text-align: center;
        position: fixed;
        font-size: 10px;
        padding-top: -20px;
    }

    .pagenum:after {
        content: counter(page);
    }

    .content {
        margin-bottom: 45px;
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
    }

    #watermark {
        height: 1000px;
        opacity: 0.6;
        left: 0;
        transform-origin: 20% 20%;
        z-index: 1000;
    }

    .page_break {
        page-break-before: always;
    }

    #paymentTermsCond .footer {
        display: none;
    }

    .quill-html img {
        max-width: 700px;
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

    .rotate {
        writing-mode: vertical-lr;
        -webkit-transform: rotate(-50deg);
        -moz-transform: rotate(-50deg);
        font-size: 70px;
        color: #ff5454 !important;
    }


</style>
<link href="{{ public_path('assets/css/app.css') }}" rel="stylesheet" type="text/css" />


<div class="content">
    <div class="row">
        <table style="width:100%" class="table_height">
            <tr>
                <td width="20%">
                    <table>
                        <tr>
                            @if($isMergedCompany)
                                <td><img src="{{$secondaryCompany['logo_url']}}" width="180px" height="60px" class="container"></td>
                            @else
                                <td><img src="{{$podata->company->logo_url}}" width="180px" height="60px" class="container"></td>
                            @endif
                        </tr>
                    </table>
                </td>
                <td width="80%">
                    <table>
                        <tr>
                            <td>
                                <h3  style="font-weight: bold; font-size:20px">
                                    @if($isMergedCompany)
                                        {{$secondaryCompany['name']}}
                                    @else
                                        @if ($podata->company)
                                            {{$podata->company->CompanyName}}
                                        @endif
                                    @endif
                                    
                                </h3>
                            </td>
                        </tr>
                    </table>
                    <table>
                        <tr>
                            <td><span  style="font-weight: bold">{{ __('custom.purchase_order') }} {{ __('custom.number') }}</span></td>
                            <td><span  style="font-weight: bold">:</span></td>
                            <td>
                                @if ($podata->purchaseOrderCode)
                                    {{$podata->purchaseOrderCode}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><span  style="font-weight: bold">{{ __('custom.purchase_order') }} {{ __('custom.date') }} </span></td>
                            <td><span  style="font-weight: bold">:</span></td>
                            <td>{{ \App\helper\Helper::dateFormat($podata->createdDateTime)}}</td>
                        </tr>
                        <tr>
                            <td><span  style="font-weight: bold">{{ __('custom.reference_number') }}</span></td>
                            <td><span  style="font-weight: bold">:</span></td>
                            <td>
                                @if ($podata->referenceNumber)
                                    {{$podata->referenceNumber}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><span  style="font-weight: bold">{{ __('custom.vat_no') }} </span></td>
                            <td><span  style="font-weight: bold">:</span></td>
                            <td>
                                @if ($podata->company->vatRegisteredYN == 1)
                                    {{$podata->company->vatRegistratonNumber}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><span  style="font-weight: bold">{{ __('custom.segment') }} </span></td>
                            <td><span  style="font-weight: bold">:</span></td>
                            <td>
                                @if (isset($podata->segment->ServiceLineDes))
                                    {{$podata->segment->ServiceLineDes}}
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
    <hr style="border-top: 2px solid black; height: 2px; color: black">
    
    <table style="width: 100%" class="table_height">
        <tr style="width: 100%">
            <td>
                <div>
                    <span style="font-size: 18px">
                        {{ __('custom.purchase_order') }}
                    </span>
                </div>
            </td>
        </tr>
    </table>
    <br>
    <br>

    <div class="row">
        <table style="width:100%">
            <tr>
                <td style="width: 60%">
                    <table style="width: 100%">
                        <tr>
                            <td colspan="3"><span style="font-size: 13px; font-weight: bold;">{{ __('custom.sold_to') }}:</span></td>
                        </tr>
                        <tr>
                            <td style="width: 100%" colspan="3"><p>
                                    @if($isMergedCompany)
                                        {{$secondaryCompany['name']}}
                                    @else
                                        @if ($podata->company)
                                            {{$podata->company->CompanyName}}
                                        @endif
                                    @endif
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 100%" colspan="3"><p>
                                    @if ($podata->soldToAddressDescriprion)
                                        {!! nl2br($podata->soldToAddressDescriprion) !!}
                                    @endif
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 100%" colspan="3"><span style="font-size: 13px; font-weight: bold;">&nbsp;</span></td>
                        </tr>
                    </table>
                    <table style="width: 100%">
                        <tr>
                            <td style="width: 20%"><span  style="font-weight: bold">{{ __('custom.order_contact') }}</span></td>
                            <td style="width: 2%"><span  style="font-weight: bold">:</span></td>
                            <td style="width: 78%">{{$podata->soldTocontactPersonID}}</td>
                        </tr>
                        <tr>
                            <td style="width: 20%"><span  style="font-weight: bold">{{ __('custom.phone') }}</span></td>
                            <td style="width: 2%"><span  style="font-weight: bold">:</span></td>
                            <td style="width: 78%">{{$podata->soldTocontactPersonTelephone}} </td>
                        </tr>
                        <tr>
                            <td style="width: 20%"><span  style="font-weight: bold">{{ __('custom.fax') }}</span></td>
                            <td style="width: 2%"><span  style="font-weight: bold">:</span></td>
                            <td style="width: 78%">{{$podata->soldTocontactPersonFaxNo}} </td>
                        </tr>
                        <tr>
                            <td style="width: 20%"><span  style="font-weight: bold">{{ __('custom.email') }}</span></td>
                            <td style="width: 2%"><span  style="font-weight: bold">:</span></td>
                            <td style="width: 78%">{{$podata->soldTocontactPersonEmail}} </td>
                        </tr>
                        <tr>
                            <td style="width: 20%"><span  style="font-weight: bold">{{ __('custom.vat_no') }}</span></td>
                            <td style="width: 2%"><span  style="font-weight: bold">:</span></td>
                            <td style="width: 78%">{{$podata->vat_number}} </td>
                        </tr>
                    </table>
                </td>
                <td style="width: 40%">
                    <table style="width:100%">
                        <tr>
                            <td colspan="3"><span style="font-size: 13px; font-weight: bold;">{{ __('custom.supplier') }}:</span></td>
                        </tr>
                        <tr>
                            <td colspan="3">{{$podata->supplierPrimaryCode}}</td>
                        </tr>
                        <tr>
                            <td colspan="3">{{$podata->supplierName}}</td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                {!! nl2br($podata->supplierAddress) !!}
                            </td>
                        </tr>
                        @if($podata->supplierVATEligible)
                        <tr>
                            <td><span  style="font-weight: bold">{{ __('custom.vat') }} #</span></td>
                            <td><span  style="font-weight: bold">:</span></td>
                            <td>
                                @if ($podata->supplier)
                                    {{$podata->supplier->vatNumber}}
                                @endif
                            </td>
                        </tr>
                        @endif
                        <tr>
                            <td><span  style="font-weight: bold">{{ __('custom.contact') }}</span></td>
                            <td><span  style="font-weight: bold">:</span></td>
                            <td>
                                @if ($podata->suppliercontact)
                                    {{$podata->suppliercontact->contactPersonName}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 20%"><span  style="font-weight: bold">{{ __('custom.phone') }}</span></td>
                            <td style="width: 2%"><span  style="font-weight: bold">:</span></td>
                            <td style="width: 78%">
                                @if ($podata->suppliercontact)
                                    {{$podata->suppliercontact->contactPersonTelephone}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 20%"><span  style="font-weight: bold">{{ __('custom.fax') }}</span></td>
                            <td style="width: 2%"><span  style="font-weight: bold">:</span></td>
                            <td style="width: 78%">
                                @if ($podata->suppliercontact)
                                    {{$podata->suppliercontact->contactPersonFax}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 20%"><span  style="font-weight: bold">{{ __('custom.email') }}</span></td>
                            <td style="width: 2%"><span  style="font-weight: bold">:</span></td>
                            <td style="width: 78%">
                                @if ($podata->suppliercontact)
                                    {{$podata->suppliercontact->contactPersonEmail}}
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
    <hr style="border-top: 2px solid black; height: 2px; color: black">
    <div class="row">

        @if ((($podata->poConfirmedYN == 0 || $podata->poConfirmedYN == 1) && $podata->approved == 0 || $podata->poCancelledYN == -1))
        <table style="  height: 1000px; 
                    opacity: 0.6; 
                    left: 0; 
                    transform-origin: 20% 20%; 
                    z-index: 1000;
                    position: fixed;
                    width: 100%;
                    height: 100%;
                    padding-top: 31%; margin-bottom: -10%;">
                <tr>
                    <td width="20%">

                    </td>
                    <td width="60%" style="text-align: center; font-weight: bold !important;">
                        <span class="watermarkText" style="font-weight: bold; ">
                            <h3 style=" font-size: 24.5px;
                                        margin-bottom: 0.1rem;
                                        font-weight: 500;
                                        line-height: 1.2;
                                        color: inherit;">
                                @if($podata->poConfirmedYN == 0 && $podata->approved == 0)
                                    {{ __('custom.not_confirmed_&_not_approved') }}<br> {{ __('custom.draft_copy') }}
                                @endif
                                @if($podata->poConfirmedYN == 1 && $podata->approved == 0)
                                        {{ __('custom.confirmed_&_not_approved') }}<br> {{ __('custom.draft_copy') }}
                                @endif
                            </h3>


                                @if($podata->poCancelledYN == -1)
                                    <h1 class="rotate">
                                {{ __('custom.cancelled') }}
                                    </h1>
                                @endif
                        </span>
                    </td>
                    <td width="20%">

                    </td>
                </tr>
            </table>
        @endif

        <table style="width:100%">
            <tr>
                <td style="width: 60%">
                    <table>
                        <tr>
                            <td colspan="3"><span style="font-size: 13px; font-weight: bold;"> {{ __('custom.ship_to') }}:</span></td>
                        </tr>
                        <tr>
                            <td colspan="3"><p>
                                    @if($isMergedCompany)
                                        {{$secondaryCompany['name']}}
                                    @else
                                        @if ($podata->company)
                                            {{$podata->company->CompanyName}}
                                        @endif
                                    @endif
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">{!! nl2br($podata->shippingAddressDescriprion) !!}</td>
                        </tr>
                        <tr>
                            <td colspan="3"><span style="font-size: 13px; font-weight: bold;">&nbsp;</span></td>
                        </tr>
                    </table>
                    <table>
                        <tr>
                            <td style="width: 34%"><span  style="font-weight: bold"> {{ __('custom.ship_contact') }}</span></td>
                            <td style="width: 2%"><span  style="font-weight: bold">:</span></td>
                            <td style="width: 64%">{{$podata->shipTocontactPersonID}} </td>
                        </tr>
                        <tr>
                            <td style="width: 28%"><span  style="font-weight: bold">{{ __('custom.phone') }}</span></td>
                            <td style="width: 2%"><span  style="font-weight: bold">:</span></td>
                            <td style="width: 70%">{{$podata->shipTocontactPersonTelephone}} </td>
                        </tr>
                        <tr>
                            <td style="width: 28%"><span  style="font-weight: bold">{{ __('custom.fax') }}</span></td>
                            <td style="width: 2%"><span  style="font-weight: bold">:</span></td>
                            <td style="width: 70%">{{$podata->shipTocontactPersonFaxNo}} </td>
                        </tr>
                        <tr>
                            <td style="width: 28%"><span  style="font-weight: bold">{{ __('custom.email') }}</span></td>
                            <td style="width: 2%"><span  style="font-weight: bold">:</span></td>
                            <td style="width: 70%">{{$podata->shipTocontactPersonEmail}} </td>
                        </tr>
                    </table>
                </td>
                <td style="width: 40%">
                    <table>
                        <tr>
                            <td colspan="3"><span style="font-size: 13px; font-weight: bold;">{{ __('custom.invoice_to') }}:</span></td>
                        </tr>
                        <tr>
                            <td colspan="3"><p>
                                    @if($isMergedCompany)
                                        {{$secondaryCompany['name']}}
                                    @else
                                        @if ($podata->company)
                                            {{$podata->company->CompanyName}}
                                        @endif
                                    @endif
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">{!! nl2br($podata->invoiceToAddressDescription) !!}</td>
                        </tr>
                        <tr>
                            <td style="width: 20%"><span  style="font-weight: bold">{{ __('custom.payment_contact') }}</span></td>
                            <td style="width: 2%"><span  style="font-weight: bold">:</span></td>
                            <td style="width: 78%">
                                    {{$podata->invoiceTocontactPersonID}}
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 20%"><span  style="font-weight: bold">{{ __('custom.phone') }}</span></td>
                            <td style="width: 2%"><span  style="font-weight: bold">:</span></td>
                            <td style="width: 78%">
                                    {{$podata->invoiceTocontactPersonTelephone}}
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 20%"><span  style="font-weight: bold">{{ __('custom.fax') }}</span></td>
                            <td style="width: 2%"><span  style="font-weight: bold">:</span></td>
                            <td style="width: 78%">
                                    {{$podata->invoiceTocontactPersonFaxNo}}
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 20%"><span  style="font-weight: bold">{{ __('custom.email') }}</span></td>
                            <td style="width: 2%"><span  style="font-weight: bold">:</span></td>
                            <td style="width: 78%">
                                    {{$podata->invoiceTocontactPersonEmail}}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
    <hr style="border-top: 2px solid black; height: 2px; color: black">
    <div class="row">
        <table style="width:100%">
            <tr style="width:88%">
                <td style="width:11%;vertical-align: top;"><span  style="font-weight: bold">{{ __('custom.narration') }}</span></td>
                <td style="width:1%;vertical-align: top;"><span  style="font-weight: bold">:</span></td>
                <td style="width:88%;vertical-align: top;">{!! nl2br($podata->narration) !!}</td>
            </tr>
        </table>
    </div>
    <div class="row">
        <table style="width:100%">
            <tr>
                <td style="width:88%">
                    <table style="padding-bottom: 1%">
                        <tr>
                            <td style="width: 16%"><span  style="font-weight: bold">{{ __('custom.expected_date') }}</span></td>
                            <td style="width: 1%"><span  style="font-weight: bold">:</span></td>
                            <td style="width: 83%">{{ \App\helper\Helper::dateFormat($podata->expectedDeliveryDate)}}</td>
                        </tr>
                    </table>
                </td>
                <td style="width:12%">
                    <table style="padding-bottom: 2%">
                        <tr>
                            <td><span  style="font-weight: bold">{{ __('custom.currency') }}</span></td>
                            <td><span  style="font-weight: bold">:</span></td>
                            <td>
                                @if ($podata->transactioncurrency)
                                    {{$podata->transactioncurrency->CurrencyCode}}
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
    <div class="row">
        <table class="table table-bordered table-striped table-sm">
            <thead>
            <tr style="border-top: 1px solid black;">
                <th style="text-align: center">#</th>
                <th style="text-align: center">{{ __('custom.item_code') }}</th>
                <th style="text-align: center">{{ __('custom.item_description') }}</th>
                @if ($isProjectBase)
                    <th colspan="4" style="text-align: center">{{ __('custom.project') }}</th>
                @endif
                <th style="text-align: center">{{ __('custom.part_no') }} / {{ __('custom.ref_no') }}</th>
                @if($detailComment==1)
                    <th style="text-align: center">{{ __('custom.comments') }}</th>
                @endif
                <th style="text-align: center">{{ __('custom.uom') }}</th>
                <th style="text-align: center">{{ __('custom.qty') }}</th>
                @if(isset($allowAltUom))
                <th style="text-align: center">{{ __('custom.alt_uom') }}</th>
                <th style="text-align: center">{{ __('custom.item_qty') }}</th>
                @endif
                <th style="text-align: center">{{ __('custom.unit_cost') }}</th>
                <th style="text-align: center">{{ __('custom.dis_per_unit') }}</th>
                @if ($podata->isVatEligible)
                  <th style="text-align: center">{{ __('custom.vat_per_unit') }}</th>
                @endif
                <th style="text-align: center">{{ __('custom.net_cost_per_unit') }}</th>
                <th style="text-align: center">{{ __('custom.net_amount') }}</th>
            </tr>
            </thead>
            <tbody style="width: 100%;">
            {{ $subTotal = 0 }}
            {{ $x = 1 }}
            {{ $subColspan = $podata->isVatEligible ? 1 : 0}}
            @foreach ($podata->detail as $det)
                {{ $netUnitCost = 0 }}
                {{ $subTotal += $det->netAmount }}
                {{ $netUnitCost = $det->unitCost - $det->discountAmount + $det->VATAmount }}
                @if($podata->rcmActivated)
                    {{ $netUnitCost = $det->unitCost - $det->discountAmount }}
                @endif

                <tr style="border-bottom: 1px solid black; width: 100%;  background-color:#f9f9f9">
                    <td>{{ $x  }}</td>
                    <td>{{$det->itemPrimaryCode}}</td>
                    <td nobr="true" >{{$det->itemDescription}} <br> {!! nl2br($det->comment) !!}</td>

                    @if ($isProjectBase)
                        @if ($det->project)
                            <td colspan="4" nobr="true">{{$det->project->projectCode}} - {{$det->project->description}}</td>
                        @else
                            <td colspan="4" nobr="true"></td>
                        @endif
                    @endif
                    
                    <td>{{$det->supplierPartNumber}}</td>
                    @if($detailComment==1)
                        <td>{{$det->comment}}</td>
                    @endif
                    <td>{{$det->unit->UnitShortCode}}</td>
                    <td class="text-right">{{$det->noQty}}</td>
                    @if(isset($allowAltUom))
                    <td>
                       @if($det->altUom)
                        {{$det->altUom->UnitShortCode}}
                        @else
                            -
                        @endif
                    </td>
                    <td>{{$det->altUnitValue}}</td>
                    @endif
                    <td class="text-right">{{number_format($det->unitCost, $numberFormatting)}}</td>
                    <td class="text-right">{{number_format($det->discountAmount, $numberFormatting)}}</td>
                    @if ($podata->isVatEligible)
                        <td class="text-right">@if($podata->rcmActivated) {{number_format(0, $numberFormatting)}}@else{{number_format($det->VATAmount, $numberFormatting)}} @endif</td>
                    @endif
                    <td class="text-right">{{number_format($netUnitCost, $numberFormatting)}}</td>
                    <td class="text-right">{{number_format($det->netAmount, $numberFormatting)}}</td>
                </tr>
                {{ $x++ }}
            @endforeach
            @foreach ($addons as $met)
                {{ $subTotal += $met->amount }}
                <tr style="border-bottom: 1px solid black; width: 100%">
                    <td colspan="2"></td>
                    <td>{{$met->category->costCatDes}}</td>
                    <td colspan="{{6 + $subColspan}}"></td>
                    @if(isset($allowAltUom))
                    <td colspan="2"></td>
                    @endif
                    <td class="text-right">{{number_format($met->amount, $numberFormatting)}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="row">
        <table style="width:100%;" class="table table-bordered">
            <tbody>
            <tr>
                <td style="border-bottom: none !important;border-left: none !important;width: 60%;">&nbsp;</td>
                <td class="text-right" style="width: 20%;border: 1px solid rgb(127, 127, 127)!important;">
                    <span style="font-weight: bold; font-size: 11px">{{ __('custom.total_order_amount') }}</span>
                </td>
                <td class="text-right" style="width: 20%;border: 1px solid rgb(127, 127, 127)!important;">
                    <span  style="font-weight: bold; font-size: 11px">
                        @if ($podata->detail)
                            {{number_format($subTotal, $numberFormatting)}}
                        @endif
                    </span>
                </td>
            </tr>
            <tr>
                <td style="border-bottom: none !important;border-left: none !important;width: 60%;">&nbsp;</td>
                <td class="text-right" style="width: 20%;border: 1px solid rgb(127, 127, 127)!important;">
                    <span style="font-weight: bold; font-size: 11px">{{ __('custom.discount') }}</span>
                </td>
                <td class="text-right" style="width: 20%;border: 1px solid rgb(127, 127, 127)!important;">
                    <span  style="font-weight: bold; font-size: 11px">
                        {{number_format($podata->poDiscountAmount, $numberFormatting)}}
                    </span>
                </td>
            </tr>
            @if ($podata->isVatEligible || $podata->vatRegisteredYN)
                <tr>
                    <td style="border-bottom: none !important;border-left: none !important;width: 60%;">&nbsp;</td>
                    <td class="text-right" style="width: 20%;border: 1px solid rgb(127, 127, 127)!important;">
                        <span style="font-weight: bold; font-size: 11px">{{ __('custom.vat') }}</span>
                    </td>
                    <td class="text-right" style="width: 20%;border: 1px solid rgb(127, 127, 127)!important;">
                        <span  style="font-weight: bold; font-size: 11px">
                            @if($podata->rcmActivated)
                                {{number_format(0, $numberFormatting)}} 
                            @else 
                                {{number_format($podata->VATAmount, $numberFormatting)}}
                            @endif
                        </span>
                    </td>
                </tr>
            @endif
            <tr>
                <td style="border-bottom: none !important;border-left: none !important;width: 60%;">&nbsp;</td>
                <td class="text-right" style="width: 20%;border: 1px solid rgb(127, 127, 127)!important;">
                    <span style="font-weight: bold; font-size: 11px">{{ __('custom.net_amount') }}</span>
                </td>
                <td class="text-right" style="width: 20%;border: 1px solid rgb(127, 127, 127)!important;">
                    <span  style="font-weight: bold; font-size: 11px">
                        @if ($podata->detail)
                            @if($podata->rcmActivated)
                                {{number_format(($podata->poTotalSupplierTransactionCurrency - $podata->VATAmount), $numberFormatting)}} 
                            @else
                                {{number_format($podata->poTotalSupplierTransactionCurrency, $numberFormatting)}}
                            @endif
                        @endif
                    </span>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

    <div class="row">
        <table style="width:100%;padding-top: 10px;">
            <tr style="padding-bottom: 2%;">
                <td style="width:13%;vertical-align: top;"><span  style="font-weight: bold">{{ __('custom.payment_terms') }}</span></td>
                <td style="width:2%;vertical-align: top;"><span  style="font-weight: bold">:</span></td>
                <td style="width:85%;vertical-align: top;">{{$paymentTermsView}}</td>
            </tr>
        </table>
    </div>
    <div class="row">
        <table style="width:100%;padding-top: 10px;">
            <tr style="padding-bottom: 2%;">
                <td style="width:13%;vertical-align: top;"><span  style="font-weight: bold">{{ __('custom.created_by') }}</span></td>
                <td style="width:2%;vertical-align: top;"><span  style="font-weight: bold">:</span></td>
                <td style="width:85%;vertical-align: top;">  
                    @if(isset($podata->created_by->empFullName))
                        {{$podata->created_by->empFullName}}
                    @endif
                </td>
            </tr>
        </table>
    </div>
    <div class="row">
        <table style="width:100%;padding-top: 10px;">
            <tr style="padding-bottom: 2%;">
                <td style="width:13%;vertical-align: top;"><span  style="font-weight: bold">{{ __('custom.created_date') }}</span></td>
                <td style="width:2%;vertical-align: top;"><span  style="font-weight: bold">:</span></td>
                <td style="width:85%;vertical-align: top;">
                    @if(isset($podata->createdDateTime))
                        {{\Carbon\Carbon::parse($podata->createdDateTime)->format('d/m/Y g:i A' )}}
                    @endif
                </td>

            </tr>
        </table>
    </div>
</div>
@if (isset($specification) && $specification==1)

<div class="row">
        <div class="page_break"></div>
        <table style="width:100%">
            <tr>
                <td width="100%" style="text-align: center;font-size: 13px;"><h4  style="font-weight: bold" style=" text-decoration: underline;">Specifications</h4></td>
            </tr>
        </table>
        <br>
      
            @if ($podata->detail)
                @foreach ($podata->detail as $det)
                @if (isset($det->item->specification->html))


                   <table style="width:100%;background: #ede7e7;margin-bottom: 20px;">
                        <tr style="height:10px;">
                            <td style="width: 0%;height:10px">
                            <td width="100%" ><span style="text-align: left;font-size: 14px;"  style="font-weight: bold" >{{$det->itemPrimaryCode}} - {{$det->itemDescription}} {!! "&nbsp;" !!}  {{$det->unit->UnitShortCode}}</span></td>
                            </td>

                        
                        </tr>
                    </table>
                    <table>
                         <tr>
                             <td class="ql-editor">
                                <div style="max-width: 700px !important" class="quill-html">
                                    {!!$det->item->specification->html !!}
                                </div>
                             </td>
                        </tr>
                    </table>
               @endif
                @endforeach
            @endif

</div>   
 


@endif
@if ($termsCond==1 && isset($paymentTermConfigs) && count($paymentTermConfigs) > 0)
    <div class="row" id="paymentTermsCond">
        <div class="page_break"></div>
        <table style="width:100%">
            <tr>
                <td width="100%" style="font-size: 11px;"><h3  style="font-weight: bold">Payment Terms and Conditions</h3></td>
            </tr>
        </table>
        <br>
        <table style="width:100%;" class="table table-bordered">
            <tbody>
                @foreach ($paymentTermConfigs as $paymentTermConfig)
                    <tr>
                        <td style="font-weight: bold; width: 20%; border: 1px solid rgb(127, 127, 127)!important;">{{ $paymentTermConfig->term }}</td>
                        <td style="width: 80%; border: 1px solid rgb(127, 127, 127)!important;">{!! $paymentTermConfig->description !!}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif


