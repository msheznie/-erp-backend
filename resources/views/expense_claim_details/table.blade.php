<table class="table table-responsive" id="expenseClaimDetails-table">
    <thead>
        <tr>
            <th>Expenseclaimmasterautoid</th>
        <th>Companyid</th>
        <th>Servicelinecode</th>
        <th>Expenseclaimcategoriesautoid</th>
        <th>Description</th>
        <th>Docref</th>
        <th>Amount</th>
        <th>Comments</th>
        <th>Glcode</th>
        <th>Glcodedescription</th>
        <th>Currencyid</th>
        <th>Currencyer</th>
        <th>Localcurrency</th>
        <th>Localcurrencyer</th>
        <th>Localamount</th>
        <th>Comrptcurrency</th>
        <th>Comrptcurrencyer</th>
        <th>Comrptamount</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($expenseClaimDetails as $expenseClaimDetails)
        <tr>
            <td>{!! $expenseClaimDetails->expenseClaimMasterAutoID !!}</td>
            <td>{!! $expenseClaimDetails->companyID !!}</td>
            <td>{!! $expenseClaimDetails->serviceLineCode !!}</td>
            <td>{!! $expenseClaimDetails->expenseClaimCategoriesAutoID !!}</td>
            <td>{!! $expenseClaimDetails->description !!}</td>
            <td>{!! $expenseClaimDetails->docRef !!}</td>
            <td>{!! $expenseClaimDetails->amount !!}</td>
            <td>{!! $expenseClaimDetails->comments !!}</td>
            <td>{!! $expenseClaimDetails->glCode !!}</td>
            <td>{!! $expenseClaimDetails->glCodeDescription !!}</td>
            <td>{!! $expenseClaimDetails->currencyID !!}</td>
            <td>{!! $expenseClaimDetails->currencyER !!}</td>
            <td>{!! $expenseClaimDetails->localCurrency !!}</td>
            <td>{!! $expenseClaimDetails->localCurrencyER !!}</td>
            <td>{!! $expenseClaimDetails->localAmount !!}</td>
            <td>{!! $expenseClaimDetails->comRptCurrency !!}</td>
            <td>{!! $expenseClaimDetails->comRptCurrencyER !!}</td>
            <td>{!! $expenseClaimDetails->comRptAmount !!}</td>
            <td>{!! $expenseClaimDetails->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['expenseClaimDetails.destroy', $expenseClaimDetails->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('expenseClaimDetails.show', [$expenseClaimDetails->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('expenseClaimDetails.edit', [$expenseClaimDetails->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>