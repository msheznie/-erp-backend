<div class="table-responsive">
    <table class="table" id="commercialBidRankingItems-table">
        <thead>
            <tr>
                <th>Bid Format Detail Id</th>
        <th>Bid Id</th>
        <th>Status</th>
        <th>Tender Id</th>
        <th>Value</th>
                <th colspan="3">Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($commercialBidRankingItems as $commercialBidRankingItems)
            <tr>
                <td>{{ $commercialBidRankingItems->bid_format_detail_id }}</td>
            <td>{{ $commercialBidRankingItems->bid_id }}</td>
            <td>{{ $commercialBidRankingItems->status }}</td>
            <td>{{ $commercialBidRankingItems->tender_id }}</td>
            <td>{{ $commercialBidRankingItems->value }}</td>
                <td>
                    {!! Form::open(['route' => ['commercialBidRankingItems.destroy', $commercialBidRankingItems->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{{ route('commercialBidRankingItems.show', [$commercialBidRankingItems->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                        <a href="{{ route('commercialBidRankingItems.edit', [$commercialBidRankingItems->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                        {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
