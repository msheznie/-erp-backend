<table class="table table-responsive" id="budgetAdjustments-table">
    <thead>
        <tr>
            <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Companyfinanceyearid</th>
        <th>Servicelinesystemid</th>
        <th>Serviceline</th>
        <th>Adjustedglcodesystemid</th>
        <th>Adjustedglcode</th>
        <th>Fromglcodesystemid</th>
        <th>Fromglcode</th>
        <th>Toglcodesystemid</th>
        <th>Toglcode</th>
        <th>Year</th>
        <th>Adjustmedlocalamount</th>
        <th>Adjustmentrptamount</th>
        <th>Createdusersystemid</th>
        <th>Createdbyuserid</th>
        <th>Modifiedusersystemid</th>
        <th>Modifiedbyuserid</th>
        <th>Createddatetime</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($budgetAdjustments as $budgetAdjustment)
        <tr>
            <td>{!! $budgetAdjustment->companySystemID !!}</td>
            <td>{!! $budgetAdjustment->companyId !!}</td>
            <td>{!! $budgetAdjustment->companyFinanceYearID !!}</td>
            <td>{!! $budgetAdjustment->serviceLineSystemID !!}</td>
            <td>{!! $budgetAdjustment->serviceLine !!}</td>
            <td>{!! $budgetAdjustment->adjustedGLCodeSystemID !!}</td>
            <td>{!! $budgetAdjustment->adjustedGLCode !!}</td>
            <td>{!! $budgetAdjustment->fromGLCodeSystemID !!}</td>
            <td>{!! $budgetAdjustment->fromGLCode !!}</td>
            <td>{!! $budgetAdjustment->toGLCodeSystemID !!}</td>
            <td>{!! $budgetAdjustment->toGLCode !!}</td>
            <td>{!! $budgetAdjustment->Year !!}</td>
            <td>{!! $budgetAdjustment->adjustmedLocalAmount !!}</td>
            <td>{!! $budgetAdjustment->adjustmentRptAmount !!}</td>
            <td>{!! $budgetAdjustment->createdUserSystemID !!}</td>
            <td>{!! $budgetAdjustment->createdByUserID !!}</td>
            <td>{!! $budgetAdjustment->modifiedUserSystemID !!}</td>
            <td>{!! $budgetAdjustment->modifiedByUserID !!}</td>
            <td>{!! $budgetAdjustment->createdDateTime !!}</td>
            <td>{!! $budgetAdjustment->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['budgetAdjustments.destroy', $budgetAdjustment->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('budgetAdjustments.show', [$budgetAdjustment->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('budgetAdjustments.edit', [$budgetAdjustment->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>