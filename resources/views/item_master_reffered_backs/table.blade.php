<table class="table table-responsive" id="itemMasterRefferedBacks-table">
    <thead>
        <tr>
            <th>Itemcodesystem</th>
        <th>Primaryitemcode</th>
        <th>Runningserialorder</th>
        <th>Documentsystemid</th>
        <th>Documentid</th>
        <th>Primarycompanysystemid</th>
        <th>Primarycompanyid</th>
        <th>Primarycode</th>
        <th>Secondaryitemcode</th>
        <th>Barcode</th>
        <th>Itemdescription</th>
        <th>Itemshortdescription</th>
        <th>Itemurl</th>
        <th>Unit</th>
        <th>Financecategorymaster</th>
        <th>Financecategorysub</th>
        <th>Itempicture</th>
        <th>Selectedforassign</th>
        <th>Isactive</th>
        <th>Rolllevforapp Curr</th>
        <th>Sentconfirmationemail</th>
        <th>Confirmationemailsentbyempid</th>
        <th>Confirmationemailsentbyempname</th>
        <th>Itemconfirmedyn</th>
        <th>Itemconfirmedbyempsystemid</th>
        <th>Itemconfirmedbyempid</th>
        <th>Itemconfirmedbyempname</th>
        <th>Itemconfirmeddate</th>
        <th>Itemapprovedbysystemid</th>
        <th>Itemapprovedby</th>
        <th>Itemapprovedyn</th>
        <th>Itemapproveddate</th>
        <th>Itemapprovedcomment</th>
        <th>Timesreferred</th>
        <th>Refferedbackyn</th>
        <th>Createdusergroup</th>
        <th>Createdpcid</th>
        <th>Createduserid</th>
        <th>Modifiedpc</th>
        <th>Modifieduser</th>
        <th>Createddatetime</th>
        <th>Timestamp</th>
        <th>Createdusersystemid</th>
        <th>Modifiedusersystemid</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($itemMasterRefferedBacks as $itemMasterRefferedBack)
        <tr>
            <td>{!! $itemMasterRefferedBack->itemCodeSystem !!}</td>
            <td>{!! $itemMasterRefferedBack->primaryItemCode !!}</td>
            <td>{!! $itemMasterRefferedBack->runningSerialOrder !!}</td>
            <td>{!! $itemMasterRefferedBack->documentSystemID !!}</td>
            <td>{!! $itemMasterRefferedBack->documentID !!}</td>
            <td>{!! $itemMasterRefferedBack->primaryCompanySystemID !!}</td>
            <td>{!! $itemMasterRefferedBack->primaryCompanyID !!}</td>
            <td>{!! $itemMasterRefferedBack->primaryCode !!}</td>
            <td>{!! $itemMasterRefferedBack->secondaryItemCode !!}</td>
            <td>{!! $itemMasterRefferedBack->barcode !!}</td>
            <td>{!! $itemMasterRefferedBack->itemDescription !!}</td>
            <td>{!! $itemMasterRefferedBack->itemShortDescription !!}</td>
            <td>{!! $itemMasterRefferedBack->itemUrl !!}</td>
            <td>{!! $itemMasterRefferedBack->unit !!}</td>
            <td>{!! $itemMasterRefferedBack->financeCategoryMaster !!}</td>
            <td>{!! $itemMasterRefferedBack->financeCategorySub !!}</td>
            <td>{!! $itemMasterRefferedBack->itemPicture !!}</td>
            <td>{!! $itemMasterRefferedBack->selectedForAssign !!}</td>
            <td>{!! $itemMasterRefferedBack->isActive !!}</td>
            <td>{!! $itemMasterRefferedBack->RollLevForApp_curr !!}</td>
            <td>{!! $itemMasterRefferedBack->sentConfirmationEmail !!}</td>
            <td>{!! $itemMasterRefferedBack->confirmationEmailSentByEmpID !!}</td>
            <td>{!! $itemMasterRefferedBack->confirmationEmailSentByEmpName !!}</td>
            <td>{!! $itemMasterRefferedBack->itemConfirmedYN !!}</td>
            <td>{!! $itemMasterRefferedBack->itemConfirmedByEMPSystemID !!}</td>
            <td>{!! $itemMasterRefferedBack->itemConfirmedByEMPID !!}</td>
            <td>{!! $itemMasterRefferedBack->itemConfirmedByEMPName !!}</td>
            <td>{!! $itemMasterRefferedBack->itemConfirmedDate !!}</td>
            <td>{!! $itemMasterRefferedBack->itemApprovedBySystemID !!}</td>
            <td>{!! $itemMasterRefferedBack->itemApprovedBy !!}</td>
            <td>{!! $itemMasterRefferedBack->itemApprovedYN !!}</td>
            <td>{!! $itemMasterRefferedBack->itemApprovedDate !!}</td>
            <td>{!! $itemMasterRefferedBack->itemApprovedComment !!}</td>
            <td>{!! $itemMasterRefferedBack->timesReferred !!}</td>
            <td>{!! $itemMasterRefferedBack->refferedBackYN !!}</td>
            <td>{!! $itemMasterRefferedBack->createdUserGroup !!}</td>
            <td>{!! $itemMasterRefferedBack->createdPcID !!}</td>
            <td>{!! $itemMasterRefferedBack->createdUserID !!}</td>
            <td>{!! $itemMasterRefferedBack->modifiedPc !!}</td>
            <td>{!! $itemMasterRefferedBack->modifiedUser !!}</td>
            <td>{!! $itemMasterRefferedBack->createdDateTime !!}</td>
            <td>{!! $itemMasterRefferedBack->timestamp !!}</td>
            <td>{!! $itemMasterRefferedBack->createdUserSystemID !!}</td>
            <td>{!! $itemMasterRefferedBack->modifiedUserSystemID !!}</td>
            <td>
                {!! Form::open(['route' => ['itemMasterRefferedBacks.destroy', $itemMasterRefferedBack->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('itemMasterRefferedBacks.show', [$itemMasterRefferedBack->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('itemMasterRefferedBacks.edit', [$itemMasterRefferedBack->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>