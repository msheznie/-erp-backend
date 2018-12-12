<table class="table table-responsive" id="bankReconciliationRefferedBacks-table">
    <thead>
        <tr>
            <th>Bankrecautoid</th>
        <th>Documentsystemid</th>
        <th>Documentid</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Bankmasterid</th>
        <th>Bankaccountautoid</th>
        <th>Bankglautoid</th>
        <th>Month</th>
        <th>Serialno</th>
        <th>Bankrecprimarycode</th>
        <th>Year</th>
        <th>Bankrecasof</th>
        <th>Openingbalance</th>
        <th>Closingbalance</th>
        <th>Description</th>
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
        <th>Timesreferred</th>
        <th>Refferedbackyn</th>
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
    @foreach($bankReconciliationRefferedBacks as $bankReconciliationRefferedBack)
        <tr>
            <td>{!! $bankReconciliationRefferedBack->bankRecAutoID !!}</td>
            <td>{!! $bankReconciliationRefferedBack->documentSystemID !!}</td>
            <td>{!! $bankReconciliationRefferedBack->documentID !!}</td>
            <td>{!! $bankReconciliationRefferedBack->companySystemID !!}</td>
            <td>{!! $bankReconciliationRefferedBack->companyID !!}</td>
            <td>{!! $bankReconciliationRefferedBack->bankMasterID !!}</td>
            <td>{!! $bankReconciliationRefferedBack->bankAccountAutoID !!}</td>
            <td>{!! $bankReconciliationRefferedBack->bankGLAutoID !!}</td>
            <td>{!! $bankReconciliationRefferedBack->month !!}</td>
            <td>{!! $bankReconciliationRefferedBack->serialNo !!}</td>
            <td>{!! $bankReconciliationRefferedBack->bankRecPrimaryCode !!}</td>
            <td>{!! $bankReconciliationRefferedBack->year !!}</td>
            <td>{!! $bankReconciliationRefferedBack->bankRecAsOf !!}</td>
            <td>{!! $bankReconciliationRefferedBack->openingBalance !!}</td>
            <td>{!! $bankReconciliationRefferedBack->closingBalance !!}</td>
            <td>{!! $bankReconciliationRefferedBack->description !!}</td>
            <td>{!! $bankReconciliationRefferedBack->confirmedYN !!}</td>
            <td>{!! $bankReconciliationRefferedBack->confirmedByEmpSystemID !!}</td>
            <td>{!! $bankReconciliationRefferedBack->confirmedByEmpID !!}</td>
            <td>{!! $bankReconciliationRefferedBack->confirmedByName !!}</td>
            <td>{!! $bankReconciliationRefferedBack->confirmedDate !!}</td>
            <td>{!! $bankReconciliationRefferedBack->approvedYN !!}</td>
            <td>{!! $bankReconciliationRefferedBack->approvedDate !!}</td>
            <td>{!! $bankReconciliationRefferedBack->approvedByUserID !!}</td>
            <td>{!! $bankReconciliationRefferedBack->approvedByUserSystemID !!}</td>
            <td>{!! $bankReconciliationRefferedBack->RollLevForApp_curr !!}</td>
            <td>{!! $bankReconciliationRefferedBack->timesReferred !!}</td>
            <td>{!! $bankReconciliationRefferedBack->refferedBackYN !!}</td>
            <td>{!! $bankReconciliationRefferedBack->createdPcID !!}</td>
            <td>{!! $bankReconciliationRefferedBack->createdUserSystemID !!}</td>
            <td>{!! $bankReconciliationRefferedBack->createdUserID !!}</td>
            <td>{!! $bankReconciliationRefferedBack->modifiedPc !!}</td>
            <td>{!! $bankReconciliationRefferedBack->modifiedUserSystemID !!}</td>
            <td>{!! $bankReconciliationRefferedBack->modifiedUser !!}</td>
            <td>{!! $bankReconciliationRefferedBack->createdDateTime !!}</td>
            <td>{!! $bankReconciliationRefferedBack->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['bankReconciliationRefferedBacks.destroy', $bankReconciliationRefferedBack->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('bankReconciliationRefferedBacks.show', [$bankReconciliationRefferedBack->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('bankReconciliationRefferedBacks.edit', [$bankReconciliationRefferedBack->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>