<table class="table table-responsive" id="unbilledGRVs-table">
    <thead>
        <tr>
            <th>Companyid</th>
        <th>Supplierid</th>
        <th>Purchaseorderid</th>
        <th>Grvautoid</th>
        <th>Grvdate</th>
        <th>Suppliertransactioncurrencyid</th>
        <th>Suppliertransactioncurrencyer</th>
        <th>Companyreportingcurrencyid</th>
        <th>Companyreportinger</th>
        <th>Localcurrencyid</th>
        <th>Localcurrencyer</th>
        <th>Tottransactionamount</th>
        <th>Totlocalamount</th>
        <th>Totrptamount</th>
        <th>Isaddon</th>
        <th>Grvtype</th>
        <th>Isreturn</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($unbilledGRVs as $unbilledGRV)
        <tr>
            <td>{!! $unbilledGRV->companyID !!}</td>
            <td>{!! $unbilledGRV->supplierID !!}</td>
            <td>{!! $unbilledGRV->purchaseOrderID !!}</td>
            <td>{!! $unbilledGRV->grvAutoID !!}</td>
            <td>{!! $unbilledGRV->grvDate !!}</td>
            <td>{!! $unbilledGRV->supplierTransactionCurrencyID !!}</td>
            <td>{!! $unbilledGRV->supplierTransactionCurrencyER !!}</td>
            <td>{!! $unbilledGRV->companyReportingCurrencyID !!}</td>
            <td>{!! $unbilledGRV->companyReportingER !!}</td>
            <td>{!! $unbilledGRV->localCurrencyID !!}</td>
            <td>{!! $unbilledGRV->localCurrencyER !!}</td>
            <td>{!! $unbilledGRV->totTransactionAmount !!}</td>
            <td>{!! $unbilledGRV->totLocalAmount !!}</td>
            <td>{!! $unbilledGRV->totRptAmount !!}</td>
            <td>{!! $unbilledGRV->isAddon !!}</td>
            <td>{!! $unbilledGRV->grvType !!}</td>
            <td>{!! $unbilledGRV->isReturn !!}</td>
            <td>{!! $unbilledGRV->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['unbilledGRVs.destroy', $unbilledGRV->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('unbilledGRVs.show', [$unbilledGRV->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('unbilledGRVs.edit', [$unbilledGRV->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>