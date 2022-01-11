<?php             use App\Models\GRVDetails;?>
@php
    $frDate = date('d/m/Y', strtotime($fromDate));
    $tDate = date('d/m/Y', strtotime($toDate));
@endphp
<div class="table-responsive">
    <table class="table table-sm table-striped hover table-bordered" width="100%">
        <thead>
        <tr>
            <th>Report Type:</th>
            <th>Supplier</th>
            <th>Date From:</th>
            <th>{{ $frDate }}</th>
            <th>Date To:</th>
            <th>{{ $tDate }}</th>

        <tr>
        <tr>
            <th>Date</th>
            <th>Document Code</th>
            <th>Reference No</th>
            <th>Vehicle No</th>
            <th>Item Short Code</th>
            <th>Item Description</th>
            <th>UOM</th>
            {{--            <th>Received Qty</th>--}}
            <th>Waste Qty</th>
            <th>Net Qty</th>
            <th>Unit Rate</th>
            <th>Total</th>
            <th>Remarks</th>
        </tr>
        </thead>
        @php
            $fromDate = date('Y/m/d', strtotime($fromDate));
            $toDate = date('Y/m/d', strtotime($toDate));

                $grvArray = array();
                $details = GRVDetails::whereIn('itemPrimaryCode',$scrapDetails)->where('createdDateTime', '>=', $fromDate)->where('createdDateTime', '<=', $toDate)->get();
                foreach($details as $detail){
                    array_push($grvArray,$detail->grv_master->supplierName);
                }
                $details = array_unique($grvArray);

        @endphp
        @foreach($details as $item)
            <tbody>
            <tr><td></td></tr>
            <tr><td>Supplier: {{$item}}</td></tr>
            <tr><td></td></tr>
            @php
                $grvItems = GRVDetails::where('createdDateTime', '>=', $fromDate)->where('createdDateTime', '<=', $toDate)->get();
                    $totWaste = 0;
                    $totQty = 0;
            @endphp
            @foreach($grvItems as $grvItem)
                @php
                    $dates = preg_split('/\s+/', $grvItem->createdDateTime, -1, PREG_SPLIT_NO_EMPTY);

                @endphp
                @if($grvItem->grv_master->supplierName == $item)
                <tr>
                    <td>{{ $dates[0] }}</td>
                    <td>{{ $grvItem->grv_master->grvPrimaryCode }}</td>
                    <td>{{ $grvItem->grv_master->grvDoRefNo }}</td>
                    <td>{{ $grvItem->grv_master->grvDOPersonVehicleNo }}</td>
                    <td>{{ $grvItem->itemPrimaryCode }}</td>
                    <td>{{ $grvItem->itemDescription }}</td>
                    <td>{{ $grvItem->unit->UnitShortCode }}</td>
                    {{--            <td>{{ $grvItem->prvRecievedQty }}</td>--}}


                    <td>{{ $grvItem->wasteQty }}</td>
                    <td>{{ $grvItem->noQty }}</td>
                    <td>{{ number_format($grvItem->unitCost,3) }}</td>
                    <td>{{ number_format($grvItem->netAmount,3) }}</td>
                    <td>{{ $grvItem->grvNarration }}</td>
                    <td style="display: none">{{ $totWaste += $grvItem->wasteQty }}</td>
                    <td style="display: none">{{ $totQty += $grvItem->noQty }}</td>
                </tr>
                @endif

            @endforeach
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>Total</td>
                <td>{{ $totWaste }}</td>
                <td>{{ $totQty }}</td>

            </tr>
            </tbody>
        @endforeach
    </table>
</div>

