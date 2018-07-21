<table class="table table-responsive" id="poAddons-table">
    <thead>
        <tr>
            <th>Poid</th>
        <th>Idaddoncostcategories</th>
        <th>Supplierid</th>
        <th>Currencyid</th>
        <th>Amount</th>
        <th>Glcode</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($poAddons as $poAddons)
        <tr>
            <td>{!! $poAddons->poId !!}</td>
            <td>{!! $poAddons->idaddOnCostCategories !!}</td>
            <td>{!! $poAddons->supplierID !!}</td>
            <td>{!! $poAddons->currencyID !!}</td>
            <td>{!! $poAddons->amount !!}</td>
            <td>{!! $poAddons->glCode !!}</td>
            <td>{!! $poAddons->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['poAddons.destroy', $poAddons->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('poAddons.show', [$poAddons->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('poAddons.edit', [$poAddons->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>