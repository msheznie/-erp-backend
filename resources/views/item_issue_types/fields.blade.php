<!-- Issuetypedes Field -->
<div class="form-group col-sm-6">
    {!! Form::label('issueTypeDes', 'Issuetypedes:') !!}
    {!! Form::text('issueTypeDes', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('itemIssueTypes.index') !!}" class="btn btn-default">Cancel</a>
</div>
