<table class="table table-responsive" id="budgetConsumedDatas-table">
    <thead>
        <tr>
            <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Servicelinesystemid</th>
        <th>Servicelinecode</th>
        <th>Documentsystemid</th>
        <th>Documentid</th>
        <th>Documentsystemcode</th>
        <th>Documentcode</th>
        <th>Chartofaccountid</th>
        <th>Glcode</th>
        <th>Year</th>
        <th>Month</th>
        <th>Consumedlocalcurrencyid</th>
        <th>Consumedlocalamount</th>
        <th>Consumedrptcurrencyid</th>
        <th>Consumedrptamount</th>
        <th>Consumeyn</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($budgetConsumedDatas as $budgetConsumedData)
        <tr>
            <td>{!! $budgetConsumedData->companySystemID !!}</td>
            <td>{!! $budgetConsumedData->companyID !!}</td>
            <td>{!! $budgetConsumedData->serviceLineSystemID !!}</td>
            <td>{!! $budgetConsumedData->serviceLineCode !!}</td>
            <td>{!! $budgetConsumedData->documentSystemID !!}</td>
            <td>{!! $budgetConsumedData->documentID !!}</td>
            <td>{!! $budgetConsumedData->documentSystemCode !!}</td>
            <td>{!! $budgetConsumedData->documentCode !!}</td>
            <td>{!! $budgetConsumedData->chartOfAccountID !!}</td>
            <td>{!! $budgetConsumedData->GLCode !!}</td>
            <td>{!! $budgetConsumedData->year !!}</td>
            <td>{!! $budgetConsumedData->month !!}</td>
            <td>{!! $budgetConsumedData->consumedLocalCurrencyID !!}</td>
            <td>{!! $budgetConsumedData->consumedLocalAmount !!}</td>
            <td>{!! $budgetConsumedData->consumedRptCurrencyID !!}</td>
            <td>{!! $budgetConsumedData->consumedRptAmount !!}</td>
            <td>{!! $budgetConsumedData->consumeYN !!}</td>
            <td>{!! $budgetConsumedData->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['budgetConsumedDatas.destroy', $budgetConsumedData->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('budgetConsumedDatas.show', [$budgetConsumedData->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('budgetConsumedDatas.edit', [$budgetConsumedData->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>