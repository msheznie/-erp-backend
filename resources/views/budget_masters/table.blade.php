<table class="table table-responsive" id="budgetMasters-table">
    <thead>
        <tr>
            <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Companyfinanceyearid</th>
        <th>Servicelinesystemid</th>
        <th>Servicelinecode</th>
        <th>Templatemasterid</th>
        <th>Year</th>
        <th>Month</th>
        <th>Createdbyusersystemid</th>
        <th>Createdbyuserid</th>
        <th>Createddatetime</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($budgetMasters as $budgetMaster)
        <tr>
            <td>{!! $budgetMaster->companySystemID !!}</td>
            <td>{!! $budgetMaster->companyID !!}</td>
            <td>{!! $budgetMaster->companyFinanceYearID !!}</td>
            <td>{!! $budgetMaster->serviceLineSystemID !!}</td>
            <td>{!! $budgetMaster->serviceLineCode !!}</td>
            <td>{!! $budgetMaster->templateMasterID !!}</td>
            <td>{!! $budgetMaster->Year !!}</td>
            <td>{!! $budgetMaster->month !!}</td>
            <td>{!! $budgetMaster->createdByUserSystemID !!}</td>
            <td>{!! $budgetMaster->createdByUserID !!}</td>
            <td>{!! $budgetMaster->createdDateTime !!}</td>
            <td>{!! $budgetMaster->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['budgetMasters.destroy', $budgetMaster->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('budgetMasters.show', [$budgetMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('budgetMasters.edit', [$budgetMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>