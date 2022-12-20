<div class="table-responsive">
    <table class="table" id="tenderFinalBids-table">
        <thead>
            <tr>
                <th>Award</th>
        <th>Bid Id</th>
        <th>Com Weightage</th>
        <th>Status</th>
        <th>Supplier Id</th>
        <th>Tech Weightage</th>
        <th>Tender Id</th>
        <th>Total Weightage</th>
                <th colspan="3">Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($tenderFinalBids as $tenderFinalBids)
            <tr>
                <td>{{ $tenderFinalBids->award }}</td>
            <td>{{ $tenderFinalBids->bid_id }}</td>
            <td>{{ $tenderFinalBids->com_weightage }}</td>
            <td>{{ $tenderFinalBids->status }}</td>
            <td>{{ $tenderFinalBids->supplier_id }}</td>
            <td>{{ $tenderFinalBids->tech_weightage }}</td>
            <td>{{ $tenderFinalBids->tender_id }}</td>
            <td>{{ $tenderFinalBids->total_weightage }}</td>
                <td>
                    {!! Form::open(['route' => ['tenderFinalBids.destroy', $tenderFinalBids->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{{ route('tenderFinalBids.show', [$tenderFinalBids->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                        <a href="{{ route('tenderFinalBids.edit', [$tenderFinalBids->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                        {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
