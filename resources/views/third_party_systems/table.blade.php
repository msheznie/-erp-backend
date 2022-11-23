<div class="table-responsive">
    <table class="table" id="thirdPartySystems-table">
        <thead>
            <tr>
                <th>Description</th>
        <th>Status</th>
                <th colspan="3">Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($thirdPartySystems as $thirdPartySystems)
            <tr>
                <td>{{ $thirdPartySystems->description }}</td>
            <td>{{ $thirdPartySystems->status }}</td>
                <td>
                    {!! Form::open(['route' => ['thirdPartySystems.destroy', $thirdPartySystems->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{{ route('thirdPartySystems.show', [$thirdPartySystems->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                        <a href="{{ route('thirdPartySystems.edit', [$thirdPartySystems->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                        {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
