<table class="table table-responsive" id="paymentBankTransferRefferedBacks-table">
    <thead>
        <tr>
            <th>Paymentbanktransferid</th>
        <th>Documentsystemid</th>
        <th>Documentid</th>
        <th>Companysystemid</th>
        <th>Banktransferdocumentcode</th>
        <th>Serialnumber</th>
        <th>Documentdate</th>
        <th>Narration</th>
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
        <th>Refferedbackyn</th>
        <th>Timesreferred</th>
        <th>Createdpcid</th>
        <th>Createdusersystemid</th>
        <th>Createduserid</th>
        <th>Modifiedpc</th>
        <th>Modifiedusersystemid</th>
        <th>Modifieduser</th>
        <th>Createddatetime</th>
        <th>Timestamp</th>
        <th>Exportedyn</th>
        <th>Exportedusersystemid</th>
        <th>Exporteddate</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($paymentBankTransferRefferedBacks as $paymentBankTransferRefferedBack)
        <tr>
            <td>{!! $paymentBankTransferRefferedBack->paymentBankTransferID !!}</td>
            <td>{!! $paymentBankTransferRefferedBack->documentSystemID !!}</td>
            <td>{!! $paymentBankTransferRefferedBack->documentID !!}</td>
            <td>{!! $paymentBankTransferRefferedBack->companySystemID !!}</td>
            <td>{!! $paymentBankTransferRefferedBack->bankTransferDocumentCode !!}</td>
            <td>{!! $paymentBankTransferRefferedBack->serialNumber !!}</td>
            <td>{!! $paymentBankTransferRefferedBack->documentDate !!}</td>
            <td>{!! $paymentBankTransferRefferedBack->narration !!}</td>
            <td>{!! $paymentBankTransferRefferedBack->bankMasterID !!}</td>
            <td>{!! $paymentBankTransferRefferedBack->bankAccountAutoID !!}</td>
            <td>{!! $paymentBankTransferRefferedBack->confirmedYN !!}</td>
            <td>{!! $paymentBankTransferRefferedBack->confirmedByEmpSystemID !!}</td>
            <td>{!! $paymentBankTransferRefferedBack->confirmedByEmpID !!}</td>
            <td>{!! $paymentBankTransferRefferedBack->confirmedByName !!}</td>
            <td>{!! $paymentBankTransferRefferedBack->confirmedDate !!}</td>
            <td>{!! $paymentBankTransferRefferedBack->approvedYN !!}</td>
            <td>{!! $paymentBankTransferRefferedBack->approvedDate !!}</td>
            <td>{!! $paymentBankTransferRefferedBack->approvedByUserID !!}</td>
            <td>{!! $paymentBankTransferRefferedBack->approvedByUserSystemID !!}</td>
            <td>{!! $paymentBankTransferRefferedBack->RollLevForApp_curr !!}</td>
            <td>{!! $paymentBankTransferRefferedBack->refferedBackYN !!}</td>
            <td>{!! $paymentBankTransferRefferedBack->timesReferred !!}</td>
            <td>{!! $paymentBankTransferRefferedBack->createdPcID !!}</td>
            <td>{!! $paymentBankTransferRefferedBack->createdUserSystemID !!}</td>
            <td>{!! $paymentBankTransferRefferedBack->createdUserID !!}</td>
            <td>{!! $paymentBankTransferRefferedBack->modifiedPc !!}</td>
            <td>{!! $paymentBankTransferRefferedBack->modifiedUserSystemID !!}</td>
            <td>{!! $paymentBankTransferRefferedBack->modifiedUser !!}</td>
            <td>{!! $paymentBankTransferRefferedBack->createdDateTime !!}</td>
            <td>{!! $paymentBankTransferRefferedBack->timeStamp !!}</td>
            <td>{!! $paymentBankTransferRefferedBack->exportedYN !!}</td>
            <td>{!! $paymentBankTransferRefferedBack->exportedUserSystemID !!}</td>
            <td>{!! $paymentBankTransferRefferedBack->exportedDate !!}</td>
            <td>
                {!! Form::open(['route' => ['paymentBankTransferRefferedBacks.destroy', $paymentBankTransferRefferedBack->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('paymentBankTransferRefferedBacks.show', [$paymentBankTransferRefferedBack->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('paymentBankTransferRefferedBacks.edit', [$paymentBankTransferRefferedBack->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>