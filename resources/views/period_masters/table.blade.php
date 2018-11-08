<table class="table table-responsive" id="periodMasters-table">
    <thead>
        <tr>
            <th>Periodmonth</th>
        <th>Periodyear</th>
        <th>Clientmonth</th>
        <th>Clientstartdate</th>
        <th>Clientenddate</th>
        <th>Noofdays</th>
        <th>Startdate</th>
        <th>Enddate</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($periodMasters as $periodMaster)
        <tr>
            <td>{!! $periodMaster->periodMonth !!}</td>
            <td>{!! $periodMaster->periodYear !!}</td>
            <td>{!! $periodMaster->clientMonth !!}</td>
            <td>{!! $periodMaster->clientStartDate !!}</td>
            <td>{!! $periodMaster->clientEndDate !!}</td>
            <td>{!! $periodMaster->noOfDays !!}</td>
            <td>{!! $periodMaster->startDate !!}</td>
            <td>{!! $periodMaster->endDate !!}</td>
            <td>{!! $periodMaster->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['periodMasters.destroy', $periodMaster->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('periodMasters.show', [$periodMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('periodMasters.edit', [$periodMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>