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
        <th>{{ trans('custom.from') }} {{(new \Illuminate\Support\Carbon($fromDate))->format('d/m/Y')}} - {{ trans('custom.to') }} {{(new \Illuminate\Support\Carbon($toDate))->format('d/m/Y')}}</th>
    </tr>
    <tr></tr>
    <tr>
        <th class="text-center"><b>{{ trans('custom.asset_code') }}</b></th>
        <th class="text-center"><b>{{ trans('custom.type') }}</b></th>
        <th class="text-center"><b>{{ trans('custom.asset_description') }}</b></th>
        <th class="text-center"><b>{{ trans('custom.category') }}</b></th>
        <th class="text-center"><b>{{ trans('custom.document_code') }}</b></th>
        <th class="text-center"><b>{{ trans('custom.document_date') }}</b></th>
        <th class="text-center"><b>{{ trans('custom.document_type') }}</b></th>
        <th class="text-center"><b>{{ trans('custom.transfer_from') }}</b></th>
        <th class="text-center"><b>{{ trans('custom.transfer_to') }}</b></th>
        <th class="text-center"><b>{{ trans('custom.location') }}</b></th>
        <th class="text-center"><b>{{ trans('custom.department') }}</b></th>
        <th class="text-center"><b>{{ trans('custom.employee') }}</b></th>
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
