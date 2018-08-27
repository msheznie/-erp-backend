<table class="table table-responsive" id="directReceiptDetails-table">
    <thead>
        <tr>
            <th>Directreceiptautoid</th>
        <th>Companyid</th>
        <th>Servicelinecode</th>
        <th>Glcode</th>
        <th>Glcodedes</th>
        <th>Contractid</th>
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
    @foreach($directReceiptDetails as $directReceiptDetail)
        <tr>
            <td>{!! $directReceiptDetail->directReceiptAutoID !!}</td>
            <td>{!! $directReceiptDetail->companyID !!}</td>
            <td>{!! $directReceiptDetail->serviceLineCode !!}</td>
            <td>{!! $directReceiptDetail->glCode !!}</td>
            <td>{!! $directReceiptDetail->glCodeDes !!}</td>
            <td>{!! $directReceiptDetail->contractID !!}</td>
            <td>{!! $directReceiptDetail->comments !!}</td>
            <td>{!! $directReceiptDetail->DRAmountCurrency !!}</td>
            <td>{!! $directReceiptDetail->DDRAmountCurrencyER !!}</td>
            <td>{!! $directReceiptDetail->DRAmount !!}</td>
            <td>{!! $directReceiptDetail->localCurrency !!}</td>
            <td>{!! $directReceiptDetail->localCurrencyER !!}</td>
            <td>{!! $directReceiptDetail->localAmount !!}</td>
            <td>{!! $directReceiptDetail->comRptCurrency !!}</td>
            <td>{!! $directReceiptDetail->comRptCurrencyER !!}</td>
            <td>{!! $directReceiptDetail->comRptAmount !!}</td>
            <td>{!! $directReceiptDetail->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['directReceiptDetails.destroy', $directReceiptDetail->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('directReceiptDetails.show', [$directReceiptDetail->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('directReceiptDetails.edit', [$directReceiptDetail->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>