<table class="table table-responsive" id="debitNoteDetails-table">
    <thead>
        <tr>
            <th>Debitnoteautoid</th>
        <th>Companyid</th>
        <th>Servicelinecode</th>
        <th>Contractid</th>
        <th>Supplierid</th>
        <th>Glcode</th>
        <th>Glcodedes</th>
        <th>Comments</th>
        <th>Debitamountcurrency</th>
        <th>Debitamountcurrencyer</th>
        <th>Debitamount</th>
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
    @foreach($debitNoteDetails as $debitNoteDetails)
        <tr>
            <td>{!! $debitNoteDetails->debitNoteAutoID !!}</td>
            <td>{!! $debitNoteDetails->companyID !!}</td>
            <td>{!! $debitNoteDetails->serviceLineCode !!}</td>
            <td>{!! $debitNoteDetails->contractID !!}</td>
            <td>{!! $debitNoteDetails->supplierID !!}</td>
            <td>{!! $debitNoteDetails->glCode !!}</td>
            <td>{!! $debitNoteDetails->glCodeDes !!}</td>
            <td>{!! $debitNoteDetails->comments !!}</td>
            <td>{!! $debitNoteDetails->debitAmountCurrency !!}</td>
            <td>{!! $debitNoteDetails->debitAmountCurrencyER !!}</td>
            <td>{!! $debitNoteDetails->debitAmount !!}</td>
            <td>{!! $debitNoteDetails->localCurrency !!}</td>
            <td>{!! $debitNoteDetails->localCurrencyER !!}</td>
            <td>{!! $debitNoteDetails->localAmount !!}</td>
            <td>{!! $debitNoteDetails->comRptCurrency !!}</td>
            <td>{!! $debitNoteDetails->comRptCurrencyER !!}</td>
            <td>{!! $debitNoteDetails->comRptAmount !!}</td>
            <td>{!! $debitNoteDetails->budgetYear !!}</td>
            <td>{!! $debitNoteDetails->timesReferred !!}</td>
            <td>{!! $debitNoteDetails->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['debitNoteDetails.destroy', $debitNoteDetails->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('debitNoteDetails.show', [$debitNoteDetails->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('debitNoteDetails.edit', [$debitNoteDetails->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>