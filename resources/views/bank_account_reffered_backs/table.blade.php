<table class="table table-responsive" id="bankAccountRefferedBacks-table">
    <thead>
        <tr>
            <th>Bankaccountautoid</th>
        <th>Bankassignedautoid</th>
        <th>Bankmasterautoid</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Documentsystemid</th>
        <th>Documentid</th>
        <th>Bankshortcode</th>
        <th>Bankname</th>
        <th>Bankbranch</th>
        <th>Branchcode</th>
        <th>Branchaddress</th>
        <th>Branchcontactperson</th>
        <th>Branchtel</th>
        <th>Branchfax</th>
        <th>Branchemail</th>
        <th>Accountno</th>
        <th>Accountcurrencyid</th>
        <th>Accountswiftcode</th>
        <th>Accountiban#</th>
        <th>Chquemanualstartingno</th>
        <th>Ismanualactive</th>
        <th>Chqueprintedstartingno</th>
        <th>Isprintedactive</th>
        <th>Chartofaccountsystemid</th>
        <th>Glcodelinked</th>
        <th>Extranote</th>
        <th>Isaccountactive</th>
        <th>Isdefault</th>
        <th>Approvedyn</th>
        <th>Approvedbyempid</th>
        <th>Approvedbyusersystemid</th>
        <th>Approvedempname</th>
        <th>Approveddate</th>
        <th>Approvedcomments</th>
        <th>Createddatetime</th>
        <th>Createdusersystemid</th>
        <th>Createdempid</th>
        <th>Createdpcid</th>
        <th>Modifeddatetime</th>
        <th>Modifiedusersystemid</th>
        <th>Modifiedbyempid</th>
        <th>Modifiedpcid</th>
        <th>Timestamp</th>
        <th>Confirmedyn</th>
        <th>Confirmedbyempsystemid</th>
        <th>Confirmedbyempid</th>
        <th>Confirmedbyname</th>
        <th>Confirmeddate</th>
        <th>Rolllevforapp Curr</th>
        <th>Refferedbackyn</th>
        <th>Timesreferred</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($bankAccountRefferedBacks as $bankAccountRefferedBack)
        <tr>
            <td>{!! $bankAccountRefferedBack->bankAccountAutoID !!}</td>
            <td>{!! $bankAccountRefferedBack->bankAssignedAutoID !!}</td>
            <td>{!! $bankAccountRefferedBack->bankmasterAutoID !!}</td>
            <td>{!! $bankAccountRefferedBack->companySystemID !!}</td>
            <td>{!! $bankAccountRefferedBack->companyID !!}</td>
            <td>{!! $bankAccountRefferedBack->documentSystemID !!}</td>
            <td>{!! $bankAccountRefferedBack->documentID !!}</td>
            <td>{!! $bankAccountRefferedBack->bankShortCode !!}</td>
            <td>{!! $bankAccountRefferedBack->bankName !!}</td>
            <td>{!! $bankAccountRefferedBack->bankBranch !!}</td>
            <td>{!! $bankAccountRefferedBack->BranchCode !!}</td>
            <td>{!! $bankAccountRefferedBack->BranchAddress !!}</td>
            <td>{!! $bankAccountRefferedBack->BranchContactPerson !!}</td>
            <td>{!! $bankAccountRefferedBack->BranchTel !!}</td>
            <td>{!! $bankAccountRefferedBack->BranchFax !!}</td>
            <td>{!! $bankAccountRefferedBack->BranchEmail !!}</td>
            <td>{!! $bankAccountRefferedBack->AccountNo !!}</td>
            <td>{!! $bankAccountRefferedBack->accountCurrencyID !!}</td>
            <td>{!! $bankAccountRefferedBack->accountSwiftCode !!}</td>
            <td>{!! $bankAccountRefferedBack->accountIBAN# !!}</td>
            <td>{!! $bankAccountRefferedBack->chqueManualStartingNo !!}</td>
            <td>{!! $bankAccountRefferedBack->isManualActive !!}</td>
            <td>{!! $bankAccountRefferedBack->chquePrintedStartingNo !!}</td>
            <td>{!! $bankAccountRefferedBack->isPrintedActive !!}</td>
            <td>{!! $bankAccountRefferedBack->chartOfAccountSystemID !!}</td>
            <td>{!! $bankAccountRefferedBack->glCodeLinked !!}</td>
            <td>{!! $bankAccountRefferedBack->extraNote !!}</td>
            <td>{!! $bankAccountRefferedBack->isAccountActive !!}</td>
            <td>{!! $bankAccountRefferedBack->isDefault !!}</td>
            <td>{!! $bankAccountRefferedBack->approvedYN !!}</td>
            <td>{!! $bankAccountRefferedBack->approvedByEmpID !!}</td>
            <td>{!! $bankAccountRefferedBack->approvedByUserSystemID !!}</td>
            <td>{!! $bankAccountRefferedBack->approvedEmpName !!}</td>
            <td>{!! $bankAccountRefferedBack->approvedDate !!}</td>
            <td>{!! $bankAccountRefferedBack->approvedComments !!}</td>
            <td>{!! $bankAccountRefferedBack->createdDateTime !!}</td>
            <td>{!! $bankAccountRefferedBack->createdUserSystemID !!}</td>
            <td>{!! $bankAccountRefferedBack->createdEmpID !!}</td>
            <td>{!! $bankAccountRefferedBack->createdPCID !!}</td>
            <td>{!! $bankAccountRefferedBack->modifedDateTime !!}</td>
            <td>{!! $bankAccountRefferedBack->modifiedUserSystemID !!}</td>
            <td>{!! $bankAccountRefferedBack->modifiedByEmpID !!}</td>
            <td>{!! $bankAccountRefferedBack->modifiedPCID !!}</td>
            <td>{!! $bankAccountRefferedBack->timeStamp !!}</td>
            <td>{!! $bankAccountRefferedBack->confirmedYN !!}</td>
            <td>{!! $bankAccountRefferedBack->confirmedByEmpSystemID !!}</td>
            <td>{!! $bankAccountRefferedBack->confirmedByEmpID !!}</td>
            <td>{!! $bankAccountRefferedBack->confirmedByName !!}</td>
            <td>{!! $bankAccountRefferedBack->confirmedDate !!}</td>
            <td>{!! $bankAccountRefferedBack->RollLevForApp_curr !!}</td>
            <td>{!! $bankAccountRefferedBack->refferedBackYN !!}</td>
            <td>{!! $bankAccountRefferedBack->timesReferred !!}</td>
            <td>
                {!! Form::open(['route' => ['bankAccountRefferedBacks.destroy', $bankAccountRefferedBack->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('bankAccountRefferedBacks.show', [$bankAccountRefferedBack->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('bankAccountRefferedBacks.edit', [$bankAccountRefferedBack->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>