<table class="table table-responsive" id="paymentBankTransfers-table">
    <thead>
        <tr>
            <th>Documentsystemid</th>
        <th>Documentid</th>
        <th>Companysystemid</th>
        <th>Banktransferdocumentcode</th>
        <th>Serialnumber</th>
        <th>Documentdate</th>
        <th>Bankmasterid</th>
        <th>Bankaccountautoid</th>
        <th>Confirmedyn</th>
        <th>Confirmedbyempsystemid</th>
        <th>Confirmedbyempid</th>
        <th>Confirmedbyname</th>
        <th>Confirmeddate</th>
        <th>Approvedyn</th>
        <th>Approveddate</th>
        <th>Approvedbyuserid</th>
        <th>Approvedbyusersystemid</th>
        <th>Rolllevforapp Curr</th>
        <th>Createdpcid</th>
        <th>Createdusersystemid</th>
        <th>Createduserid</th>
        <th>Modifiedpc</th>
        <th>Modifiedusersystemid</th>
        <th>Modifieduser</th>
        <th>Createddatetime</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($paymentBankTransfers as $paymentBankTransfer)
        <tr>
            <td>{!! $paymentBankTransfer->documentSystemID !!}</td>
            <td>{!! $paymentBankTransfer->documentID !!}</td>
            <td>{!! $paymentBankTransfer->companySystemID !!}</td>
            <td>{!! $paymentBankTransfer->bankTransferDocumentCode !!}</td>
            <td>{!! $paymentBankTransfer->serialNumber !!}</td>
            <td>{!! $paymentBankTransfer->documentDate !!}</td>
            <td>{!! $paymentBankTransfer->bankMasterID !!}</td>
            <td>{!! $paymentBankTransfer->bankAccountAutoID !!}</td>
            <td>{!! $paymentBankTransfer->confirmedYN !!}</td>
            <td>{!! $paymentBankTransfer->confirmedByEmpSystemID !!}</td>
            <td>{!! $paymentBankTransfer->confirmedByEmpID !!}</td>
            <td>{!! $paymentBankTransfer->confirmedByName !!}</td>
            <td>{!! $paymentBankTransfer->confirmedDate !!}</td>
            <td>{!! $paymentBankTransfer->approvedYN !!}</td>
            <td>{!! $paymentBankTransfer->approvedDate !!}</td>
            <td>{!! $paymentBankTransfer->approvedByUserID !!}</td>
            <td>{!! $paymentBankTransfer->approvedByUserSystemID !!}</td>
            <td>{!! $paymentBankTransfer->RollLevForApp_curr !!}</td>
            <td>{!! $paymentBankTransfer->createdPcID !!}</td>
            <td>{!! $paymentBankTransfer->createdUserSystemID !!}</td>
            <td>{!! $paymentBankTransfer->createdUserID !!}</td>
            <td>{!! $paymentBankTransfer->modifiedPc !!}</td>
            <td>{!! $paymentBankTransfer->modifiedUserSystemID !!}</td>
            <td>{!! $paymentBankTransfer->modifiedUser !!}</td>
            <td>{!! $paymentBankTransfer->createdDateTime !!}</td>
            <td>{!! $paymentBankTransfer->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['paymentBankTransfers.destroy', $paymentBankTransfer->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('paymentBankTransfers.show', [$paymentBankTransfer->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('paymentBankTransfers.edit', [$paymentBankTransfer->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>