<table class="table table-responsive" id="customerMasterCategories-table">
    <thead>
        <tr>
            <th>Categorydescription</th>
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
    @foreach($customerMasterCategories as $customerMasterCategory)
        <tr>
            <td>{!! $customerMasterCategory->categoryDescription !!}</td>
            <td>{!! $customerMasterCategory->companySystemID !!}</td>
            <td>{!! $customerMasterCategory->companyID !!}</td>
            <td>{!! $customerMasterCategory->createdUserGroup !!}</td>
            <td>{!! $customerMasterCategory->createdPCID !!}</td>
            <td>{!! $customerMasterCategory->createdUserID !!}</td>
            <td>{!! $customerMasterCategory->createdDateTime !!}</td>
            <td>{!! $customerMasterCategory->createdUserName !!}</td>
            <td>{!! $customerMasterCategory->modifiedPCID !!}</td>
            <td>{!! $customerMasterCategory->modifiedUserID !!}</td>
            <td>{!! $customerMasterCategory->modifiedDateTime !!}</td>
            <td>{!! $customerMasterCategory->modifiedUserName !!}</td>
            <td>{!! $customerMasterCategory->TIMESTAMP !!}</td>
            <td>
                {!! Form::open(['route' => ['customerMasterCategories.destroy', $customerMasterCategory->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('customerMasterCategories.show', [$customerMasterCategory->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('customerMasterCategories.edit', [$customerMasterCategory->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>