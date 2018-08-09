<table class="table table-responsive" id="directInvoiceDetails-table">
    <thead>
        <tr>
            <th>Directinvoiceautoid</th>
        <th>Companyid</th>
        <th>Servicelinecode</th>
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
    @foreach($directInvoiceDetails as $directInvoiceDetails)
        <tr>
            <td>{!! $directInvoiceDetails->directInvoiceAutoID !!}</td>
            <td>{!! $directInvoiceDetails->companyID !!}</td>
            <td>{!! $directInvoiceDetails->serviceLineCode !!}</td>
            <td>{!! $directInvoiceDetails->glCode !!}</td>
            <td>{!! $directInvoiceDetails->glCodeDes !!}</td>
            <td>{!! $directInvoiceDetails->comments !!}</td>
            <td>{!! $directInvoiceDetails->percentage !!}</td>
            <td>{!! $directInvoiceDetails->DIAmountCurrency !!}</td>
            <td>{!! $directInvoiceDetails->DIAmountCurrencyER !!}</td>
            <td>{!! $directInvoiceDetails->DIAmount !!}</td>
            <td>{!! $directInvoiceDetails->localCurrency !!}</td>
            <td>{!! $directInvoiceDetails->localCurrencyER !!}</td>
            <td>{!! $directInvoiceDetails->localAmount !!}</td>
            <td>{!! $directInvoiceDetails->comRptCurrency !!}</td>
            <td>{!! $directInvoiceDetails->comRptCurrencyER !!}</td>
            <td>{!! $directInvoiceDetails->comRptAmount !!}</td>
            <td>{!! $directInvoiceDetails->budgetYear !!}</td>
            <td>{!! $directInvoiceDetails->isExtraAddon !!}</td>
            <td>{!! $directInvoiceDetails->timesReferred !!}</td>
            <td>{!! $directInvoiceDetails->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['directInvoiceDetails.destroy', $directInvoiceDetails->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('directInvoiceDetails.show', [$directInvoiceDetails->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('directInvoiceDetails.edit', [$directInvoiceDetails->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>