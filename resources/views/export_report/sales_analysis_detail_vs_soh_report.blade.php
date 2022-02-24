<div class="row">
    <div class="col-md-6">
        <p><b>Report Type :<span class="p-l-10"></span> Sales Vs Current SOH </b></p>
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
                    <th colspan="6"></th>
                    @foreach($warehouses as $item)
                    <th  colspan="5">
                        {{ $item }}
                    </th>
                    @endforeach

                </tr>
                <tr>

                    <th>Segment</th>
                    <th>Item Code</th>
                    <th>Item Description</th>
                    <th>UOM</th>
                    <th>Barcode</th>
                    <th>Sub Category</th>
                        @foreach($invoiceDetails as $item)


                        <td>Total Sold Qty</td>
                    @php $currency = isset($item[0]->CurrencyCode) ?  $item[0]->CurrencyCode: null @endphp
                       @if($currency != null) <td>Total Sales Amount ({{ isset($item[0]->CurrencyCode) ?  $item[0]->CurrencyCode: 0 }})</td> @endif
                       @if($currency == null) <td>Total Sales Amount</td> @endif
                        <td>Opening Stock</td>
                        <td>Current Period SOH</td>
                        <td>Total SOH</td>

                        @endforeach

                </tr>

                </thead>
                <tbody>
                @foreach($invoiceDetails as $item1)
                    @php $i = $loop->index @endphp
                    @foreach($item1 as $item2)
                        @php $k = $loop->index @endphp
                        <tr>
                            <td>{{$item2->ServiceLineDes}}</td>
                            <td>{{$item2->itemPrimaryCode}}</td>
                            <td>{{$item2->itemDescription}}</td>
                            <td>{{$item2->UnitShortCode}}</td>
                            <td>{{$item2->barcode}}</td>
                            <td>{{$item2->categoryDescription}}</td>

                                @foreach($warehouseArraySum as $item3)
                                    @php $j = $loop->index;
                                         $tot  = isset($totalReturn[$j][$k][0]->totalReturned) ? $totalReturn[$j][$k][0]->totalReturned: 0;


                                    @endphp
                                @if($i == $j)  <td>{{$item2->totalQty - $tot}}</td> @endif
                                @if($i != $j)  <td>0</td> @endif

                                @if($i == $j)
                                    @if($currencyID == 1)
                                        <td>{{ number_format(($item2->totalQty - $tot) * $item2->sellingCostAfterMarginLocal,$company->localcurrency->DecimalPlaces) }}</td>
                                    @endif
                                    @if($currencyID == 2)
                                        <td>{{ number_format(($item2->totalQty - $tot) * $item2->sellingCostAfterMarginRpt,$company->reportingcurrency->DecimalPlaces) }}</td>
                                    @endif
                                @endif
                                @if($i != $j)    <td>0</td> @endif

                                <td>{{ isset($item3[$k][0][0][0]->totalOpening) ?  $item3[$k][0][0][0]->totalOpening: 0}}</td>


                                    @php

                                        $totalOpening = isset($item3[$k][0][0][0]->totalOpening) ?  $item3[$k][0][0][0]->totalOpening: 0;
                                        $totalCurrent = isset($item3[$k][0][1][0]->totalCurrent) ?  $item3[$k][0][1][0]->totalCurrent: 0;
                                    @endphp
                                   <td>{{$totalCurrent -  $totalOpening  }}</td>



                                    <td>{{ isset($item3[$k][0][1][0]->totalCurrent) ?  $item3[$k][0][1][0]->totalCurrent: 0 }}</td>


                            @endforeach
                        </tr>
                    @endforeach
                @endforeach
                </tbody>

            </table>
        </div>
        <br>
    </div>
</div>
