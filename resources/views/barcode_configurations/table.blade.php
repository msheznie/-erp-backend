<div class="table-responsive">
    <table class="table" id="barcodeConfigurations-table">
        <thead>
            <tr>
                <th>Barcode Font</th>
        <th>Height</th>
        <th>No Of Coulmns</th>
        <th>No Of Rows</th>
        <th>Page Size</th>
        <th>Width</th>
                <th colspan="3">Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($barcodeConfigurations as $barcodeConfiguration)
            <tr>
                <td>{{ $barcodeConfiguration->barcode_font }}</td>
            <td>{{ $barcodeConfiguration->height }}</td>
            <td>{{ $barcodeConfiguration->no_of_coulmns }}</td>
            <td>{{ $barcodeConfiguration->no_of_rows }}</td>
            <td>{{ $barcodeConfiguration->page_size }}</td>
            <td>{{ $barcodeConfiguration->width }}</td>
                <td>
                    {!! Form::open(['route' => ['barcodeConfigurations.destroy', $barcodeConfiguration->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{{ route('barcodeConfigurations.show', [$barcodeConfiguration->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                        <a href="{{ route('barcodeConfigurations.edit', [$barcodeConfiguration->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                        {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
