<table class="table table-responsive" id="warehouseBinLocations-table">
    <thead>
        <tr>
            <th>Binlocationdes</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Warehousesystemcode</th>
        <th>Createdby</th>
        <th>Datecreated</th>
        <th>Isactive</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($warehouseBinLocations as $warehouseBinLocation)
        <tr>
            <td>{!! $warehouseBinLocation->binLocationDes !!}</td>
            <td>{!! $warehouseBinLocation->companySystemID !!}</td>
            <td>{!! $warehouseBinLocation->companyID !!}</td>
            <td>{!! $warehouseBinLocation->wareHouseSystemCode !!}</td>
            <td>{!! $warehouseBinLocation->createdBy !!}</td>
            <td>{!! $warehouseBinLocation->dateCreated !!}</td>
            <td>{!! $warehouseBinLocation->isActive !!}</td>
            <td>{!! $warehouseBinLocation->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['warehouseBinLocations.destroy', $warehouseBinLocation->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('warehouseBinLocations.show', [$warehouseBinLocation->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('warehouseBinLocations.edit', [$warehouseBinLocation->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>