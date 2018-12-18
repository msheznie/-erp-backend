<table class="table table-responsive" id="customerMasterRefferedBacks-table">
    <thead>
        <tr>
            <th>Customercodesystem</th>
        <th>Primarycompanysystemid</th>
        <th>Primarycompanyid</th>
        <th>Documentsystemid</th>
        <th>Documentid</th>
        <th>Lastserialorder</th>
        <th>Cutomercode</th>
        <th>Customershortcode</th>
        <th>Custglaccountsystemid</th>
        <th>Custglaccount</th>
        <th>Customername</th>
        <th>Reporttitle</th>
        <th>Customeraddress1</th>
        <th>Customeraddress2</th>
        <th>Customercity</th>
        <th>Customercountry</th>
        <th>Custwebsite</th>
        <th>Creditlimit</th>
        <th>Creditdays</th>
        <th>Customerlogo</th>
        <th>Companylinkedtosystemid</th>
        <th>Companylinkedto</th>
        <th>Iscustomeractive</th>
        <th>Isallowedqhse</th>
        <th>Vateligible</th>
        <th>Vatnumber</th>
        <th>Vatpercentage</th>
        <th>Issupplierforiegn</th>
        <th>Approvedyn</th>
        <th>Approvedempsystemid</th>
        <th>Approvedempid</th>
        <th>Approveddate</th>
        <th>Approvedcomment</th>
        <th>Confirmedyn</th>
        <th>Confirmedempsystemid</th>
        <th>Confirmedempid</th>
        <th>Confirmedempname</th>
        <th>Confirmeddate</th>
        <th>Rolllevforapp Curr</th>
        <th>Refferedbackyn</th>
        <th>Timesreferred</th>
        <th>Createdusergroup</th>
        <th>Createduserid</th>
        <th>Createddatetime</th>
        <th>Createdpcid</th>
        <th>Modifiedpc</th>
        <th>Modifieduser</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($customerMasterRefferedBacks as $customerMasterRefferedBack)
        <tr>
            <td>{!! $customerMasterRefferedBack->customerCodeSystem !!}</td>
            <td>{!! $customerMasterRefferedBack->primaryCompanySystemID !!}</td>
            <td>{!! $customerMasterRefferedBack->primaryCompanyID !!}</td>
            <td>{!! $customerMasterRefferedBack->documentSystemID !!}</td>
            <td>{!! $customerMasterRefferedBack->documentID !!}</td>
            <td>{!! $customerMasterRefferedBack->lastSerialOrder !!}</td>
            <td>{!! $customerMasterRefferedBack->CutomerCode !!}</td>
            <td>{!! $customerMasterRefferedBack->customerShortCode !!}</td>
            <td>{!! $customerMasterRefferedBack->custGLAccountSystemID !!}</td>
            <td>{!! $customerMasterRefferedBack->custGLaccount !!}</td>
            <td>{!! $customerMasterRefferedBack->CustomerName !!}</td>
            <td>{!! $customerMasterRefferedBack->ReportTitle !!}</td>
            <td>{!! $customerMasterRefferedBack->customerAddress1 !!}</td>
            <td>{!! $customerMasterRefferedBack->customerAddress2 !!}</td>
            <td>{!! $customerMasterRefferedBack->customerCity !!}</td>
            <td>{!! $customerMasterRefferedBack->customerCountry !!}</td>
            <td>{!! $customerMasterRefferedBack->CustWebsite !!}</td>
            <td>{!! $customerMasterRefferedBack->creditLimit !!}</td>
            <td>{!! $customerMasterRefferedBack->creditDays !!}</td>
            <td>{!! $customerMasterRefferedBack->customerLogo !!}</td>
            <td>{!! $customerMasterRefferedBack->companyLinkedToSystemID !!}</td>
            <td>{!! $customerMasterRefferedBack->companyLinkedTo !!}</td>
            <td>{!! $customerMasterRefferedBack->isCustomerActive !!}</td>
            <td>{!! $customerMasterRefferedBack->isAllowedQHSE !!}</td>
            <td>{!! $customerMasterRefferedBack->vatEligible !!}</td>
            <td>{!! $customerMasterRefferedBack->vatNumber !!}</td>
            <td>{!! $customerMasterRefferedBack->vatPercentage !!}</td>
            <td>{!! $customerMasterRefferedBack->isSupplierForiegn !!}</td>
            <td>{!! $customerMasterRefferedBack->approvedYN !!}</td>
            <td>{!! $customerMasterRefferedBack->approvedEmpSystemID !!}</td>
            <td>{!! $customerMasterRefferedBack->approvedEmpID !!}</td>
            <td>{!! $customerMasterRefferedBack->approvedDate !!}</td>
            <td>{!! $customerMasterRefferedBack->approvedComment !!}</td>
            <td>{!! $customerMasterRefferedBack->confirmedYN !!}</td>
            <td>{!! $customerMasterRefferedBack->confirmedEmpSystemID !!}</td>
            <td>{!! $customerMasterRefferedBack->confirmedEmpID !!}</td>
            <td>{!! $customerMasterRefferedBack->confirmedEmpName !!}</td>
            <td>{!! $customerMasterRefferedBack->confirmedDate !!}</td>
            <td>{!! $customerMasterRefferedBack->RollLevForApp_curr !!}</td>
            <td>{!! $customerMasterRefferedBack->refferedBackYN !!}</td>
            <td>{!! $customerMasterRefferedBack->timesReferred !!}</td>
            <td>{!! $customerMasterRefferedBack->createdUserGroup !!}</td>
            <td>{!! $customerMasterRefferedBack->createdUserID !!}</td>
            <td>{!! $customerMasterRefferedBack->createdDateTime !!}</td>
            <td>{!! $customerMasterRefferedBack->createdPcID !!}</td>
            <td>{!! $customerMasterRefferedBack->modifiedPc !!}</td>
            <td>{!! $customerMasterRefferedBack->modifiedUser !!}</td>
            <td>{!! $customerMasterRefferedBack->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['customerMasterRefferedBacks.destroy', $customerMasterRefferedBack->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('customerMasterRefferedBacks.show', [$customerMasterRefferedBack->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('customerMasterRefferedBacks.edit', [$customerMasterRefferedBack->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>