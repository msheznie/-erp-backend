<table class="table table-responsive" id="outletUsers-table">
    <thead>
        <tr>
            <th>Userid</th>
        <th>Warehouseid</th>
        <th>Counterid</th>
        <th>Isactive</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Createdpcid</th>
        <th>Createdusersystemid</th>
        <th>Createdusergroup</th>
        <th>Createduserid</th>
        <th>Createddatetime</th>
        <th>Createdusername</th>
        <th>Modifiedpcid</th>
        <th>Modifiedusersystemid</th>
        <th>Modifieduserid</th>
        <th>Modifieddatetime</th>
        <th>Modifiedusername</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($outletUsers as $outletUsers)
        <tr>
            <td>{!! $outletUsers->userID !!}</td>
            <td>{!! $outletUsers->wareHouseID !!}</td>
            <td>{!! $outletUsers->counterID !!}</td>
            <td>{!! $outletUsers->isActive !!}</td>
            <td>{!! $outletUsers->companySystemID !!}</td>
            <td>{!! $outletUsers->companyID !!}</td>
            <td>{!! $outletUsers->createdPCID !!}</td>
            <td>{!! $outletUsers->createdUserSystemID !!}</td>
            <td>{!! $outletUsers->createdUserGroup !!}</td>
            <td>{!! $outletUsers->createdUserID !!}</td>
            <td>{!! $outletUsers->createdDateTime !!}</td>
            <td>{!! $outletUsers->createdUserName !!}</td>
            <td>{!! $outletUsers->modifiedPCID !!}</td>
            <td>{!! $outletUsers->modifiedUserSystemID !!}</td>
            <td>{!! $outletUsers->modifiedUserID !!}</td>
            <td>{!! $outletUsers->modifiedDateTime !!}</td>
            <td>{!! $outletUsers->modifiedUserName !!}</td>
            <td>{!! $outletUsers->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['outletUsers.destroy', $outletUsers->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('outletUsers.show', [$outletUsers->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('outletUsers.edit', [$outletUsers->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>