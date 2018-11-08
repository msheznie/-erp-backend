<table class="table table-responsive" id="monthlyAdditionDetails-table">
    <thead>
        <tr>
            <th>Monthlyadditionsmasterid</th>
        <th>Expenseclaimmasterautoid</th>
        <th>Empsystemid</th>
        <th>Empid</th>
        <th>Empdepartment</th>
        <th>Description</th>
        <th>Declarecurrency</th>
        <th>Declareamount</th>
        <th>Amountma</th>
        <th>Currencymaid</th>
        <th>Approvedyn</th>
        <th>Glcode</th>
        <th>Localcurrencyid</th>
        <th>Localcurrencyer</th>
        <th>Localamount</th>
        <th>Rptcurrencyid</th>
        <th>Rptcurrencyer</th>
        <th>Rptamount</th>
        <th>Issso</th>
        <th>Istax</th>
        <th>Createdpc</th>
        <th>Createdusergroup</th>
        <th>Modifiedusersystemid</th>
        <th>Modifieduser</th>
        <th>Modifiedpc</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($monthlyAdditionDetails as $monthlyAdditionDetail)
        <tr>
            <td>{!! $monthlyAdditionDetail->monthlyAdditionsMasterID !!}</td>
            <td>{!! $monthlyAdditionDetail->expenseClaimMasterAutoID !!}</td>
            <td>{!! $monthlyAdditionDetail->empSystemID !!}</td>
            <td>{!! $monthlyAdditionDetail->empID !!}</td>
            <td>{!! $monthlyAdditionDetail->empdepartment !!}</td>
            <td>{!! $monthlyAdditionDetail->description !!}</td>
            <td>{!! $monthlyAdditionDetail->declareCurrency !!}</td>
            <td>{!! $monthlyAdditionDetail->declareAmount !!}</td>
            <td>{!! $monthlyAdditionDetail->amountMA !!}</td>
            <td>{!! $monthlyAdditionDetail->currencyMAID !!}</td>
            <td>{!! $monthlyAdditionDetail->approvedYN !!}</td>
            <td>{!! $monthlyAdditionDetail->glCode !!}</td>
            <td>{!! $monthlyAdditionDetail->localCurrencyID !!}</td>
            <td>{!! $monthlyAdditionDetail->localCurrencyER !!}</td>
            <td>{!! $monthlyAdditionDetail->localAmount !!}</td>
            <td>{!! $monthlyAdditionDetail->rptCurrencyID !!}</td>
            <td>{!! $monthlyAdditionDetail->rptCurrencyER !!}</td>
            <td>{!! $monthlyAdditionDetail->rptAmount !!}</td>
            <td>{!! $monthlyAdditionDetail->IsSSO !!}</td>
            <td>{!! $monthlyAdditionDetail->IsTax !!}</td>
            <td>{!! $monthlyAdditionDetail->createdpc !!}</td>
            <td>{!! $monthlyAdditionDetail->createdUserGroup !!}</td>
            <td>{!! $monthlyAdditionDetail->modifiedUserSystemID !!}</td>
            <td>{!! $monthlyAdditionDetail->modifieduser !!}</td>
            <td>{!! $monthlyAdditionDetail->modifiedpc !!}</td>
            <td>{!! $monthlyAdditionDetail->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['monthlyAdditionDetails.destroy', $monthlyAdditionDetail->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('monthlyAdditionDetails.show', [$monthlyAdditionDetail->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('monthlyAdditionDetails.edit', [$monthlyAdditionDetail->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>