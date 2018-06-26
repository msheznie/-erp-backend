<table class="table table-responsive" id="companyFinanceYears-table">
    <thead>
        <tr>
            <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Bigginingdate</th>
        <th>Endingdate</th>
        <th>Isactive</th>
        <th>Iscurrent</th>
        <th>Isclosed</th>
        <th>Closedbyempsystemid</th>
        <th>Closedbyempid</th>
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
    @foreach($companyFinanceYears as $companyFinanceYear)
        <tr>
            <td>{!! $companyFinanceYear->companySystemID !!}</td>
            <td>{!! $companyFinanceYear->companyID !!}</td>
            <td>{!! $companyFinanceYear->bigginingDate !!}</td>
            <td>{!! $companyFinanceYear->endingDate !!}</td>
            <td>{!! $companyFinanceYear->isActive !!}</td>
            <td>{!! $companyFinanceYear->isCurrent !!}</td>
            <td>{!! $companyFinanceYear->isClosed !!}</td>
            <td>{!! $companyFinanceYear->closedByEmpSystemID !!}</td>
            <td>{!! $companyFinanceYear->closedByEmpID !!}</td>
            <td>{!! $companyFinanceYear->closedByEmpName !!}</td>
            <td>{!! $companyFinanceYear->closedDate !!}</td>
            <td>{!! $companyFinanceYear->comments !!}</td>
            <td>{!! $companyFinanceYear->createdUserGroup !!}</td>
            <td>{!! $companyFinanceYear->createdUserID !!}</td>
            <td>{!! $companyFinanceYear->createdPcID !!}</td>
            <td>{!! $companyFinanceYear->createdDateTime !!}</td>
            <td>{!! $companyFinanceYear->modifiedUser !!}</td>
            <td>{!! $companyFinanceYear->modifiedPc !!}</td>
            <td>{!! $companyFinanceYear->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['companyFinanceYears.destroy', $companyFinanceYear->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('companyFinanceYears.show', [$companyFinanceYear->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('companyFinanceYears.edit', [$companyFinanceYear->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>