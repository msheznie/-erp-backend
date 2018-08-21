<table class="table table-responsive" id="creditNoteDetails-table">
    <thead>
        <tr>
            <th>Creditnoteautoid</th>
        <th>Companyid</th>
        <th>Customerid</th>
        <th>Chartofaccountsystemid</th>
        <th>Glcode</th>
        <th>Glcodedes</th>
        <th>Servicelinecode</th>
        <th>Clientcontractid</th>
        <th>Comments</th>
        <th>Creditamountcurrency</th>
        <th>Creditamountcurrencyer</th>
        <th>Creditamount</th>
        <th>Localcurrency</th>
        <th>Localcurrencyer</th>
        <th>Localamount</th>
        <th>Comrptcurrency</th>
        <th>Comrptcurrencyer</th>
        <th>Comrptamount</th>
        <th>Budgetyear</th>
        <th>Timesreferred</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($creditNoteDetails as $creditNoteDetails)
        <tr>
            <td>{!! $creditNoteDetails->creditNoteAutoID !!}</td>
            <td>{!! $creditNoteDetails->companyID !!}</td>
            <td>{!! $creditNoteDetails->customerID !!}</td>
            <td>{!! $creditNoteDetails->chartOfAccountSystemID !!}</td>
            <td>{!! $creditNoteDetails->glCode !!}</td>
            <td>{!! $creditNoteDetails->glCodeDes !!}</td>
            <td>{!! $creditNoteDetails->serviceLineCode !!}</td>
            <td>{!! $creditNoteDetails->clientContractID !!}</td>
            <td>{!! $creditNoteDetails->comments !!}</td>
            <td>{!! $creditNoteDetails->creditAmountCurrency !!}</td>
            <td>{!! $creditNoteDetails->creditAmountCurrencyER !!}</td>
            <td>{!! $creditNoteDetails->creditAmount !!}</td>
            <td>{!! $creditNoteDetails->localCurrency !!}</td>
            <td>{!! $creditNoteDetails->localCurrencyER !!}</td>
            <td>{!! $creditNoteDetails->localAmount !!}</td>
            <td>{!! $creditNoteDetails->comRptCurrency !!}</td>
            <td>{!! $creditNoteDetails->comRptCurrencyER !!}</td>
            <td>{!! $creditNoteDetails->comRptAmount !!}</td>
            <td>{!! $creditNoteDetails->budgetYear !!}</td>
            <td>{!! $creditNoteDetails->timesReferred !!}</td>
            <td>{!! $creditNoteDetails->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['creditNoteDetails.destroy', $creditNoteDetails->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('creditNoteDetails.show', [$creditNoteDetails->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('creditNoteDetails.edit', [$creditNoteDetails->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>