<table class="table table-responsive" id="customerAssigneds-table">
    <thead>
        <tr>
            <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Customercodesystem</th>
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
        <th>Isrelatedpartyyn</th>
        <th>Isactive</th>
        <th>Isassigned</th>
        <th>Vateligible</th>
        <th>Vatnumber</th>
        <th>Vatpercentage</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($customerAssigneds as $customerAssigned)
        <tr>
            <td>{!! $customerAssigned->companySystemID !!}</td>
            <td>{!! $customerAssigned->companyID !!}</td>
            <td>{!! $customerAssigned->customerCodeSystem !!}</td>
            <td>{!! $customerAssigned->CutomerCode !!}</td>
            <td>{!! $customerAssigned->customerShortCode !!}</td>
            <td>{!! $customerAssigned->custGLAccountSystemID !!}</td>
            <td>{!! $customerAssigned->custGLaccount !!}</td>
            <td>{!! $customerAssigned->CustomerName !!}</td>
            <td>{!! $customerAssigned->ReportTitle !!}</td>
            <td>{!! $customerAssigned->customerAddress1 !!}</td>
            <td>{!! $customerAssigned->customerAddress2 !!}</td>
            <td>{!! $customerAssigned->customerCity !!}</td>
            <td>{!! $customerAssigned->customerCountry !!}</td>
            <td>{!! $customerAssigned->CustWebsite !!}</td>
            <td>{!! $customerAssigned->creditLimit !!}</td>
            <td>{!! $customerAssigned->creditDays !!}</td>
            <td>{!! $customerAssigned->isRelatedPartyYN !!}</td>
            <td>{!! $customerAssigned->isActive !!}</td>
            <td>{!! $customerAssigned->isAssigned !!}</td>
            <td>{!! $customerAssigned->vatEligible !!}</td>
            <td>{!! $customerAssigned->vatNumber !!}</td>
            <td>{!! $customerAssigned->vatPercentage !!}</td>
            <td>{!! $customerAssigned->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['customerAssigneds.destroy', $customerAssigned->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('customerAssigneds.show', [$customerAssigned->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('customerAssigneds.edit', [$customerAssigned->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>