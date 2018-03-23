<table class="table table-responsive" id="itemMasters-table">
    <thead>
        <tr>
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
        <th>Sentconfirmationemail</th>
        <th>Confirmationemailsentbyempid</th>
        <th>Confirmationemailsentbyempname</th>
        <th>Itemconfirmedyn</th>
        <th>Itemconfirmedbyempid</th>
        <th>Itemconfirmedbyempname</th>
        <th>Itemconfirmeddate</th>
        <th>Itemapprovedby</th>
        <th>Itemapprovedyn</th>
        <th>Itemapproveddate</th>
        <th>Itemapprovedcomment</th>
        <th>Createdusergroup</th>
        <th>Createdpcid</th>
        <th>Createduserid</th>
        <th>Modifiedpc</th>
        <th>Modifieduser</th>
        <th>Createddatetime</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($itemMasters as $itemMaster)
        <tr>
            <td>{!! $itemMaster->primaryItemCode !!}</td>
            <td>{!! $itemMaster->runningSerialOrder !!}</td>
            <td>{!! $itemMaster->documentSystemID !!}</td>
            <td>{!! $itemMaster->documentID !!}</td>
            <td>{!! $itemMaster->primaryCompanySystemID !!}</td>
            <td>{!! $itemMaster->primaryCompanyID !!}</td>
            <td>{!! $itemMaster->primaryCode !!}</td>
            <td>{!! $itemMaster->secondaryItemCode !!}</td>
            <td>{!! $itemMaster->barcode !!}</td>
            <td>{!! $itemMaster->itemDescription !!}</td>
            <td>{!! $itemMaster->itemShortDescription !!}</td>
            <td>{!! $itemMaster->itemUrl !!}</td>
            <td>{!! $itemMaster->unit !!}</td>
            <td>{!! $itemMaster->financeCategoryMaster !!}</td>
            <td>{!! $itemMaster->financeCategorySub !!}</td>
            <td>{!! $itemMaster->itemPicture !!}</td>
            <td>{!! $itemMaster->selectedForAssign !!}</td>
            <td>{!! $itemMaster->isActive !!}</td>
            <td>{!! $itemMaster->sentConfirmationEmail !!}</td>
            <td>{!! $itemMaster->confirmationEmailSentByEmpID !!}</td>
            <td>{!! $itemMaster->confirmationEmailSentByEmpName !!}</td>
            <td>{!! $itemMaster->itemConfirmedYN !!}</td>
            <td>{!! $itemMaster->itemConfirmedByEMPID !!}</td>
            <td>{!! $itemMaster->itemConfirmedByEMPName !!}</td>
            <td>{!! $itemMaster->itemConfirmedDate !!}</td>
            <td>{!! $itemMaster->itemApprovedBy !!}</td>
            <td>{!! $itemMaster->itemApprovedYN !!}</td>
            <td>{!! $itemMaster->itemApprovedDate !!}</td>
            <td>{!! $itemMaster->itemApprovedComment !!}</td>
            <td>{!! $itemMaster->createdUserGroup !!}</td>
            <td>{!! $itemMaster->createdPcID !!}</td>
            <td>{!! $itemMaster->createdUserID !!}</td>
            <td>{!! $itemMaster->modifiedPc !!}</td>
            <td>{!! $itemMaster->modifiedUser !!}</td>
            <td>{!! $itemMaster->createdDateTime !!}</td>
            <td>{!! $itemMaster->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['itemMasters.destroy', $itemMaster->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('itemMasters.show', [$itemMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('itemMasters.edit', [$itemMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>