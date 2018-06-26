<table class="table table-responsive" id="taxFormulaMasters-table">
    <thead>
        <tr>
            <th>Description</th>
        <th>Taxtype</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Createdusergroup</th>
        <th>Createdpcid</th>
        <th>Createduserid</th>
        <th>Createddatetime</th>
        <th>Createdusername</th>
        <th>Modifiedpcid</th>
        <th>Modifieduserid</th>
        <th>Modifieddatetime</th>
        <th>Modifiedusername</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($taxFormulaMasters as $taxFormulaMaster)
        <tr>
            <td>{!! $taxFormulaMaster->Description !!}</td>
            <td>{!! $taxFormulaMaster->taxType !!}</td>
            <td>{!! $taxFormulaMaster->companySystemID !!}</td>
            <td>{!! $taxFormulaMaster->companyID !!}</td>
            <td>{!! $taxFormulaMaster->createdUserGroup !!}</td>
            <td>{!! $taxFormulaMaster->createdPCID !!}</td>
            <td>{!! $taxFormulaMaster->createdUserID !!}</td>
            <td>{!! $taxFormulaMaster->createdDateTime !!}</td>
            <td>{!! $taxFormulaMaster->createdUserName !!}</td>
            <td>{!! $taxFormulaMaster->modifiedPCID !!}</td>
            <td>{!! $taxFormulaMaster->modifiedUserID !!}</td>
            <td>{!! $taxFormulaMaster->modifiedDateTime !!}</td>
            <td>{!! $taxFormulaMaster->modifiedUserName !!}</td>
            <td>{!! $taxFormulaMaster->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['taxFormulaMasters.destroy', $taxFormulaMaster->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('taxFormulaMasters.show', [$taxFormulaMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('taxFormulaMasters.edit', [$taxFormulaMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>