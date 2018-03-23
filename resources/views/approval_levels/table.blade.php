<table class="table table-responsive" id="approvalLevels-table">
    <thead>
        <tr>
            <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Departmentsystemid</th>
        <th>Departmentid</th>
        <th>Servicelinewise</th>
        <th>Servicelinesystemid</th>
        <th>Servicelinecode</th>
        <th>Documentsystemid</th>
        <th>Documentid</th>
        <th>Leveldescription</th>
        <th>Nooflevels</th>
        <th>Valuewise</th>
        <th>Valuefrom</th>
        <th>Valueto</th>
        <th>Iscategorywiseapproval</th>
        <th>Categoryid</th>
        <th>Isactive</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($approvalLevels as $approvalLevel)
        <tr>
            <td>{!! $approvalLevel->companySystemID !!}</td>
            <td>{!! $approvalLevel->companyID !!}</td>
            <td>{!! $approvalLevel->departmentSystemID !!}</td>
            <td>{!! $approvalLevel->departmentID !!}</td>
            <td>{!! $approvalLevel->serviceLineWise !!}</td>
            <td>{!! $approvalLevel->serviceLineSystemID !!}</td>
            <td>{!! $approvalLevel->serviceLineCode !!}</td>
            <td>{!! $approvalLevel->documentSystemID !!}</td>
            <td>{!! $approvalLevel->documentID !!}</td>
            <td>{!! $approvalLevel->levelDescription !!}</td>
            <td>{!! $approvalLevel->noOfLevels !!}</td>
            <td>{!! $approvalLevel->valueWise !!}</td>
            <td>{!! $approvalLevel->valueFrom !!}</td>
            <td>{!! $approvalLevel->valueTo !!}</td>
            <td>{!! $approvalLevel->isCategoryWiseApproval !!}</td>
            <td>{!! $approvalLevel->categoryID !!}</td>
            <td>{!! $approvalLevel->isActive !!}</td>
            <td>{!! $approvalLevel->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['approvalLevels.destroy', $approvalLevel->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('approvalLevels.show', [$approvalLevel->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('approvalLevels.edit', [$approvalLevel->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>