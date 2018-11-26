<table class="table table-responsive" id="creditNoteDetailsRefferdbacks-table">
    <thead>
        <tr>
            <th>Creditnotedetailsid</th>
        <th>Creditnoteautoid</th>
        <th>Companyid</th>
        <th>Customerid</th>
        <th>Glcode</th>
        <th>Glcodedes</th>
        <th>Servicelinecode</th>
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
        <th>Timesreferred</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($creditNoteDetailsRefferdbacks as $creditNoteDetailsRefferdback)
        <tr>
            <td>{!! $creditNoteDetailsRefferdback->creditNoteDetailsID !!}</td>
            <td>{!! $creditNoteDetailsRefferdback->creditNoteAutoID !!}</td>
            <td>{!! $creditNoteDetailsRefferdback->companyID !!}</td>
            <td>{!! $creditNoteDetailsRefferdback->customerID !!}</td>
            <td>{!! $creditNoteDetailsRefferdback->glCode !!}</td>
            <td>{!! $creditNoteDetailsRefferdback->glCodeDes !!}</td>
            <td>{!! $creditNoteDetailsRefferdback->serviceLineCode !!}</td>
            <td>{!! $creditNoteDetailsRefferdback->comments !!}</td>
            <td>{!! $creditNoteDetailsRefferdback->creditAmountCurrency !!}</td>
            <td>{!! $creditNoteDetailsRefferdback->creditAmountCurrencyER !!}</td>
            <td>{!! $creditNoteDetailsRefferdback->creditAmount !!}</td>
            <td>{!! $creditNoteDetailsRefferdback->localCurrency !!}</td>
            <td>{!! $creditNoteDetailsRefferdback->localCurrencyER !!}</td>
            <td>{!! $creditNoteDetailsRefferdback->localAmount !!}</td>
            <td>{!! $creditNoteDetailsRefferdback->comRptCurrency !!}</td>
            <td>{!! $creditNoteDetailsRefferdback->comRptCurrencyER !!}</td>
            <td>{!! $creditNoteDetailsRefferdback->comRptAmount !!}</td>
            <td>{!! $creditNoteDetailsRefferdback->timesReferred !!}</td>
            <td>{!! $creditNoteDetailsRefferdback->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['creditNoteDetailsRefferdbacks.destroy', $creditNoteDetailsRefferdback->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('creditNoteDetailsRefferdbacks.show', [$creditNoteDetailsRefferdback->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('creditNoteDetailsRefferdbacks.edit', [$creditNoteDetailsRefferdback->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>