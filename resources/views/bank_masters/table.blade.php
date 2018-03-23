<table class="table table-responsive" id="bankMasters-table">
    <thead>
        <tr>
            <th>Bankshortcode</th>
        <th>Bankname</th>
        <th>Createddatetime</th>
        <th>Createdbyempid</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($bankMasters as $bankMaster)
        <tr>
            <td>{!! $bankMaster->bankShortCode !!}</td>
            <td>{!! $bankMaster->bankName !!}</td>
            <td>{!! $bankMaster->createdDateTime !!}</td>
            <td>{!! $bankMaster->createdByEmpID !!}</td>
            <td>{!! $bankMaster->TimeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['bankMasters.destroy', $bankMaster->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('bankMasters.show', [$bankMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('bankMasters.edit', [$bankMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>