<table class="table table-responsive" id="customerCurrencies-table">
    <thead>
        <tr>
            <th>Customercodesystem</th>
        <th>Customercode</th>
        <th>Currencyid</th>
        <th>Isdefault</th>
        <th>Isassigned</th>
        <th>Createdby</th>
        <th>Createddatetime</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($customerCurrencies as $customerCurrency)
        <tr>
            <td>{!! $customerCurrency->customerCodeSystem !!}</td>
            <td>{!! $customerCurrency->customerCode !!}</td>
            <td>{!! $customerCurrency->currencyID !!}</td>
            <td>{!! $customerCurrency->isDefault !!}</td>
            <td>{!! $customerCurrency->isAssigned !!}</td>
            <td>{!! $customerCurrency->createdBy !!}</td>
            <td>{!! $customerCurrency->createdDateTime !!}</td>
            <td>{!! $customerCurrency->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['customerCurrencies.destroy', $customerCurrency->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('customerCurrencies.show', [$customerCurrency->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('customerCurrencies.edit', [$customerCurrency->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>