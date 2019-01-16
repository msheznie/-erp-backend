<table class="table table-responsive" id="currencyDenominations-table">
    <thead>
        <tr>
            <th>Currencyid</th>
        <th>Currencycode</th>
        <th>Amount</th>
        <th>Value</th>
        <th>Isnote</th>
        <th>Caption</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($currencyDenominations as $currencyDenomination)
        <tr>
            <td>{!! $currencyDenomination->currencyID !!}</td>
            <td>{!! $currencyDenomination->currencyCode !!}</td>
            <td>{!! $currencyDenomination->amount !!}</td>
            <td>{!! $currencyDenomination->value !!}</td>
            <td>{!! $currencyDenomination->isNote !!}</td>
            <td>{!! $currencyDenomination->caption !!}</td>
            <td>
                {!! Form::open(['route' => ['currencyDenominations.destroy', $currencyDenomination->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('currencyDenominations.show', [$currencyDenomination->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('currencyDenominations.edit', [$currencyDenomination->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>