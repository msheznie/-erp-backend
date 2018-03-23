<table class="table table-responsive" id="yesNoSelections-table">
    <thead>
        <tr>
            <th>Yesno</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($yesNoSelections as $yesNoSelection)
        <tr>
            <td>{!! $yesNoSelection->YesNo !!}</td>
            <td>
                {!! Form::open(['route' => ['yesNoSelections.destroy', $yesNoSelection->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('yesNoSelections.show', [$yesNoSelection->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('yesNoSelections.edit', [$yesNoSelection->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>