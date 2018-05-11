<table class="table table-responsive" id="companyPolicyCategories-table">
    <thead>
        <tr>
            <th>Companypolicycategorydescription</th>
        <th>Applicabledocumentid</th>
        <th>Documentid</th>
        <th>Impletemed</th>
        <th>Isactive</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($companyPolicyCategories as $companyPolicyCategory)
        <tr>
            <td>{!! $companyPolicyCategory->companyPolicyCategoryDescription !!}</td>
            <td>{!! $companyPolicyCategory->applicableDocumentID !!}</td>
            <td>{!! $companyPolicyCategory->documentID !!}</td>
            <td>{!! $companyPolicyCategory->impletemed !!}</td>
            <td>{!! $companyPolicyCategory->isActive !!}</td>
            <td>{!! $companyPolicyCategory->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['companyPolicyCategories.destroy', $companyPolicyCategory->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('companyPolicyCategories.show', [$companyPolicyCategory->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('companyPolicyCategories.edit', [$companyPolicyCategory->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>