<div class="table-responsive">
    <table class="table" id="poCategories-table">
        <thead>
            <tr>
                <th>Description</th>
        <th>Isactive</th>
        <th>Isdefault</th>
        <th>Createddatetime</th>
                <th colspan="3">Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($poCategories as $poCategory)
            <tr>
                <td>{{ $poCategory->description }}</td>
            <td>{{ $poCategory->isActive }}</td>
            <td>{{ $poCategory->isDefault }}</td>
            <td>{{ $poCategory->createdDateTime }}</td>
                <td>
                    {!! Form::open(['route' => ['poCategories.destroy', $poCategory->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{{ route('poCategories.show', [$poCategory->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                        <a href="{{ route('poCategories.edit', [$poCategory->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                        {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
