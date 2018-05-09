<div class="row">
    <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
        <h3 style="bottom: 0;position: absolute;">
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
    </div>
    <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
        <h3>
            @if ($podata->company)
                $podata->company->CompanyName
            @endif
        </h3>
        <table>
            <tr>
                <td width="170px"><span class="font-weight-bold">Purchase Order Number</span></td>
                <td width="40px"><span class="font-weight-bold">:</span></td>
                <td>
                    @if ($podata->purchaseOrderCode)
                        $podata->purchaseOrderCode
                    @endif
                </td>
            </tr>
            <tr>
                <td><span class="font-weight-bold">Purchase Order Date </span></td>
                <td><span class="font-weight-bold">:</span></td>
                <td>
                    @if ($podata->createdDateTime)
                        $podata->createdDateTime
                    @endif
                </td>
            </tr>
            <tr>
                <td><span class="font-weight-bold">Reference Number </span></td>
                <td><span class="font-weight-bold">:</span></td>
                <td>
                    @if ($podata->referenceNumber)
                        $podata->referenceNumber
                    @endif
                </td>
            </tr>
        </table>
    </div>
</div>
<hr>
<div class="row">
    <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
        <h4>Sold To:</h4>
        <p>@if ($podata->company)
                $podata->company->CompanyName
            @endif
        </p>
        <p>@if ($podata->soldToAddressDescriprion)
                $podata->soldToAddressDescriprion
            @endif
        </p>
        <div style="min-height: 25px"></div>
        <table>
            <tr>
                <td width="170px"><span class="font-weight-bold">Order Contact</span></td>
                <td width="40px"><span class="font-weight-bold">:</span></td>
                <td>
                    @if ($podata->soldTocontactPersonID)
                        $podata->soldTocontactPersonID
                    @endif
                </td>
            </tr>
            <tr>
                <td><span class="font-weight-bold">Phone </span></td>
                <td><span class="font-weight-bold">:</span></td>
                <td>
                    @if ($podata->soldTocontactPersonTelephone)
                        $podata->soldTocontactPersonTelephone
                    @endif
                </td>
            </tr>
            <tr>
                <td><span class="font-weight-bold">Fax </span></td>
                <td><span class="font-weight-bold">:</span></td>
                <td>
                    @if ($podata->soldTocontactPersonFaxNo)
                        $podata->soldTocontactPersonFaxNo
                    @endif
                </td>
            </tr>
            <tr>
                <td><span class="font-weight-bold">Email </span></td>
                <td><span class="font-weight-bold">:</span></td>
                <td>
                    @if ($podata->soldTocontactPersonEmail)
                        $podata->soldTocontactPersonEmail
                    @endif
                </td>
            </tr>
        </table>
    </div>
    <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
        <h4>Supplier:</h4>
        <p>@if ($podata->supplierPrimaryCode)
                $podata->supplierPrimaryCode
            @endif
        </p>
        <p>@if ($podata->supplierName)
                $podata->supplierName
            @endif
        </p>
        <p>@if ($podata->supplierAddress)
                $podata->supplierAddress
            @endif
        </p>
        <table>
            <tr>
                <td width="170px"><span class="font-weight-bold">Contact</span></td>
                <td width="40px"><span class="font-weight-bold">:</span></td>
                <td>
                    @if ($podata->suppliercontact)
                        $podata->suppliercontact->contactPersonName
                    @endif
                </td>
            </tr>
            <tr>
                <td><span class="font-weight-bold">Phone </span></td>
                <td><span class="font-weight-bold">:</span></td>
                <td>
                    @if ($podata->suppliercontact)
                        $podata->suppliercontact->contactPersonTelephone
                    @endif
                </td>
            </tr>
            <tr>
                <td><span class="font-weight-bold">Fax </span></td>
                <td><span class="font-weight-bold">:</span></td>
                <td>
                    @if ($podata->suppliercontact)
                        $podata->suppliercontact->contactPersonFax
                    @endif
                </td>
            </tr>
            <tr>
                <td><span class="font-weight-bold">Email </span></td>
                <td><span class="font-weight-bold">:</span></td>
                <td>
                    @if ($podata->suppliercontact)
                        $podata->suppliercontact->contactPersonEmail
                    @endif
                </td>
            </tr>
        </table>
    </div>
