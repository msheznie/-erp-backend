<table class="table table-responsive" id="companyFinanceYearperiodMasters-table">
    <thead>
        <tr>
            <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Companyfinanceyearid</th>
        <th>Datefrom</th>
        <th>Dateto</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($companyFinanceYearperiodMasters as $companyFinanceYearperiodMaster)
        <tr>
            <td>{!! $companyFinanceYearperiodMaster->companySystemID !!}</td>
            <td>{!! $companyFinanceYearperiodMaster->companyID !!}</td>
            <td>{!! $companyFinanceYearperiodMaster->companyFinanceYearID !!}</td>
            <td>{!! $companyFinanceYearperiodMaster->dateFrom !!}</td>
            <td>{!! $companyFinanceYearperiodMaster->dateTo !!}</td>
            <td>{!! $companyFinanceYearperiodMaster->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['companyFinanceYearperiodMasters.destroy', $companyFinanceYearperiodMaster->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('companyFinanceYearperiodMasters.show', [$companyFinanceYearperiodMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('companyFinanceYearperiodMasters.edit', [$companyFinanceYearperiodMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>