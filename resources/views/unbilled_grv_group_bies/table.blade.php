<table class="table table-responsive" id="unbilledGrvGroupBies-table">
    <thead>
        <tr>
            <th>Companysystemid</th>
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
        <th>Selectedforbooking</th>
        <th>Fullybooked</th>
        <th>Grvtype</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($unbilledGrvGroupBies as $unbilledGrvGroupBy)
        <tr>
            <td>{!! $unbilledGrvGroupBy->companySystemID !!}</td>
            <td>{!! $unbilledGrvGroupBy->companyID !!}</td>
            <td>{!! $unbilledGrvGroupBy->supplierID !!}</td>
            <td>{!! $unbilledGrvGroupBy->purchaseOrderID !!}</td>
            <td>{!! $unbilledGrvGroupBy->grvAutoID !!}</td>
            <td>{!! $unbilledGrvGroupBy->grvDate !!}</td>
            <td>{!! $unbilledGrvGroupBy->supplierTransactionCurrencyID !!}</td>
            <td>{!! $unbilledGrvGroupBy->supplierTransactionCurrencyER !!}</td>
            <td>{!! $unbilledGrvGroupBy->companyReportingCurrencyID !!}</td>
            <td>{!! $unbilledGrvGroupBy->companyReportingER !!}</td>
            <td>{!! $unbilledGrvGroupBy->localCurrencyID !!}</td>
            <td>{!! $unbilledGrvGroupBy->localCurrencyER !!}</td>
            <td>{!! $unbilledGrvGroupBy->totTransactionAmount !!}</td>
            <td>{!! $unbilledGrvGroupBy->totLocalAmount !!}</td>
            <td>{!! $unbilledGrvGroupBy->totRptAmount !!}</td>
            <td>{!! $unbilledGrvGroupBy->isAddon !!}</td>
            <td>{!! $unbilledGrvGroupBy->selectedForBooking !!}</td>
            <td>{!! $unbilledGrvGroupBy->fullyBooked !!}</td>
            <td>{!! $unbilledGrvGroupBy->grvType !!}</td>
            <td>{!! $unbilledGrvGroupBy->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['unbilledGrvGroupBies.destroy', $unbilledGrvGroupBy->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('unbilledGrvGroupBies.show', [$unbilledGrvGroupBy->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('unbilledGrvGroupBies.edit', [$unbilledGrvGroupBy->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>