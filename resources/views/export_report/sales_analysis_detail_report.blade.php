<div class="row">
    <div class="col-md-6">
        <p><b>Report Type :<span class="p-l-10"></span> Sales Detail </b></p>
        <p><b>From Date :<span class="p-l-10"></span>{{ \Carbon\Carbon::parse($fromDate)->format("d/m/Y") }} </b></p>
        <p><b>To Date :<span class="p-l-10"></span>{{ \Carbon\Carbon::parse($toDate)->format("d/m/Y") }} </b></p>
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
                    @if($invoiceDetail->master != null && $invoiceDetail->item_by != null)
                    <tr>
                    <td>{{ $invoiceDetail->master->warehouse->wareHouseCode }}</td>
                    <td>{{ $invoiceDetail->master->warehouse->location->locationName }}</td>
                    <td>{{ $invoiceDetail->master->segment->ServiceLineDes }}</td>
                    <td>{{ $invoiceDetail->master->documentID }}</td>
                    <td>{{ $invoiceDetail->master->bookingInvCode }}</td>
                    <td>{{ \Carbon\Carbon::parse($invoiceDetail->master->createdDateAndTime)->format("d/m/Y") }}</td>
                    <td>{{ $invoiceDetail->comments }}</td>
                    <td>{{ $invoiceDetail->itemPrimaryCode }}</td>
                    <td>{{ $invoiceDetail->itemDescription }}</td>
                    <td>{{ $invoiceDetail->uom_default->UnitShortCode }}</td>
                    <td>{{ $invoiceDetail->item_by->barcode }}</td>
                    <td>{{ $invoiceDetail->item_by->financeSubCategory->categoryDescription }}</td>
                    <td>{{ $invoiceDetail->financeGLcodeRevenue }}</td>
                        @php $isSalesReturn = isset($invoiceDetail->sales_return_details->master->approvedYN) ? $invoiceDetail->sales_return_details->master->approvedYN: 0; @endphp
                        @if($isSalesReturn == -1 )
                    <td>{{ $invoiceDetail->qtyIssued - $invoiceDetail->sales_return_details->qtyReturned }}</td>
                        @endif  @if($isSalesReturn != -1 )
                    <td>{{ $invoiceDetail->qtyIssued }}</td>
                        @endif

                        @if($currencyID == 1)
                    <td>{{ $invoiceDetail->local_currency->CurrencyCode }}</td>
                        @endif
                        @if($currencyID == 2)
                    <td>{{ $invoiceDetail->reporting_currency->CurrencyCode }}</td>
                        @endif

                        @if($currencyID == 1)
                        <td>{{ number_format($invoiceDetail->sellingCostAfterMarginLocal,$company->localcurrency->DecimalPlaces) }}</td>
                        @endif
                        @if($currencyID == 2)
                        <td>{{ number_format($invoiceDetail->sellingCostAfterMarginRpt,$company->reportingcurrency->DecimalPlaces) }}</td>
                        @endif

                        @if($currencyID == 1)
                        <td>{{ number_format($invoiceDetail->sellingTotal / $invoiceDetail->localCurrencyER,$company->localcurrency->DecimalPlaces) }}</td>
                        @endif
                        @if($currencyID == 2)
                        <td>{{ number_format($invoiceDetail->sellingTotal / $invoiceDetail->reportingCurrencyER,$company->reportingcurrency->DecimalPlaces) }}</td>
                        @endif


                    <td>0</td>
                    </tr>
                    @endif
                @endforeach

                </tbody>
            </table>
        </div>
    </div>
</div>
