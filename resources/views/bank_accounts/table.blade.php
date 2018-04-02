<table class="table table-responsive" id="bankAccounts-table">
    <thead>
        <tr>
            <th>Bankassignedautoid</th>
        <th>Bankmasterautoid</th>
        <th>Companyid</th>
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
        <th>Glcodelinked</th>
        <th>Extranote</th>
        <th>Isaccountactive</th>
        <th>Isdefault</th>
        <th>Approvedyn</th>
        <th>Approvedbyempid</th>
        <th>Approvedempname</th>
        <th>Approveddate</th>
        <th>Approvedcomments</th>
        <th>Createddatetime</th>
        <th>Createdempid</th>
        <th>Createdpcid</th>
        <th>Modifeddatetime</th>
        <th>Modifiedbyempid</th>
        <th>Modifiedpcid</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($bankAccounts as $bankAccount)
        <tr>
            <td>{!! $bankAccount->bankAssignedAutoID !!}</td>
            <td>{!! $bankAccount->bankmasterAutoID !!}</td>
            <td>{!! $bankAccount->companyID !!}</td>
            <td>{!! $bankAccount->bankShortCode !!}</td>
            <td>{!! $bankAccount->bankName !!}</td>
            <td>{!! $bankAccount->bankBranch !!}</td>
            <td>{!! $bankAccount->BranchCode !!}</td>
            <td>{!! $bankAccount->BranchAddress !!}</td>
            <td>{!! $bankAccount->BranchContactPerson !!}</td>
            <td>{!! $bankAccount->BranchTel !!}</td>
            <td>{!! $bankAccount->BranchFax !!}</td>
            <td>{!! $bankAccount->BranchEmail !!}</td>
            <td>{!! $bankAccount->AccountNo !!}</td>
            <td>{!! $bankAccount->accountCurrencyID !!}</td>
            <td>{!! $bankAccount->accountSwiftCode !!}</td>
            <td>{!! $bankAccount->accountIBAN# !!}</td>
            <td>{!! $bankAccount->chqueManualStartingNo !!}</td>
            <td>{!! $bankAccount->isManualActive !!}</td>
            <td>{!! $bankAccount->chquePrintedStartingNo !!}</td>
            <td>{!! $bankAccount->isPrintedActive !!}</td>
            <td>{!! $bankAccount->glCodeLinked !!}</td>
            <td>{!! $bankAccount->extraNote !!}</td>
            <td>{!! $bankAccount->isAccountActive !!}</td>
            <td>{!! $bankAccount->isDefault !!}</td>
            <td>{!! $bankAccount->approvedYN !!}</td>
            <td>{!! $bankAccount->approvedByEmpID !!}</td>
            <td>{!! $bankAccount->approvedEmpName !!}</td>
            <td>{!! $bankAccount->approvedDate !!}</td>
            <td>{!! $bankAccount->approvedComments !!}</td>
            <td>{!! $bankAccount->createdDateTime !!}</td>
            <td>{!! $bankAccount->createdEmpID !!}</td>
            <td>{!! $bankAccount->createdPCID !!}</td>
            <td>{!! $bankAccount->modifedDateTime !!}</td>
            <td>{!! $bankAccount->modifiedByEmpID !!}</td>
            <td>{!! $bankAccount->modifiedPCID !!}</td>
            <td>{!! $bankAccount->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['bankAccounts.destroy', $bankAccount->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('bankAccounts.show', [$bankAccount->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('bankAccounts.edit', [$bankAccount->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>