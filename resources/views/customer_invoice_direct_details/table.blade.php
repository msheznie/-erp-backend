<table class="table table-responsive" id="customerInvoiceDirectDetails-table">
    <thead>
        <tr>
            <th>Custinvoicedirectid</th>
        <th>Companyid</th>
        <th>Servicelinecode</th>
        <th>Customerid</th>
        <th>Glcode</th>
        <th>Glcodedes</th>
        <th>Accounttype</th>
        <th>Comments</th>
        <th>Invoiceamountcurrency</th>
        <th>Invoiceamountcurrencyer</th>
        <th>Unitofmeasure</th>
        <th>Invoiceqty</th>
        <th>Unitcost</th>
        <th>Invoiceamount</th>
        <th>Localcurrency</th>
        <th>Localcurrencyer</th>
        <th>Localamount</th>
        <th>Comrptcurrency</th>
        <th>Comrptcurrencyer</th>
        <th>Comrptamount</th>
        <th>Discountlocalamount</th>
        <th>Discountamount</th>
        <th>Discountrptamount</th>
        <th>Discountrate</th>
        <th>Performamasterid</th>
        <th>Clientcontractid</th>
        <th>Timesreferred</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($customerInvoiceDirectDetails as $customerInvoiceDirectDetail)
        <tr>
            <td>{!! $customerInvoiceDirectDetail->custInvoiceDirectID !!}</td>
            <td>{!! $customerInvoiceDirectDetail->companyID !!}</td>
            <td>{!! $customerInvoiceDirectDetail->serviceLineCode !!}</td>
            <td>{!! $customerInvoiceDirectDetail->customerID !!}</td>
            <td>{!! $customerInvoiceDirectDetail->glCode !!}</td>
            <td>{!! $customerInvoiceDirectDetail->glCodeDes !!}</td>
            <td>{!! $customerInvoiceDirectDetail->accountType !!}</td>
            <td>{!! $customerInvoiceDirectDetail->comments !!}</td>
            <td>{!! $customerInvoiceDirectDetail->invoiceAmountCurrency !!}</td>
            <td>{!! $customerInvoiceDirectDetail->invoiceAmountCurrencyER !!}</td>
            <td>{!! $customerInvoiceDirectDetail->unitOfMeasure !!}</td>
            <td>{!! $customerInvoiceDirectDetail->invoiceQty !!}</td>
            <td>{!! $customerInvoiceDirectDetail->unitCost !!}</td>
            <td>{!! $customerInvoiceDirectDetail->invoiceAmount !!}</td>
            <td>{!! $customerInvoiceDirectDetail->localCurrency !!}</td>
            <td>{!! $customerInvoiceDirectDetail->localCurrencyER !!}</td>
            <td>{!! $customerInvoiceDirectDetail->localAmount !!}</td>
            <td>{!! $customerInvoiceDirectDetail->comRptCurrency !!}</td>
            <td>{!! $customerInvoiceDirectDetail->comRptCurrencyER !!}</td>
            <td>{!! $customerInvoiceDirectDetail->comRptAmount !!}</td>
            <td>{!! $customerInvoiceDirectDetail->discountLocalAmount !!}</td>
            <td>{!! $customerInvoiceDirectDetail->discountAmount !!}</td>
            <td>{!! $customerInvoiceDirectDetail->discountRptAmount !!}</td>
            <td>{!! $customerInvoiceDirectDetail->discountRate !!}</td>
            <td>{!! $customerInvoiceDirectDetail->performaMasterID !!}</td>
            <td>{!! $customerInvoiceDirectDetail->clientContractID !!}</td>
            <td>{!! $customerInvoiceDirectDetail->timesReferred !!}</td>
            <td>{!! $customerInvoiceDirectDetail->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['customerInvoiceDirectDetails.destroy', $customerInvoiceDirectDetail->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('customerInvoiceDirectDetails.show', [$customerInvoiceDirectDetail->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('customerInvoiceDirectDetails.edit', [$customerInvoiceDirectDetail->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>