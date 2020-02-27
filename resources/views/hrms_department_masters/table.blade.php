<div class="table-responsive">
    <table class="table" id="hrmsDepartmentMasters-table">
        <thead>
            <tr>
                <th>Servicelinesystemid</th>
        <th>Departmentdescription</th>
        <th>Isactive</th>
        <th>Servicelinecode</th>
        <th>Companyid</th>
        <th>Showincombo</th>
        <th>Timestamp</th>
                <th colspan="3">Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($hrmsDepartmentMasters as $hrmsDepartmentMaster)
            <tr>
                <td>{!! $hrmsDepartmentMaster->serviceLineSystemID !!}</td>
            <td>{!! $hrmsDepartmentMaster->DepartmentDescription !!}</td>
            <td>{!! $hrmsDepartmentMaster->isActive !!}</td>
            <td>{!! $hrmsDepartmentMaster->ServiceLineCode !!}</td>
            <td>{!! $hrmsDepartmentMaster->CompanyID !!}</td>
            <td>{!! $hrmsDepartmentMaster->showInCombo !!}</td>
            <td>{!! $hrmsDepartmentMaster->timestamp !!}</td>
                <td>
                    {!! Form::open(['route' => ['hrmsDepartmentMasters.destroy', $hrmsDepartmentMaster->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{!! route('hrmsDepartmentMasters.show', [$hrmsDepartmentMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                        <a href="{!! route('hrmsDepartmentMasters.edit', [$hrmsDepartmentMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                        {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
