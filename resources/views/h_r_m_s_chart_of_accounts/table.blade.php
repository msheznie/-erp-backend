<table class="table table-responsive" id="hRMSChartOfAccounts-table">
    <thead>
        <tr>
            <th>Accountcode</th>
        <th>Accountdescription</th>
        <th>Empgroup</th>
        <th>Createdpcid</th>
        <th>Createdusergroup</th>
        <th>Createduserid</th>
        <th>Createddatetime</th>
        <th>Modifiedpc</th>
        <th>Modifieduser</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($hRMSChartOfAccounts as $hRMSChartOfAccounts)
        <tr>
            <td>{!! $hRMSChartOfAccounts->AccountCode !!}</td>
            <td>{!! $hRMSChartOfAccounts->AccountDescription !!}</td>
            <td>{!! $hRMSChartOfAccounts->empGroup !!}</td>
            <td>{!! $hRMSChartOfAccounts->createdPcID !!}</td>
            <td>{!! $hRMSChartOfAccounts->createdUserGroup !!}</td>
            <td>{!! $hRMSChartOfAccounts->createdUserID !!}</td>
            <td>{!! $hRMSChartOfAccounts->createdDateTime !!}</td>
            <td>{!! $hRMSChartOfAccounts->modifiedPc !!}</td>
            <td>{!! $hRMSChartOfAccounts->modifiedUser !!}</td>
            <td>{!! $hRMSChartOfAccounts->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['hRMSChartOfAccounts.destroy', $hRMSChartOfAccounts->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('hRMSChartOfAccounts.show', [$hRMSChartOfAccounts->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('hRMSChartOfAccounts.edit', [$hRMSChartOfAccounts->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>