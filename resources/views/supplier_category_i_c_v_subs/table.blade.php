<table class="table table-responsive" id="supplierCategoryICVSubs-table">
    <thead>
        <tr>
            <th>Supcategoryicvmasterid</th>
        <th>Subcategorycode</th>
        <th>Categorydescription</th>
        <th>Timestamp</th>
        <th>Createddatetime</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($supplierCategoryICVSubs as $supplierCategoryICVSub)
        <tr>
            <td>{!! $supplierCategoryICVSub->supCategoryICVMasterID !!}</td>
            <td>{!! $supplierCategoryICVSub->subCategoryCode !!}</td>
            <td>{!! $supplierCategoryICVSub->categoryDescription !!}</td>
            <td>{!! $supplierCategoryICVSub->timeStamp !!}</td>
            <td>{!! $supplierCategoryICVSub->createdDateTime !!}</td>
            <td>
                {!! Form::open(['route' => ['supplierCategoryICVSubs.destroy', $supplierCategoryICVSub->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('supplierCategoryICVSubs.show', [$supplierCategoryICVSub->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('supplierCategoryICVSubs.edit', [$supplierCategoryICVSub->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>