<table class="table table-responsive" id="chartOfAccounts-table">
    <thead>
        <tr>
            <th>Documentsystemid</th>
        <th>Documentid</th>
        <th>Accountcode</th>
        <th>Accountdescription</th>
        <th>Masteraccount</th>
        <th>Catogaryblorpl</th>
        <th>Controllaccountyn</th>
        <th>Controlaccounts</th>
        <th>Isapproved</th>
        <th>Approvedby</th>
        <th>Approveddate</th>
        <th>Approvedcomment</th>
        <th>Isactive</th>
        <th>Isbank</th>
        <th>Allocationid</th>
        <th>Relatedpartyyn</th>
        <th>Intercompanyid</th>
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
    @foreach($chartOfAccounts as $chartOfAccount)
        <tr>
            <td>{!! $chartOfAccount->documentSystemID !!}</td>
            <td>{!! $chartOfAccount->documentID !!}</td>
            <td>{!! $chartOfAccount->AccountCode !!}</td>
            <td>{!! $chartOfAccount->AccountDescription !!}</td>
            <td>{!! $chartOfAccount->masterAccount !!}</td>
            <td>{!! $chartOfAccount->catogaryBLorPL !!}</td>
            <td>{!! $chartOfAccount->controllAccountYN !!}</td>
            <td>{!! $chartOfAccount->controlAccounts !!}</td>
            <td>{!! $chartOfAccount->isApproved !!}</td>
            <td>{!! $chartOfAccount->approvedBy !!}</td>
            <td>{!! $chartOfAccount->approvedDate !!}</td>
            <td>{!! $chartOfAccount->approvedComment !!}</td>
            <td>{!! $chartOfAccount->isActive !!}</td>
            <td>{!! $chartOfAccount->isBank !!}</td>
            <td>{!! $chartOfAccount->AllocationID !!}</td>
            <td>{!! $chartOfAccount->relatedPartyYN !!}</td>
            <td>{!! $chartOfAccount->interCompanyID !!}</td>
            <td>{!! $chartOfAccount->createdPcID !!}</td>
            <td>{!! $chartOfAccount->createdUserGroup !!}</td>
            <td>{!! $chartOfAccount->createdUserID !!}</td>
            <td>{!! $chartOfAccount->createdDateTime !!}</td>
            <td>{!! $chartOfAccount->modifiedPc !!}</td>
            <td>{!! $chartOfAccount->modifiedUser !!}</td>
            <td>{!! $chartOfAccount->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['chartOfAccounts.destroy', $chartOfAccount->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('chartOfAccounts.show', [$chartOfAccount->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('chartOfAccounts.edit', [$chartOfAccount->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>