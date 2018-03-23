<table class="table table-responsive" id="unitConversions-table">
    <thead>
        <tr>
            <th>Masterunitid</th>
        <th>Subunitid</th>
        <th>Conversion</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($unitConversions as $unitConversion)
        <tr>
            <td>{!! $unitConversion->masterUnitID !!}</td>
            <td>{!! $unitConversion->subUnitID !!}</td>
            <td>{!! $unitConversion->conversion !!}</td>
            <td>{!! $unitConversion->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['unitConversions.destroy', $unitConversion->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('unitConversions.show', [$unitConversion->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('unitConversions.edit', [$unitConversion->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>