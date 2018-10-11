<table class="table table-responsive" id="insurancePolicyTypes-table">
    <thead>
        <tr>
            <th>Policydescription</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($insurancePolicyTypes as $insurancePolicyType)
        <tr>
            <td>{!! $insurancePolicyType->policyDescription !!}</td>
            <td>
                {!! Form::open(['route' => ['insurancePolicyTypes.destroy', $insurancePolicyType->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('insurancePolicyTypes.show', [$insurancePolicyType->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('insurancePolicyTypes.edit', [$insurancePolicyType->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>