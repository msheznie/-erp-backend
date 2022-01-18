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
            <th>Item</th>
            <th>Date From:</th>
            <th>{{ $frDate }}</th>
            <th>Date To:</th>
            <th>{{ $tDate }}</th>

        <tr>

        </thead>
        @php
            $fromDate = date('Y/m/d', strtotime($fromDate));
            $toDate = date('Y/m/d', strtotime($toDate));

                $grvArray = array();
                $details = GRVDetails::with(['grv_master','unit'])->whereHas('grv_master', function($q) use($suppliers,$companySystemID) {
            $q->where('companySystemID', $companySystemID);
            $q->whereIn('supplierPrimaryCode', $suppliers);
        })->whereIn('itemPrimaryCode',$scrapDetails)->where('createdDateTime', '>=', $fromDate)->where('createdDateTime', '<=', $toDate)->get();
                foreach($details as $detail){
                    array_push($grvArray,$detail->itemPrimaryCode);
                }
                $details = array_unique($grvArray);

        @endphp
        @foreach($details as $item)
            @php
            $grvTotalWaste = GRVDetails::where('itemPrimaryCode',$item)->where('createdDateTime', '>=', $fromDate)->where('createdDateTime', '<=', $toDate)->sum('wasteQty');
            @endphp
            @if($grvTotalWaste != 0)
            <thead>
            <tr>
                <th>Date</th>
                <th>Document Code</th>
                <th>Reference No</th>
                <th>Vehicle No</th>
                <th>Item Short Code</th>
                <th>Supplier</th>
                <th>UOM</th>
                {{--            <th>Received Qty</th>--}}
                <th>Waste Qty</th>
                <th>Net Qty</th>
                <th>Unit Rate</th>
                <th>Total</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
        <tr><td></td></tr>
        <tr><td>Item: {{$item}}</td></tr>
        <tr><td></td></tr>


        @php
            $grvItems = GRVDetails::with(['grv_master','unit'])->whereHas('grv_master', function($q) use($suppliers,$companySystemID) {
            $q->where('companySystemID', $companySystemID);
            $q->whereIn('supplierPrimaryCode', $suppliers);
        })->where('itemPrimaryCode',$item)->where('createdDateTime', '>=', $fromDate)->where('createdDateTime', '<=', $toDate)->get();
            $totWaste = 0;
            $totQty = 0;
        @endphp
        @foreach($grvItems as $grvItem)
            @php
            $dates = preg_split('/\s+/', $grvItem->createdDateTime->format('d/m/y'), -1, PREG_SPLIT_NO_EMPTY);
            @endphp
            @if($grvItem->wasteQty != 0)
        <tr>
            <td>{{ $dates[0] }}</td>
            <td>{{ $grvItem->grv_master->grvPrimaryCode }}</td>
            <td>{{ $grvItem->grv_master->grvDoRefNo }}</td>
            <td>{{ $grvItem->grv_master->grvDOPersonVehicleNo }}</td>
            <td>{{ $grvItem->itemPrimaryCode }}</td>
            <td>{{ $grvItem->grv_master->supplierName }}</td>
            <td>{{ $grvItem->unit->UnitShortCode }}</td>
{{--            <td>{{ $grvItem->prvRecievedQty }}</td>--}}
            <td>{{ $grvItem->wasteQty }}</td>
            <td>{{ $grvItem->noQty }}</td>

            @if($currency_id == 1)
            <td>{{ number_format($grvItem->unitCost,$company->localcurrency->DecimalPlaces) }}</td>

            @endif

            @if($currency_id == 2)
            <td>{{ number_format($grvItem->unitCost,$company->reportingcurrency->DecimalPlaces) }}</td>

            @endif

            @if($currency_id == 1)

            <td>{{ number_format($grvItem->netAmount,$company->localcurrency->DecimalPlaces) }}</td>

            @endif

            @if($currency_id == 2)

            <td>{{ number_format($grvItem->netAmount,$company->reportingcurrency->DecimalPlaces) }}</td>

            @endif

            <td>{{ $grvItem->grvNarration }}</td>
                @php
                $totWaste += $grvItem->wasteQty;
                $totQty += $grvItem->noQty;
                @endphp
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
        <tr></tr>
        </tbody>
            @endif
        @endforeach
    </table>
</div>

