<table class="table table-responsive" id="countryMasters-table">
    <thead>
        <tr>
            <th>Countrycode</th>
        <th>Countryname</th>
        <th>Countryname O</th>
        <th>Nationality</th>
        <th>Islocal</th>
        <th>Countryflag</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($countryMasters as $countryMaster)
        <tr>
            <td>{!! $countryMaster->countryCode !!}</td>
            <td>{!! $countryMaster->countryName !!}</td>
            <td>{!! $countryMaster->countryName_O !!}</td>
            <td>{!! $countryMaster->nationality !!}</td>
            <td>{!! $countryMaster->isLocal !!}</td>
            <td>{!! $countryMaster->countryFlag !!}</td>
            <td>
                {!! Form::open(['route' => ['countryMasters.destroy', $countryMaster->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('countryMasters.show', [$countryMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('countryMasters.edit', [$countryMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>