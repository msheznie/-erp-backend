<div class="table-responsive">
    <table class="table" id="bidDocumentVerifications-table">
        <thead>
            <tr>
                <th>Attachment Id</th>
        <th>Bis Submission Master Id</th>
        <th>Document Submit Type</th>
        <th>Submit Remarks</th>
        <th>Verified By</th>
        <th>Verified Date</th>
        <th>Status</th>
        <th>Remarks</th>
                <th colspan="3">Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($bidDocumentVerifications as $bidDocumentVerification)
            <tr>
                <td>{{ $bidDocumentVerification->attachment_id }}</td>
            <td>{{ $bidDocumentVerification->bis_submission_master_id }}</td>
            <td>{{ $bidDocumentVerification->document_submit_type }}</td>
            <td>{{ $bidDocumentVerification->submit_remarks }}</td>
            <td>{{ $bidDocumentVerification->verified_by }}</td>
            <td>{{ $bidDocumentVerification->verified_date }}</td>
            <td>{{ $bidDocumentVerification->status }}</td>
            <td>{{ $bidDocumentVerification->remarks }}</td>
                <td>
                    {!! Form::open(['route' => ['bidDocumentVerifications.destroy', $bidDocumentVerification->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{{ route('bidDocumentVerifications.show', [$bidDocumentVerification->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                        <a href="{{ route('bidDocumentVerifications.edit', [$bidDocumentVerification->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                        {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
