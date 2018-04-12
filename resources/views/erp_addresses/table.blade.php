<table class="table table-responsive" id="erpAddresses-table">
    <thead>
        <tr>
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
    @foreach($erpAddresses as $erpAddress)
        <tr>
            <td>{!! $erpAddress->companyID !!}</td>
            <td>{!! $erpAddress->locationID !!}</td>
            <td>{!! $erpAddress->departmentID !!}</td>
            <td>{!! $erpAddress->addressTypeID !!}</td>
            <td>{!! $erpAddress->addressDescrption !!}</td>
            <td>{!! $erpAddress->contactPersonID !!}</td>
            <td>{!! $erpAddress->contactPersonTelephone !!}</td>
            <td>{!! $erpAddress->contactPersonFaxNo !!}</td>
            <td>{!! $erpAddress->contactPersonEmail !!}</td>
            <td>{!! $erpAddress->isDefault !!}</td>
            <td>{!! $erpAddress->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['erpAddresses.destroy', $erpAddress->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('erpAddresses.show', [$erpAddress->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('erpAddresses.edit', [$erpAddress->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>