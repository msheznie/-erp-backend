<table class="table table-responsive" id="directInvoiceDetailsRefferedBacks-table">
    <thead>
        <tr>
            <th>Directinvoicedetailsid</th>
        <th>Directinvoiceautoid</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Servicelinesystemid</th>
        <th>Servicelinecode</th>
        <th>Chartofaccountsystemid</th>
        <th>Glcode</th>
        <th>Glcodedes</th>
        <th>Comments</th>
        <th>Percentage</th>
        <th>Diamountcurrency</th>
        <th>Diamountcurrencyer</th>
        <th>Diamount</th>
        <th>Localcurrency</th>
        <th>Localcurrencyer</th>
        <th>Localamount</th>
        <th>Comrptcurrency</th>
        <th>Comrptcurrencyer</th>
        <th>Comrptamount</th>
        <th>Budgetyear</th>
        <th>Isextraaddon</th>
        <th>Timesreferred</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($directInvoiceDetailsRefferedBacks as $directInvoiceDetailsRefferedBack)
        <tr>
            <td>{!! $directInvoiceDetailsRefferedBack->directInvoiceDetailsID !!}</td>
            <td>{!! $directInvoiceDetailsRefferedBack->directInvoiceAutoID !!}</td>
            <td>{!! $directInvoiceDetailsRefferedBack->companySystemID !!}</td>
            <td>{!! $directInvoiceDetailsRefferedBack->companyID !!}</td>
            <td>{!! $directInvoiceDetailsRefferedBack->serviceLineSystemID !!}</td>
            <td>{!! $directInvoiceDetailsRefferedBack->serviceLineCode !!}</td>
            <td>{!! $directInvoiceDetailsRefferedBack->chartOfAccountSystemID !!}</td>
            <td>{!! $directInvoiceDetailsRefferedBack->glCode !!}</td>
            <td>{!! $directInvoiceDetailsRefferedBack->glCodeDes !!}</td>
            <td>{!! $directInvoiceDetailsRefferedBack->comments !!}</td>
            <td>{!! $directInvoiceDetailsRefferedBack->percentage !!}</td>
            <td>{!! $directInvoiceDetailsRefferedBack->DIAmountCurrency !!}</td>
            <td>{!! $directInvoiceDetailsRefferedBack->DIAmountCurrencyER !!}</td>
            <td>{!! $directInvoiceDetailsRefferedBack->DIAmount !!}</td>
            <td>{!! $directInvoiceDetailsRefferedBack->localCurrency !!}</td>
            <td>{!! $directInvoiceDetailsRefferedBack->localCurrencyER !!}</td>
            <td>{!! $directInvoiceDetailsRefferedBack->localAmount !!}</td>
            <td>{!! $directInvoiceDetailsRefferedBack->comRptCurrency !!}</td>
            <td>{!! $directInvoiceDetailsRefferedBack->comRptCurrencyER !!}</td>
            <td>{!! $directInvoiceDetailsRefferedBack->comRptAmount !!}</td>
            <td>{!! $directInvoiceDetailsRefferedBack->budgetYear !!}</td>
            <td>{!! $directInvoiceDetailsRefferedBack->isExtraAddon !!}</td>
            <td>{!! $directInvoiceDetailsRefferedBack->timesReferred !!}</td>
            <td>{!! $directInvoiceDetailsRefferedBack->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['directInvoiceDetailsRefferedBacks.destroy', $directInvoiceDetailsRefferedBack->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('directInvoiceDetailsRefferedBacks.show', [$directInvoiceDetailsRefferedBack->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('directInvoiceDetailsRefferedBacks.edit', [$directInvoiceDetailsRefferedBack->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>