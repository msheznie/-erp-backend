<table class="table table-responsive" id="taxdetails-table">
    <thead>
        <tr>
            <th>Taxmasterautoid</th>
        <th>Companyid</th>
        <th>Documentid</th>
        <th>Documentsystemcode</th>
        <th>Documentcode</th>
        <th>Taxshortcode</th>
        <th>Taxdescription</th>
        <th>Taxpercent</th>
        <th>Payeesystemcode</th>
        <th>Payeecode</th>
        <th>Payeename</th>
        <th>Currency</th>
        <th>Currencyer</th>
        <th>Amount</th>
        <th>Payeedefaultcurrencyid</th>
        <th>Payeedefaultcurrencyer</th>
        <th>Payeedefaultamount</th>
        <th>Localcurrencyid</th>
        <th>Localcurrencyer</th>
        <th>Localamount</th>
        <th>Rptcurrencyid</th>
        <th>Rptcurrencyer</th>
        <th>Rptamount</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($taxdetails as $taxdetail)
        <tr>
            <td>{!! $taxdetail->taxMasterAutoID !!}</td>
            <td>{!! $taxdetail->companyID !!}</td>
            <td>{!! $taxdetail->documentID !!}</td>
            <td>{!! $taxdetail->documentSystemCode !!}</td>
            <td>{!! $taxdetail->documentCode !!}</td>
            <td>{!! $taxdetail->taxShortCode !!}</td>
            <td>{!! $taxdetail->taxDescription !!}</td>
            <td>{!! $taxdetail->taxPercent !!}</td>
            <td>{!! $taxdetail->payeeSystemCode !!}</td>
            <td>{!! $taxdetail->payeeCode !!}</td>
            <td>{!! $taxdetail->payeeName !!}</td>
            <td>{!! $taxdetail->currency !!}</td>
            <td>{!! $taxdetail->currencyER !!}</td>
            <td>{!! $taxdetail->amount !!}</td>
            <td>{!! $taxdetail->payeeDefaultCurrencyID !!}</td>
            <td>{!! $taxdetail->payeeDefaultCurrencyER !!}</td>
            <td>{!! $taxdetail->payeeDefaultAmount !!}</td>
            <td>{!! $taxdetail->localCurrencyID !!}</td>
            <td>{!! $taxdetail->localCurrencyER !!}</td>
            <td>{!! $taxdetail->localAmount !!}</td>
            <td>{!! $taxdetail->rptCurrencyID !!}</td>
            <td>{!! $taxdetail->rptCurrencyER !!}</td>
            <td>{!! $taxdetail->rptAmount !!}</td>
            <td>{!! $taxdetail->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['taxdetails.destroy', $taxdetail->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('taxdetails.show', [$taxdetail->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('taxdetails.edit', [$taxdetail->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>