<table class="table table-responsive" id="currencyConversions-table">
    <thead>
        <tr>
            <th>Mastercurrencyid</th>
        <th>Subcurrencyid</th>
        <th>Conversion</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($currencyConversions as $currencyConversion)
        <tr>
            <td>{!! $currencyConversion->masterCurrencyID !!}</td>
            <td>{!! $currencyConversion->subCurrencyID !!}</td>
            <td>{!! $currencyConversion->conversion !!}</td>
            <td>{!! $currencyConversion->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['currencyConversions.destroy', $currencyConversion->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('currencyConversions.show', [$currencyConversion->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('currencyConversions.edit', [$currencyConversion->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>