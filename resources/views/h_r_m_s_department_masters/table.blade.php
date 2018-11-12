<table class="table table-responsive" id="hRMSDepartmentMasters-table">
    <thead>
        <tr>
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
    @foreach($hRMSDepartmentMasters as $hRMSDepartmentMaster)
        <tr>
            <td>{!! $hRMSDepartmentMaster->DepartmentDescription !!}</td>
            <td>{!! $hRMSDepartmentMaster->isActive !!}</td>
            <td>{!! $hRMSDepartmentMaster->ServiceLineCode !!}</td>
            <td>{!! $hRMSDepartmentMaster->CompanyID !!}</td>
            <td>{!! $hRMSDepartmentMaster->showInCombo !!}</td>
            <td>{!! $hRMSDepartmentMaster->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['hRMSDepartmentMasters.destroy', $hRMSDepartmentMaster->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('hRMSDepartmentMasters.show', [$hRMSDepartmentMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('hRMSDepartmentMasters.edit', [$hRMSDepartmentMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>