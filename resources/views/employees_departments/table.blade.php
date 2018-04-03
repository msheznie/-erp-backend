<table class="table table-responsive" id="employeesDepartments-table">
    <thead>
        <tr>
            <th>Employeesystemid</th>
        <th>Employeeid</th>
        <th>Employeegroupid</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Documentsystemid</th>
        <th>Documentid</th>
        <th>Departmentid</th>
        <th>Servicelinesystemid</th>
        <th>Servicelineid</th>
        <th>Warehousesystemcode</th>
        <th>Reportingmanagerid</th>
        <th>Isdefault</th>
        <th>Dischargedyn</th>
        <th>Approvaldeligated</th>
        <th>Approvaldeligatedfromempid</th>
        <th>Approvaldeligatedfrom</th>
        <th>Approvaldeligatedto</th>
        <th>Dmsisuploadenable</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($employeesDepartments as $employeesDepartment)
        <tr>
            <td>{!! $employeesDepartment->employeeSystemID !!}</td>
            <td>{!! $employeesDepartment->employeeID !!}</td>
            <td>{!! $employeesDepartment->employeeGroupID !!}</td>
            <td>{!! $employeesDepartment->companySystemID !!}</td>
            <td>{!! $employeesDepartment->companyId !!}</td>
            <td>{!! $employeesDepartment->documentSystemID !!}</td>
            <td>{!! $employeesDepartment->documentID !!}</td>
            <td>{!! $employeesDepartment->departmentID !!}</td>
            <td>{!! $employeesDepartment->ServiceLineSystemID !!}</td>
            <td>{!! $employeesDepartment->ServiceLineID !!}</td>
            <td>{!! $employeesDepartment->warehouseSystemCode !!}</td>
            <td>{!! $employeesDepartment->reportingManagerID !!}</td>
            <td>{!! $employeesDepartment->isDefault !!}</td>
            <td>{!! $employeesDepartment->dischargedYN !!}</td>
            <td>{!! $employeesDepartment->approvalDeligated !!}</td>
            <td>{!! $employeesDepartment->approvalDeligatedFromEmpID !!}</td>
            <td>{!! $employeesDepartment->approvalDeligatedFrom !!}</td>
            <td>{!! $employeesDepartment->approvalDeligatedTo !!}</td>
            <td>{!! $employeesDepartment->dmsIsUploadEnable !!}</td>
            <td>{!! $employeesDepartment->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['employeesDepartments.destroy', $employeesDepartment->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('employeesDepartments.show', [$employeesDepartment->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('employeesDepartments.edit', [$employeesDepartment->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>