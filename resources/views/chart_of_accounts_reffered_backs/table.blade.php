<table class="table table-responsive" id="chartOfAccountsRefferedBacks-table">
    <thead>
        <tr>
            <th>Chartofaccountsystemid</th>
        <th>Primarycompanysystemid</th>
        <th>Primarycompanyid</th>
        <th>Documentsystemid</th>
        <th>Documentid</th>
        <th>Accountcode</th>
        <th>Accountdescription</th>
        <th>Masteraccount</th>
        <th>Catogaryblorplid</th>
        <th>Catogaryblorpl</th>
        <th>Controllaccountyn</th>
        <th>Controlaccountssystemid</th>
        <th>Controlaccounts</th>
        <th>Isapproved</th>
        <th>Approvedbysystemid</th>
        <th>Approvedby</th>
        <th>Approveddate</th>
        <th>Approvedcomment</th>
        <th>Isactive</th>
        <th>Isbank</th>
        <th>Allocationid</th>
        <th>Relatedpartyyn</th>
        <th>Intercompanysystemid</th>
        <th>Intercompanyid</th>
        <th>Confirmedyn</th>
        <th>Confirmedempsystemid</th>
        <th>Confirmedempid</th>
        <th>Confirmedempname</th>
        <th>Confirmedempdate</th>
        <th>Ismasteraccount</th>
        <th>Rolllevforapp Curr</th>
        <th>Refferedbackyn</th>
        <th>Timesreferred</th>
        <th>Createdpcid</th>
        <th>Createdusergroup</th>
        <th>Createduserid</th>
        <th>Createddatetime</th>
        <th>Modifiedpc</th>
        <th>Modifieduser</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($chartOfAccountsRefferedBacks as $chartOfAccountsRefferedBack)
        <tr>
            <td>{!! $chartOfAccountsRefferedBack->chartOfAccountSystemID !!}</td>
            <td>{!! $chartOfAccountsRefferedBack->primaryCompanySystemID !!}</td>
            <td>{!! $chartOfAccountsRefferedBack->primaryCompanyID !!}</td>
            <td>{!! $chartOfAccountsRefferedBack->documentSystemID !!}</td>
            <td>{!! $chartOfAccountsRefferedBack->documentID !!}</td>
            <td>{!! $chartOfAccountsRefferedBack->AccountCode !!}</td>
            <td>{!! $chartOfAccountsRefferedBack->AccountDescription !!}</td>
            <td>{!! $chartOfAccountsRefferedBack->masterAccount !!}</td>
            <td>{!! $chartOfAccountsRefferedBack->catogaryBLorPLID !!}</td>
            <td>{!! $chartOfAccountsRefferedBack->catogaryBLorPL !!}</td>
            <td>{!! $chartOfAccountsRefferedBack->controllAccountYN !!}</td>
            <td>{!! $chartOfAccountsRefferedBack->controlAccountsSystemID !!}</td>
            <td>{!! $chartOfAccountsRefferedBack->controlAccounts !!}</td>
            <td>{!! $chartOfAccountsRefferedBack->isApproved !!}</td>
            <td>{!! $chartOfAccountsRefferedBack->approvedBySystemID !!}</td>
            <td>{!! $chartOfAccountsRefferedBack->approvedBy !!}</td>
            <td>{!! $chartOfAccountsRefferedBack->approvedDate !!}</td>
            <td>{!! $chartOfAccountsRefferedBack->approvedComment !!}</td>
            <td>{!! $chartOfAccountsRefferedBack->isActive !!}</td>
            <td>{!! $chartOfAccountsRefferedBack->isBank !!}</td>
            <td>{!! $chartOfAccountsRefferedBack->AllocationID !!}</td>
            <td>{!! $chartOfAccountsRefferedBack->relatedPartyYN !!}</td>
            <td>{!! $chartOfAccountsRefferedBack->interCompanySystemID !!}</td>
            <td>{!! $chartOfAccountsRefferedBack->interCompanyID !!}</td>
            <td>{!! $chartOfAccountsRefferedBack->confirmedYN !!}</td>
            <td>{!! $chartOfAccountsRefferedBack->confirmedEmpSystemID !!}</td>
            <td>{!! $chartOfAccountsRefferedBack->confirmedEmpID !!}</td>
            <td>{!! $chartOfAccountsRefferedBack->confirmedEmpName !!}</td>
            <td>{!! $chartOfAccountsRefferedBack->confirmedEmpDate !!}</td>
            <td>{!! $chartOfAccountsRefferedBack->isMasterAccount !!}</td>
            <td>{!! $chartOfAccountsRefferedBack->RollLevForApp_curr !!}</td>
            <td>{!! $chartOfAccountsRefferedBack->refferedBackYN !!}</td>
            <td>{!! $chartOfAccountsRefferedBack->timesReferred !!}</td>
            <td>{!! $chartOfAccountsRefferedBack->createdPcID !!}</td>
            <td>{!! $chartOfAccountsRefferedBack->createdUserGroup !!}</td>
            <td>{!! $chartOfAccountsRefferedBack->createdUserID !!}</td>
            <td>{!! $chartOfAccountsRefferedBack->createdDateTime !!}</td>
            <td>{!! $chartOfAccountsRefferedBack->modifiedPc !!}</td>
            <td>{!! $chartOfAccountsRefferedBack->modifiedUser !!}</td>
            <td>{!! $chartOfAccountsRefferedBack->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['chartOfAccountsRefferedBacks.destroy', $chartOfAccountsRefferedBack->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('chartOfAccountsRefferedBacks.show', [$chartOfAccountsRefferedBack->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('chartOfAccountsRefferedBacks.edit', [$chartOfAccountsRefferedBack->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>