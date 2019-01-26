<table class="table table-responsive" id="gposInvoiceDetails-table">
    <thead>
        <tr>
            <th>Invoiceid</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Itemautoid</th>
        <th>Itemsystemcode</th>
        <th>Itemdescription</th>
        <th>Itemcategory</th>
        <th>Financecategory</th>
        <th>Itemfinancecategory</th>
        <th>Itemfinancecategorysub</th>
        <th>Defaultuom</th>
        <th>Unitofmeasure</th>
        <th>Conversionrateuom</th>
        <th>Expenseglautoid</th>
        <th>Expenseglcode</th>
        <th>Expensesystemglcode</th>
        <th>Expensegldescription</th>
        <th>Expensegltype</th>
        <th>Revenueglautoid</th>
        <th>Revenueglcode</th>
        <th>Revenuesystemglcode</th>
        <th>Revenuegldescription</th>
        <th>Revenuegltype</th>
        <th>Assetglautoid</th>
        <th>Assetglcode</th>
        <th>Assetsystemglcode</th>
        <th>Assetgldescription</th>
        <th>Assetgltype</th>
        <th>Qty</th>
        <th>Price</th>
        <th>Totalamount</th>
        <th>Discountpercentage</th>
        <th>Discountamount</th>
        <th>Wacamount</th>
        <th>Netamount</th>
        <th>Transactioncurrencyid</th>
        <th>Transactioncurrency</th>
        <th>Transactionamountbeforediscount</th>
        <th>Transactionamount</th>
        <th>Transactioncurrencydecimalplaces</th>
        <th>Transactionexchangerate</th>
        <th>Companylocalcurrencyid</th>
        <th>Companylocalcurrency</th>
        <th>Companylocalamount</th>
        <th>Companylocalexchangerate</th>
        <th>Companylocalcurrencydecimalplaces</th>
        <th>Companyreportingcurrencyid</th>
        <th>Companyreportingcurrency</th>
        <th>Companyreportingamount</th>
        <th>Companyreportingcurrencydecimalplaces</th>
        <th>Companyreportingexchangerate</th>
        <th>Createdusergroup</th>
        <th>Createdpcid</th>
        <th>Createduserid</th>
        <th>Createddatetime</th>
        <th>Createdusername</th>
        <th>Modifiedpcid</th>
        <th>Modifieduserid</th>
        <th>Modifieddatetime</th>
        <th>Modifiedusername</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($gposInvoiceDetails as $gposInvoiceDetail)
        <tr>
            <td>{!! $gposInvoiceDetail->invoiceID !!}</td>
            <td>{!! $gposInvoiceDetail->companySystemID !!}</td>
            <td>{!! $gposInvoiceDetail->companyID !!}</td>
            <td>{!! $gposInvoiceDetail->itemAutoID !!}</td>
            <td>{!! $gposInvoiceDetail->itemSystemCode !!}</td>
            <td>{!! $gposInvoiceDetail->itemDescription !!}</td>
            <td>{!! $gposInvoiceDetail->itemCategory !!}</td>
            <td>{!! $gposInvoiceDetail->financeCategory !!}</td>
            <td>{!! $gposInvoiceDetail->itemFinanceCategory !!}</td>
            <td>{!! $gposInvoiceDetail->itemFinanceCategorySub !!}</td>
            <td>{!! $gposInvoiceDetail->defaultUOM !!}</td>
            <td>{!! $gposInvoiceDetail->unitOfMeasure !!}</td>
            <td>{!! $gposInvoiceDetail->conversionRateUOM !!}</td>
            <td>{!! $gposInvoiceDetail->expenseGLAutoID !!}</td>
            <td>{!! $gposInvoiceDetail->expenseGLCode !!}</td>
            <td>{!! $gposInvoiceDetail->expenseSystemGLCode !!}</td>
            <td>{!! $gposInvoiceDetail->expenseGLDescription !!}</td>
            <td>{!! $gposInvoiceDetail->expenseGLType !!}</td>
            <td>{!! $gposInvoiceDetail->revenueGLAutoID !!}</td>
            <td>{!! $gposInvoiceDetail->revenueGLCode !!}</td>
            <td>{!! $gposInvoiceDetail->revenueSystemGLCode !!}</td>
            <td>{!! $gposInvoiceDetail->revenueGLDescription !!}</td>
            <td>{!! $gposInvoiceDetail->revenueGLType !!}</td>
            <td>{!! $gposInvoiceDetail->assetGLAutoID !!}</td>
            <td>{!! $gposInvoiceDetail->assetGLCode !!}</td>
            <td>{!! $gposInvoiceDetail->assetSystemGLCode !!}</td>
            <td>{!! $gposInvoiceDetail->assetGLDescription !!}</td>
            <td>{!! $gposInvoiceDetail->assetGLType !!}</td>
            <td>{!! $gposInvoiceDetail->qty !!}</td>
            <td>{!! $gposInvoiceDetail->price !!}</td>
            <td>{!! $gposInvoiceDetail->totalAmount !!}</td>
            <td>{!! $gposInvoiceDetail->discountPercentage !!}</td>
            <td>{!! $gposInvoiceDetail->discountAmount !!}</td>
            <td>{!! $gposInvoiceDetail->wacAmount !!}</td>
            <td>{!! $gposInvoiceDetail->netAmount !!}</td>
            <td>{!! $gposInvoiceDetail->transactionCurrencyID !!}</td>
            <td>{!! $gposInvoiceDetail->transactionCurrency !!}</td>
            <td>{!! $gposInvoiceDetail->transactionAmountBeforeDiscount !!}</td>
            <td>{!! $gposInvoiceDetail->transactionAmount !!}</td>
            <td>{!! $gposInvoiceDetail->transactionCurrencyDecimalPlaces !!}</td>
            <td>{!! $gposInvoiceDetail->transactionExchangeRate !!}</td>
            <td>{!! $gposInvoiceDetail->companyLocalCurrencyID !!}</td>
            <td>{!! $gposInvoiceDetail->companyLocalCurrency !!}</td>
            <td>{!! $gposInvoiceDetail->companyLocalAmount !!}</td>
            <td>{!! $gposInvoiceDetail->companyLocalExchangeRate !!}</td>
            <td>{!! $gposInvoiceDetail->companyLocalCurrencyDecimalPlaces !!}</td>
            <td>{!! $gposInvoiceDetail->companyReportingCurrencyID !!}</td>
            <td>{!! $gposInvoiceDetail->companyReportingCurrency !!}</td>
            <td>{!! $gposInvoiceDetail->companyReportingAmount !!}</td>
            <td>{!! $gposInvoiceDetail->companyReportingCurrencyDecimalPlaces !!}</td>
            <td>{!! $gposInvoiceDetail->companyReportingExchangeRate !!}</td>
            <td>{!! $gposInvoiceDetail->createdUserGroup !!}</td>
            <td>{!! $gposInvoiceDetail->createdPCID !!}</td>
            <td>{!! $gposInvoiceDetail->createdUserID !!}</td>
            <td>{!! $gposInvoiceDetail->createdDateTime !!}</td>
            <td>{!! $gposInvoiceDetail->createdUserName !!}</td>
            <td>{!! $gposInvoiceDetail->modifiedPCID !!}</td>
            <td>{!! $gposInvoiceDetail->modifiedUserID !!}</td>
            <td>{!! $gposInvoiceDetail->modifiedDateTime !!}</td>
            <td>{!! $gposInvoiceDetail->modifiedUserName !!}</td>
            <td>{!! $gposInvoiceDetail->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['gposInvoiceDetails.destroy', $gposInvoiceDetail->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('gposInvoiceDetails.show', [$gposInvoiceDetail->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('gposInvoiceDetails.edit', [$gposInvoiceDetail->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>