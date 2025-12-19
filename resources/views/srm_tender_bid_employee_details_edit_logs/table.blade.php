<div class="table-responsive">
    <table class="table" id="srmTenderBidEmployeeDetailsEditLogs-table">
        <thead>
            <tr>
                <th>Commercial Eval Remarks</th>
        <th>Commercial Eval Status</th>
        <th>Emp Id</th>
        <th>Modify Type</th>
        <th>Remarks</th>
        <th>Status</th>
        <th>Tender Award Commite Mem Comment</th>
        <th>Tender Award Commite Mem Status</th>
        <th>Tender Edit Version Id</th>
        <th>Tender Id</th>
                <th colspan="3">Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($srmTenderBidEmployeeDetailsEditLogs as $srmTenderBidEmployeeDetailsEditLog)
            <tr>
                <td>{{ $srmTenderBidEmployeeDetailsEditLog->commercial_eval_remarks }}</td>
            <td>{{ $srmTenderBidEmployeeDetailsEditLog->commercial_eval_status }}</td>
            <td>{{ $srmTenderBidEmployeeDetailsEditLog->emp_id }}</td>
            <td>{{ $srmTenderBidEmployeeDetailsEditLog->modify_type }}</td>
            <td>{{ $srmTenderBidEmployeeDetailsEditLog->remarks }}</td>
            <td>{{ $srmTenderBidEmployeeDetailsEditLog->status }}</td>
            <td>{{ $srmTenderBidEmployeeDetailsEditLog->tender_award_commite_mem_comment }}</td>
            <td>{{ $srmTenderBidEmployeeDetailsEditLog->tender_award_commite_mem_status }}</td>
            <td>{{ $srmTenderBidEmployeeDetailsEditLog->tender_edit_version_id }}</td>
            <td>{{ $srmTenderBidEmployeeDetailsEditLog->tender_id }}</td>
                <td>
                    {!! Form::open(['route' => ['srmTenderBidEmployeeDetailsEditLogs.destroy', $srmTenderBidEmployeeDetailsEditLog->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{{ route('srmTenderBidEmployeeDetailsEditLogs.show', [$srmTenderBidEmployeeDetailsEditLog->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                        <a href="{{ route('srmTenderBidEmployeeDetailsEditLogs.edit', [$srmTenderBidEmployeeDetailsEditLog->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                        {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
