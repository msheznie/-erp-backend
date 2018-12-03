<table class="table table-responsive" id="supplierCategoryICVMasters-table">
    <thead>
        <tr>
            <th>Categorycode</th>
        <th>Categorydescription</th>
        <th>Createddatetime</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($supplierCategoryICVMasters as $supplierCategoryICVMaster)
        <tr>
            <td>{!! $supplierCategoryICVMaster->categoryCode !!}</td>
            <td>{!! $supplierCategoryICVMaster->categoryDescription !!}</td>
            <td>{!! $supplierCategoryICVMaster->createdDateTime !!}</td>
            <td>{!! $supplierCategoryICVMaster->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['supplierCategoryICVMasters.destroy', $supplierCategoryICVMaster->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('supplierCategoryICVMasters.show', [$supplierCategoryICVMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('supplierCategoryICVMasters.edit', [$supplierCategoryICVMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>