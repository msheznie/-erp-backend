<table class="table table-responsive" id="customerInvoiceCollectionDetails-table">
    <thead>
        <tr>
            <th>Customerinvoiceid</th>
        <th>Invoicestatustypeid</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Collectiondate</th>
        <th>Comments</th>
        <th>Actionrequired</th>
        <th>Createddatetime</th>
        <th>Createdusergroup</th>
        <th>Createdusersystemid</th>
        <th>Createduserid</th>
        <th>Createdpcid</th>
        <th>Modifiedusersystemid</th>
        <th>Modifieduser</th>
        <th>Modifiedpc</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($customerInvoiceCollectionDetails as $customerInvoiceCollectionDetail)
        <tr>
            <td>{!! $customerInvoiceCollectionDetail->customerInvoiceID !!}</td>
            <td>{!! $customerInvoiceCollectionDetail->invoiceStatusTypeID !!}</td>
            <td>{!! $customerInvoiceCollectionDetail->companySystemID !!}</td>
            <td>{!! $customerInvoiceCollectionDetail->companyID !!}</td>
            <td>{!! $customerInvoiceCollectionDetail->collectionDate !!}</td>
            <td>{!! $customerInvoiceCollectionDetail->comments !!}</td>
            <td>{!! $customerInvoiceCollectionDetail->actionRequired !!}</td>
            <td>{!! $customerInvoiceCollectionDetail->createdDateTime !!}</td>
            <td>{!! $customerInvoiceCollectionDetail->createdUserGroup !!}</td>
            <td>{!! $customerInvoiceCollectionDetail->createdUserSystemID !!}</td>
            <td>{!! $customerInvoiceCollectionDetail->createdUserID !!}</td>
            <td>{!! $customerInvoiceCollectionDetail->createdPcID !!}</td>
            <td>{!! $customerInvoiceCollectionDetail->modifiedUserSystemID !!}</td>
            <td>{!! $customerInvoiceCollectionDetail->modifiedUser !!}</td>
            <td>{!! $customerInvoiceCollectionDetail->modifiedPc !!}</td>
            <td>{!! $customerInvoiceCollectionDetail->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['customerInvoiceCollectionDetails.destroy', $customerInvoiceCollectionDetail->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('customerInvoiceCollectionDetails.show', [$customerInvoiceCollectionDetail->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('customerInvoiceCollectionDetails.edit', [$customerInvoiceCollectionDetail->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>