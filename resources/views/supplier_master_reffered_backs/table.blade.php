<table class="table table-responsive" id="supplierMasterRefferedBacks-table">
    <thead>
        <tr>
            <th>Suppliercodesystem</th>
        <th>Uniquetextcode</th>
        <th>Primarycompanysystemid</th>
        <th>Primarycompanyid</th>
        <th>Documentsystemid</th>
        <th>Documentid</th>
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
        <th>Approvedyn</th>
        <th>Approvedempsystemid</th>
        <th>Approvedby</th>
        <th>Approveddate</th>
        <th>Approvedcomment</th>
        <th>Isactive</th>
        <th>Issupplierforiegn</th>
        <th>Supplierconfirmedyn</th>
        <th>Supplierconfirmedempid</th>
        <th>Supplierconfirmedempsystemid</th>
        <th>Supplierconfirmedempname</th>
        <th>Supplierconfirmeddate</th>
        <th>Iscriticalyn</th>
        <th>Companylinkedtosystemid</th>
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
        <th>Vateligible</th>
        <th>Vatnumber</th>
        <th>Vatpercentage</th>
        <th>Supcategoryicvmasterid</th>
        <th>Supcategorysubicvid</th>
        <th>Islccyn</th>
        <th>Rolllevforapp Curr</th>
        <th>Refferedbackyn</th>
        <th>Timesreferred</th>
        <th>Timestamp</th>
        <th>Createdusersystemid</th>
        <th>Modifiedusersystemid</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($supplierMasterRefferedBacks as $supplierMasterRefferedBack)
        <tr>
            <td>{!! $supplierMasterRefferedBack->supplierCodeSystem !!}</td>
            <td>{!! $supplierMasterRefferedBack->uniqueTextcode !!}</td>
            <td>{!! $supplierMasterRefferedBack->primaryCompanySystemID !!}</td>
            <td>{!! $supplierMasterRefferedBack->primaryCompanyID !!}</td>
            <td>{!! $supplierMasterRefferedBack->documentSystemID !!}</td>
            <td>{!! $supplierMasterRefferedBack->documentID !!}</td>
            <td>{!! $supplierMasterRefferedBack->primarySupplierCode !!}</td>
            <td>{!! $supplierMasterRefferedBack->secondarySupplierCode !!}</td>
            <td>{!! $supplierMasterRefferedBack->supplierName !!}</td>
            <td>{!! $supplierMasterRefferedBack->liabilityAccountSysemID !!}</td>
            <td>{!! $supplierMasterRefferedBack->liabilityAccount !!}</td>
            <td>{!! $supplierMasterRefferedBack->UnbilledGRVAccountSystemID !!}</td>
            <td>{!! $supplierMasterRefferedBack->UnbilledGRVAccount !!}</td>
            <td>{!! $supplierMasterRefferedBack->address !!}</td>
            <td>{!! $supplierMasterRefferedBack->countryID !!}</td>
            <td>{!! $supplierMasterRefferedBack->supplierCountryID !!}</td>
            <td>{!! $supplierMasterRefferedBack->telephone !!}</td>
            <td>{!! $supplierMasterRefferedBack->fax !!}</td>
            <td>{!! $supplierMasterRefferedBack->supEmail !!}</td>
            <td>{!! $supplierMasterRefferedBack->webAddress !!}</td>
            <td>{!! $supplierMasterRefferedBack->currency !!}</td>
            <td>{!! $supplierMasterRefferedBack->nameOnPaymentCheque !!}</td>
            <td>{!! $supplierMasterRefferedBack->creditLimit !!}</td>
            <td>{!! $supplierMasterRefferedBack->creditPeriod !!}</td>
            <td>{!! $supplierMasterRefferedBack->supCategoryMasterID !!}</td>
            <td>{!! $supplierMasterRefferedBack->supCategorySubID !!}</td>
            <td>{!! $supplierMasterRefferedBack->registrationNumber !!}</td>
            <td>{!! $supplierMasterRefferedBack->registrationExprity !!}</td>
            <td>{!! $supplierMasterRefferedBack->approvedYN !!}</td>
            <td>{!! $supplierMasterRefferedBack->approvedEmpSystemID !!}</td>
            <td>{!! $supplierMasterRefferedBack->approvedby !!}</td>
            <td>{!! $supplierMasterRefferedBack->approvedDate !!}</td>
            <td>{!! $supplierMasterRefferedBack->approvedComment !!}</td>
            <td>{!! $supplierMasterRefferedBack->isActive !!}</td>
            <td>{!! $supplierMasterRefferedBack->isSupplierForiegn !!}</td>
            <td>{!! $supplierMasterRefferedBack->supplierConfirmedYN !!}</td>
            <td>{!! $supplierMasterRefferedBack->supplierConfirmedEmpID !!}</td>
            <td>{!! $supplierMasterRefferedBack->supplierConfirmedEmpSystemID !!}</td>
            <td>{!! $supplierMasterRefferedBack->supplierConfirmedEmpName !!}</td>
            <td>{!! $supplierMasterRefferedBack->supplierConfirmedDate !!}</td>
            <td>{!! $supplierMasterRefferedBack->isCriticalYN !!}</td>
            <td>{!! $supplierMasterRefferedBack->companyLinkedToSystemID !!}</td>
            <td>{!! $supplierMasterRefferedBack->companyLinkedTo !!}</td>
            <td>{!! $supplierMasterRefferedBack->createdUserGroup !!}</td>
            <td>{!! $supplierMasterRefferedBack->createdPcID !!}</td>
            <td>{!! $supplierMasterRefferedBack->createdUserID !!}</td>
            <td>{!! $supplierMasterRefferedBack->modifiedPc !!}</td>
            <td>{!! $supplierMasterRefferedBack->modifiedUser !!}</td>
            <td>{!! $supplierMasterRefferedBack->createdDateTime !!}</td>
            <td>{!! $supplierMasterRefferedBack->isDirect !!}</td>
            <td>{!! $supplierMasterRefferedBack->supplierImportanceID !!}</td>
            <td>{!! $supplierMasterRefferedBack->supplierNatureID !!}</td>
            <td>{!! $supplierMasterRefferedBack->supplierTypeID !!}</td>
            <td>{!! $supplierMasterRefferedBack->WHTApplicable !!}</td>
            <td>{!! $supplierMasterRefferedBack->vatEligible !!}</td>
            <td>{!! $supplierMasterRefferedBack->vatNumber !!}</td>
            <td>{!! $supplierMasterRefferedBack->vatPercentage !!}</td>
            <td>{!! $supplierMasterRefferedBack->supCategoryICVMasterID !!}</td>
            <td>{!! $supplierMasterRefferedBack->supCategorySubICVID !!}</td>
            <td>{!! $supplierMasterRefferedBack->isLCCYN !!}</td>
            <td>{!! $supplierMasterRefferedBack->RollLevForApp_curr !!}</td>
            <td>{!! $supplierMasterRefferedBack->refferedBackYN !!}</td>
            <td>{!! $supplierMasterRefferedBack->timesReferred !!}</td>
            <td>{!! $supplierMasterRefferedBack->timestamp !!}</td>
            <td>{!! $supplierMasterRefferedBack->createdUserSystemID !!}</td>
            <td>{!! $supplierMasterRefferedBack->modifiedUserSystemID !!}</td>
            <td>
                {!! Form::open(['route' => ['supplierMasterRefferedBacks.destroy', $supplierMasterRefferedBack->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('supplierMasterRefferedBacks.show', [$supplierMasterRefferedBack->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('supplierMasterRefferedBacks.edit', [$supplierMasterRefferedBack->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>