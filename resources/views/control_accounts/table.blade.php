<table class="table table-responsive" id="controlAccounts-table">
    <thead>
        <tr>
            <th>Controlaccountcode</th>
        <th>Description</th>
        <th>Itemledgershymbol</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($controlAccounts as $controlAccount)
        <tr>
            <td>{!! $controlAccount->controlAccountCode !!}</td>
            <td>{!! $controlAccount->description !!}</td>
            <td>{!! $controlAccount->itemLedgerShymbol !!}</td>
            <td>{!! $controlAccount->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['controlAccounts.destroy', $controlAccount->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('controlAccounts.show', [$controlAccount->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('controlAccounts.edit', [$controlAccount->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>