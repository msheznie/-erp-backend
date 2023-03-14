<div class="table-responsive">
    <table class="table" id="tenderEditLogMasters-table">
        <thead>
            <tr>
                <th>Approved</th>
        <th>Approved By User System Id</th>
        <th>Approved Date</th>
        <th>Companyid</th>
        <th>Companysystemid</th>
        <th>Departmentid</th>
        <th>Departmentsystemid</th>
        <th>Description</th>
        <th>Documentcode</th>
        <th>Documentsystemcode</th>
        <th>Employeeid</th>
        <th>Employeesystemid</th>
        <th>Status</th>
        <th>Type</th>
        <th>Version</th>
                <th colspan="3">Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($tenderEditLogMasters as $tenderEditLogMaster)
            <tr>
                <td>{{ $tenderEditLogMaster->approved }}</td>
            <td>{{ $tenderEditLogMaster->approved_by_user_system_id }}</td>
            <td>{{ $tenderEditLogMaster->approved_date }}</td>
            <td>{{ $tenderEditLogMaster->companyID }}</td>
            <td>{{ $tenderEditLogMaster->companySystemID }}</td>
            <td>{{ $tenderEditLogMaster->departmentID }}</td>
            <td>{{ $tenderEditLogMaster->departmentSystemID }}</td>
            <td>{{ $tenderEditLogMaster->description }}</td>
            <td>{{ $tenderEditLogMaster->documentCode }}</td>
            <td>{{ $tenderEditLogMaster->documentSystemCode }}</td>
            <td>{{ $tenderEditLogMaster->employeeID }}</td>
            <td>{{ $tenderEditLogMaster->employeeSystemID }}</td>
            <td>{{ $tenderEditLogMaster->status }}</td>
            <td>{{ $tenderEditLogMaster->type }}</td>
            <td>{{ $tenderEditLogMaster->version }}</td>
                <td>
                    {!! Form::open(['route' => ['tenderEditLogMasters.destroy', $tenderEditLogMaster->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{{ route('tenderEditLogMasters.show', [$tenderEditLogMaster->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                        <a href="{{ route('tenderEditLogMasters.edit', [$tenderEditLogMaster->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                        {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
