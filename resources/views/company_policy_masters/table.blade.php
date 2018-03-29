<table class="table table-responsive" id="companyPolicyMasters-table">
    <thead>
        <tr>
            <th>Companypolicycategoryid</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Documentid</th>
        <th>Isyesno</th>
        <th>Policyvalue</th>
        <th>Createdbyuserid</th>
        <th>Createdbyusername</th>
        <th>Createdbypcid</th>
        <th>Modifiedbyuserid</th>
        <th>Createddatetime</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($companyPolicyMasters as $companyPolicyMaster)
        <tr>
            <td>{!! $companyPolicyMaster->companyPolicyCategoryID !!}</td>
            <td>{!! $companyPolicyMaster->companySystemID !!}</td>
            <td>{!! $companyPolicyMaster->companyID !!}</td>
            <td>{!! $companyPolicyMaster->documentID !!}</td>
            <td>{!! $companyPolicyMaster->isYesNO !!}</td>
            <td>{!! $companyPolicyMaster->policyValue !!}</td>
            <td>{!! $companyPolicyMaster->createdByUserID !!}</td>
            <td>{!! $companyPolicyMaster->createdByUserName !!}</td>
            <td>{!! $companyPolicyMaster->createdByPCID !!}</td>
            <td>{!! $companyPolicyMaster->modifiedByUserID !!}</td>
            <td>{!! $companyPolicyMaster->createdDateTime !!}</td>
            <td>{!! $companyPolicyMaster->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['companyPolicyMasters.destroy', $companyPolicyMaster->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('companyPolicyMasters.show', [$companyPolicyMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('companyPolicyMasters.edit', [$companyPolicyMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>