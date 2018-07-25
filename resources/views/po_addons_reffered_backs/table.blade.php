<table class="table table-responsive" id="poAddonsRefferedBacks-table">
    <thead>
        <tr>
            <th>Idpoaddons</th>
        <th>Poid</th>
        <th>Idaddoncostcategories</th>
        <th>Supplierid</th>
        <th>Currencyid</th>
        <th>Amount</th>
        <th>Glcode</th>
        <th>Timesreferred</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($poAddonsRefferedBacks as $poAddonsRefferedBack)
        <tr>
            <td>{!! $poAddonsRefferedBack->idpoAddons !!}</td>
            <td>{!! $poAddonsRefferedBack->poId !!}</td>
            <td>{!! $poAddonsRefferedBack->idaddOnCostCategories !!}</td>
            <td>{!! $poAddonsRefferedBack->supplierID !!}</td>
            <td>{!! $poAddonsRefferedBack->currencyID !!}</td>
            <td>{!! $poAddonsRefferedBack->amount !!}</td>
            <td>{!! $poAddonsRefferedBack->glCode !!}</td>
            <td>{!! $poAddonsRefferedBack->timesReferred !!}</td>
            <td>{!! $poAddonsRefferedBack->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['poAddonsRefferedBacks.destroy', $poAddonsRefferedBack->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('poAddonsRefferedBacks.show', [$poAddonsRefferedBack->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('poAddonsRefferedBacks.edit', [$poAddonsRefferedBack->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>