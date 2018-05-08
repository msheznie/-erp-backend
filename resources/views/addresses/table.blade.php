<table class="table table-responsive" id="addresses-table">
    <thead>
        <tr>
            <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Locationid</th>
        <th>Departmentid</th>
        <th>Addresstypeid</th>
        <th>Addressdescrption</th>
        <th>Contactpersonid</th>
        <th>Contactpersontelephone</th>
        <th>Contactpersonfaxno</th>
        <th>Contactpersonemail</th>
        <th>Isdefault</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($addresses as $address)
        <tr>
            <td>{!! $address->companySystemID !!}</td>
            <td>{!! $address->companyID !!}</td>
            <td>{!! $address->locationID !!}</td>
            <td>{!! $address->departmentID !!}</td>
            <td>{!! $address->addressTypeID !!}</td>
            <td>{!! $address->addressDescrption !!}</td>
            <td>{!! $address->contactPersonID !!}</td>
            <td>{!! $address->contactPersonTelephone !!}</td>
            <td>{!! $address->contactPersonFaxNo !!}</td>
            <td>{!! $address->contactPersonEmail !!}</td>
            <td>{!! $address->isDefault !!}</td>
            <td>{!! $address->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['addresses.destroy', $address->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('addresses.show', [$address->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('addresses.edit', [$address->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>