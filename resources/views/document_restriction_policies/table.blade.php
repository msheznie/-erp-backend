<table class="table table-responsive" id="documentRestrictionPolicies-table">
    <thead>
        <tr>
            <th>Documentsystemid</th>
        <th>Documentid</th>
        <th>Policydescription</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($documentRestrictionPolicies as $documentRestrictionPolicy)
        <tr>
            <td>{!! $documentRestrictionPolicy->documentSystemID !!}</td>
            <td>{!! $documentRestrictionPolicy->documentID !!}</td>
            <td>{!! $documentRestrictionPolicy->policyDescription !!}</td>
            <td>
                {!! Form::open(['route' => ['documentRestrictionPolicies.destroy', $documentRestrictionPolicy->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('documentRestrictionPolicies.show', [$documentRestrictionPolicy->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('documentRestrictionPolicies.edit', [$documentRestrictionPolicy->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>