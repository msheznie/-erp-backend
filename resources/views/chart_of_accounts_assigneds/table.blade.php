<table class="table table-responsive" id="chartOfAccountsAssigneds-table">
    <thead>
        <tr>
            <th>Chartofaccountsystemid</th>
        <th>Accountcode</th>
        <th>Accountdescription</th>
        <th>Masteraccount</th>
        <th>Catogaryblorplid</th>
        <th>Catogaryblorpl</th>
        <th>Controllaccountyn</th>
        <th>Controlaccountssystemid</th>
        <th>Controlaccounts</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Isactive</th>
        <th>Isassigned</th>
        <th>Isbank</th>
        <th>Allocationid</th>
        <th>Relatedpartyyn</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($chartOfAccountsAssigneds as $chartOfAccountsAssigned)
        <tr>
            <td>{!! $chartOfAccountsAssigned->chartOfAccountSystemID !!}</td>
            <td>{!! $chartOfAccountsAssigned->AccountCode !!}</td>
            <td>{!! $chartOfAccountsAssigned->AccountDescription !!}</td>
            <td>{!! $chartOfAccountsAssigned->masterAccount !!}</td>
            <td>{!! $chartOfAccountsAssigned->catogaryBLorPLID !!}</td>
            <td>{!! $chartOfAccountsAssigned->catogaryBLorPL !!}</td>
            <td>{!! $chartOfAccountsAssigned->controllAccountYN !!}</td>
            <td>{!! $chartOfAccountsAssigned->controlAccountsSystemID !!}</td>
            <td>{!! $chartOfAccountsAssigned->controlAccounts !!}</td>
            <td>{!! $chartOfAccountsAssigned->companySystemID !!}</td>
            <td>{!! $chartOfAccountsAssigned->companyID !!}</td>
            <td>{!! $chartOfAccountsAssigned->isActive !!}</td>
            <td>{!! $chartOfAccountsAssigned->isAssigned !!}</td>
            <td>{!! $chartOfAccountsAssigned->isBank !!}</td>
            <td>{!! $chartOfAccountsAssigned->AllocationID !!}</td>
            <td>{!! $chartOfAccountsAssigned->relatedPartyYN !!}</td>
            <td>{!! $chartOfAccountsAssigned->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['chartOfAccountsAssigneds.destroy', $chartOfAccountsAssigned->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('chartOfAccountsAssigneds.show', [$chartOfAccountsAssigned->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('chartOfAccountsAssigneds.edit', [$chartOfAccountsAssigned->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>