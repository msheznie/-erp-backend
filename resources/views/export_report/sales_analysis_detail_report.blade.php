<div class="row">
    <div class="col-md-6">
        <p><b>{{ __('custom.report_type') }} :<span class="p-l-10"></span> {{ __('custom.sales_detail') }} </b></p>
        <p><b>{{ __('custom.from_date') }} :<span class="p-l-10"></span>{{ \Carbon\Carbon::parse($fromDate)->format("d/m/Y") }} </b></p>
        <p><b>{{ __('custom.to_date') }} :<span class="p-l-10"></span>{{ \Carbon\Carbon::parse($toDate)->format("d/m/Y") }} </b></p>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-sm table-striped hover table-bordered" width="100%">
                <thead>
                <tr>
                    <th>{{ __('custom.location_code') }}</th>
                    <th>{{ __('custom.location') }}</th>
                    <th>{{ __('custom.segment') }}</th>
                    <th>{{ __('custom.transaction_type') }}</th>
                    <th>{{ __('custom.transaction_code') }}</th>
                    <th>{{ __('custom.transaction_date') }}</th>
                    <th>{{ __('custom.narration') }}</th>
                    <th>{{ __('custom.item_code') }}</th>
                    <th>{{ __('custom.item_description') }}</th>
                    <th>{{ __('custom.uom') }}</th>
                    <th>{{ __('custom.barcode') }}</th>
                    <th>{{ __('custom.sub_category') }}</th>
                    <th>{{ __('custom.revenue_acc_code') }}</th>
                    <th>{{ __('custom.sold_qty') }}</th>
                    <th>{{ __('custom.currency') }}</th>
                    <th>{{ __('custom.sold_price') }}</th>
                    <th>{{ __('custom.net_sales_amount') }}</th>
                    <th>{{ __('custom.discount_amount') }}</th>
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
