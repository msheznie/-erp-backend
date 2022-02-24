

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

    .page_break {
        page-break-before: always;
    }

    #paymentTermsCond .footer {
        display: none;
    }

    .quill-html img {
        max-width: 700px;
    }

</style>
<link href="{{ public_path('assets/css/app.css') }}" rel="stylesheet" type="text/css" />
<div class="footer">
    <table style="width:100%; margin-top: 2%">
        <tr>
            <td><span class="font-weight-bold">Electronically Approved By :</span></td>
        </tr>
        <tr>
            &nbsp;
        </tr>
    </table>
    <table style="width:100%;padding-top: 2%">
        <tr>
            @if ($podata->approved_by)
                @foreach ($podata->approved_by as $det)
                    <td style="padding-right: 25px;font-size: 9px;">
                        <div>
                            @if($det->employee)
                                {{$det->employee->empFullName }}
                            @endif
                        </div>
                        <div><span>
                @if(!empty($det->approvedDate))
                                    {{ \App\helper\Helper::dateFormat($det->approvedDate)}}
                                @endif
              </span></div>
                        <div style="width: 3px"></div>
                    </td>
                @endforeach
            @endif
        </tr>
    </table>
    <table style="width:100%;">
        <tr>
            <td colspan="3" style="width:100%">
                <hr style="background-color: black">
            </td>
        </tr>
        <tr>
            <td style="width:33%;font-size: 10px;vertical-align: top;">
                <p><span class="font-weight-bold"><span [innerHTML]="docRefNumber"
                                                        class="white-space-pre-line">{!! nl2br($docRef["docRefNumber"]) !!}</span></span>
                </p>
            </td>
            <td style="width:33%; text-align: center;font-size: 10px;vertical-align: top;">
                <span style="text-align: center">Page <span class="pagenum"></span></span><br>
                @if($isMergedCompany)
                    {{$secondaryCompany['name']}}
                @else
                    @if ($podata->company)
                        {{$podata->company->CompanyName}}
                    @endif
                @endif
            </td>
            <td style="width:33%;font-size: 10px;vertical-align: top;">
                <span style="margin-left: 38%;">Printed Date : {{date("d-M-y", strtotime(now()))}}</span>
            </td>
        </tr>
    </table>
</div>
<div id="watermark">
         <span class="watermarkText">
           <h3 class="text-muted">
               @if($podata->poConfirmedYN == 0 && $podata->approved == 0)
                   Not Confirmed & Not Approved <br> Draft Copy
               @endif
               @if($podata->poConfirmedYN == 1 && $podata->approved == 0)
                   Confirmed & Not Approved <br> Draft Copy
               @endif
           </h3>
         </span>
