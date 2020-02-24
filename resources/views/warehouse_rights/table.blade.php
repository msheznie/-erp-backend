<div class="table-responsive">
    <table class="table" id="warehouseRights-table">
        <thead>
            <tr>
                <th>Timestamp</th>
        <th>Modifieddatetime</th>
        <th>Modifiedpcid</th>
        <th>Modifiedusersystemid</th>
        <th>Createddatetime</th>
        <th>Createdpcid</th>
        <th>Createdusersystemid</th>
        <th>Warehousesystemcode</th>
        <th>Companysystemid</th>
        <th>Employeesystemid</th>
        <th>Companyrightsid</th>
                <th colspan="3">Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($warehouseRights as $warehouseRights)
            <tr>
                <td>{!! $warehouseRights->timestamp !!}</td>
            <td>{!! $warehouseRights->modifiedDateTime !!}</td>
            <td>{!! $warehouseRights->modifiedPcID !!}</td>
            <td>{!! $warehouseRights->modifiedUserSystemID !!}</td>
            <td>{!! $warehouseRights->createdDateTime !!}</td>
            <td>{!! $warehouseRights->createdPcID !!}</td>
            <td>{!! $warehouseRights->createdUserSystemID !!}</td>
            <td>{!! $warehouseRights->wareHouseSystemCode !!}</td>
            <td>{!! $warehouseRights->companySystemID !!}</td>
            <td>{!! $warehouseRights->employeeSystemID !!}</td>
            <td>{!! $warehouseRights->companyrightsID !!}</td>
                <td>
                    {!! Form::open(['route' => ['warehouseRights.destroy', $warehouseRights->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{!! route('warehouseRights.show', [$warehouseRights->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                        <a href="{!! route('warehouseRights.edit', [$warehouseRights->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                        {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
