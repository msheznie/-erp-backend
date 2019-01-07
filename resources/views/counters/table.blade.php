<table class="table table-responsive" id="counters-table">
    <thead>
        <tr>
            <th>Countercode</th>
        <th>Countername</th>
        <th>Isactive</th>
        <th>Warehouseid</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Createdpcid</th>
        <th>Createduserid</th>
        <th>Createdusersystemid</th>
        <th>Createdusername</th>
        <th>Createdusergroup</th>
        <th>Createddatetime</th>
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
    @foreach($counters as $counter)
        <tr>
            <td>{!! $counter->counterCode !!}</td>
            <td>{!! $counter->counterName !!}</td>
            <td>{!! $counter->isActive !!}</td>
            <td>{!! $counter->wareHouseID !!}</td>
            <td>{!! $counter->companySystemID !!}</td>
            <td>{!! $counter->companyID !!}</td>
            <td>{!! $counter->createdPCID !!}</td>
            <td>{!! $counter->createdUserID !!}</td>
            <td>{!! $counter->createdUserSystemID !!}</td>
            <td>{!! $counter->createdUserName !!}</td>
            <td>{!! $counter->createdUserGroup !!}</td>
            <td>{!! $counter->createdDateTime !!}</td>
            <td>{!! $counter->modifiedPCID !!}</td>
            <td>{!! $counter->modifiedUserSystemID !!}</td>
            <td>{!! $counter->modifiedUserID !!}</td>
            <td>{!! $counter->modifiedDateTime !!}</td>
            <td>{!! $counter->modifiedUserName !!}</td>
            <td>{!! $counter->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['counters.destroy', $counter->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('counters.show', [$counter->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('counters.edit', [$counter->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>