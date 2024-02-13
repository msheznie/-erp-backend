<html>
<center>
<table>
    <thead>
    <tr></tr>
    <tr>
        <td colspan="2"></td>
        <td><h1>Asset Tracking Report</h1></td>
    </tr>
    <tr>
        <td colspan="2"></td>
        <th style="font-size:15px;">From {{(new \Illuminate\Support\Carbon($fromDate))->format('d/m/Y')}} - To {{(new \Illuminate\Support\Carbon($toDate))->format('d/m/Y')}}</B></th>
    </tr>
    <tr></tr>
    <tr>
        <th class="text-center">Asset Code</th>
        <th class="text-center">Type</th>
        <th class="text-center">Asset Description</th>
        <th class="text-center">Category</th>
        <th class="text-center">Document Code</th>
        <th class="text-center">Document Date</th>
        <th class="text-center">Document Type</th>
        <th class="text-center">Transfer From</th>
        <th class="text-center">Transfer To</th>
        <th class="text-center">Location</th>
        <th class="text-center">Department</th>
        <th class="text-center">Employee</th>
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
                <td class="text-left">{{ $data->fromName }}</td>
                <td class="text-left">{{ $data->toName }}</td>
                <td class="text-left">{{ $data->locationName }}</td>
                <td class="text-left">{{ $data->depName }}</td>
                <td class="text-left">{{ $data->reqName }}</td>
            </tr>
    @endforeach
    </tbody>
</table>
</center>
</html>
