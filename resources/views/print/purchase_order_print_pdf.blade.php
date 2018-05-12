<html>
<head>
    <title>Purchase Order</title>
    <style>
        body {
            font-size: 12px;
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
            font-family: inherit;
            font-weight: 500;
            line-height: 1.2;
            color: inherit;
        }


        table > tbody > tr > td {
            font-size: 11.5px;
        }

        .theme-tr-head {
            background-color: #DEDEDE !important;
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

        .table th, .table td {
            border-top: 1px solid #c2cfd6 !important;
        }

        .table th, .table td {
            padding: 0.4rem !important;
        }

        .table th {
            background-color: #c2cfd6 !important;
        }

        tfoot > tr > td {
            border: 1px solid #a4b7c1;
        }

        .text-right {
            text-align: right !important;
        }

        table-bordered > thead > tr > th, .table-bordered > tbody > tr > th, .table-bordered > tfoot > tr > th, .table-bordered > thead > tr > td, .table-bordered > tbody > tr > td, .table-bordered > tfoot > tr > td {
            border: 1px solid #a4b7c1;
        }

        .font-weight-bold {
            font-weight: 700 !important;
        }

        hr {
            border: 0;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
        }

        table > thead > tr > th {
            font-size: 11.5px;
        }

        th {
            text-align: inherit;
            font-weight: bold;
        }

        .white-space-pre-line {
            white-space: pre-line;
        }
        p {
            margin-top: 0 !important;
        }
        .title{
            font-size: 1.3125rem;
            font-weight: 500;
        }
    </style>
</head>
<body>
<div class="row" id="print-section">
    <table style="width:100%">
        <tr>
            <td width="60%">
                <h3>
                    @if ($podata->documentSystemID == 2)
                        Purchase Order
                    @elseif ($podata->documentSystemID == 5 && $podata->poType_N == 5)
                        Work Order
                    @elseif ($podata->documentSystemID == 5 && $podata->poType_N == 6)
                        Sub Work Order
                    @elseif ($podata->documentSystemID == 52)
                        Direct Order
                    @endif
                </h3>
            </td>
            <td width="40%">
                <table>
                    <tr>
                        <td>
                            <h3>
                                @if ($podata->company)
                                    {{$podata->company->CompanyName}}
                                @endif
                            </h3>
                        </td>
                    </tr>
                </table>
                <table>
                    <tr>
                        <td width="170px"><span class="font-weight-bold">Purchase Order Number</span></td>
                        <td width="40px"><span class="font-weight-bold">:</span></td>
                        <td>
                            @if ($podata->purchaseOrderCode)
                                {{$podata->purchaseOrderCode}}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td><span class="font-weight-bold">Purchase Order Date </span></td>
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
<hr>
<div class="row">
    <table style="width:100%">
        <tr>
            <td style="width: 60%">
                <table>
                    <tr>
                        <td colspan="3"><span class="title">Sold To:</span></td>
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
                        <td colspan="3"><p>
                                @if ($podata->soldToAddressDescriprion)
                                    {{$podata->soldToAddressDescriprion}}
                                @endif
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3"><span class="title">Sold To:</span></td>
                    </tr>
                    <tr>
                        <td width="170px"><span class="font-weight-bold">Order Contact</span></td>
                        <td width="40px"><span class="font-weight-bold">:</span></td>
                        <td>{{$podata->soldTocontactPersonID}} </td>
                    </tr>
                    <tr>
                        <td><span class="font-weight-bold">Phone</span></td>
                        <td><span class="font-weight-bold">:</span></td>
                        <td>{{$podata->soldTocontactPersonTelephone}} </td>
                    </tr>
                    <tr>
                        <td><span class="font-weight-bold">Fax</span></td>
                        <td><span class="font-weight-bold">:</span></td>
                        <td>{{$podata->soldTocontactPersonFaxNo}} </td>
                    </tr>
                    <tr>
                        <td><span class="font-weight-bold">Email</span></td>
                        <td><span class="font-weight-bold">:</span></td>
                        <td>{{$podata->soldTocontactPersonEmail}} </td>
                    </tr>
                </table>
            </td>
            <td style="width: 40%">
                <table>
                    <tr>
                        <td><span class="title">Supplier:</span></td>
                    </tr>
                    <tr>
                        <td>{{$podata->supplierPrimaryCode}}</td>
                    </tr>
                    <tr>
                        <td>{{$podata->supplierName}}</td>
                    </tr>
                    <tr>
                        <td>{{$podata->supplierAddress}}</td>
                    </tr>
                </table>
                <table>
                    <tr>
                        <td width="170px"><span class="font-weight-bold">Contact</span></td>
                        <td width="40px"><span class="font-weight-bold">:</span></td>
                        <td>
                            @if ($podata->suppliercontact)
                                {{$podata->suppliercontact->contactPersonName}}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td><span class="font-weight-bold">Phone</span></td>
                        <td><span class="font-weight-bold">:</span></td>
                        <td>
                            @if ($podata->suppliercontact)
                                {{$podata->suppliercontact->contactPersonTelephone}}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td><span class="font-weight-bold">Fax</span></td>
                        <td><span class="font-weight-bold">:</span></td>
                        <td>
                            @if ($podata->suppliercontact)
                                {{$podata->suppliercontact->contactPersonFax}}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td><span class="font-weight-bold">Email</span></td>
                        <td><span class="font-weight-bold">:</span></td>
                        <td>
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
<hr>
<div class="row">
    <table style="width:100%">
        <tr>
            <td style="width: 60%">
                <table>
                    <tr>
                        <td><span class="title">Ship To:</span></td>
                    </tr>
                    <tr>
                        <td>
                            <p>
                                @if ($podata->company)
                                    {{$podata->company->CompanyName}}
                                @endif
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>
                                @if ($podata->shippingAddressDescriprion)
                                    {{$podata->shippingAddressDescriprion}}
                                @endif
                            </p>
                        </td>
                    </tr>
                </table>
                <table>
                    <tr>
                        <td width="170px"><span class="font-weight-bold">Ship Contact</span></td>
                        <td width="40px"><span class="font-weight-bold">:</span></td>
                        <td>{{$podata->shipTocontactPersonID}} </td>
                    </tr>
                    <tr>
                        <td><span class="font-weight-bold">Phone</span></td>
                        <td><span class="font-weight-bold">:</span></td>
                        <td>{{$podata->shipTocontactPersonTelephone}} </td>
                    </tr>
                    <tr>
                        <td><span class="font-weight-bold">Fax</span></td>
                        <td><span class="font-weight-bold">:</span></td>
                        <td>{{$podata->shipTocontactPersonFaxNo}} </td>
                    </tr>
                    <tr>
                        <td><span class="font-weight-bold">Email</span></td>
                        <td><span class="font-weight-bold">:</span></td>
                        <td>{{$podata->shipTocontactPersonEmail}} </td>
                    </tr>
                </table>
            </td>
            <td style="width: 40%">
                <table>
                    <tr>
                        <td><span class="title">Invoice To:</span></td>
                    </tr>
                    <tr>
                        <td>
                            <p>
                                @if ($podata->company)
                                    {{$podata->company->CompanyName}}
                                @endif
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>
                                @if ($podata->invoiceToAddressDescription)
                                    {{$podata->invoiceToAddressDescription}}
                                @endif
                            </p>
                        </td>
                    </tr>
                </table>
                <table>
                    <tr>
                        <td width="170px"><span class="font-weight-bold">Payment Contact</span></td>
                        <td width="40px"><span class="font-weight-bold">:</span></td>
                        <td>
                            @if ($podata->suppliercontact)
                                {{$podata->invoiceTocontactPersonID}}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td><span class="font-weight-bold">Phone</span></td>
                        <td><span class="font-weight-bold">:</span></td>
                        <td>
                            @if ($podata->suppliercontact)
                                {{$podata->invoiceTocontactPersonTelephone}}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td><span class="font-weight-bold">Fax</span></td>
                        <td><span class="font-weight-bold">:</span></td>
                        <td>
                            @if ($podata->suppliercontact)
                                {{$podata->invoiceTocontactPersonFaxNo}}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td><span class="font-weight-bold">Email</span></td>
                        <td><span class="font-weight-bold">:</span></td>
                        <td>
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
<hr>
<div class="row">
    <table style="width:50%">
        <tr>
            <td style="width:10.5%"><span class="font-weight-bold">Narration</span></td>
            <td style="width:10%"><span class="font-weight-bold">:</span></td>
            <td style="width:20%">{{$podata->narration}}</td>
        </tr>
    </table>
</div>
<div class="row">
    <table style="width:100%">
        <tr>
            <td style="width:90%">
                <table>
                    <tr>
                        <td style="width: 0%"><span class="font-weight-bold">Expected Date</span></td>
                        <td style="width: 0%"><span class="font-weight-bold">:</span></td>
                        <td style="width: 23%">{{ \App\helper\Helper::dateFormat($podata->expectedDeliveryDate)}}</td>
                    </tr>
                </table>
            </td>
            <td style="width:10%">
                <table>
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
    <table style="width:100%" class="table table-bordered table-sm">
        <thead>
        <tr>
            <th>#</th>
            <th>Item Code</th>
            <th>Item Description</th>
            <th>Supp.Part No</th>
            <th>UOM</th>
            <th>Qty</th>
            <th>Unit Cost</th>
            <th>Dis. Per Unit</th>
            <th>Net Cost Pern Unit</th>
            <th>Net Amount</th>
        </tr>
        </thead>
        <tbody>
        {{ $subTotal = 0 }}

        {{ $x = 1 }}
        @foreach ($podata->detail as $det)
            {{ $netUnitCost = 0 }}
            {{ $subTotal += $det->netAmount }}
            {{ $netUnitCost = $det->unitCost - $det->discountAmount }}
            <tr>
                <td>{{ $x = 1 }}</td>
                <td>{{$det->itemPrimaryCode}}</td>
                <td>{{$det->itemDescription}}</td>
                <td>{{$det->supplierPartNumber}}</td>
                <td>{{$det->unit->UnitShortCode}}</td>
                <td class="text-right">{{$det->noQty}}</td>
                <td class="text-right">{{number_format($det->unitCost, 2)}}</td>
                <td class="text-right">{{number_format($det->discountAmount, 2)}}</td>
                <td class="text-right">{{number_format($netUnitCost, 2)}}</td>
                <td class="text-right">{{number_format($det->netAmount, 2)}}</td>
            </tr>
            {{ $x++ }}
        @endforeach
        </tbody>
        <tfoot>
        <tr>
            <td colspan="9" class="text-right"><span class="font-weight-bold" style="font-size: 12px">Sub Total</span>
            </td>
            <td class="text-right" style="font-size: 12px">
                @if ($podata->detail)
                    {{number_format($subTotal, 2)}}
                @endif
            </td>
        </tr>
        <tr>
            <td colspan="9" class="text-right"><span class="font-weight-bold" style="font-size: 12px">Discount</span>
            </td>
            <td class="text-right" style="font-size: 12px"><span class="font-weight-bold">
                     {{number_format($podata->poDiscountAmount, 2)}}
                </span></td>
        </tr>
        @if ($podata->supplierVATEligible)
            <td colspan="9" class="text-right"><span
                        class="font-weight-bold" style="font-size: 12px">Tax Amount({{$podata->VATPercentage .'%'}}
                    )</span></td>
            <td class="text-right" style="font-size: 12px"><span
                        class="font-weight-bold">{{number_format($podata->VATAmount, 2)}}</span>
            </td>
        @endif
        <tr>
            <td colspan="9" class="text-right"><span class="font-weight-bold" style="font-size: 12px">Net Amount</span>
            </td>
            <td class="text-right" style="font-size: 12px">
                @if ($podata->detail)
                    {{number_format($subTotal - $podata->poDiscountAmount + $podata->VATAmount, 2)}}
                @endif
            </td>
        </tr>
        </tfoot>
    </table>
</div>
<div class="row">
    <table style="width:100%">
        <tr>
            <td width="100px" valign="top"><span class="font-weight-bold">Delivery Terms</span></td>
            <td width="50" valign="top"><span class="font-weight-bold">:</span></td>
            <td>
                @if ($podata->transactioncurrency)
                    {{$podata->deliveryTerms}}
                @endif
            </td>
        </tr>
        <tr>
            <td valign="top"><span class="font-weight-bold">Panalty Terms</span></td>
            <td valign="top"><span class="font-weight-bold">:</span></td>
            <td>
                @if ($podata->transactioncurrency)
                    {{$podata->panaltyTerms}}
                @endif
            </td>
        </tr>
        <tr>
            <td valign="top"><span class="font-weight-bold">Payment Terms</span></td>
            <td valign="top"><span class="font-weight-bold">:</span></td>
            <td>
                @if ($podata->transactioncurrency)
                    {{$podata->paymentTerms}}
                @endif
            </td>
        </tr>
    </table>
</div>
<table style="padding: 5px;">
    <tr>
        <td><span class="font-weight-bold">Electronically Approved By :</span></td>
    </tr>
    <tr>
        &nbsp;
    </tr>
</table>
<div class="row">
    <table style="width:100%;padding: 5px;">
        <tr>
            @if ($podata->approved_by)
                @foreach ($podata->approved_by as $det)
                    <td style="padding-right: 25px">
                        <div>
                            @if($det->employee)
                                {{$det->employee->empFullName }}
                            @endif
                        </div>
                        <div><span>{{$det->approvedDate }}</span></div>
                        <div style="width: 3px"></div>
                    </td>
                @endforeach
            @endif
        </tr>
    </table>
</div>
<div class="row">
    <table style="width:100%; margin-bottom: 10%;padding: 5px;">
        <tr>
            <td style="width:100%">
                <p><span class="font-weight-bold"><span [innerHTML]="docRefNumber"
                                                        class="white-space-pre-line">{!! nl2br($docRef["docRefNumber"]) !!}</span></span>
                </p>

            </td>
        </tr>
    </table>
</div>
</body>
</html>
