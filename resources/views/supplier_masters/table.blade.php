<table class="table table-responsive" id="supplierMasters-table">
    <thead>
        <tr>
            <th>Uniquetextcode</th>
        <th>Primarycompanysystemid</th>
        <th>Primarycompanyid</th>
        <th>Primarysuppliercode</th>
        <th>Secondarysuppliercode</th>
        <th>Suppliername</th>
        <th>Liabilityaccountsysemid</th>
        <th>Liabilityaccount</th>
        <th>Unbilledgrvaccountsystemid</th>
        <th>Unbilledgrvaccount</th>
        <th>Address</th>
        <th>Countryid</th>
        <th>Suppliercountryid</th>
        <th>Telephone</th>
        <th>Fax</th>
        <th>Supemail</th>
        <th>Webaddress</th>
        <th>Currency</th>
        <th>Nameonpaymentcheque</th>
        <th>Creditlimit</th>
        <th>Creditperiod</th>
        <th>Supcategorymasterid</th>
        <th>Supcategorysubid</th>
        <th>Registrationnumber</th>
        <th>Registrationexprity</th>
        <th>Approvedby</th>
        <th>Approvedyn</th>
        <th>Approveddate</th>
        <th>Approvedcomment</th>
        <th>Isactive</th>
        <th>Issupplierforiegn</th>
        <th>Supplierconfirmedyn</th>
        <th>Supplierconfirmedempid</th>
        <th>Supplierconfirmedempname</th>
        <th>Supplierconfirmeddate</th>
        <th>Iscriticalyn</th>
        <th>Companylinkedto</th>
        <th>Createdusergroup</th>
        <th>Createdpcid</th>
        <th>Createduserid</th>
        <th>Modifiedpc</th>
        <th>Modifieduser</th>
        <th>Createddatetime</th>
        <th>Isdirect</th>
        <th>Supplierimportanceid</th>
        <th>Suppliernatureid</th>
        <th>Suppliertypeid</th>
        <th>Whtapplicable</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($supplierMasters as $supplierMaster)
        <tr>
            <td>{!! $supplierMaster->uniqueTextcode !!}</td>
            <td>{!! $supplierMaster->primaryCompanySystemID !!}</td>
            <td>{!! $supplierMaster->primaryCompanyID !!}</td>
            <td>{!! $supplierMaster->primarySupplierCode !!}</td>
            <td>{!! $supplierMaster->secondarySupplierCode !!}</td>
            <td>{!! $supplierMaster->supplierName !!}</td>
            <td>{!! $supplierMaster->liabilityAccountSysemID !!}</td>
            <td>{!! $supplierMaster->liabilityAccount !!}</td>
            <td>{!! $supplierMaster->UnbilledGRVAccountSystemID !!}</td>
            <td>{!! $supplierMaster->UnbilledGRVAccount !!}</td>
            <td>{!! $supplierMaster->address !!}</td>
            <td>{!! $supplierMaster->countryID !!}</td>
            <td>{!! $supplierMaster->supplierCountryID !!}</td>
            <td>{!! $supplierMaster->telephone !!}</td>
            <td>{!! $supplierMaster->fax !!}</td>
            <td>{!! $supplierMaster->supEmail !!}</td>
            <td>{!! $supplierMaster->webAddress !!}</td>
            <td>{!! $supplierMaster->currency !!}</td>
            <td>{!! $supplierMaster->nameOnPaymentCheque !!}</td>
            <td>{!! $supplierMaster->creditLimit !!}</td>
            <td>{!! $supplierMaster->creditPeriod !!}</td>
            <td>{!! $supplierMaster->supCategoryMasterID !!}</td>
            <td>{!! $supplierMaster->supCategorySubID !!}</td>
            <td>{!! $supplierMaster->registrationNumber !!}</td>
            <td>{!! $supplierMaster->registrationExprity !!}</td>
            <td>{!! $supplierMaster->approvedby !!}</td>
            <td>{!! $supplierMaster->approvedYN !!}</td>
            <td>{!! $supplierMaster->approvedDate !!}</td>
            <td>{!! $supplierMaster->approvedComment !!}</td>
            <td>{!! $supplierMaster->isActive !!}</td>
            <td>{!! $supplierMaster->isSupplierForiegn !!}</td>
            <td>{!! $supplierMaster->supplierConfirmedYN !!}</td>
            <td>{!! $supplierMaster->supplierConfirmedEmpID !!}</td>
            <td>{!! $supplierMaster->supplierConfirmedEmpName !!}</td>
            <td>{!! $supplierMaster->supplierConfirmedDate !!}</td>
            <td>{!! $supplierMaster->isCriticalYN !!}</td>
            <td>{!! $supplierMaster->companyLinkedTo !!}</td>
            <td>{!! $supplierMaster->createdUserGroup !!}</td>
            <td>{!! $supplierMaster->createdPcID !!}</td>
            <td>{!! $supplierMaster->createdUserID !!}</td>
            <td>{!! $supplierMaster->modifiedPc !!}</td>
            <td>{!! $supplierMaster->modifiedUser !!}</td>
            <td>{!! $supplierMaster->createdDateTime !!}</td>
            <td>{!! $supplierMaster->isDirect !!}</td>
            <td>{!! $supplierMaster->supplierImportanceID !!}</td>
            <td>{!! $supplierMaster->supplierNatureID !!}</td>
            <td>{!! $supplierMaster->supplierTypeID !!}</td>
            <td>{!! $supplierMaster->WHTApplicable !!}</td>
            <td>{!! $supplierMaster->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['supplierMasters.destroy', $supplierMaster->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('supplierMasters.show', [$supplierMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('supplierMasters.edit', [$supplierMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>