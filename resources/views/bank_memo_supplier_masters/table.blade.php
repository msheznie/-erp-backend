<table class="table table-responsive" id="bankMemoSupplierMasters-table">
    <thead>
        <tr>
            <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Memoheader</th>
        <th>Memodetail</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($bankMemoSupplierMasters as $bankMemoSupplierMaster)
        <tr>
            <td>{!! $bankMemoSupplierMaster->companySystemID !!}</td>
            <td>{!! $bankMemoSupplierMaster->companyID !!}</td>
            <td>{!! $bankMemoSupplierMaster->memoHeader !!}</td>
            <td>{!! $bankMemoSupplierMaster->memoDetail !!}</td>
            <td>{!! $bankMemoSupplierMaster->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['bankMemoSupplierMasters.destroy', $bankMemoSupplierMaster->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('bankMemoSupplierMasters.show', [$bankMemoSupplierMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('bankMemoSupplierMasters.edit', [$bankMemoSupplierMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>