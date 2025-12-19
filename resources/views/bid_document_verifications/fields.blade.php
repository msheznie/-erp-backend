<!-- Attachment Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('attachment_id', 'Attachment Id:') !!}
    {!! Form::number('attachment_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Bis Submission Master Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('bis_submission_master_id', 'Bis Submission Master Id:') !!}
    {!! Form::number('bis_submission_master_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Document Submit Type Field -->
<div class="form-group col-sm-6">
    {!! Form::label('document_submit_type', 'Document Submit Type:') !!}
    <label class="checkbox-inline">
        {!! Form::hidden('document_submit_type', 0) !!}
        {!! Form::checkbox('document_submit_type', '1', null) !!}
    </label>
</div>


<!-- Submit Remarks Field -->
<div class="form-group col-sm-6">
    {!! Form::label('submit_remarks', 'Submit Remarks:') !!}
    {!! Form::text('submit_remarks', null, ['class' => 'form-control']) !!}
</div>

<!-- Verified By Field -->
<div class="form-group col-sm-6">
    {!! Form::label('verified_by', 'Verified By:') !!}
    {!! Form::number('verified_by', null, ['class' => 'form-control']) !!}
</div>

<!-- Verified Date Field -->
<div class="form-group col-sm-6">
    {!! Form::label('verified_date', 'Verified Date:') !!}
    {!! Form::date('verified_date', null, ['class' => 'form-control','id'=>'verified_date']) !!}
</div>

@section('scripts')
    <script type="text/javascript">
        $('#verified_date').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            useCurrent: false
        })
    </script>
@endsection

<!-- Status Field -->
<div class="form-group col-sm-6">
    {!! Form::label('status', 'Status:') !!}
    <label class="checkbox-inline">
        {!! Form::hidden('status', 0) !!}
        {!! Form::checkbox('status', '1', null) !!}
    </label>
</div>


<!-- Remarks Field -->
<div class="form-group col-sm-6">
    {!! Form::label('remarks', 'Remarks:') !!}
    {!! Form::text('remarks', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{{ route('bidDocumentVerifications.index') }}" class="btn btn-default">Cancel</a>
</div>
