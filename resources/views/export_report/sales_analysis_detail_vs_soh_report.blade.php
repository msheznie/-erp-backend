<div class="row">
    <div class="col-md-6">
        <p><b>Report Type :<span class="p-l-10"></span> Sales Vs Current SOH </b></p>
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
                        @foreach($warehouses as $item)


                        <td>Total Sold Qty</td>
                        <td>Total Sales Amount (OMR)</td>
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

                        <tr>
                            <td>{{$item2->ServiceLineDes}}</td>
                            <td>{{$item2->itemPrimaryCode}}</td>
                            <td>{{$item2->itemDescription}}</td>
                            <td>{{$item2->UnitShortCode}}</td>
                            <td>{{$item2->barcode}}</td>
                            <td>{{$item2->categoryDescription}}</td>

                                @foreach($warehouseCodes as $item4)
                                    @php $j = $loop->index @endphp

                                @if($i == $j)  <td>{{$item2->totalQty}}</td> @endif
                                @if($i != $j)  <td></td> @endif




                                @if($i == $j)      <td>{{ number_format($item2->sellingCostAfterMarginRpt * $item2->totalQty,$company->reportingcurrency->DecimalPlaces) }}</td> @endif
                                @if($i != $j)    <td></td> @endif


                                @foreach($warehouseArraySum as $item3)
                                    @php $x = $loop->index @endphp

                                    @if($i == $j && $x == $j)<td>{{ isset($item3[0][0][0]->totalOpening) ?  $item3[0][0][0]->totalOpening: null}}</td>@endif
                                @endforeach
                                @if($i != $j)    <td></td> @endif


                                    @foreach($warehouseArraySum as $item3)
                                        @php $x = $loop->index @endphp
                                    @if($i == $j && $x == $j)<td>{{ isset($item3[0][1][0]->totalCurrent) ?  $item3[0][1][0]->totalCurrent: null }}</td>@endif
                                    @endforeach
                                @if($i != $j)    <td></td> @endif

                                @foreach($warehouseArraySum as $item3)
                                    @php $x = $loop->index @endphp
                                    @if($i == $j && $x == $j)<td>{{ isset($item3[0][0][0]->totalOpening) ?  $item3[0][0][0]->totalOpening: null + isset($item3[0][1][0]->totalCurrent) ?  $item3[0][1][0]->totalCurrent: null}}</td> @endif
                                @endforeach
                                @if($i != $j)    <td></td> @endif


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
