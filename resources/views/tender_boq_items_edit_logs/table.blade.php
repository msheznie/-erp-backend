<div class="table-responsive">
    <table class="table" id="tenderBoqItemsEditLogs-table">
        <thead>
            <tr>
                <th>Company Id</th>
        <th>Description</th>
        <th>Item Name</th>
        <th>Main Work Id</th>
        <th>Master Id</th>
        <th>Modify Type</th>
        <th>Qty</th>
        <th>Tender Edit Version Id</th>
        <th>Tender Id</th>
        <th>Tender Ranking Line Item</th>
        <th>Uom</th>
                <th colspan="3">Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($tenderBoqItemsEditLogs as $tenderBoqItemsEditLog)
            <tr>
                <td>{{ $tenderBoqItemsEditLog->company_id }}</td>
            <td>{{ $tenderBoqItemsEditLog->description }}</td>
            <td>{{ $tenderBoqItemsEditLog->item_name }}</td>
            <td>{{ $tenderBoqItemsEditLog->main_work_id }}</td>
            <td>{{ $tenderBoqItemsEditLog->master_id }}</td>
            <td>{{ $tenderBoqItemsEditLog->modify_type }}</td>
            <td>{{ $tenderBoqItemsEditLog->qty }}</td>
            <td>{{ $tenderBoqItemsEditLog->tender_edit_version_id }}</td>
            <td>{{ $tenderBoqItemsEditLog->tender_id }}</td>
            <td>{{ $tenderBoqItemsEditLog->tender_ranking_line_item }}</td>
            <td>{{ $tenderBoqItemsEditLog->uom }}</td>
                <td>
                    {!! Form::open(['route' => ['tenderBoqItemsEditLogs.destroy', $tenderBoqItemsEditLog->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{{ route('tenderBoqItemsEditLogs.show', [$tenderBoqItemsEditLog->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                        <a href="{{ route('tenderBoqItemsEditLogs.edit', [$tenderBoqItemsEditLog->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                        {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
