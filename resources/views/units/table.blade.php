<table class="table table-responsive" id="units-table">
    <thead>
        <tr>
            <th>Unitshortcode</th>
        <th>Unitdes</th>
        <th>Createdusergroup</th>
        <th>Createdpcid</th>
        <th>Createduserid</th>
        <th>Modifiedpc</th>
        <th>Modifieduser</th>
        <th>Createddatetime</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($units as $unit)
        <tr>
            <td>{!! $unit->UnitShortCode !!}</td>
            <td>{!! $unit->UnitDes !!}</td>
            <td>{!! $unit->createdUserGroup !!}</td>
            <td>{!! $unit->createdPcID !!}</td>
            <td>{!! $unit->createdUserID !!}</td>
            <td>{!! $unit->modifiedPc !!}</td>
            <td>{!! $unit->modifiedUser !!}</td>
            <td>{!! $unit->createdDateTime !!}</td>
            <td>{!! $unit->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['units.destroy', $unit->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('units.show', [$unit->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('units.edit', [$unit->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>