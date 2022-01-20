<div class="row">
    <div class="col-md-6">
        <p><b>Report Type :<span class="p-l-10"></span> Sales Detail </b></p>
        <p><b>From Date :<span class="p-l-10"></span>{{ $fromDate }} </b></p>
        <p><b>To Date :<span class="p-l-10"></span>{{ $toDate }} </b></p>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-sm table-striped hover table-bordered" width="100%">
                <thead>
                <tr>
                    <th>Location Code</th>
                    <th>Location</th>
                    <th>Segment</th>
                    <th>Transaction Type</th>
                    <th>Transaction Code</th>
                    <th>Transaction Date</th>
                    <th>Narration</th>
                    <th>Item Code</th>
                    <th>Item Description</th>
                    <th>UOM</th>
                    <th>Barcode</th>
                    <th>Sub Category</th>
                    <th>Revenue Acc Code</th>
                    <th>Sold Qty</th>
                    <th>Currency</th>
                    <th>Sold Price</th>
                    <th>Net Sales Amount</th>
                    <th>Discount Amount</th>
                </tr>
                </thead>
                <tbody>
                @foreach($invoiceDetails as $invoiceDetail)
                    @if($invoiceDetail->master != null)
                    <tr>
                    <td>{{ $invoiceDetail->master->warehouse->wareHouseCode }}</td>
                    <td>{{ $invoiceDetail->master->warehouse->location->locationName }}</td>
                    <td>{{ $invoiceDetail->master->segment->serviceLineDes }}</td>
                    <td>{{ $invoiceDetail->master->documentID }}</td>
                    <td>{{ $invoiceDetail->master->bookingInvCode }}</td>
                    <td>{{ $invoiceDetail->master->createdDateAndTime }}</td>
                    <td>{{ $invoiceDetail->comments }}</td>
                    <td>{{ $invoiceDetail->itemPrimaryCode }}</td>
                    <td>{{ $invoiceDetail->itemDescription }}</td>
                    <td>{{ $invoiceDetail->uom_default->UnitShortCode }}</td>
                    <td>{{ $invoiceDetail->item_by->barcode }}</td>
                    <td>{{ $invoiceDetail->item_by->financeSubCategory->categoryDescription }}</td>
                    <td>{{ $invoiceDetail->financeGLcodeRevenue }}</td>
                    <td>{{ $invoiceDetail->qtyIssued }}</td>
                    <td>{{ $invoiceDetail->currency->CurrencyCode }}</td>
                    <td>{{ number_format($invoiceDetail->sellingCostAfterMarginRpt,$company->reportingcurrency->DecimalPlaces) }}</td>
                    <td>{{ number_format($invoiceDetail->sellingCostAfterMarginRpt * $invoiceDetail->qtyIssued,$company->reportingcurrency->DecimalPlaces) }}</td>
                    <td>0</td>
                    </tr>
                    @endif
                @endforeach

                </tbody>
            </table>
        </div>
    </div>
</div>
