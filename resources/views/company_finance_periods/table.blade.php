<table class="table table-responsive" id="companyFinancePeriods-table">
    <thead>
        <tr>
            <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Departmentsystemid</th>
        <th>Departmentid</th>
        <th>Companyfinanceyearid</th>
        <th>Datefrom</th>
        <th>Dateto</th>
        <th>Isactive</th>
        <th>Iscurrent</th>
        <th>Isclosed</th>
        <th>Closedbyempid</th>
        <th>Closedbyempsystemid</th>
        <th>Closedbyempname</th>
        <th>Closeddate</th>
        <th>Comments</th>
        <th>Createdusergroup</th>
        <th>Createduserid</th>
        <th>Createdpcid</th>
        <th>Createddatetime</th>
        <th>Modifieduser</th>
        <th>Modifiedpc</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($companyFinancePeriods as $companyFinancePeriod)
        <tr>
            <td>{!! $companyFinancePeriod->companySystemID !!}</td>
            <td>{!! $companyFinancePeriod->companyID !!}</td>
            <td>{!! $companyFinancePeriod->departmentSystemID !!}</td>
            <td>{!! $companyFinancePeriod->departmentID !!}</td>
            <td>{!! $companyFinancePeriod->companyFinanceYearID !!}</td>
            <td>{!! $companyFinancePeriod->dateFrom !!}</td>
            <td>{!! $companyFinancePeriod->dateTo !!}</td>
            <td>{!! $companyFinancePeriod->isActive !!}</td>
            <td>{!! $companyFinancePeriod->isCurrent !!}</td>
            <td>{!! $companyFinancePeriod->isClosed !!}</td>
            <td>{!! $companyFinancePeriod->closedByEmpID !!}</td>
            <td>{!! $companyFinancePeriod->closedByEmpSystemID !!}</td>
            <td>{!! $companyFinancePeriod->closedByEmpName !!}</td>
            <td>{!! $companyFinancePeriod->closedDate !!}</td>
            <td>{!! $companyFinancePeriod->comments !!}</td>
            <td>{!! $companyFinancePeriod->createdUserGroup !!}</td>
            <td>{!! $companyFinancePeriod->createdUserID !!}</td>
            <td>{!! $companyFinancePeriod->createdPcID !!}</td>
            <td>{!! $companyFinancePeriod->createdDateTime !!}</td>
            <td>{!! $companyFinancePeriod->modifiedUser !!}</td>
            <td>{!! $companyFinancePeriod->modifiedPc !!}</td>
            <td>{!! $companyFinancePeriod->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['companyFinancePeriods.destroy', $companyFinancePeriod->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('companyFinancePeriods.show', [$companyFinancePeriod->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('companyFinancePeriods.edit', [$companyFinancePeriod->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>