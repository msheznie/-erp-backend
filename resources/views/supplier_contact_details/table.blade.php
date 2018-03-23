<table class="table table-responsive" id="supplierContactDetails-table">
    <thead>
        <tr>
            <th>Supplierid</th>
        <th>Contacttypeid</th>
        <th>Contactpersonname</th>
        <th>Contactpersontelephone</th>
        <th>Contactpersonfax</th>
        <th>Contactpersonemail</th>
        <th>Isdefault</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($supplierContactDetails as $supplierContactDetails)
        <tr>
            <td>{!! $supplierContactDetails->supplierID !!}</td>
            <td>{!! $supplierContactDetails->contactTypeID !!}</td>
            <td>{!! $supplierContactDetails->contactPersonName !!}</td>
            <td>{!! $supplierContactDetails->contactPersonTelephone !!}</td>
            <td>{!! $supplierContactDetails->contactPersonFax !!}</td>
            <td>{!! $supplierContactDetails->contactPersonEmail !!}</td>
            <td>{!! $supplierContactDetails->isDefault !!}</td>
            <td>{!! $supplierContactDetails->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['supplierContactDetails.destroy', $supplierContactDetails->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('supplierContactDetails.show', [$supplierContactDetails->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('supplierContactDetails.edit', [$supplierContactDetails->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>