</div>
<hr>
<div class="row">
    <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
        <h4>Ship To:</h4>

        <p><span *ngIf="podata.company">{{podata.company.CompanyName}}</span></p>

        <p>{{podata.shippingAddressDescriprion}}</p>
        <table>
            <tr>
                <td width="170px"><span class="font-weight-bold">Ship Contact</span></td>
                <td width="40px"><span class="font-weight-bold">:</span></td>
                <td><span>{{podata.shipTocontactPersonID}}</span></td>
            </tr>
            <tr>
                <td><span class="font-weight-bold">Phone </span></td>
                <td><span class="font-weight-bold">:</span></td>
                <td><span>{{podata.shipTocontactPersonTelephone}}</span></td>
            </tr>
            <tr>
                <td><span class="font-weight-bold">Fax </span></td>
                <td><span class="font-weight-bold">:</span></td>
                <td><span>{{podata.shipTocontactPersonFaxNo}}</span></td>
            </tr>
            <tr>
                <td><span class="font-weight-bold">Email </span></td>
                <td><span class="font-weight-bold">:</span></td>
                <td><span>{{podata.shipTocontactPersonEmail}}</span></td>
            </tr>
        </table>
    </div>
    <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
        <h4>Invoice To:</h4>

        <p><span *ngIf="podata.company">{{podata.company.CompanyName}}</span></p>

        <p>{{podata.invoiceToAddressDescription}}</p>
        <table>
            <tr>
                <td width="170px"><span class="font-weight-bold">Payment Contact</span></td>
                <td width="40px"><span class="font-weight-bold">:</span></td>
                <td><span *ngIf="podata.suppliercontact">{{podata.invoiceTocontactPersonID}}</span></td>
            </tr>
            <tr>
                <td><span class="font-weight-bold">Phone </span></td>
                <td><span class="font-weight-bold">:</span></td>
                <td><span *ngIf="podata.suppliercontact">{{podata.invoiceTocontactPersonTelephone}}</span></td>
            </tr>
            <tr>
                <td><span class="font-weight-bold">Fax </span></td>
                <td><span class="font-weight-bold">:</span></td>
                <td><span *ngIf="podata.suppliercontact">{{podata.invoiceTocontactPersonFaxNo}}</span></td>
            </tr>
            <tr>
                <td><span class="font-weight-bold">Email </span></td>
                <td><span class="font-weight-bold">:</span></td>
                <td><span *ngIf="podata.suppliercontact">{{podata.invoiceTocontactPersonEmail}}</span></td>
            </tr>
        </table>
    </div>
</div>
<hr>
<div class="row">
    <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
        <table>
            <tr>
                <td width="170px" valign="top"><span class="font-weight-bold">Narration</span></td>
                <td width="40px" valign="top"><span class="font-weight-bold">:</span></td>
                <td valign="top"><span>{{podata.narration}}</span></td>
            </tr>
        </table>
    </div>
