<table class="table table-responsive" id="budgetTransferFormDetails-table">
    <thead>
        <tr>
            <th>Budgettransferformautoid</th>
        <th>Year</th>
        <th>Fromtemplatedetailid</th>
        <th>Fromservicelinesystemid</th>
        <th>Fromservicelinecode</th>
        <th>Fromchartofaccountsystemid</th>
        <th>Fromglcode</th>
        <th>Fromglcodedescription</th>
        <th>Totemplatedetailid</th>
        <th>Toservicelinesystemid</th>
        <th>Toservicelinecode</th>
        <th>Tochartofaccountsystemid</th>
        <th>Toglcode</th>
        <th>Toglcodedescription</th>
        <th>Adjustmentamountlocal</th>
        <th>Adjustmentamountrpt</th>
        <th>Remarks</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($budgetTransferFormDetails as $budgetTransferFormDetail)
        <tr>
            <td>{!! $budgetTransferFormDetail->budgetTransferFormAutoID !!}</td>
            <td>{!! $budgetTransferFormDetail->year !!}</td>
            <td>{!! $budgetTransferFormDetail->fromTemplateDetailID !!}</td>
            <td>{!! $budgetTransferFormDetail->fromServiceLineSystemID !!}</td>
            <td>{!! $budgetTransferFormDetail->fromServiceLineCode !!}</td>
            <td>{!! $budgetTransferFormDetail->fromChartOfAccountSystemID !!}</td>
            <td>{!! $budgetTransferFormDetail->FromGLCode !!}</td>
            <td>{!! $budgetTransferFormDetail->FromGLCodeDescription !!}</td>
            <td>{!! $budgetTransferFormDetail->toTemplateDetailID !!}</td>
            <td>{!! $budgetTransferFormDetail->toServiceLineSystemID !!}</td>
            <td>{!! $budgetTransferFormDetail->toServiceLineCode !!}</td>
            <td>{!! $budgetTransferFormDetail->toChartOfAccountSystemID !!}</td>
            <td>{!! $budgetTransferFormDetail->toGLCode !!}</td>
            <td>{!! $budgetTransferFormDetail->toGLCodeDescription !!}</td>
            <td>{!! $budgetTransferFormDetail->adjustmentAmountLocal !!}</td>
            <td>{!! $budgetTransferFormDetail->adjustmentAmountRpt !!}</td>
            <td>{!! $budgetTransferFormDetail->remarks !!}</td>
            <td>{!! $budgetTransferFormDetail->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['budgetTransferFormDetails.destroy', $budgetTransferFormDetail->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('budgetTransferFormDetails.show', [$budgetTransferFormDetail->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('budgetTransferFormDetails.edit', [$budgetTransferFormDetail->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>