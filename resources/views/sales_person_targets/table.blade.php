<table class="table table-responsive" id="salesPersonTargets-table">
    <thead>
        <tr>
            <th>Salespersonid</th>
        <th>Datefrom</th>
        <th>Dateto</th>
        <th>Currencyid</th>
        <th>Percentage</th>
        <th>Fromtargetamount</th>
        <th>Totargetamount</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Createdusergroup</th>
        <th>Createdpcid</th>
        <th>Createduserid</th>
        <th>Createddatetime</th>
        <th>Createdusername</th>
        <th>Modifiedpcid</th>
        <th>Modifieduserid</th>
        <th>Modifieddatetime</th>
        <th>Modifiedusername</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($salesPersonTargets as $salesPersonTarget)
        <tr>
            <td>{!! $salesPersonTarget->salesPersonID !!}</td>
            <td>{!! $salesPersonTarget->datefrom !!}</td>
            <td>{!! $salesPersonTarget->dateTo !!}</td>
            <td>{!! $salesPersonTarget->currencyID !!}</td>
            <td>{!! $salesPersonTarget->percentage !!}</td>
            <td>{!! $salesPersonTarget->fromTargetAmount !!}</td>
            <td>{!! $salesPersonTarget->toTargetAmount !!}</td>
            <td>{!! $salesPersonTarget->companySystemID !!}</td>
            <td>{!! $salesPersonTarget->companyID !!}</td>
            <td>{!! $salesPersonTarget->createdUserGroup !!}</td>
            <td>{!! $salesPersonTarget->createdPCID !!}</td>
            <td>{!! $salesPersonTarget->createdUserID !!}</td>
            <td>{!! $salesPersonTarget->createdDateTime !!}</td>
            <td>{!! $salesPersonTarget->createdUserName !!}</td>
            <td>{!! $salesPersonTarget->modifiedPCID !!}</td>
            <td>{!! $salesPersonTarget->modifiedUserID !!}</td>
            <td>{!! $salesPersonTarget->modifiedDateTime !!}</td>
            <td>{!! $salesPersonTarget->modifiedUserName !!}</td>
            <td>{!! $salesPersonTarget->TIMESTAMP !!}</td>
            <td>
                {!! Form::open(['route' => ['salesPersonTargets.destroy', $salesPersonTarget->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('salesPersonTargets.show', [$salesPersonTarget->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('salesPersonTargets.edit', [$salesPersonTarget->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>