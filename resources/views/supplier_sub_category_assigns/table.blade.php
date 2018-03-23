<table class="table table-responsive" id="supplierSubCategoryAssigns-table">
    <thead>
        <tr>
            <th>Supplierid</th>
        <th>Supsubcategoryid</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($supplierSubCategoryAssigns as $supplierSubCategoryAssign)
        <tr>
            <td>{!! $supplierSubCategoryAssign->supplierID !!}</td>
            <td>{!! $supplierSubCategoryAssign->supSubCategoryID !!}</td>
            <td>{!! $supplierSubCategoryAssign->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['supplierSubCategoryAssigns.destroy', $supplierSubCategoryAssign->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('supplierSubCategoryAssigns.show', [$supplierSubCategoryAssign->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('supplierSubCategoryAssigns.edit', [$supplierSubCategoryAssign->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>