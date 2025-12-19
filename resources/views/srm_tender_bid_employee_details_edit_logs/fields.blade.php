<!-- Commercial Eval Remarks Field -->
<div class="form-group col-sm-6">
    {!! Form::label('commercial_eval_remarks', 'Commercial Eval Remarks:') !!}
    {!! Form::text('commercial_eval_remarks', null, ['class' => 'form-control']) !!}
</div>

<!-- Commercial Eval Status Field -->
<div class="form-group col-sm-6">
    {!! Form::label('commercial_eval_status', 'Commercial Eval Status:') !!}
    <label class="checkbox-inline">
        {!! Form::hidden('commercial_eval_status', 0) !!}
        {!! Form::checkbox('commercial_eval_status', '1', null) !!}
    </label>
</div>


<!-- Emp Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('emp_id', 'Emp Id:') !!}
    {!! Form::number('emp_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Modify Type Field -->
<div class="form-group col-sm-6">
    {!! Form::label('modify_type', 'Modify Type:') !!}
    {!! Form::number('modify_type', null, ['class' => 'form-control']) !!}
</div>

<!-- Remarks Field -->
<div class="form-group col-sm-6">
    {!! Form::label('remarks', 'Remarks:') !!}
    {!! Form::text('remarks', null, ['class' => 'form-control']) !!}
</div>

<!-- Status Field -->
<div class="form-group col-sm-6">
    {!! Form::label('status', 'Status:') !!}
    <label class="checkbox-inline">
        {!! Form::hidden('status', 0) !!}
        {!! Form::checkbox('status', '1', null) !!}
    </label>
</div>


<!-- Tender Award Commite Mem Comment Field -->
<div class="form-group col-sm-6">
    {!! Form::label('tender_award_commite_mem_comment', 'Tender Award Commite Mem Comment:') !!}
    {!! Form::text('tender_award_commite_mem_comment', null, ['class' => 'form-control']) !!}
</div>

<!-- Tender Award Commite Mem Status Field -->
<div class="form-group col-sm-6">
    {!! Form::label('tender_award_commite_mem_status', 'Tender Award Commite Mem Status:') !!}
    <label class="checkbox-inline">
        {!! Form::hidden('tender_award_commite_mem_status', 0) !!}
        {!! Form::checkbox('tender_award_commite_mem_status', '1', null) !!}
    </label>
</div>


<!-- Tender Edit Version Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('tender_edit_version_id', 'Tender Edit Version Id:') !!}
    {!! Form::number('tender_edit_version_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Tender Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('tender_id', 'Tender Id:') !!}
    {!! Form::number('tender_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{{ route('srmTenderBidEmployeeDetailsEditLogs.index') }}" class="btn btn-default">Cancel</a>
</div>
