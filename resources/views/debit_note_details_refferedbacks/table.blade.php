<table class="table table-responsive" id="debitNoteDetailsRefferedbacks-table">
    <thead>
        <tr>
            <th>Debitnotedetailsid</th>
        <th>Debitnoteautoid</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Servicelinesystemid</th>
        <th>Servicelinecode</th>
        <th>Contractid</th>
        <th>Supplierid</th>
        <th>Chartofaccountsystemid</th>
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
    @foreach($debitNoteDetailsRefferedbacks as $debitNoteDetailsRefferedback)
        <tr>
            <td>{!! $debitNoteDetailsRefferedback->debitNoteDetailsID !!}</td>
            <td>{!! $debitNoteDetailsRefferedback->debitNoteAutoID !!}</td>
            <td>{!! $debitNoteDetailsRefferedback->companySystemID !!}</td>
            <td>{!! $debitNoteDetailsRefferedback->companyID !!}</td>
            <td>{!! $debitNoteDetailsRefferedback->serviceLineSystemID !!}</td>
            <td>{!! $debitNoteDetailsRefferedback->serviceLineCode !!}</td>
            <td>{!! $debitNoteDetailsRefferedback->contractID !!}</td>
            <td>{!! $debitNoteDetailsRefferedback->supplierID !!}</td>
            <td>{!! $debitNoteDetailsRefferedback->chartOfAccountSystemID !!}</td>
            <td>{!! $debitNoteDetailsRefferedback->glCode !!}</td>
            <td>{!! $debitNoteDetailsRefferedback->glCodeDes !!}</td>
            <td>{!! $debitNoteDetailsRefferedback->comments !!}</td>
            <td>{!! $debitNoteDetailsRefferedback->debitAmountCurrency !!}</td>
            <td>{!! $debitNoteDetailsRefferedback->debitAmountCurrencyER !!}</td>
            <td>{!! $debitNoteDetailsRefferedback->debitAmount !!}</td>
            <td>{!! $debitNoteDetailsRefferedback->localCurrency !!}</td>
            <td>{!! $debitNoteDetailsRefferedback->localCurrencyER !!}</td>
            <td>{!! $debitNoteDetailsRefferedback->localAmount !!}</td>
            <td>{!! $debitNoteDetailsRefferedback->comRptCurrency !!}</td>
            <td>{!! $debitNoteDetailsRefferedback->comRptCurrencyER !!}</td>
            <td>{!! $debitNoteDetailsRefferedback->comRptAmount !!}</td>
            <td>{!! $debitNoteDetailsRefferedback->budgetYear !!}</td>
            <td>{!! $debitNoteDetailsRefferedback->timesReferred !!}</td>
            <td>{!! $debitNoteDetailsRefferedback->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['debitNoteDetailsRefferedbacks.destroy', $debitNoteDetailsRefferedback->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('debitNoteDetailsRefferedbacks.show', [$debitNoteDetailsRefferedback->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('debitNoteDetailsRefferedbacks.edit', [$debitNoteDetailsRefferedback->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>