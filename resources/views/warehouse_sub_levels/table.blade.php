<div class="table-responsive">
    <table class="table" id="warehouseSubLevels-table">
        <thead>
            <tr>
                <th>Company Id</th>
        <th>Warehouse Id</th>
        <th>Level</th>
        <th>Parent Id</th>
        <th>Name</th>
        <th>Description</th>
        <th>Isfinallevel</th>
        <th>Created By</th>
        <th>Created Pc</th>
        <th>Updated By</th>
        <th>Updated Pc</th>
                <th colspan="3">Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($warehouseSubLevels as $warehouseSubLevels)
            <tr>
                <td>{{ $warehouseSubLevels->company_id }}</td>
            <td>{{ $warehouseSubLevels->warehouse_id }}</td>
            <td>{{ $warehouseSubLevels->level }}</td>
            <td>{{ $warehouseSubLevels->parent_id }}</td>
            <td>{{ $warehouseSubLevels->name }}</td>
            <td>{{ $warehouseSubLevels->description }}</td>
            <td>{{ $warehouseSubLevels->isFinalLevel }}</td>
            <td>{{ $warehouseSubLevels->created_by }}</td>
            <td>{{ $warehouseSubLevels->created_pc }}</td>
            <td>{{ $warehouseSubLevels->updated_by }}</td>
            <td>{{ $warehouseSubLevels->updated_pc }}</td>
                <td>
                    {!! Form::open(['route' => ['warehouseSubLevels.destroy', $warehouseSubLevels->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{{ route('warehouseSubLevels.show', [$warehouseSubLevels->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                        <a href="{{ route('warehouseSubLevels.edit', [$warehouseSubLevels->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                        {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
