<div class="table-responsive">
    <table class="table" id="segmentRights-table">
        <thead>
            <tr>
                <th>Companyrightsid</th>
        <th>Employeesystemid</th>
        <th>Companysystemid</th>
        <th>Servicelinesystemid</th>
        <th>Createdusersystemid</th>
        <th>Createdpcid</th>
        <th>Createddatetime</th>
        <th>Modifiedusersystemid</th>
        <th>Modifiedpcid</th>
        <th>Modifieddatetime</th>
        <th>Timestamp</th>
                <th colspan="3">Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($segmentRights as $segmentRights)
            <tr>
                <td>{!! $segmentRights->companyrightsID !!}</td>
            <td>{!! $segmentRights->employeeSystemID !!}</td>
            <td>{!! $segmentRights->companySystemID !!}</td>
            <td>{!! $segmentRights->serviceLineSystemID !!}</td>
            <td>{!! $segmentRights->createdUserSystemID !!}</td>
            <td>{!! $segmentRights->createdPcID !!}</td>
            <td>{!! $segmentRights->createdDateTime !!}</td>
            <td>{!! $segmentRights->modifiedUserSystemID !!}</td>
            <td>{!! $segmentRights->modifiedPcID !!}</td>
            <td>{!! $segmentRights->modifiedDateTime !!}</td>
            <td>{!! $segmentRights->timestamp !!}</td>
                <td>
                    {!! Form::open(['route' => ['segmentRights.destroy', $segmentRights->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{!! route('segmentRights.show', [$segmentRights->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                        <a href="{!! route('segmentRights.edit', [$segmentRights->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                        {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
