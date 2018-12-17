<table class="table table-responsive" id="documentRestrictionAssigns-table">
    <thead>
        <tr>
            <th>Documentrestrictionpolicyid</th>
        <th>Documentsystemid</th>
        <th>Documentid</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Usergroupid</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($documentRestrictionAssigns as $documentRestrictionAssign)
        <tr>
            <td>{!! $documentRestrictionAssign->documentRestrictionPolicyID !!}</td>
            <td>{!! $documentRestrictionAssign->documentSystemID !!}</td>
            <td>{!! $documentRestrictionAssign->documentID !!}</td>
            <td>{!! $documentRestrictionAssign->companySystemID !!}</td>
            <td>{!! $documentRestrictionAssign->companyID !!}</td>
            <td>{!! $documentRestrictionAssign->userGroupID !!}</td>
            <td>
                {!! Form::open(['route' => ['documentRestrictionAssigns.destroy', $documentRestrictionAssign->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('documentRestrictionAssigns.show', [$documentRestrictionAssign->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('documentRestrictionAssigns.edit', [$documentRestrictionAssign->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>