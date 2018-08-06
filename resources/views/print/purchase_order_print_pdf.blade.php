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


</style>

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
                @if ($podata->company)
                    {{$podata->company->CompanyName}}
                @endif
            </td>
            <td style="width:33%;font-size: 10px;vertical-align: top;">
                <span style="margin-left: 38%;">Printed Date :  {{date("d-M-y", strtotime(now()))}}</span>
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
                            <td><img src="logos/{{$podata->company->companyLogo}}" width="180px" height="60px"></td>
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
                                    @if ($podata->company)
                                        {{$podata->company->CompanyName}}
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
                                    @if ($podata->company)
                                        {{$podata->company->CompanyName}}
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
                                    @if ($podata->company)
                                        {{$podata->company->CompanyName}}
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
                                    @if ($podata->company)
                                        {{$podata->company->CompanyName}}
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
        <table style="width:100%;" class="table table-bordered table-striped table-sm">
            <thead>
            <tr style="border-top: 1px solid black;">
                <th style="text-align: center; width: 2%;">#</th>
                <th style="text-align: center; width: 8%;">Item Code</th>
                <th style="text-align: center; width: 34%;">Item Description</th>
                <th style="text-align: center; width: 5%;">Sup.Part No</th>
                <th style="text-align: center ; width: 5%;">UOM</th>
                <th style="text-align: center ; width: 5%;">Qty</th>
                <th style="text-align: center ; width: 10%;">Unit Cost</th>
                <th style="text-align: center ; width: 8%;">Dis. Per Unit</th>
                <th style="text-align: center ; width: 10%;">Net Cost Per Unit</th>
                <th style="text-align: center ; width: 13%;">Net Amount</th>
            </tr>
            </thead>
            <tbody>
            {{ $subTotal = 0 }}
            {{ $x = 1 }}
            @foreach ($podata->detail as $det)
                {{ $netUnitCost = 0 }}
                {{ $subTotal += $det->netAmount }}
                {{ $netUnitCost = $det->unitCost - $det->discountAmount }}
                <tr style="border-bottom: 1px solid black;">
                    <td>{{ $x  }}</td>
                    <td>{{$det->itemPrimaryCode}}</td>
                    <td nobr="true">{{$det->itemDescription}} <br> {!! nl2br($det->comment) !!}</td>
                    <td>{{$det->supplierPartNumber}}</td>
                    <td>{{$det->unit->UnitShortCode}}</td>
                    <td class="text-right">{{$det->noQty}}</td>
                    <td class="text-right">{{number_format($det->unitCost, $numberFormatting)}}</td>
                    <td class="text-right">{{number_format($det->discountAmount, $numberFormatting)}}</td>
                    <td class="text-right">{{number_format($netUnitCost, $numberFormatting)}}</td>
                    <td class="text-right">{{number_format($det->netAmount, $numberFormatting)}}</td>
                </tr>
                {{ $x++ }}
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="row">
        <table style="width:100%;" class="table table-bordered">
            <tbody>
            <tr>
                <td  style="border-bottom: none !important;border-left: none !important;width: 60%;">&nbsp;</td>
                <td  class="text-right" style="width: 20%;border-left: 1px solid rgb(127, 127, 127)!important;"><span class="font-weight-bold"
                                                         style="font-size: 11px">Sub Total</span>
                </td>
                <td class="text-right" style="font-size: 11px;width: 20%;border-left: 1px solid rgb(127, 127, 127) !important;border-right: 1px solid rgb(127, 127, 127) !important;">
                <span class="font-weight-bold">
                @if ($podata->detail)
                        {{number_format($subTotal, $numberFormatting)}}
                    @endif
                </span>
                </td>
            </tr>
            <tr>
                <td style="border-bottom: none !important;border-top: none !important;border-left: none !important;">&nbsp;</td>
                <td class="text-right" style="font-size: 11px;border-left: 1px solid rgb(127, 127, 127)!important;"><span class="font-weight-bold"
                                                                                 style="font-size: 11px">Discount</span>
                </td>
                <td class="text-right" style="font-size: 11px;border-left: 1px solid rgb(127, 127, 127) !important;border-right: 1px solid rgb(127, 127, 127) !important;"><span class="font-weight-bold">
                     {{number_format($podata->poDiscountAmount, $numberFormatting)}}
                </span>
                </td>
            </tr>
            @if ($podata->supplierVATEligible)
                <tr>
                    <td style="border-bottom: none !important;border-top: none !important;border-left: none !important;">&nbsp;</td>
                    <td class="text-right" style="border-left: 1px solid rgb(127, 127, 127)!important;"><span
                                class="font-weight-bold"
                                style="font-size: 11px">Tax Amount({{$podata->VATPercentage .'%'}}
                            )</span></td>
                    <td class="text-right" style="font-size: 11px;border-left: 1px solid rgb(127, 127, 127) !important;border-right: 1px solid rgb(127, 127, 127) !important;"><span
                                class="font-weight-bold">{{number_format($podata->VATAmount, $numberFormatting)}}</span>
                    </td>
                </tr>
            @endif
            <tr>
                <td style="border-bottom: none !important;border-top: none !important;border-left: none !important;">&nbsp;</td>
                <td class="text-right" style="border-left: 1px solid rgb(127, 127, 127)!important;"><span class="font-weight-bold"
                                                         style="font-size: 11px">Net Amount</span>
                </td>
                <td class="text-right" style="font-size: 11px;border-left: 1px solid rgb(127, 127, 127) !important;border-right: 1px solid rgb(127, 127, 127) !important;">
                <span class="font-weight-bold">
                @if ($podata->detail)
                        {{number_format($subTotal - $podata->poDiscountAmount + $podata->VATAmount, $numberFormatting)}}
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
