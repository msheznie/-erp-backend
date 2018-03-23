<table class="table table-responsive" id="customerMasters-table">
    <thead>
        <tr>
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
        <th>Companylinkedto</th>
        <th>Iscustomeractive</th>
        <th>Isallowedqhse</th>
        <th>Vateligible</th>
        <th>Vatnumber</th>
        <th>Vatpercentage</th>
        <th>Issupplierforiegn</th>
        <th>Approvedyn</th>
        <th>Approveddate</th>
        <th>Approvedcomment</th>
        <th>Confirmedyn</th>
        <th>Confirmedempsystemid</th>
        <th>Confirmedempid</th>
        <th>Confirmedempname</th>
        <th>Confirmeddate</th>
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
    @foreach($customerMasters as $customerMaster)
        <tr>
            <td>{!! $customerMaster->primaryCompanySystemID !!}</td>
            <td>{!! $customerMaster->primaryCompanyID !!}</td>
            <td>{!! $customerMaster->documentSystemID !!}</td>
            <td>{!! $customerMaster->documentID !!}</td>
            <td>{!! $customerMaster->lastSerialOrder !!}</td>
            <td>{!! $customerMaster->CutomerCode !!}</td>
            <td>{!! $customerMaster->customerShortCode !!}</td>
            <td>{!! $customerMaster->custGLAccountSystemID !!}</td>
            <td>{!! $customerMaster->custGLaccount !!}</td>
            <td>{!! $customerMaster->CustomerName !!}</td>
            <td>{!! $customerMaster->ReportTitle !!}</td>
            <td>{!! $customerMaster->customerAddress1 !!}</td>
            <td>{!! $customerMaster->customerAddress2 !!}</td>
            <td>{!! $customerMaster->customerCity !!}</td>
            <td>{!! $customerMaster->customerCountry !!}</td>
            <td>{!! $customerMaster->CustWebsite !!}</td>
            <td>{!! $customerMaster->creditLimit !!}</td>
            <td>{!! $customerMaster->creditDays !!}</td>
            <td>{!! $customerMaster->customerLogo !!}</td>
            <td>{!! $customerMaster->companyLinkedTo !!}</td>
            <td>{!! $customerMaster->isCustomerActive !!}</td>
            <td>{!! $customerMaster->isAllowedQHSE !!}</td>
            <td>{!! $customerMaster->vatEligible !!}</td>
            <td>{!! $customerMaster->vatNumber !!}</td>
            <td>{!! $customerMaster->vatPercentage !!}</td>
            <td>{!! $customerMaster->isSupplierForiegn !!}</td>
            <td>{!! $customerMaster->approvedYN !!}</td>
            <td>{!! $customerMaster->approvedDate !!}</td>
            <td>{!! $customerMaster->approvedComment !!}</td>
            <td>{!! $customerMaster->confirmedYN !!}</td>
            <td>{!! $customerMaster->confirmedEmpSystemID !!}</td>
            <td>{!! $customerMaster->confirmedEmpID !!}</td>
            <td>{!! $customerMaster->confirmedEmpName !!}</td>
            <td>{!! $customerMaster->confirmedDate !!}</td>
            <td>{!! $customerMaster->createdUserGroup !!}</td>
            <td>{!! $customerMaster->createdUserID !!}</td>
            <td>{!! $customerMaster->createdDateTime !!}</td>
            <td>{!! $customerMaster->createdPcID !!}</td>
            <td>{!! $customerMaster->modifiedPc !!}</td>
            <td>{!! $customerMaster->modifiedUser !!}</td>
            <td>{!! $customerMaster->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['customerMasters.destroy', $customerMaster->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('customerMasters.show', [$customerMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('customerMasters.edit', [$customerMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>