</div>
<div class="content">
    <div class="row">
        <table style="width:100%">
            <tr>
                <td width="60%">
                    <table>
                        <tr>
                            @if($isMergedCompany)
                                <td><img src="{{$secondaryCompany['logo_url']}}" width="180px" height="60px"></td>
                            @else
                                <td><img src="{{$podata->company->logo_url}}" width="180px" height="60px"></td>
                            @endif
                        </tr>
                        <tr>
                            <td>
                                <h3 class="font-weight-bold">
                                    {{$title}}
                                </h3>
                            </td>
                        </tr>
                    </table>
                </td>
                <td width="40%">
                    <table>
                        <tr>
                            <td>
                                <h3 class="font-weight-bold">
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
                            <td><span class="font-weight-bold">{{$title}} Number</span></td>
                            <td><span class="font-weight-bold">:</span></td>
                            <td>
                                @if ($podata->purchaseOrderCode)
                                    {{$podata->purchaseOrderCode}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><span class="font-weight-bold">{{$title}} Date </span></td>
                            <td><span class="font-weight-bold">:</span></td>
                            <td>{{ \App\helper\Helper::dateFormat($podata->createdDateTime)}}</td>
                        </tr>
                        <tr>
                            <td><span class="font-weight-bold">Reference Number </span></td>
                            <td><span class="font-weight-bold">:</span></td>
                            <td>
                                @if ($podata->referenceNumber)
                                    {{$podata->referenceNumber}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><span class="font-weight-bold">VAT No </span></td>
                            <td><span class="font-weight-bold">:</span></td>
                            <td>
                                @if ($podata->company->vatRegisteredYN == 1)
                                    {{$podata->company->vatRegistratonNumber}}
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
    <hr style="background-color: black">
    <div class="row">
        <table style="width:100%">
            <tr>
                <td style="width: 60%">
                    <table style="width: 100%">
                        <tr>
                            <td colspan="3"><span class="title">Sold To:</span></td>
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
                            <td style="width: 100%" colspan="3"><span class="title">&nbsp;</span></td>
                        </tr>
                    </table>
                    <table style="width: 100%">
                        <tr>
                            <td style="width: 20%"><span class="font-weight-bold">Order Contact</span></td>
                            <td style="width: 2%"><span class="font-weight-bold">:</span></td>
                            <td style="width: 78%">{{$podata->soldTocontactPersonID}}</td>
                        </tr>
                        <tr>
                            <td style="width: 20%"><span class="font-weight-bold">Phone</span></td>
                            <td style="width: 2%"><span class="font-weight-bold">:</span></td>
                            <td style="width: 78%">{{$podata->soldTocontactPersonTelephone}} </td>
                        </tr>
                        <tr>
                            <td style="width: 20%"><span class="font-weight-bold">Fax</span></td>
                            <td style="width: 2%"><span class="font-weight-bold">:</span></td>
                            <td style="width: 78%">{{$podata->soldTocontactPersonFaxNo}} </td>
                        </tr>
                        <tr>
                            <td style="width: 20%"><span class="font-weight-bold">Email</span></td>
                            <td style="width: 2%"><span class="font-weight-bold">:</span></td>
                            <td style="width: 78%">{{$podata->soldTocontactPersonEmail}} </td>
                        </tr>
                        <tr>
                            <td style="width: 20%"><span class="font-weight-bold">VAT No</span></td>
                            <td style="width: 2%"><span class="font-weight-bold">:</span></td>
                            <td style="width: 78%">{{$podata->supplier->vatNumber}} </td>
                        </tr>
                    </table>
                </td>
                <td style="width: 40%">
                    <table style="width:100%">
                        <tr>
                            <td colspan="3"><span class="title">Supplier:</span></td>
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
                            <td><span class="font-weight-bold">VAT #</span></td>
                            <td><span class="font-weight-bold">:</span></td>
                            <td>
                                @if ($podata->supplier)
                                    {{$podata->supplier->vatNumber}}
                                @endif
                            </td>
                        </tr>
                        @endif
                        <tr>
                            <td><span class="font-weight-bold">Contact</span></td>
                            <td><span class="font-weight-bold">:</span></td>
                            <td>
                                @if ($podata->suppliercontact)
                                    {{$podata->suppliercontact->contactPersonName}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 20%"><span class="font-weight-bold">Phone</span></td>
                            <td style="width: 2%"><span class="font-weight-bold">:</span></td>
                            <td style="width: 78%">
                                @if ($podata->suppliercontact)
                                    {{$podata->suppliercontact->contactPersonTelephone}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 20%"><span class="font-weight-bold">Fax</span></td>
                            <td style="width: 2%"><span class="font-weight-bold">:</span></td>
                            <td style="width: 78%">
                                @if ($podata->suppliercontact)
                                    {{$podata->suppliercontact->contactPersonFax}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 20%"><span class="font-weight-bold">Email</span></td>
                            <td style="width: 2%"><span class="font-weight-bold">:</span></td>
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
    <hr style="background-color: black">
    <div class="row">
        <table style="width:100%">
            <tr>
                <td style="width: 60%">
                    <table>
                        <tr>
                            <td colspan="3"><span class="title">Ship To:</span></td>
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
                            <td colspan="3"><span class="title">&nbsp;</span></td>
                        </tr>
                    </table>
                    <table>
                        <tr>
                            <td style="width: 34%"><span class="font-weight-bold">Ship Contact</span></td>
                            <td style="width: 2%"><span class="font-weight-bold">:</span></td>
                            <td style="width: 64%">{{$podata->shipTocontactPersonID}} </td>
                        </tr>
                        <tr>
                            <td style="width: 28%"><span class="font-weight-bold">Phone</span></td>
                            <td style="width: 2%"><span class="font-weight-bold">:</span></td>
                            <td style="width: 70%">{{$podata->shipTocontactPersonTelephone}} </td>
                        </tr>
                        <tr>
                            <td style="width: 28%"><span class="font-weight-bold">Fax</span></td>
                            <td style="width: 2%"><span class="font-weight-bold">:</span></td>
                            <td style="width: 70%">{{$podata->shipTocontactPersonFaxNo}} </td>
                        </tr>
                        <tr>
                            <td style="width: 28%"><span class="font-weight-bold">Email</span></td>
                            <td style="width: 2%"><span class="font-weight-bold">:</span></td>
                            <td style="width: 70%">{{$podata->shipTocontactPersonEmail}} </td>
                        </tr>
                    </table>
                </td>
                <td style="width: 40%">
                    <table>
                        <tr>
                            <td colspan="3"><span class="title">Invoice To:</span></td>
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
                            <td style="width: 20%"><span class="font-weight-bold">Payment Contact</span></td>
                            <td style="width: 2%"><span class="font-weight-bold">:</span></td>
                            <td style="width: 78%">
                                @if ($podata->suppliercontact)
                                    {{$podata->invoiceTocontactPersonID}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 20%"><span class="font-weight-bold">Phone</span></td>
                            <td style="width: 2%"><span class="font-weight-bold">:</span></td>
                            <td style="width: 78%">
                                @if ($podata->suppliercontact)
                                    {{$podata->invoiceTocontactPersonTelephone}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 20%"><span class="font-weight-bold">Fax</span></td>
                            <td style="width: 2%"><span class="font-weight-bold">:</span></td>
                            <td style="width: 78%">
                                @if ($podata->suppliercontact)
                                    {{$podata->invoiceTocontactPersonFaxNo}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 20%"><span class="font-weight-bold">Email</span></td>
                            <td style="width: 2%"><span class="font-weight-bold">:</span></td>
                            <td style="width: 78%">
                                @if ($podata->suppliercontact)
                                    {{$podata->invoiceTocontactPersonEmail}}
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
    <hr style="background-color: black">
    <div class="row">
        <table style="width:100%">
            <tr>
                <td style="width:11%;vertical-align: top;"><span class="font-weight-bold">Narration</span></td>
                <td style="width:1%;vertical-align: top;"><span class="font-weight-bold">:</span></td>
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
                            <td style="width: 7%"><span class="font-weight-bold">Expected Date</span></td>
                            <td style="width: 1%"><span class="font-weight-bold">:</span></td>
                            <td style="width: 92%">{{ \App\helper\Helper::dateFormat($podata->expectedDeliveryDate)}}</td>
                        </tr>
                    </table>
                </td>
                <td style="width:12%">
                    <table style="padding-bottom: 2%">
                        <tr>
                            <td><span class="font-weight-bold">Currency</span></td>
                            <td><span class="font-weight-bold">:</span></td>
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
                <th style="text-align: center">Item Code</th>
                <th style="text-align: center">Item Description</th>
                <th style="text-align: center">Part No / Ref.Number</th>
                <th style="text-align: center">UOM</th>
                <th style="text-align: center">Qty</th>
                @if(isset($allowAltUom))
                <th style="text-align: center">Alt.UOM</th>
                <th style="text-align: center">Alt.Qty</th>
                @endif
                <th style="text-align: center">Unit Cost</th>
                <th style="text-align: center">Dis. Per Unit</th>
                @if ($podata->isVatEligible)
                  <th style="text-align: center">VAT. Per Unit</th>
                @endif
                <th style="text-align: center">Net Cost Per Unit</th>
                <th style="text-align: center">Net Amount</th>
            </tr>
            </thead>
            <tbody style="width: 100%">
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

                <tr style="border-bottom: 1px solid black; width: 100%">
                    <td>{{ $x  }}</td>
                    <td>{{$det->itemPrimaryCode}}</td>
                    <td nobr="true" style="width: 30%">{{$det->itemDescription}} <br> {!! nl2br($det->comment) !!}</td>
                    <td>{{$det->supplierPartNumber}}</td>
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
                <td class="text-right" style="width: 20%;border-left: 1px solid rgb(127, 127, 127)!important;"><span
                            class="font-weight-bold" style="font-size: 11px">Total Order Amount</span></td>
                <td class="text-right"
                    style="font-size: 11px;width: 20%;border-left: 1px solid rgb(127, 127, 127) !important;border-right: 1px solid rgb(127, 127, 127) !important;">
                <span class="font-weight-bold">
                @if ($podata->detail)
                        {{number_format($subTotal, $numberFormatting)}}
                    @endif
                </span>
                </td>
            </tr>
            <tr>
                <td style="border-bottom: none !important;border-top: none !important;border-left: none !important;">
                    &nbsp;</td>
                <td class="text-right"
                    style="font-size: 11px;border-left: 1px solid rgb(127, 127, 127)!important;"><span
                            class="font-weight-bold"
                            style="font-size: 11px">Discount</span>
                </td>
                <td class="text-right"
                    style="font-size: 11px;border-left: 1px solid rgb(127, 127, 127) !important;border-right: 1px solid rgb(127, 127, 127) !important;"><span
                            class="font-weight-bold">
                     {{number_format($podata->poDiscountAmount, $numberFormatting)}}
                </span>
                </td>
            </tr>
            @if ($podata->isVatEligible || $podata->vatRegisteredYN)
                <tr>
                    <td style="border-bottom: none !important;border-top: none !important;border-left: none !important;">
                        &nbsp;</td>
                    <td class="text-right" style="border-left: 1px solid rgb(127, 127, 127)!important;"><span
                                class="font-weight-bold"
                                style="font-size: 11px">VAT{{--({{$podata->VATPercentage .'%'}})--}}
                        </span></td>
                    <td class="text-right"
                        style="font-size: 11px;border-left: 1px solid rgb(127, 127, 127) !important;border-right: 1px solid rgb(127, 127, 127) !important;"><span
                                class="font-weight-bold">@if($podata->rcmActivated){{number_format(0, $numberFormatting)}} @else {{number_format($podata->VATAmount, $numberFormatting)}}@endif</span>
                    </td>
                </tr>
            @endif
            <tr>
                <td style="border-bottom: none !important;border-top: none !important;border-left: none !important;">
                    &nbsp;</td>
                <td class="text-right" style="border-left: 1px solid rgb(127, 127, 127)!important;"><span
                            class="font-weight-bold"
                            style="font-size: 11px">Net Amount</span>
                </td>
                <td class="text-right"
                    style="font-size: 11px;border-left: 1px solid rgb(127, 127, 127) !important;border-right: 1px solid rgb(127, 127, 127) !important;">
                <span class="font-weight-bold">
                @if ($podata->detail)
                        {{number_format($podata->poTotalSupplierTransactionCurrency, $numberFormatting)}}
                    @endif
                </span>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="row">
        <table style="width:100%;padding-top: 3%;">
            <tr>
                <td style="width:13%;vertical-align: top;"><span class="font-weight-bold">Delivery Terms</span></td>
                <td style="width:2%;vertical-align: top;"><span class="font-weight-bold">:</span></td>
                <td style="width:85%;vertical-align: top;">{!! nl2br($podata->deliveryTerms) !!}</td>
            </tr>
        </table>
    </div>
    <div class="row">
        <table style="width:100%;padding-top: 3%;">
            <tr style="padding-bottom: 2%;">
                <td style="width:13%;vertical-align: top;"><span class="font-weight-bold">Penalty Terms</span></td>
                <td style="width:2%;vertical-align: top;"><span class="font-weight-bold">:</span></td>
                <td style="width:85%;vertical-align: top;">{!! nl2br($podata->panaltyTerms) !!}</td>
            </tr>
        </table>
    </div>
    <div class="row">
        <table style="width:100%;padding-top: 3%;padding-bottom: 50px">
            <tr style="padding-bottom: 2%;">
                <td style="width:13%;vertical-align: top;"><span class="font-weight-bold">Payment Terms</span></td>
                <td style="width:2%;vertical-align: top;"><span class="font-weight-bold">:</span></td>
                <td style="width:85%;vertical-align: top;">{{$paymentTermsView}}</td>
            </tr>
        </table>
    </div>
</div>
@if (isset($specification) && $specification==1)

<div class="row">
        <div class="page_break"></div>
        <table style="width:100%">
            <tr>
                <td width="100%" style="text-align: center;font-size: 13px;"><h4 class="font-weight-bold" style=" text-decoration: underline;">Specifications</h4></td>
            </tr>
        </table>
        <br>
      
            @if ($podata->detail)
                @foreach ($podata->detail as $det)
                @if (isset($det->item->specification->html))


                   <table style="width:100%;background: #ede7e7;margin-bottom: 20px;">
                        <tr style="height:10px;">
                            <td style="width: 0%;height:10px">
                            <td width="100%" ><span style="text-align: left;font-size: 14px;" class="font-weight-bold" >{{$det->itemPrimaryCode}} - {{$det->itemDescription}} {!! "&nbsp;" !!}  {{$det->unit->UnitShortCode}}</span></td>
                            </td>

                        
                        </tr>
                    </table>
                    <table style="width:100% !important" class="table">
                         <tr>
                             <td class="ql-container ql-snow">
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
@if ($termsCond==1)
    <div class="row" id="paymentTermsCond">
        <div class="page_break"></div>
        <table style="width:100%">
            <tr>
                <td width="100%" style="text-align: center;font-size: 9px;"><h3 class="font-weight-bold" style=" text-decoration: underline;">Master Service
                        Agreement for Purchase of Goods and
                        Services</h3></td>
            </tr>
        </table>
        <br>
        <table style="width:100%">
            <tr>
                <td width="50%" style="text-align: justify;font-size: 9px;">
                    <span class="font-weight-bold">1. DEFINITIONS</span><br>
                    In these Conditions:<br>
                    (A) “Affiliate” means in relation to any person, a subsidiary of that person or a holding company of
                    that person or any other subsidiary of that holding company.<br>
                    (B) “Company” means either NPS Bahrain for Oil and Gas Wells Services W.L.L, Gulf Energy SAOC,
                    National
                    Energy Services Reunited Corp or any of their respective Affiliates placing the Order.<br>
                    (C) " Goods" means the articles, raw materials or any of them to be supplied by the Supplier to the
                    Company pursuant to the Order (including any articles or materials supplied in connection with the
                    Services).<br>
                    (D) "Information” means specifications, drawings, sketches, models, samples, designs, technical
                    information and data and other proprietary information whether written, oral or otherwise.<br>
                    (E) "Order" means a purchase order in respect of Goods and/or Services issued by the Company to the
                    Supplier on the Company's official purchase order form, together with all other documents referred
                    to
                    therein.<br>
                    (F) “Services" means work and/or services or any of them to be performed by the Supplier for the
                    Company
                    pursuant to the Order.<br>
                    (G) "Supplier" means the person, firm or the Company to whom the Order is addressed.<br>
                    (H) "Tooling" means tools, jigs, dies, fixtures, molds, patterns and/or equipment which is furnished
                    to
                    the Supplier and which is supplied or paid for by the Company or for which the Company is liable to
                    pay
                    under the terms of the Order.<br><br>
                    <span class="font-weight-bold">2. APPLICATION</span><br>
                    These Conditions shall apply to and be incorporated in the contract between the Supplier and the
                    Company
                    for the supply of the Goods and/or the Services and shall be in substitution for any oral
                    arrangements
                    made between the Company and the Supplier and shall prevail over any inconsistent terms or
                    conditions
                    contained in or referred to in the Supplier's quotation or acceptance of order or correspondence or
                    elsewhere or implied by trade, custom or practice or course of dealing and no addition to or
                    variation
                    of or exclusion or attempted exclusion of the Order and/or these Conditions or any of them shall be
                    binding upon the Company unless specifically agreed to in writing and signed by a duly authorized
                    representative of theCompany.<br><br>
                    <span class="font-weight-bold">3. ACCEPTANCE OF ORDER</span><br>
                    All the terms of the contract between the Company and the Supplier are contained in or referred to
                    in
                    the Order and in these Conditions. The execution and return of the acknowledgement copy of the Order
                    by
                    the Supplier or the Supplier's execution or commencement of work or commencement of delivery
                    pursuant to
                    the Order constitutes acceptance of the Order on the terms hereof by the Supplier and of the
                    Company’s
                    conditions of purchase for goods and services. The acceptance of the Order is limited to and
                    conditional
                    upon acceptance by the Supplier of these Conditions.<br><br>
                    <span class="font-weight-bold">4. PACKING, MARKING AND DOCUMENTATION</span><br>
                    (A) The Goods shall be properly packed, marked and delivered at the Supplier's expense in accordance
                    with the Order. The Company shall not accept a charge for packages, containers or freight unless
                    specified in the Order.<br>
                    (B) Each advice note, bill of lading and invoice shall bear the applicable Order number, delivery
                    date
                    and / or date of completion of the Services and the location to which the Goods are to be delivered
                    or
                    at which the Services are to be provided.<br>
                    (C) Advice notes and invoices must be sent as directed by the Order.<br>
                    (D) The Supplier agrees on request to supply the Company with any required certifications, including
                    without limitation any necessary declarations and documents stating the origin of the Goods and the
                    manner in which they qualify for E.E.C.,
                    E.F.T.A. or other applicable preferences.<br>
                    (E) All lifting equipment shall meet the requirements of BSEN12079 standard. All chemicals shall be
                    supplied with latest MSDS and packaging/marking shall be done as specified on MSDS. Batch number,
                    Production and Expiry date shall also be marked on chemical products.<br>
                    (F) Supplier shall provide Certificate of Conformity, operating and maintenance manual and other
                    relevant certificates with all equipment, machineries, accessories, spare parts etc. All goods shall
                    be
                    manufactured in accordance with the latest version of international standards, especially PCE’s
                    shall
                    meet latest version of API6A, API16A and NACEMR0175 standards and CT string shall meet API5ST
                    standard.<br><br>
                    <span class="font-weight-bold">5. DELIVERY</span><br>
                    (A) Time is of the essence in the performance by the Supplier of the Order. If delivery dates for
                    the
                    Goods or the dates for the provision and/or performance of the Services cannot be met, the Supplier
                    shall promptly notify the Company of the earliest possible date for delivery of the Goods or the
                    provision of the Services. Notwithstanding such notice, and unless a substitute delivery date for
                    the
                    Goods or date for the provision of the Services has been expressly agreed to by the Company in
                    writing,
                    the Supplier's failure to effect delivery of the Goods or the provision of Services on the date
                    specified shall entitle the Company to cancel the Order without liability to the Supplier, to
                    purchase
                    substitute items or services elsewhere, and to recover
                    from the Supplier any loss and additional costs incurred.<br>
                    (B) If delivery or performance pursuant to the Order is incomplete, the Company reserves the right
                    (without prejudice to any of its other rights) to accept or reject the goods so delivered or
                    Services so performed and to cancel or vary the balance of the Order.<br>
                    (C) The Goods must be delivered at the delivery point specified in the Order. If the Goods are
                    incorrectly delivered, the Supplier will be liable for any additional expense involved in handling
                    and delivering them to their correct destination.
                </td>
                <td width="1%">&nbsp;</td>
                <td width="49%" style="text-align: justify; font-size: 9px;">
                    (D) In the event, of default by the Supplier, the Supplier shall pay the Company, as liquidated
                    damages 2% of the total value of the delayed Goods and/or Services, (and not as a penalty) for each
                    week’s (7 consecutive days) delay, up to a maximum of the value of the relevant Order. The Supplier
                    shall pay these liquidated damages on demand or the Company may deduct them from its payments to the
                    Supplier. These liquidated damages shall be due and payable by Supplier to the Company without
                    prejudice to any other rights the Company may have hereunder. The parties confirm that these
                    liquidated damages are reasonable and proportionate to protect the Company’s legitimate interest in
                    performance.<br><br>
                    <span class="font-weight-bold">6. WARRANTY</span><br>
                    (A) The Supplier warrants, and it is a condition of the Order, that the Goods supplied to the
                    Company
                    under the Order shall be of first class materials and workmanship throughout, will meet the
                    governing
                    specifications referred to in the Order as to quantity, quality standards and description and will
                    conform with any samples furnished by the Supplier and accepted by the Company; that the design,
                    construction and quality of the Goods will comply in all respects with applicable laws and
                    regulations
                    which may be in force at the time of delivery and that the Goods will be fit and suitable for the
                    purpose intended by the Company, of merchantable quality and free from defect.<br>
                    (B) The Supplier warrants, and it is a condition of the Order, that the Services shall be supplied
                    in
                    full accordance with the terms of the Order and shall be executed with reasonable care and skill by
                    properly qualified and experienced persons; and that the provision of the Services will comply in
                    all
                    respects with applicable laws and regulations which may be in force at the time the Services are
                    provided.<br>
                    (C) The warranties and remedies provided for in this Condition 6 shall be in addition to those
                    implied
                    by or available at law or in equity and shall continue in force notwithstanding the acceptance by
                    the
                    Company of all or part of the Goods or the Services in respect of which such warranties and remedies
                    are
                    applicable.<br><br>
                    <span class="font-weight-bold">7. QUANTITY, QUALITY AND DESCRIPTION</span><br>
                    (A) The Supplier shall not make any changes whatsoever in the color, characteristics,
                    specifications,
                    design or composition of the Goods.<br>
                    (B) The Supplier shall have implemented and documented a Quality Assurance Program meeting the
                    requirements of IS09001 or an internationally recognized standard of the same level, and conforming
                    to
                    laws and regulations of the jurisdiction governing its operations, and shall have a defined person
                    within its organization responsible for compliance there with.
                    The Supplier shall send copies of all
                    non-conformance reports and dispensations issued by any regulatory agency following the most recent
                    certifications, to the Company as soon as the same become available. The Company may conduct a
                    quality
                    audit at any time in relation to all or any of the foregoing and may terminate the Order if the
                    Supplier
                    at any time does not meet, in the Company's reasonable opinion, the requirements mentioned above, or
                    fails to implement corrective actions recommended by the Company.<br>
                    (C) The Supplier agrees to allow a representative of the Company to enter the Supplier's premises on reasonable prior notice to inspect the Goods and/or Services.<br>
                    (D) The Company reserves the right at any time to change the relevant Order by written instruction, in which event the Supplier shall notify the Company of any consequent change in price within seven (7) days of receipt of such change order, which the Company shall then accept or reject. No increase in price shall be allowed if the Supplier fails to give timely notification to the Company.<br><br>
                    <span class="font-weight-bold"> 8. ACCEPTANCE OF GOODS AND SERVICES</span><br>
                    (A) The Goods and the provision of the Services shall be subject to inspection and testing by the
                    Company prior to acceptance. In any case where the Goods, the Services or any part thereof (whether
                    or
                    not inspected or tested by the Company) do not comply with the requirements of the Order, the
                    Company
                    shall give written notice to the Supplier. If the Supplier does not rectify the matter within three
                    (3)
                    working days after receipt of the notice, the Company shall have the right to repair such Goods and
                    rectify such Services at the expense of the Supplier or to reject the Goods and Services concerned
                    and
                    shall thereafter return any Goods concerned to the Supplier at the Supplier's risk and expense. In
                    case
                    of rejection, the Company may (at its discretion) either cancel the Order forthwith or demand that
                    the
                    Supplier within a reasonable time replace such rejected Goods or Services with Goods or Services
                    which
                    are in all respects in accordance with the Order. If the Supplier shall fail to replace any rejected
                    Goods or Services within a reasonable time as demanded by the Company, the Company shall have the
                    right
                    to purchase replacement goods or services from another source and any money paid by the Company to
                    the
                    Supplier in respect of the rejected Goods or Services together with any additional expenditure over
                    and
                    above the contract price reasonably incurred by the Company in obtaining replacement goods or
                    services
                    shall be paid by the Supplier to the Company.<br>
                    (B) The rights and obligations of the parties hereto shall apply to all defects appearing in Goods
                    or
                    Services or any part thereof during the period of twelve (12) consecutive months (or in the case of
                    any
                    latent or inherent defect, the period of twelve
                    (12) consecutive months after the same could first reasonably have been discovered) commencing on
                    the
                    date of acceptance.<br><br>
                    <span class="font-weight-bold"> 9. INDEMNITY</span><br>
                    The Supplier agrees to indemnify and at all times to hold the Company, its agents, employees, officers, subsidiaries, associated companies and assigns harmless from and against any and all liability, damage, loss, cost or expense, including without limitation any liability arising from any injury or loss to any person or persons or any
                    damage to or loss of any property, directly or indirectly arising out of or in
                    connection with:<br>
                </td>
            </tr>
        </table>
        <div class="page_break"></div>
        <table style="width:100%">
            <tr>
                <td width="50%" style="text-align: justify;font-size: 9px;">
                    (i) any alleged or actual infringement of any patent, registered design, copyright, trade
                    mark or other rights of property vested in any other person, firm or company resulting
                    from the purchase, use or resale by the Company, its servants, agents or clients of the
                    Goods or the Services or any part thereof;<br>
                    (ii) any act or omission in the performance of or in connection with any or all of the
                    obligations undertaken by the Supplier pursuant to the Order, whether by reason of the
                    negligence of the Supplier, its agents, employees or sub-contractors or their agents or
                    employees, or otherwise;<br>
                    (iii) any alleged fault or defect howsoever rising in the Goods (whether in materials,
                    workmanship or otherwise); Provided that the Supplier shall have no obligation to
                    indemnify under this Condition if and to the extent that any relevant liability,
                    damage, loss, cost or expense incurred was only incurred because the Supplier
                    delivered the Goods or provided the Services solely in accordance with designs, plans
                    or specifications supplied by the Company.<br><br>
                    <span class="font-weight-bold">10. INSURANCE</span><br>
                    (A) The Supplier will at all times insure and keep itself insured with a reputable
                    insurance company in compliance with local legislation against all insurable liability
                    under the Order and in respect of the Goods or the Services including without
                    limitation all the Supplier's liabilities under Condition 9. The Supplier will promptly
                    advise the Company of any claim made against the Supplier arising out of the
                    Supplier’s performance of the Order. The Supplier will provide all facilities, assistance
                    and advice required by the Company or the Company's insurers for the purpose of
                    contesting or dealing with any action, claim or matter arising out of the Supplier's
                    performance of the Order.<br>
                    (B) Without limitation of its liabilities and obligations hereunder, the Supplier shall at its
                    own cost obtain and maintain in full force and effect, as a minimum, the following
                    insurance policies all of which shall be endorsed to show the Company as
                    additional insured and all liability policies of which shall include a cross liability clause
                    to the effect that the Company will be treated as third party towards the first named
                    insured in respect of any claim made by the Company against the additional insured.
                    Deductibles, if any, will be borne by the Supplier and the rejection or late
                    settlement of any claim will not be opposed to the Company:<br>
                    (i) Workmen's Compensation insurance up to statutory limits;<br>
                    (ii) Employer's Liability up to statutory limits or US$2,000,000 for any one
                    occurrence, whichever is the higher;<br>
                    (iii) Automobile Insurance up to statutory limits, but not less than US$2,000,000 for any
                    one accident in respect of personal injury and US$500,000 for any one accident in
                    respect of property damage;<br>
                    (iv) General Third Party Liability Insurance including product liability and, if
                    applicable, contractual liability with a combined single limit of not less than
                    US$2,000,000 for any one occurrence for personal injury and property damage;<br>
                    (v) If applicable, Professional Liability insurance up to with limits to be agreed between
                    the parties in writing;<br>
                    (vi) Fire and Explosion insurance in respect of the Company's property while in the
                    care, custody or control of, the Supplier or Supplier's contractors in amount(s) agreed
                    in writing by the Company;<br>
                    (vii) Insurance of the Company’s property against other risks to which it may be
                    exposed while transported by or on behalf of, or while in the care, custody or control of
                    the Supplier must also be subscribed but may be subject to exemption with the written
                    approval of the Company.<br>
                    (C) The Supplier shall at the Company's request and expense provide and maintain
                    any additional insurances to those specified under Condition (B) above as requested
                    by the Company in writing.<br>
                    (D) The Supplier shall, upon request by the Company at any time, cause its insurers
                    (or its brokers with the Company's approval) to furnish the Company with
                    certificates of the above- mentioned insurance policies giving evidence of the limits
                    and the dates of effect and renewal of each insurance cover, and a statement that no
                    insurance will be cancelled or materially changed during the term of the contract
                    without thirty (30) days' prior written notice to the Company at the address shown on
                    the certificate.<br><br>
                    <span class="font-weight-bold">11. TERMINATION</span><br>
                    (A) Without prejudice to any other rights or remedies to which it may be entitled, the
                    Company may by written notice to the Supplier terminate the Order immediately and
                    without liability in the event that:<br>
                    (i) The Supplier fails within a reasonable time to return to the Company
                    the acknowledgement copy of the Order, or the Supplier refuses or fails to make
                    deliveries of the Goods or to perform the Services within the time specified in the
                    Order or refuses or fails to perform any other provisions of the Order and fails to
                    remedy such breach within ten (10) days after receipt of written notice from the
                    Company requiring remedy thereof; or<br>
                    (ii) the Supplier enters into a Deed of Arrangement or commits an act of bankruptcy or
                    compounds with its creditors; or if a receiving order is made against it; or if an order is
                    made or a Resolution is passed for the winding up of the Supplier (otherwise than for
                    the purposes of amalgamation or reconstruction previously approved in writing by the
                    Company); or if a Receiver is appointed of any of the Supplier's assets or undertaking;
                    or if circumstances arise which entitle the court or a creditor to appoint a Receiver or
                    Manager or which entitle the court to make a winding up order; or if the Supplier takes
                    or suffers any similar or analogous action in consequence of debt or commits any
                    breach of this or any other contract between the Company and the Supplier; or if
                    the Company reasonably apprehends that any of the above is likely to occur.<br><br><br><br><br>
                </td>
                <td width="1%">&nbsp;</td>
                <td width="49%" style="text-align: justify;font-size: 9px;">
                    (B) Without prejudice to any other rights or remedies to which it may be entitled, the
                    Company shall have the right to terminate the Order in whole or in part at any time by
                    giving the Supplier notice in writing. The Supplier shall on receipt of such notice
                    immediately discontinue the supply of Goods or the provision of Services. The
                    Company shall pay a fair and reasonable price for such work-in-progress properly
                    performed and the Supplier shall afford the Company every assistance to ascertain
                    the extent of, and to minimize expenses and costs in relation to, such work-in-progress
                    and shall submit all final invoices to the Company within two (2) months of notice
                    of termination. Such payment by the Company shall constitute full and final
                    satisfaction of any claims arising out of such termination and upon such payment the
                    Supplier shall deliver to the Company all work completed or in progress. In no event
                    shall the amount payable by the Company under this Condition (B) exceed the amount
                    that would have been payable had the Order not been terminated.<br><br>
                    <span class="font-weight-bold">12. TITLE AND RISK</span><br>
                    The property and risk in the Goods shall pass to the Company on delivery of the
                    Goods in accordance with the Order, without prejudice to any right of rejection which
                    may accrue to the Company under these Conditions or otherwise. The Supplier shall
                    be liable for, and indemnify the Company against, any and all liens, charges,
                    claims and other encumbrances in respect of any and all Goods or Services
                    provided hereunder.<br><br>
                    <span class="font-weight-bold">13. ASSIGNMENT</span><br>
                    Neither the Order nor any part thereof shall be assigned, sub-contracted or transferred
                    in any other manner to a third party without the Company's prior written
                    consent. Any such consent shall not relieve the Supplier of any obligation to comply
                    with these Conditions or the Order.<br><br>
                    <span class="font-weight-bold">14. PRICE</span><br>
                    (A) All prices for the Goods and the Services shall be as stated in the Order, and
                    unless otherwise provided cover the cost of packaging, insurance and freight. Only
                    variations agreed to in writing by the parties as a result of changes in the Order will be
                    accepted. If no such price is stated, the price of the Goods or the Services shall be the
                    lowest price currently quoted or charged at the date of the Order by the Supplier for
                    those Goods or Services, but in no event higher than the price most recently charged
                    to the Company by the Supplier for those Goods or Services.<br>
                    (B) Where Goods or Services are subject to purchase tax, value added tax or any
                    other similar taxation, the amount legally payable by the Company is to be rendered as
                    a separate item of account on a valid tax invoice and, if required by the Company, the
                    Supplier will produce bona fide evidence of the amount paid or to be paid in respect
                    thereof.<br><br>
                    <span class="font-weight-bold">15. TERMS OF PAYMENT AND CONTRASUMS</span><br>
                    (A) Unless otherwise stated in the Order, payment of invoices shall be made by the
                    end of the month following the month in which the Goods are received or the Services
                    are completed in accordance with the Order.<br>
                    (B) The Supplier shall within thirty (30) days of delivery to the Company provide a
                    separate invoice for each Order, or for each installment where delivery by instalment
                    has been accepted by the Company , which shall bear the number of the Order,
                    details of delivery, the price, discounts and, if applicable, any related expenses
                    referred to in the Order.<br>
                    (C) The Company reserves the right to deduct from any monies due or becoming due
                    to the Supplier any monies due from the Supplier to the Company in connection with
                    the Order.<br><br>
                    <span class="font-weight-bold"> 16. INFORMATION</span><br>
                    (A) All Information furnished to the Supplier by the Company or on its behalf and all
                    therein shall remain the property of the Company or any holding company of the
                    Company or any subsidiary of such holding company (holding company and subsidiary
                    having the meanings ascribed to them by Section 736 of the Companies Act 1985) and
                    shall be returned promptly to the Company (together with all copies) at the Company's
                    request. Such Information shall be treated as strictly confidential, shall be kept safely
                    and shall not be used or disclosed by the Supplier except strictly as required in the
                    course of performance of this Order or any other Order. Subject to Condition (B)
                    below, unless the Company has otherwise agreed in .writing, all Information of every
                    description prepared by the Supplier in connection with the Order shall be the
                    Company's sole property and the Company may reproduce and use the said items
                    freely for any purpose whatsoever.<br>
                    (B) Any invention, whether or not patentable, made by the Supplier (or any
                    subcontractor of the Supplier) in connection with, but outside the scope of the Order,
                    shall belong to the Supplier (or its subcontractor) save that the Company shall be
                    granted an irrevocable, royalty-free, nonexclusive license to utilize the same, under all
                    patents, know-how and other proprietary information now or hereafter owned by the
                    Supplier (or any subcontractor of the Supplier). In addition, the Supplier shall (so far as
                    it is able) grant the Company an irrevocable, royalty-free, non-exclusive license to
                    enable the Company to maintain, repair or alter any Goods or any unit or component
                    used or specified by the Supplier pursuant to the Order. The Supplier agrees to
                    perform such acts, deeds and things as the Company may deem necessary to vest
                    such rights as aforesaid in the Company (or any assignee of the Company).<br>
                    (C) Subject to Condition (B) above, all inventions, whether or not patentable, made by
                    the Supplier (or any subcontractor of the Supplier) and information obtained and knowhow
                    gained by the Supplier (or subcontractor of the Supplier) in connection with the
                    Order shall belong absolutely to the Company and the exploitation of any of the
                    aforesaid by the Supplier (or subcontractor of the Supplier) shall be limited to execution of the Order save where expressly otherwise agreed with the Company. The Supplier shall be obliged:<br>
                    (i) promptly to disclose to the Company all inventions which it, its subcontractor or its
                    or their employees have made pursuant to or in connection with the Order; and<br>

                </td>
            </tr>
        </table>
        <div class="page_break"></div>
        <table style="width:100%">
            <tr>
                <td width="50%" style="text-align: justify;font-size: 9px;">
                    (ii) to execute or have executed all documents and perform or have performed all such
                    other acts, deeds and things as the Company may deem necessary or desirable
                    to protect the Company's (or its assignee's) title to such inventions and to obtain and
                    maintain patent coverage therefor throughout the world, subject to the Company
                    agreeing to reimburse the Supplier for all the Supplier's reasonable costs incurred
                    thereby.<br><br>
                    <span class="font-weight-bold"> 17. TOOLING</span><br>
                    All Tooling shall be and remains the property of the Company and the Supplier shall
                    mark the Company’s name on such Tooling. The Supplier shall at the Supplier's
                    expense maintain all Tooling in first class condition and immediately replace any
                    Tooling which is lost or destroyed or becomes worn out. The Supplier shall adequately
                    insure all Tooling against loss or destruction and shall produce on demand by the
                    Company the policy of such insurance and the premium receipts. No Tooling shall be
                    moved from the Supplier's premises or disposed of by the Supplier without the prior
                    written approval of the Company. No Tooling shall be used in the production,
                    manufacture or design of any goods or materials other than those contracted for by or
                    in pursuance neither of the relevant Order nor for larger quantities than those
                    specified. The Company shall accept the invoicing of Tooling only if such Tooling has
                    been specifically ordered and accepted by the Company under the Order.<br><br>
                    <span class="font-weight-bold"> 18. THE COMPANY'S PROPERTY</span><br>
                    The following provisions of this Condition shall apply to any material or property
                    provided by the Company to the Supplier for any purpose in connection with the Order
                    and whenever the Order requires the Supplier to repair or apply a process to goods or
                    materials owned by the Company (hereinafter called "The Company’s Property") which
                    the Company makes available for that purpose:-<br>
                    (i) The Company's Property shall be returnable on demand;<br>
                    (ii) The Supplier shall indemnify the Company against loss of or damage to the
                    Company's Property while it is in the care, custody or control of the Supplier or of any
                    permitted subcontractor;<br>
                    (iii) the Supplier shall keep The Company's Property safe, secure and separate
                    from all property of others and shall clearly mark The Company’s Property with the
                    Company’s name. The Company's Property shall not be removed from the Supplier's
                    premises without the Company's written authority (except for the purpose of fulfilling
                    the Order);<br>
                    (iv) the Supplier shall keep separate account of all the Company's Property and will
                    furnish statements on request giving details, description and location thereof both
                    before and after repair or processing (as the case may be) as well as any other
                    information regarding the Company's Property asked for by the Company. The
                    Company and persons authorized by it shall be entitled at all reasonable times to
                    check and inspect the Company's Property and the Supplier's records thereof and may
                    enter the Supplier's land and buildings for those purposes;<br>
                    (v) The Supplier shall promptly pay to the Company on demand the full replacement
                    value of any of the Company's Property which is not returned.<br><br>
                    <span class="font-weight-bold">19. INDEPENDENT CONTRACTOR</span><br>
                    The Supplier acts solely as an independent contractor in supplying the Goods
                    and/or performing the Services.<br><br>
                    <span class="font-weight-bold">20. LICENCES</span><br>
                    If the performance of the Order requires the Company to have any permit or license
                    from any government or other relevant authority, the Order shall be conditional upon
                    such permit or license being available at the required time.<br><br>

                </td>
                <td width="1%">&nbsp;</td>
                <td width="49%" style="text-align: justify;font-size: 9px;">
                    <span class="font-weight-bold">21. ADVERTISING</span><br>
                    The Supplier will not without the prior written consent of the Company advertise or
                    publish in any way whatsoever the fact that the Supplier has contracted to supply the
                    Goods or the Services to the Company.<br><br>
                    <span class="font-weight-bold">22. SEVERABILITY</span><br>
                    Any provision or term of this contract which is or may be void or unenforceable shall to
                    the extent of such invalidity or unenforceability be deemed severable and shall not
                    affect any other provision hereof.<br><br>
                    <span class="font-weight-bold">23. NOTICES</span><br>
                    Any notice hereunder shall be deemed to have been duly given if sent by prepaid first
                    class post, telex, telefax or telegraph to the party concerned at, in the case of the
                    Supplier, its last known address, and, in the case of the Company, the address
                    appearing on the Order. Notices sent by first class post shall be deemed to have been
                    given seven (7) days after dispatch and notices sent by telex, telefax or telegraph shall
                    be deemed to have been given on the date of dispatch.<br><br>
                    <span class="font-weight-bold">24. WAIVER</span><br>
                    Failure by the Company to exercise or enforce any rights under this contract or at law
                    shall not be deemed to be a waiver of any such right nor operate to bar its exercise or
                    enforcement at any future time or times.<br><br>
                    <span class="font-weight-bold">25. ENGLISH TEXT</span><br>
                    In the case of conflict between the English text of this contract and translations into
                    other languages, the English text shall prevail.<br><br>
                    <span class="font-weight-bold">26. GOVERNING LAW</span><br>
                    These Conditions and the Order shall be governed by and construed in accordance
                    with the laws of [JURISDICTION (consult with legal)] and the parties submit to the nonexclusive
                    jurisdiction of the courts of that place.<br><br>
                    <span class="font-weight-bold">27. HEALTH, SAFETY & ENVIRONMENT</span><br>
                    Supplier shall be responsible for providing a healthy and safe working environment for
                    its employees and sub-contractors during performance on the Company’s premises.
                    Supplier shall protect the environment, health and safety of Supplier's,
                    subcontractor' and the Company's employees and third parties from any danger
                    associated with such works. As minimum, Supplier shall ensure that Services are
                    performed in compliance with the Company’s, Health, Safety, and Environment policy
                    and site specific requirements. Supplier shall report all relevant accidents, injuries
                    and near-misses promptly to the Company.<br><br>
                    <span class="font-weight-bold">28. FORCE MAJEURE</span><br>
                    Neither party shall be in breach of the Conditions nor liable for delay in performing, or
                    failure to perform, any of its obligations under the Conditions if such delay or failure
                    result from events, circumstances or causes beyond its reasonable control. If the
                    period of delay or non-performance continues for Eight (8) weeks the Company may
                    terminate the Conditions or Order by giving 30 days written notice to the Supplier.<br><br>
                    <span class="font-weight-bold">29. VARIATION</span><br>
                    Except as set out in these Conditions, no variation, including the introduction of any
                    additional terms and conditions, shall be effective unless it is agreed in writing and
                    signed by the parties or their authorized representatives.
                </td>
            </tr>
        </table>
    </div>
@endif


