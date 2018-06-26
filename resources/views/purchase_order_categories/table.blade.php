<table class="table table-responsive" id="purchaseOrderCategories-table">
    <thead>
        <tr>
            <th>Description</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($purchaseOrderCategories as $purchaseOrderCategory)
        <tr>
            <td>{!! $purchaseOrderCategory->description !!}</td>
            <td>
                {!! Form::open(['route' => ['purchaseOrderCategories.destroy', $purchaseOrderCategory->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('purchaseOrderCategories.show', [$purchaseOrderCategory->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('purchaseOrderCategories.edit', [$purchaseOrderCategory->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>