<table class="table table-responsive" id="departmentMasters-table">
    <thead>
        <tr>
            <th>Departmentid</th>
        <th>Departmentdescription</th>
        <th>Isactive</th>
        <th>Depimage</th>
        <th>Masterlevel</th>
        <th>Companylevel</th>
        <th>Listorder</th>
        <th>Isreport</th>
        <th>Reportmenu</th>
        <th>Menuinitialimage</th>
        <th>Menuinitialselectedimage</th>
        <th>Showincombo</th>
        <th>Hrleaveapprovallevels</th>
        <th>Managerfield</th>
        <th>Isfunctionaldepartment</th>
        <th>Isreportgroupyn</th>
        <th>Hrobjectivesetting</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($departmentMasters as $departmentMaster)
        <tr>
            <td>{!! $departmentMaster->DepartmentID !!}</td>
            <td>{!! $departmentMaster->DepartmentDescription !!}</td>
            <td>{!! $departmentMaster->isActive !!}</td>
            <td>{!! $departmentMaster->depImage !!}</td>
            <td>{!! $departmentMaster->masterLevel !!}</td>
            <td>{!! $departmentMaster->companyLevel !!}</td>
            <td>{!! $departmentMaster->listOrder !!}</td>
            <td>{!! $departmentMaster->isReport !!}</td>
            <td>{!! $departmentMaster->ReportMenu !!}</td>
            <td>{!! $departmentMaster->menuInitialImage !!}</td>
            <td>{!! $departmentMaster->menuInitialSelectedImage !!}</td>
            <td>{!! $departmentMaster->showInCombo !!}</td>
            <td>{!! $departmentMaster->hrLeaveApprovalLevels !!}</td>
            <td>{!! $departmentMaster->managerfield !!}</td>
            <td>{!! $departmentMaster->isFunctionalDepartment !!}</td>
            <td>{!! $departmentMaster->isReportGroupYN !!}</td>
            <td>{!! $departmentMaster->hrObjectiveSetting !!}</td>
            <td>{!! $departmentMaster->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['departmentMasters.destroy', $departmentMaster->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('departmentMasters.show', [$departmentMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('departmentMasters.edit', [$departmentMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>