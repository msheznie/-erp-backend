<table class="table table-responsive" id="taxes-table">
    <thead>
        <tr>
            <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Taxdescription</th>
        <th>Taxshortcode</th>
        <th>Taxtype</th>
        <th>Isactive</th>
        <th>Authorityautoid</th>
        <th>Glautoid</th>
        <th>Currencyid</th>
        <th>Effectivefrom</th>
        <th>Taxreferenceno</th>
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
    @foreach($taxes as $tax)
        <tr>
            <td>{!! $tax->companySystemID !!}</td>
            <td>{!! $tax->companyID !!}</td>
            <td>{!! $tax->taxDescription !!}</td>
            <td>{!! $tax->taxShortCode !!}</td>
            <td>{!! $tax->taxType !!}</td>
            <td>{!! $tax->isActive !!}</td>
            <td>{!! $tax->authorityAutoID !!}</td>
            <td>{!! $tax->GLAutoID !!}</td>
            <td>{!! $tax->currencyID !!}</td>
            <td>{!! $tax->effectiveFrom !!}</td>
            <td>{!! $tax->taxReferenceNo !!}</td>
            <td>{!! $tax->createdUserGroup !!}</td>
            <td>{!! $tax->createdPCID !!}</td>
            <td>{!! $tax->createdUserID !!}</td>
            <td>{!! $tax->createdDateTime !!}</td>
            <td>{!! $tax->createdUserName !!}</td>
            <td>{!! $tax->modifiedPCID !!}</td>
            <td>{!! $tax->modifiedUserID !!}</td>
            <td>{!! $tax->modifiedDateTime !!}</td>
            <td>{!! $tax->modifiedUserName !!}</td>
            <td>{!! $tax->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['taxes.destroy', $tax->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('taxes.show', [$tax->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('taxes.edit', [$tax->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>