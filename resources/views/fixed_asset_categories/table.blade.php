<table class="table table-responsive" id="fixedAssetCategories-table">
    <thead>
        <tr>
            <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Catdescription</th>
        <th>Isactive</th>
        <th>Createdpcid</th>
        <th>Createdusergroup</th>
        <th>Createdusersystemid</th>
        <th>Createduserid</th>
        <th>Createddatetime</th>
        <th>Modifiedpc</th>
        <th>Modifiedusersystemid</th>
        <th>Modifieduser</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($fixedAssetCategories as $fixedAssetCategory)
        <tr>
            <td>{!! $fixedAssetCategory->companySystemID !!}</td>
            <td>{!! $fixedAssetCategory->companyID !!}</td>
            <td>{!! $fixedAssetCategory->catDescription !!}</td>
            <td>{!! $fixedAssetCategory->isActive !!}</td>
            <td>{!! $fixedAssetCategory->createdPcID !!}</td>
            <td>{!! $fixedAssetCategory->createdUserGroup !!}</td>
            <td>{!! $fixedAssetCategory->createdUserSystemID !!}</td>
            <td>{!! $fixedAssetCategory->createdUserID !!}</td>
            <td>{!! $fixedAssetCategory->createdDateTime !!}</td>
            <td>{!! $fixedAssetCategory->modifiedPc !!}</td>
            <td>{!! $fixedAssetCategory->modifiedUserSystemID !!}</td>
            <td>{!! $fixedAssetCategory->modifiedUser !!}</td>
            <td>{!! $fixedAssetCategory->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['fixedAssetCategories.destroy', $fixedAssetCategory->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('fixedAssetCategories.show', [$fixedAssetCategory->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('fixedAssetCategories.edit', [$fixedAssetCategory->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>