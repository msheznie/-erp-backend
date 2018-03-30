<table class="table table-responsive" id="yesNoSelectionForMinuses-table">
    <thead>
        <tr>
            <th>Selection</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($yesNoSelectionForMinuses as $yesNoSelectionForMinus)
        <tr>
            <td>{!! $yesNoSelectionForMinus->selection !!}</td>
            <td>
                {!! Form::open(['route' => ['yesNoSelectionForMinuses.destroy', $yesNoSelectionForMinus->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('yesNoSelectionForMinuses.show', [$yesNoSelectionForMinus->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('yesNoSelectionForMinuses.edit', [$yesNoSelectionForMinus->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>