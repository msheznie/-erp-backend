<table class="table table-responsive" id="poPaymentTerms-table">
    <thead>
        <tr>
            <th>Paymenttermscategory</th>
        <th>Poid</th>
        <th>Paymenttemdes</th>
        <th>Comamount</th>
        <th>Compercentage</th>
        <th>Indays</th>
        <th>Comdate</th>
        <th>Lcpaymentyn</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($poPaymentTerms as $poPaymentTerms)
        <tr>
            <td>{!! $poPaymentTerms->paymentTermsCategory !!}</td>
            <td>{!! $poPaymentTerms->poID !!}</td>
            <td>{!! $poPaymentTerms->paymentTemDes !!}</td>
            <td>{!! $poPaymentTerms->comAmount !!}</td>
            <td>{!! $poPaymentTerms->comPercentage !!}</td>
            <td>{!! $poPaymentTerms->inDays !!}</td>
            <td>{!! $poPaymentTerms->comDate !!}</td>
            <td>{!! $poPaymentTerms->LCPaymentYN !!}</td>
            <td>{!! $poPaymentTerms->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['poPaymentTerms.destroy', $poPaymentTerms->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('poPaymentTerms.show', [$poPaymentTerms->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('poPaymentTerms.edit', [$poPaymentTerms->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>