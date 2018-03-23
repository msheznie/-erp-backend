<table class="table table-responsive" id="supplierCurrencies-table">
    <thead>
        <tr>
            <th>Suppliercodesystem</th>
        <th>Currencyid</th>
        <th>Bankmemo</th>
        <th>Timestamp</th>
        <th>Isassigned</th>
        <th>Isdefault</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($supplierCurrencies as $supplierCurrency)
        <tr>
            <td>{!! $supplierCurrency->supplierCodeSystem !!}</td>
            <td>{!! $supplierCurrency->currencyID !!}</td>
            <td>{!! $supplierCurrency->bankMemo !!}</td>
            <td>{!! $supplierCurrency->timestamp !!}</td>
            <td>{!! $supplierCurrency->isAssigned !!}</td>
            <td>{!! $supplierCurrency->isDefault !!}</td>
            <td>
                {!! Form::open(['route' => ['supplierCurrencies.destroy', $supplierCurrency->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('supplierCurrencies.show', [$supplierCurrency->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('supplierCurrencies.edit', [$supplierCurrency->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>