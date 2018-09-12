<table class="table table-responsive" id="logisticModeOfImports-table">
    <thead>
        <tr>
            <th>Modeimportdescription</th>
        <th>Createduserid</th>
        <th>Createdpcid</th>
        <th>Createddatetime</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($logisticModeOfImports as $logisticModeOfImport)
        <tr>
            <td>{!! $logisticModeOfImport->modeImportDescription !!}</td>
            <td>{!! $logisticModeOfImport->createdUserID !!}</td>
            <td>{!! $logisticModeOfImport->createdPCID !!}</td>
            <td>{!! $logisticModeOfImport->createdDateTime !!}</td>
            <td>{!! $logisticModeOfImport->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['logisticModeOfImports.destroy', $logisticModeOfImport->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('logisticModeOfImports.show', [$logisticModeOfImport->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('logisticModeOfImports.edit', [$logisticModeOfImport->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>