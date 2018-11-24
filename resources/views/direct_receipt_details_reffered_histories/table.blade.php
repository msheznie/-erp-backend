<table class="table table-responsive" id="directReceiptDetailsRefferedHistories-table">
    <thead>
        <tr>
            <th>Directreceiptdetailsid</th>
        <th>Directreceiptautoid</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Servicelinesystemid</th>
        <th>Servicelinecode</th>
        <th>Glsystemid</th>
        <th>Chartofaccountsystemid</th>
        <th>Glcode</th>
        <th>Glcodedes</th>
        <th>Contractid</th>
        <th>Contractuid</th>
        <th>Comments</th>
        <th>Dramountcurrency</th>
        <th>Ddramountcurrencyer</th>
        <th>Dramount</th>
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
    @foreach($directReceiptDetailsRefferedHistories as $directReceiptDetailsRefferedHistory)
        <tr>
            <td>{!! $directReceiptDetailsRefferedHistory->directReceiptDetailsID !!}</td>
            <td>{!! $directReceiptDetailsRefferedHistory->directReceiptAutoID !!}</td>
            <td>{!! $directReceiptDetailsRefferedHistory->companySystemID !!}</td>
            <td>{!! $directReceiptDetailsRefferedHistory->companyID !!}</td>
            <td>{!! $directReceiptDetailsRefferedHistory->serviceLineSystemID !!}</td>
            <td>{!! $directReceiptDetailsRefferedHistory->serviceLineCode !!}</td>
            <td>{!! $directReceiptDetailsRefferedHistory->glSystemID !!}</td>
            <td>{!! $directReceiptDetailsRefferedHistory->chartOfAccountSystemID !!}</td>
            <td>{!! $directReceiptDetailsRefferedHistory->glCode !!}</td>
            <td>{!! $directReceiptDetailsRefferedHistory->glCodeDes !!}</td>
            <td>{!! $directReceiptDetailsRefferedHistory->contractID !!}</td>
            <td>{!! $directReceiptDetailsRefferedHistory->contractUID !!}</td>
            <td>{!! $directReceiptDetailsRefferedHistory->comments !!}</td>
            <td>{!! $directReceiptDetailsRefferedHistory->DRAmountCurrency !!}</td>
            <td>{!! $directReceiptDetailsRefferedHistory->DDRAmountCurrencyER !!}</td>
            <td>{!! $directReceiptDetailsRefferedHistory->DRAmount !!}</td>
            <td>{!! $directReceiptDetailsRefferedHistory->localCurrency !!}</td>
            <td>{!! $directReceiptDetailsRefferedHistory->localCurrencyER !!}</td>
            <td>{!! $directReceiptDetailsRefferedHistory->localAmount !!}</td>
            <td>{!! $directReceiptDetailsRefferedHistory->comRptCurrency !!}</td>
            <td>{!! $directReceiptDetailsRefferedHistory->comRptCurrencyER !!}</td>
            <td>{!! $directReceiptDetailsRefferedHistory->comRptAmount !!}</td>
            <td>{!! $directReceiptDetailsRefferedHistory->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['directReceiptDetailsRefferedHistories.destroy', $directReceiptDetailsRefferedHistory->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('directReceiptDetailsRefferedHistories.show', [$directReceiptDetailsRefferedHistory->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('directReceiptDetailsRefferedHistories.edit', [$directReceiptDetailsRefferedHistory->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>