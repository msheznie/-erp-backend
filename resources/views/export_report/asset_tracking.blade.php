<html>
<center>
<table>
    <thead>
    <tr></tr>
    <tr>
        <td colspan="2"></td>
        <td><h1>{{ trans('custom.asset_tracking_report') }}</h1></td>
    </tr>
    <tr>
        <td colspan="2"></td>
        <th style="font-size:15px;">{{ trans('custom.from') }} {{(new \Illuminate\Support\Carbon($fromDate))->format('d/m/Y')}} - {{ trans('custom.to') }} {{(new \Illuminate\Support\Carbon($toDate))->format('d/m/Y')}}</B></th>
    </tr>
    <tr></tr>
    <tr>
        <th class="text-center">{{ trans('custom.asset_code') }}</th>
        <th class="text-center">{{ trans('custom.type') }}</th>
        <th class="text-center">{{ trans('custom.asset_description') }}</th>
        <th class="text-center">{{ trans('custom.category') }}</th>
        <th class="text-center">{{ trans('custom.document_code') }}</th>
        <th class="text-center">{{ trans('custom.document_date') }}</th>
        <th class="text-center">{{ trans('custom.document_type') }}</th>
        <th class="text-center">{{ trans('custom.transfer_from') }}</th>
        <th class="text-center">{{ trans('custom.transfer_to') }}</th>
        <th class="text-center">{{ trans('custom.location') }}</th>
        <th class="text-center">{{ trans('custom.department') }}</th>
        <th class="text-center">{{ trans('custom.employee') }}</th>
    </tr>
    </thead>
    <tbody>
    @foreach($reportData as $data)
            <tr>
                <td class="text-left">{{ $data->assetCode }}</td>
                <td class="text-left">{{ $data->assetType }}</td>
                <td class="text-left">{{ $data->assetDescription }}</td>
                <td class="text-left">{{ $data->category }}</td>
                <td class="text-left">{{ $data->documentCode }}</td>
                <td class="text-left">{{ \Carbon\Carbon::parse($data->documentDate)->format('d/m/Y') }}</td>
                <td class="text-left">{{ $data->transferType }}</td>
                @if($data->type != 3)
                <td class="text-left">{{ $data->fromName }}</td>
                @endif
                @if($data->type == 3)
                <td class="text-left">{{ $data->fromEmpName }}</td>
                @endif

                @if($data->type != 3 && $data->type != 1 && $data->type != 4)
                <td class="text-left">{{ $data->toName }}</td>
                @endif
                @if($data->type == 3)
                <td class="text-left">{{ $data->toEmpName }}</td>
                @endif
                @if($data->type == 1)
                <td class="text-left">{{ $data->reqName }}</td>
                @endif
                @if($data->type == 4)
                <td class="text-left">{{ $data->transferDepName }}</td>
                @endif
                <td class="text-left">{{ $data->locationName }}</td>
                @if($data->type == 4)
                    <td class="text-left">{{ $data->transferDepName }}</td>
                @endif
                @if($data->type != 4)
                <td class="text-left">{{ $data->depName }}</td>
                @endif
                @if($data->type == 3)
                <td class="text-left">{{ $data->toEmpName }}</td>
                @endif
                @if($data->type != 3)
                <td class="text-left">{{ $data->reqName }}</td>
                @endif
            </tr>
    @endforeach
    </tbody>
</table>
</center>
</html>
