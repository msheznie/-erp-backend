<div class="table-responsive">
    <table class="table" id="chequeTemplateMasters-table">
        <thead>
            <tr>
                <th>Description</th>
        <th>View Name</th>
        <th>Is Active</th>
                <th colspan="3">Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($chequeTemplateMasters as $chequeTemplateMaster)
            <tr>
                <td>{{ $chequeTemplateMaster->description }}</td>
            <td>{{ $chequeTemplateMaster->view_name }}</td>
            <td>{{ $chequeTemplateMaster->is_active }}</td>
                <td>
                    {!! Form::open(['route' => ['chequeTemplateMasters.destroy', $chequeTemplateMaster->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{{ route('chequeTemplateMasters.show', [$chequeTemplateMaster->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                        <a href="{{ route('chequeTemplateMasters.edit', [$chequeTemplateMaster->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                        {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
