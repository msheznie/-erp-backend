<table class="table table-responsive" id="taxAuthorities-table">
    <thead>
        <tr>
            <th>Authoritysystemcode</th>
        <th>Authoritysecondarycode</th>
        <th>Serialno</th>
        <th>Authorityname</th>
        <th>Currencyid</th>
        <th>Telephone</th>
        <th>Email</th>
        <th>Fax</th>
        <th>Address</th>
        <th>Taxpayableglautoid</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Createdusergroup</th>
        <th>Createdpcid</th>
        <th>Createduserid</th>
        <th>Createdusername</th>
        <th>Createddatetime</th>
        <th>Modifiedpcid</th>
        <th>Modifieduserid</th>
        <th>Modifiedusername</th>
        <th>Modifieddatetime</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($taxAuthorities as $taxAuthority)
        <tr>
            <td>{!! $taxAuthority->authoritySystemCode !!}</td>
            <td>{!! $taxAuthority->authoritySecondaryCode !!}</td>
            <td>{!! $taxAuthority->serialNo !!}</td>
            <td>{!! $taxAuthority->AuthorityName !!}</td>
            <td>{!! $taxAuthority->currencyID !!}</td>
            <td>{!! $taxAuthority->telephone !!}</td>
            <td>{!! $taxAuthority->email !!}</td>
            <td>{!! $taxAuthority->fax !!}</td>
            <td>{!! $taxAuthority->address !!}</td>
            <td>{!! $taxAuthority->taxPayableGLAutoID !!}</td>
            <td>{!! $taxAuthority->companySystemID !!}</td>
            <td>{!! $taxAuthority->companyID !!}</td>
            <td>{!! $taxAuthority->createdUserGroup !!}</td>
            <td>{!! $taxAuthority->createdPCID !!}</td>
            <td>{!! $taxAuthority->createdUserID !!}</td>
            <td>{!! $taxAuthority->createdUserName !!}</td>
            <td>{!! $taxAuthority->createdDateTime !!}</td>
            <td>{!! $taxAuthority->modifiedPCID !!}</td>
            <td>{!! $taxAuthority->modifiedUserID !!}</td>
            <td>{!! $taxAuthority->modifiedUserName !!}</td>
            <td>{!! $taxAuthority->modifiedDateTime !!}</td>
            <td>{!! $taxAuthority->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['taxAuthorities.destroy', $taxAuthority->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('taxAuthorities.show', [$taxAuthority->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('taxAuthorities.edit', [$taxAuthority->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>