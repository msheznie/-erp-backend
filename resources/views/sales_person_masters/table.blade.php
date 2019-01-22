<table class="table table-responsive" id="salesPersonMasters-table">
    <thead>
        <tr>
            <th>Empsystemid</th>
        <th>Salespersoncode</th>
        <th>Salespersonname</th>
        <th>Salespersonimage</th>
        <th>Warehouseautoid</th>
        <th>Warehousecode</th>
        <th>Warehousedescription</th>
        <th>Warehouselocation</th>
        <th>Salespersonemail</th>
        <th>Secondarycode</th>
        <th>Contactnumber</th>
        <th>Salespersontargettype</th>
        <th>Salespersontarget</th>
        <th>Salespersonaddress</th>
        <th>Receivableautoid</th>
        <th>Receivablesystemglcode</th>
        <th>Receivableglaccount</th>
        <th>Receivabledescription</th>
        <th>Receivabletype</th>
        <th>Expenseautoid</th>
        <th>Expensesystemglcode</th>
        <th>Expenseglaccount</th>
        <th>Expensedescription</th>
        <th>Expensetype</th>
        <th>Salespersoncurrencyid</th>
        <th>Salespersoncurrency</th>
        <th>Salespersoncurrencydecimalplaces</th>
        <th>Segmentid</th>
        <th>Segmentcode</th>
        <th>Isactive</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Createdusergroup</th>
        <th>Createdpcid</th>
        <th>Createduserid</th>
        <th>Createddatetime</th>
        <th>Createdusername</th>
        <th>Modifiedpcid</th>
        <th>Modifieduserid</th>
        <th>Modifieddatetime</th>
        <th>Modifiedusername</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($salesPersonMasters as $salesPersonMaster)
        <tr>
            <td>{!! $salesPersonMaster->empSystemID !!}</td>
            <td>{!! $salesPersonMaster->SalesPersonCode !!}</td>
            <td>{!! $salesPersonMaster->SalesPersonName !!}</td>
            <td>{!! $salesPersonMaster->salesPersonImage !!}</td>
            <td>{!! $salesPersonMaster->wareHouseAutoID !!}</td>
            <td>{!! $salesPersonMaster->wareHouseCode !!}</td>
            <td>{!! $salesPersonMaster->wareHouseDescription !!}</td>
            <td>{!! $salesPersonMaster->wareHouseLocation !!}</td>
            <td>{!! $salesPersonMaster->SalesPersonEmail !!}</td>
            <td>{!! $salesPersonMaster->SecondaryCode !!}</td>
            <td>{!! $salesPersonMaster->contactNumber !!}</td>
            <td>{!! $salesPersonMaster->salesPersonTargetType !!}</td>
            <td>{!! $salesPersonMaster->salesPersonTarget !!}</td>
            <td>{!! $salesPersonMaster->SalesPersonAddress !!}</td>
            <td>{!! $salesPersonMaster->receivableAutoID !!}</td>
            <td>{!! $salesPersonMaster->receivableSystemGLCode !!}</td>
            <td>{!! $salesPersonMaster->receivableGLAccount !!}</td>
            <td>{!! $salesPersonMaster->receivableDescription !!}</td>
            <td>{!! $salesPersonMaster->receivableType !!}</td>
            <td>{!! $salesPersonMaster->expenseAutoID !!}</td>
            <td>{!! $salesPersonMaster->expenseSystemGLCode !!}</td>
            <td>{!! $salesPersonMaster->expenseGLAccount !!}</td>
            <td>{!! $salesPersonMaster->expenseDescription !!}</td>
            <td>{!! $salesPersonMaster->expenseType !!}</td>
            <td>{!! $salesPersonMaster->salesPersonCurrencyID !!}</td>
            <td>{!! $salesPersonMaster->salesPersonCurrency !!}</td>
            <td>{!! $salesPersonMaster->salesPersonCurrencyDecimalPlaces !!}</td>
            <td>{!! $salesPersonMaster->segmentID !!}</td>
            <td>{!! $salesPersonMaster->segmentCode !!}</td>
            <td>{!! $salesPersonMaster->isActive !!}</td>
            <td>{!! $salesPersonMaster->companySystemID !!}</td>
            <td>{!! $salesPersonMaster->companyID !!}</td>
            <td>{!! $salesPersonMaster->createdUserGroup !!}</td>
            <td>{!! $salesPersonMaster->createdPCID !!}</td>
            <td>{!! $salesPersonMaster->createdUserID !!}</td>
            <td>{!! $salesPersonMaster->createdDateTime !!}</td>
            <td>{!! $salesPersonMaster->createdUserName !!}</td>
            <td>{!! $salesPersonMaster->modifiedPCID !!}</td>
            <td>{!! $salesPersonMaster->modifiedUserID !!}</td>
            <td>{!! $salesPersonMaster->modifiedDateTime !!}</td>
            <td>{!! $salesPersonMaster->modifiedUserName !!}</td>
            <td>{!! $salesPersonMaster->TIMESTAMP !!}</td>
            <td>
                {!! Form::open(['route' => ['salesPersonMasters.destroy', $salesPersonMaster->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('salesPersonMasters.show', [$salesPersonMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('salesPersonMasters.edit', [$salesPersonMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>