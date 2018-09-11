<table class="table table-responsive" id="performaTemps-table">
    <thead>
        <tr>
            <th>Performamasterid</th>
        <th>Mystdtitle</th>
        <th>Companyid</th>
        <th>Contractid</th>
        <th>Performainvoiceno</th>
        <th>Sumofsumofstandbyamount</th>
        <th>Ticketno</th>
        <th>Myticketno</th>
        <th>Clientid</th>
        <th>Performadate</th>
        <th>Performafinanceconfirmed</th>
        <th>Performaopconfirmed</th>
        <th>Performafinanceconfirmedby</th>
        <th>Performaopconfirmeddate</th>
        <th>Performafinanceconfirmeddate</th>
        <th>Stdglcode</th>
        <th>Sortorder</th>
        <th>Timestamp</th>
        <th>Proformacomment</th>
        <th>Isdiscount</th>
        <th>Discountdescription</th>
        <th>Discountpercentage</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($performaTemps as $performaTemp)
        <tr>
            <td>{!! $performaTemp->performaMasterID !!}</td>
            <td>{!! $performaTemp->myStdTitle !!}</td>
            <td>{!! $performaTemp->companyID !!}</td>
            <td>{!! $performaTemp->contractid !!}</td>
            <td>{!! $performaTemp->performaInvoiceNo !!}</td>
            <td>{!! $performaTemp->sumofsumofStandbyAmount !!}</td>
            <td>{!! $performaTemp->TicketNo !!}</td>
            <td>{!! $performaTemp->myTicketNo !!}</td>
            <td>{!! $performaTemp->clientID !!}</td>
            <td>{!! $performaTemp->performaDate !!}</td>
            <td>{!! $performaTemp->performaFinanceConfirmed !!}</td>
            <td>{!! $performaTemp->PerformaOpConfirmed !!}</td>
            <td>{!! $performaTemp->performaFinanceConfirmedBy !!}</td>
            <td>{!! $performaTemp->performaOpConfirmedDate !!}</td>
            <td>{!! $performaTemp->performaFinanceConfirmedDate !!}</td>
            <td>{!! $performaTemp->stdGLcode !!}</td>
            <td>{!! $performaTemp->sortOrder !!}</td>
            <td>{!! $performaTemp->timestamp !!}</td>
            <td>{!! $performaTemp->proformaComment !!}</td>
            <td>{!! $performaTemp->isDiscount !!}</td>
            <td>{!! $performaTemp->discountDescription !!}</td>
            <td>{!! $performaTemp->DiscountPercentage !!}</td>
            <td>
                {!! Form::open(['route' => ['performaTemps.destroy', $performaTemp->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('performaTemps.show', [$performaTemp->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('performaTemps.edit', [$performaTemp->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>