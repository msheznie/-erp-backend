<div class="row">
    <div class="col-md-6">
        <p><b>Report Type :<span class="p-l-10"></span> Sales Detail Summary </b></p>
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
                    <th  colspan="4">
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
                    @foreach($warehouses as $item)
                        <td>Total Sold Qty</td>
                        <td>Currency</td>
                        <td>Total Sales Amount</td>
                        <td>Total Discount Amount</td>
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

                                @foreach($warehouseCodes as $item3)
                                    @php
                                        $j = $loop->index;
                                         $tot  = isset($totalReturn[$j][$k][0]->totalReturned) ? $totalReturn[$j][$k][0]->totalReturned: 0;

                                    @endphp

                            @if($i == $j)  <td>{{$item2->totalQty - $tot}}</td> @endif
                            @if($i != $j)  <td>0</td> @endif


                            @if($i == $j)    <td>{{ $item2->CurrencyCode }}</td> @endif
                            @if($i != $j)    <td>-</td> @endif

                            @if($i == $j)
                                @if($currencyID == 1)
                                    <td>{{ number_format(($item2->totalQty - $tot) * $item2->sellingCostAfterMarginLocal,$company->localcurrency->DecimalPlaces) }}</td>
                                @endif
                                @if($currencyID == 2)
                                    <td>{{ number_format(($item2->totalQty - $tot) * $item2->sellingCostAfterMarginRpt,$company->reportingcurrency->DecimalPlaces) }}</td>
                                @endif
                            @endif
                            @if($i != $j)    <td>0</td> @endif

                            @if($i == $j)    <td>0</td> @endif
                            @if($i != $j)    <td>0</td> @endif
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
