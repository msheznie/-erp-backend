<div class="table-responsive">
    <table class="table" id="sMEPayAssets-table">
        <thead>
            <tr>
                <th>Empid</th>
        <th>Assettypeid</th>
        <th>Description</th>
        <th>Asset Serial No</th>
        <th>Assetconditionid</th>
        <th>Handoverdate</th>
        <th>Returnstatus</th>
        <th>Returndate</th>
        <th>Returncomment</th>
        <th>Companyid</th>
        <th>Createdusergroup</th>
        <th>Createdpcid</th>
        <th>Createduserid</th>
        <th>Createddatetime</th>
        <th>Modifiedpcid</th>
        <th>Modifieduserid</th>
        <th>Modifieddatetime</th>
        <th>Timestamp</th>
                <th colspan="3">Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($sMEPayAssets as $sMEPayAsset)
            <tr>
                <td>{{ $sMEPayAsset->empID }}</td>
            <td>{{ $sMEPayAsset->assetTypeID }}</td>
            <td>{{ $sMEPayAsset->description }}</td>
            <td>{{ $sMEPayAsset->asset_serial_no }}</td>
            <td>{{ $sMEPayAsset->assetConditionID }}</td>
            <td>{{ $sMEPayAsset->handOverDate }}</td>
            <td>{{ $sMEPayAsset->returnStatus }}</td>
            <td>{{ $sMEPayAsset->returnDate }}</td>
            <td>{{ $sMEPayAsset->returnComment }}</td>
            <td>{{ $sMEPayAsset->companyID }}</td>
            <td>{{ $sMEPayAsset->createdUserGroup }}</td>
            <td>{{ $sMEPayAsset->createdPCID }}</td>
            <td>{{ $sMEPayAsset->createdUserID }}</td>
            <td>{{ $sMEPayAsset->createdDateTime }}</td>
            <td>{{ $sMEPayAsset->modifiedPCID }}</td>
            <td>{{ $sMEPayAsset->modifiedUserID }}</td>
            <td>{{ $sMEPayAsset->modifiedDateTime }}</td>
            <td>{{ $sMEPayAsset->timestamp }}</td>
                <td>
                    {!! Form::open(['route' => ['sMEPayAssets.destroy', $sMEPayAsset->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{{ route('sMEPayAssets.show', [$sMEPayAsset->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                        <a href="{{ route('sMEPayAssets.edit', [$sMEPayAsset->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                        {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
