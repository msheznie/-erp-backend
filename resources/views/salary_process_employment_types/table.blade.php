<table class="table table-responsive" id="salaryProcessEmploymentTypes-table">
    <thead>
        <tr>
            <th>Salaryprocessid</th>
        <th>Emptype</th>
        <th>Periodid</th>
        <th>Companyid</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($salaryProcessEmploymentTypes as $salaryProcessEmploymentTypes)
        <tr>
            <td>{!! $salaryProcessEmploymentTypes->salaryProcessID !!}</td>
            <td>{!! $salaryProcessEmploymentTypes->empType !!}</td>
            <td>{!! $salaryProcessEmploymentTypes->periodID !!}</td>
            <td>{!! $salaryProcessEmploymentTypes->companyID !!}</td>
            <td>{!! $salaryProcessEmploymentTypes->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['salaryProcessEmploymentTypes.destroy', $salaryProcessEmploymentTypes->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('salaryProcessEmploymentTypes.show', [$salaryProcessEmploymentTypes->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('salaryProcessEmploymentTypes.edit', [$salaryProcessEmploymentTypes->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>