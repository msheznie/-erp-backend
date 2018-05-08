<table class="table table-responsive" id="addressTypes-table">
    <thead>
        <tr>
            <th>Addresstypedescription</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($addressTypes as $addressType)
        <tr>
            <td>{!! $addressType->addressTypeDescription !!}</td>
            <td>{!! $addressType->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['addressTypes.destroy', $addressType->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('addressTypes.show', [$addressType->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('addressTypes.edit', [$addressType->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>