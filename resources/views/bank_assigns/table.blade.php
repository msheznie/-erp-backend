<table class="table table-responsive" id="bankAssigns-table">
    <thead>
        <tr>
            <th>Bankmasterautoid</th>
        <th>Companyid</th>
        <th>Bankshortcode</th>
        <th>Bankname</th>
        <th>Isassigned</th>
        <th>Isdefault</th>
        <th>Isactive</th>
        <th>Createddatetime</th>
        <th>Createdbyempid</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($bankAssigns as $bankAssign)
        <tr>
            <td>{!! $bankAssign->bankmasterAutoID !!}</td>
            <td>{!! $bankAssign->companyID !!}</td>
            <td>{!! $bankAssign->bankShortCode !!}</td>
            <td>{!! $bankAssign->bankName !!}</td>
            <td>{!! $bankAssign->isAssigned !!}</td>
            <td>{!! $bankAssign->isDefault !!}</td>
            <td>{!! $bankAssign->isActive !!}</td>
            <td>{!! $bankAssign->createdDateTime !!}</td>
            <td>{!! $bankAssign->createdByEmpID !!}</td>
            <td>{!! $bankAssign->TimeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['bankAssigns.destroy', $bankAssign->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('bankAssigns.show', [$bankAssign->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('bankAssigns.edit', [$bankAssign->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>