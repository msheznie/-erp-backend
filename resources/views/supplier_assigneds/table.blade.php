<table class="table table-responsive" id="supplierAssigneds-table">
    <thead>
        <tr>
            <th>Suppliercodesytem</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Uniquetextcode</th>
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
        <th>Supplierimportanceid</th>
        <th>Suppliernatureid</th>
        <th>Suppliertypeid</th>
        <th>Whtapplicable</th>
        <th>Isrelatedpartyyn</th>
        <th>Iscriticalyn</th>
        <th>Isactive</th>
        <th>Isassigned</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($supplierAssigneds as $supplierAssigned)
        <tr>
            <td>{!! $supplierAssigned->supplierCodeSytem !!}</td>
            <td>{!! $supplierAssigned->companySystemID !!}</td>
            <td>{!! $supplierAssigned->companyID !!}</td>
            <td>{!! $supplierAssigned->uniqueTextcode !!}</td>
            <td>{!! $supplierAssigned->primarySupplierCode !!}</td>
            <td>{!! $supplierAssigned->secondarySupplierCode !!}</td>
            <td>{!! $supplierAssigned->supplierName !!}</td>
            <td>{!! $supplierAssigned->liabilityAccountSysemID !!}</td>
            <td>{!! $supplierAssigned->liabilityAccount !!}</td>
            <td>{!! $supplierAssigned->UnbilledGRVAccountSystemID !!}</td>
            <td>{!! $supplierAssigned->UnbilledGRVAccount !!}</td>
            <td>{!! $supplierAssigned->address !!}</td>
            <td>{!! $supplierAssigned->countryID !!}</td>
            <td>{!! $supplierAssigned->supplierCountryID !!}</td>
            <td>{!! $supplierAssigned->telephone !!}</td>
            <td>{!! $supplierAssigned->fax !!}</td>
            <td>{!! $supplierAssigned->supEmail !!}</td>
            <td>{!! $supplierAssigned->webAddress !!}</td>
            <td>{!! $supplierAssigned->currency !!}</td>
            <td>{!! $supplierAssigned->nameOnPaymentCheque !!}</td>
            <td>{!! $supplierAssigned->creditLimit !!}</td>
            <td>{!! $supplierAssigned->creditPeriod !!}</td>
            <td>{!! $supplierAssigned->supCategoryMasterID !!}</td>
            <td>{!! $supplierAssigned->supCategorySubID !!}</td>
            <td>{!! $supplierAssigned->registrationNumber !!}</td>
            <td>{!! $supplierAssigned->registrationExprity !!}</td>
            <td>{!! $supplierAssigned->supplierImportanceID !!}</td>
            <td>{!! $supplierAssigned->supplierNatureID !!}</td>
            <td>{!! $supplierAssigned->supplierTypeID !!}</td>
            <td>{!! $supplierAssigned->WHTApplicable !!}</td>
            <td>{!! $supplierAssigned->isRelatedPartyYN !!}</td>
            <td>{!! $supplierAssigned->isCriticalYN !!}</td>
            <td>{!! $supplierAssigned->isActive !!}</td>
            <td>{!! $supplierAssigned->isAssigned !!}</td>
            <td>{!! $supplierAssigned->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['supplierAssigneds.destroy', $supplierAssigned->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('supplierAssigneds.show', [$supplierAssigned->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('supplierAssigneds.edit', [$supplierAssigned->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>