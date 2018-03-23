<table class="table table-responsive" id="bankMemoSuppliers-table">
    <thead>
        <tr>
            <th>Memoheader</th>
        <th>Memodetail</th>
        <th>Suppliercodesystem</th>
        <th>Suppliercurrencyid</th>
        <th>Updatedbyuserid</th>
        <th>Updatedbyusername</th>
        <th>Updateddate</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($bankMemoSuppliers as $bankMemoSupplier)
        <tr>
            <td>{!! $bankMemoSupplier->memoHeader !!}</td>
            <td>{!! $bankMemoSupplier->memoDetail !!}</td>
            <td>{!! $bankMemoSupplier->supplierCodeSystem !!}</td>
            <td>{!! $bankMemoSupplier->supplierCurrencyID !!}</td>
            <td>{!! $bankMemoSupplier->updatedByUserID !!}</td>
            <td>{!! $bankMemoSupplier->updatedByUserName !!}</td>
            <td>{!! $bankMemoSupplier->updatedDate !!}</td>
            <td>{!! $bankMemoSupplier->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['bankMemoSuppliers.destroy', $bankMemoSupplier->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('bankMemoSuppliers.show', [$bankMemoSupplier->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('bankMemoSuppliers.edit', [$bankMemoSupplier->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>