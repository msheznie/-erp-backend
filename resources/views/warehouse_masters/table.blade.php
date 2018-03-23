<table class="table table-responsive" id="warehouseMasters-table">
    <thead>
        <tr>
            <th>Warehousecode</th>
        <th>Warehousedescription</th>
        <th>Warehouselocation</th>
        <th>Isactive</th>
        <th>Companyid</th>
        <th>Companysystemid</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($warehouseMasters as $warehouseMaster)
        <tr>
            <td>{!! $warehouseMaster->wareHouseCode !!}</td>
            <td>{!! $warehouseMaster->wareHouseDescription !!}</td>
            <td>{!! $warehouseMaster->wareHouseLocation !!}</td>
            <td>{!! $warehouseMaster->isActive !!}</td>
            <td>{!! $warehouseMaster->companyID !!}</td>
            <td>{!! $warehouseMaster->companySystemID !!}</td>
            <td>{!! $warehouseMaster->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['warehouseMasters.destroy', $warehouseMaster->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('warehouseMasters.show', [$warehouseMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('warehouseMasters.edit', [$warehouseMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>