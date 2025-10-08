<?php             use App\Models\GRVDetails;?>
@php
    $frDate = date('d/m/Y', strtotime($fromDate));
    $tDate = date('d/m/Y', strtotime($toDate));
@endphp

<table>
        <thead>
        <div style="font-size: 26px !important;">
                <th colspan="3"></th>
                <th style="font-size:26px"><B>{{$companyName}} </B></th>
            </div>
        </thead>
    </table>
    <table>
        <thead>
        <div>
                <td colspan="3"></td>
                <td style="font-size: 16px !important;"><B>{{$Title}}</B></td>
            </div>
        </thead>
    </table>
    <br>
    
<table>
</table>

<div class="table-responsive">
    <table class="table table-sm table-striped hover table-bordered" >
        <thead>
        <tr>
            <th>{{ trans('custom.report_type') }}:</th>
            <th>{{ trans('custom.item') }}</th>
            <th>{{ trans('custom.date_from') }}</th>
            <th>{{ $frDate }}</th>
            <th>{{ trans('custom.date_to') }}</th>
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
                <th>{{ trans('custom.date') }}</th>
                <th>{{ trans('custom.document_code') }}</th>
                <th>{{ trans('custom.reference_no') }}</th>
                <th>{{ trans('custom.vehicle_no') }}</th>
                <th>{{ trans('custom.item_short_code') }}</th>
                <th>{{ trans('custom.supplier') }}</th>
                <th>{{ trans('custom.uom') }}</th>
                {{--            <th>Received Qty</th>--}}
                <th>{{ trans('custom.waste_qty') }}</th>
                <th>{{ trans('custom.net_qty') }}</th>
                <th>{{ trans('custom.unit_rate') }}</th>
                <th>{{ trans('custom.total') }}</th>
                <th>{{ trans('custom.remarks') }}</th>
            </tr>
        </thead>
        <tbody>
        <tr><td></td></tr>
        <tr><td>{{ trans('custom.item_label') }} {{$item}}</td></tr>
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
            <td>{{ $grvItem->grv_master->grvDOpersonVehicleNo }}</td>
            <td>{{ $grvItem->itemPrimaryCode }}</td>
            <td>{{ $grvItem->grv_master->supplierName }}</td>
            <td>{{ $grvItem->unit->UnitShortCode }}</td>
{{--            <td>{{ $grvItem->prvRecievedQty }}</td>--}}
            <td>{{ $grvItem->wasteQty }}</td>
            <td>{{ $grvItem->noQty }}</td>

            @if($currency_id == 1)
            <td>{{ number_format($grvItem->unitCost  / $grvItem->localCurrencyER,$company->localcurrency->DecimalPlaces) }}</td>

            @endif

            @if($currency_id == 2)
            <td>{{ number_format($grvItem->unitCost  / $grvItem->companyReportingER,$company->reportingcurrency->DecimalPlaces) }}</td>

            @endif

            @if($currency_id == 1)

            <td>{{ number_format($grvItem->netAmount  / $grvItem->localCurrencyER,$company->localcurrency->DecimalPlaces) }}</td>

            @endif

            @if($currency_id == 2)

            <td>{{ number_format($grvItem->netAmount  / $grvItem->companyReportingER,$company->reportingcurrency->DecimalPlaces) }}</td>

            @endif

            <td>{{ $grvItem->comment }}</td>
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
            <td>{{ trans('custom.total') }}</td>
            <td>{{ $totWaste }}</td>
            <td>{{ $totQty }}</td>

        </tr>
        <tr></tr>
        </tbody>
            @endif
        @endforeach
    </table>
</div>

