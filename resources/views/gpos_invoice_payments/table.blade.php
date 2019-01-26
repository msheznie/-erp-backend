<table class="table table-responsive" id="gposInvoicePayments-table">
    <thead>
        <tr>
            <th>Invoiceid</th>
        <th>Paymentconfigmasterid</th>
        <th>Paymentconfigdetailid</th>
        <th>Glaccounttype</th>
        <th>Glcode</th>
        <th>Amount</th>
        <th>Reference</th>
        <th>Customerautoid</th>
        <th>Isadvancepayment</th>
        <th>Createdusergroup</th>
        <th>Createdpcid</th>
        <th>Createduserid</th>
        <th>Createdusername</th>
        <th>Createddatetime</th>
        <th>Modifiedpcid</th>
        <th>Modifieduserid</th>
        <th>Modifiedusername</th>
        <th>Modifieddatetime</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($gposInvoicePayments as $gposInvoicePayments)
        <tr>
            <td>{!! $gposInvoicePayments->invoiceID !!}</td>
            <td>{!! $gposInvoicePayments->paymentConfigMasterID !!}</td>
            <td>{!! $gposInvoicePayments->paymentConfigDetailID !!}</td>
            <td>{!! $gposInvoicePayments->glAccountType !!}</td>
            <td>{!! $gposInvoicePayments->GLCode !!}</td>
            <td>{!! $gposInvoicePayments->amount !!}</td>
            <td>{!! $gposInvoicePayments->reference !!}</td>
            <td>{!! $gposInvoicePayments->customerAutoID !!}</td>
            <td>{!! $gposInvoicePayments->isAdvancePayment !!}</td>
            <td>{!! $gposInvoicePayments->createdUserGroup !!}</td>
            <td>{!! $gposInvoicePayments->createdPCID !!}</td>
            <td>{!! $gposInvoicePayments->createdUserID !!}</td>
            <td>{!! $gposInvoicePayments->createdUserName !!}</td>
            <td>{!! $gposInvoicePayments->createdDateTime !!}</td>
            <td>{!! $gposInvoicePayments->modifiedPCID !!}</td>
            <td>{!! $gposInvoicePayments->modifiedUserID !!}</td>
            <td>{!! $gposInvoicePayments->modifiedUserName !!}</td>
            <td>{!! $gposInvoicePayments->modifiedDateTime !!}</td>
            <td>{!! $gposInvoicePayments->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['gposInvoicePayments.destroy', $gposInvoicePayments->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('gposInvoicePayments.show', [$gposInvoicePayments->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('gposInvoicePayments.edit', [$gposInvoicePayments->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>