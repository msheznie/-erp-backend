<table class="table table-responsive" id="expenseClaimCategories-table">
    <thead>
        <tr>
            <th>Claimcategoriesdescription</th>
        <th>Glcode</th>
        <th>Glcodedescription</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($expenseClaimCategories as $expenseClaimCategories)
        <tr>
            <td>{!! $expenseClaimCategories->claimcategoriesDescription !!}</td>
            <td>{!! $expenseClaimCategories->glCode !!}</td>
            <td>{!! $expenseClaimCategories->glCodeDescription !!}</td>
            <td>{!! $expenseClaimCategories->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['expenseClaimCategories.destroy', $expenseClaimCategories->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('expenseClaimCategories.show', [$expenseClaimCategories->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('expenseClaimCategories.edit', [$expenseClaimCategories->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>