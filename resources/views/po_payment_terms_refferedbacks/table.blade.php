<table class="table table-responsive" id="poPaymentTermsRefferedbacks-table">
    <thead>
        <tr>
            <th>Paymenttermid</th>
        <th>Paymenttermscategory</th>
        <th>Poid</th>
        <th>Paymenttemdes</th>
        <th>Comamount</th>
        <th>Compercentage</th>
        <th>Indays</th>
        <th>Comdate</th>
        <th>Lcpaymentyn</th>
        <th>Isrequested</th>
        <th>Timesreferred</th>
        <th>Createddatetime</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($poPaymentTermsRefferedbacks as $poPaymentTermsRefferedback)
        <tr>
            <td>{!! $poPaymentTermsRefferedback->paymentTermID !!}</td>
            <td>{!! $poPaymentTermsRefferedback->paymentTermsCategory !!}</td>
            <td>{!! $poPaymentTermsRefferedback->poID !!}</td>
            <td>{!! $poPaymentTermsRefferedback->paymentTemDes !!}</td>
            <td>{!! $poPaymentTermsRefferedback->comAmount !!}</td>
            <td>{!! $poPaymentTermsRefferedback->comPercentage !!}</td>
            <td>{!! $poPaymentTermsRefferedback->inDays !!}</td>
            <td>{!! $poPaymentTermsRefferedback->comDate !!}</td>
            <td>{!! $poPaymentTermsRefferedback->LCPaymentYN !!}</td>
            <td>{!! $poPaymentTermsRefferedback->isRequested !!}</td>
            <td>{!! $poPaymentTermsRefferedback->timesReferred !!}</td>
            <td>{!! $poPaymentTermsRefferedback->createdDateTime !!}</td>
            <td>{!! $poPaymentTermsRefferedback->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['poPaymentTermsRefferedbacks.destroy', $poPaymentTermsRefferedback->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('poPaymentTermsRefferedbacks.show', [$poPaymentTermsRefferedback->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('poPaymentTermsRefferedbacks.edit', [$poPaymentTermsRefferedback->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>