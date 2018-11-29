<table class="table table-responsive" id="customerInvoiceDirectDetRefferedbacks-table">
    <thead>
        <tr>
            <th>Custinvdirdetautoid</th>
        <th>Custinvoicedirectid</th>
        <th>Companyid</th>
        <th>Servicelinesystemid</th>
        <th>Servicelinecode</th>
        <th>Customerid</th>
        <th>Glsystemid</th>
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
        <th>Discountlocalamount</th>
        <th>Discountamount</th>
        <th>Discountrptamount</th>
        <th>Discountrate</th>
        <th>Comrptamount</th>
        <th>Performamasterid</th>
        <th>Clientcontractid</th>
        <th>Contractid</th>
        <th>Timesreferred</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($customerInvoiceDirectDetRefferedbacks as $customerInvoiceDirectDetRefferedback)
        <tr>
            <td>{!! $customerInvoiceDirectDetRefferedback->custInvDirDetAutoID !!}</td>
            <td>{!! $customerInvoiceDirectDetRefferedback->custInvoiceDirectID !!}</td>
            <td>{!! $customerInvoiceDirectDetRefferedback->companyID !!}</td>
            <td>{!! $customerInvoiceDirectDetRefferedback->serviceLineSystemID !!}</td>
            <td>{!! $customerInvoiceDirectDetRefferedback->serviceLineCode !!}</td>
            <td>{!! $customerInvoiceDirectDetRefferedback->customerID !!}</td>
            <td>{!! $customerInvoiceDirectDetRefferedback->glSystemID !!}</td>
            <td>{!! $customerInvoiceDirectDetRefferedback->glCode !!}</td>
            <td>{!! $customerInvoiceDirectDetRefferedback->glCodeDes !!}</td>
            <td>{!! $customerInvoiceDirectDetRefferedback->accountType !!}</td>
            <td>{!! $customerInvoiceDirectDetRefferedback->comments !!}</td>
            <td>{!! $customerInvoiceDirectDetRefferedback->invoiceAmountCurrency !!}</td>
            <td>{!! $customerInvoiceDirectDetRefferedback->invoiceAmountCurrencyER !!}</td>
            <td>{!! $customerInvoiceDirectDetRefferedback->unitOfMeasure !!}</td>
            <td>{!! $customerInvoiceDirectDetRefferedback->invoiceQty !!}</td>
            <td>{!! $customerInvoiceDirectDetRefferedback->unitCost !!}</td>
            <td>{!! $customerInvoiceDirectDetRefferedback->invoiceAmount !!}</td>
            <td>{!! $customerInvoiceDirectDetRefferedback->localCurrency !!}</td>
            <td>{!! $customerInvoiceDirectDetRefferedback->localCurrencyER !!}</td>
            <td>{!! $customerInvoiceDirectDetRefferedback->localAmount !!}</td>
            <td>{!! $customerInvoiceDirectDetRefferedback->comRptCurrency !!}</td>
            <td>{!! $customerInvoiceDirectDetRefferedback->comRptCurrencyER !!}</td>
            <td>{!! $customerInvoiceDirectDetRefferedback->discountLocalAmount !!}</td>
            <td>{!! $customerInvoiceDirectDetRefferedback->discountAmount !!}</td>
            <td>{!! $customerInvoiceDirectDetRefferedback->discountRptAmount !!}</td>
            <td>{!! $customerInvoiceDirectDetRefferedback->discountRate !!}</td>
            <td>{!! $customerInvoiceDirectDetRefferedback->comRptAmount !!}</td>
            <td>{!! $customerInvoiceDirectDetRefferedback->performaMasterID !!}</td>
            <td>{!! $customerInvoiceDirectDetRefferedback->clientContractID !!}</td>
            <td>{!! $customerInvoiceDirectDetRefferedback->contractID !!}</td>
            <td>{!! $customerInvoiceDirectDetRefferedback->timesReferred !!}</td>
            <td>{!! $customerInvoiceDirectDetRefferedback->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['customerInvoiceDirectDetRefferedbacks.destroy', $customerInvoiceDirectDetRefferedback->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('customerInvoiceDirectDetRefferedbacks.show', [$customerInvoiceDirectDetRefferedback->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('customerInvoiceDirectDetRefferedbacks.edit', [$customerInvoiceDirectDetRefferedback->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>