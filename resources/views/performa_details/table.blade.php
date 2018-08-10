<table class="table table-responsive" id="performaDetails-table">
    <thead>
        <tr>
            <th>Companyid</th>
        <th>Serviceline</th>
        <th>Customerid</th>
        <th>Contractid</th>
        <th>Performamasterid</th>
        <th>Performacode</th>
        <th>Ticketno</th>
        <th>Currencyid</th>
        <th>Totamount</th>
        <th>Financeglcode</th>
        <th>Invoicessytemcode</th>
        <th>Vendorcode</th>
        <th>Bankid</th>
        <th>Accountid</th>
        <th>Paymentperioddays</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($performaDetails as $performaDetails)
        <tr>
            <td>{!! $performaDetails->companyID !!}</td>
            <td>{!! $performaDetails->serviceLine !!}</td>
            <td>{!! $performaDetails->customerID !!}</td>
            <td>{!! $performaDetails->contractID !!}</td>
            <td>{!! $performaDetails->performaMasterID !!}</td>
            <td>{!! $performaDetails->performaCode !!}</td>
            <td>{!! $performaDetails->ticketNo !!}</td>
            <td>{!! $performaDetails->currencyID !!}</td>
            <td>{!! $performaDetails->totAmount !!}</td>
            <td>{!! $performaDetails->financeGLcode !!}</td>
            <td>{!! $performaDetails->invoiceSsytemCode !!}</td>
            <td>{!! $performaDetails->vendorCode !!}</td>
            <td>{!! $performaDetails->bankID !!}</td>
            <td>{!! $performaDetails->accountID !!}</td>
            <td>{!! $performaDetails->paymentPeriodDays !!}</td>
            <td>{!! $performaDetails->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['performaDetails.destroy', $performaDetails->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('performaDetails.show', [$performaDetails->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('performaDetails.edit', [$performaDetails->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>