</div>
<div class="row">
    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
        <table>
            <tr>
                <td width="170px"><span class="font-weight-bold">Expected Date</span></td>
                <td width="40px"><span class="font-weight-bold">:</span></td>
                <td><span>{{podata.expectedDeliveryDate | date:'dd/MM/yyyy'}}</span></td>
            </tr>
        </table>
    </div>
    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
        <div class="float-right">
            <table>
                <tr>
                    <td><span class="font-weight-bold">Currency</span></td>
                    <td><span class="font-weight-bold">:</span></td>
                    <td><span
                                *ngIf="podata.transactioncurrency">{{podata.transactioncurrency.CurrencyCode }}</span>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <table class="table table-bordered table-sm">
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
            <tr *ngFor="let det of podata.detail; let i = index">
                <td>{{i+1}}</td>
                <td>{{det.itemPrimaryCode}}</td>
                <td>{{det.itemDescription}}</td>
                <td>{{det.supplierPartNumber}}</td>
                <td>{{det.unit.UnitShortCode}}</td>
                <td class="text-right">{{det.noQty}}</td>
                <td class="text-right">{{det.unitCost | number: decimalPlaces}}</td>
                <td class="text-right">{{det.discountAmount | number: decimalPlaces}}</td>
                <td class="text-right">{{det.unitCost - det.discountAmount | number:decimalPlaces}}</td>
                <td class="text-right">{{det.netAmount | number:decimalPlaces}}</td>
            </tr>
            <tr *ngIf="podata.detail?.length == 0">
                <td colspan="10">No records found!</td>
            </tr>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="9" class="text-right"><span class="font-weight-bold">Sub Total</span></td>
                <td class="text-right">
                    <span *ngIf="podata.detail" class="font-weight-bold">{{subTotal | number:decimalPlaces}}</span>
                </td>
            </tr>
            <tr>
                <td colspan="9" class="text-right"><span class="font-weight-bold">Discount</span></td>
                <td class="text-right"><span
                            class="font-weight-bold">{{podata.poDiscountAmount | number:decimalPlaces}}</span></td>
            </tr>
            <tr *ngIf="podata.supplierVATEligible">
                <td colspan="9" class="text-right"><span
                            class="font-weight-bold">Tax Amount({{podata.VATPercentage + '%'}})</span></td>
                <td class="text-right"><span class="font-weight-bold">{{podata.VATAmount | number:decimalPlaces}}</span>
                </td>
            </tr>
            <tr>
                <td colspan="9" class="text-right"><span class="font-weight-bold">Net Amount</span></td>
                <td class="text-right"><span *ngIf="podata.detail"
                                             class="font-weight-bold">{{subTotal - podata.poDiscountAmount + podata.VATAmount| number:decimalPlaces}}</span>
                </td>
            </tr>
            </tfoot>
        </table>
    </div>
</div>
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <table>
            <tr>
                <td width="100px" valign="top"><span class="font-weight-bold">Delivery Terms</span></td>
                <td width="50" valign="top"><span class="font-weight-bold">:</span></td>
                <td valign="top"><span
                            *ngIf="podata.transactioncurrency">{{podata.deliveryTerms}}</span>
                </td>
            </tr>
            <tr>
                <td valign="top"><span class="font-weight-bold">Panalty Terms</span></td>
                <td valign="top"><span class="font-weight-bold">:</span></td>
                <td valign="top"><span
                            *ngIf="podata.transactioncurrency"> {{podata.panaltyTerms}}</span>
                </td>
            </tr>
            <tr>
                <td valign="top"><span class="font-weight-bold">Payment Terms</span></td>
                <td valign="top"><span class="font-weight-bold">:</span></td>
                <td valign="top"><span
                            *ngIf="podata.transactioncurrency"> {{podata.paymentTerms}}</span>
                </td>
            </tr>
        </table>
    </div>
</div>
<div class="row" style="margin-top: 10px">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <span class="font-weight-bold">Electronically Approved By :</span>
    </div>
</div>
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="row" *ngIf="podata.approved">
            <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2" *ngFor="let det of podata.approved; let i = index">
                <div><span *ngIf="det.employee">{{ det.employee.empFullName }}</span></div>
                <div><span>{{ det.approvedDate | date:'dd/MM/yyyy' }}</span></div>
            </div>
        </div>
    </div>
</div>
<div class="row" style="margin-top: 30px">
    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
        <p><span class="font-weight-bold"><span [innerHTML]="docRefNumber" class="white-space-pre-line"></span></span>
        </p>
        <!--<p><span class="font-weight-bold">{{now | date:'dd/MM/yyyy'}}</span></p>-->
    </div>
</div>

