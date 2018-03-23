<table class="table table-responsive" id="supplierCategoryMasters-table">
    <thead>
        <tr>
            <th>Categorycode</th>
        <th>Categorydescription</th>
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
    @foreach($supplierCategoryMasters as $supplierCategoryMaster)
        <tr>
            <td>{!! $supplierCategoryMaster->categoryCode !!}</td>
            <td>{!! $supplierCategoryMaster->categoryDescription !!}</td>
            <td>{!! $supplierCategoryMaster->createdUserGroup !!}</td>
            <td>{!! $supplierCategoryMaster->createdPcID !!}</td>
            <td>{!! $supplierCategoryMaster->createdUserID !!}</td>
            <td>{!! $supplierCategoryMaster->modifiedPc !!}</td>
            <td>{!! $supplierCategoryMaster->modifiedUser !!}</td>
            <td>{!! $supplierCategoryMaster->createdDateTime !!}</td>
            <td>{!! $supplierCategoryMaster->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['supplierCategoryMasters.destroy', $supplierCategoryMaster->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('supplierCategoryMasters.show', [$supplierCategoryMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('supplierCategoryMasters.edit', [$supplierCategoryMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>