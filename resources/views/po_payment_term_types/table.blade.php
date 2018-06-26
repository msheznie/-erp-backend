<table class="table table-responsive" id="poPaymentTermTypes-table">
    <thead>
        <tr>
            <th>Categorydescription</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($poPaymentTermTypes as $poPaymentTermTypes)
        <tr>
            <td>{!! $poPaymentTermTypes->categoryDescription !!}</td>
            <td>
                {!! Form::open(['route' => ['poPaymentTermTypes.destroy', $poPaymentTermTypes->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('poPaymentTermTypes.show', [$poPaymentTermTypes->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('poPaymentTermTypes.edit', [$poPaymentTermTypes->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>