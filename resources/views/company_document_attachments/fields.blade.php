<!-- Companysystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companySystemID', 'Companysystemid:') !!}
    {!! Form::number('companySystemID', null, ['class' => 'form-control']) !!}
</div>

<!-- Companyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companyID', 'Companyid:') !!}
    {!! Form::text('companyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Documentsystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('documentSystemID', 'Documentsystemid:') !!}
    {!! Form::number('documentSystemID', null, ['class' => 'form-control']) !!}
</div>

<!-- Documentid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('documentID', 'Documentid:') !!}
    {!! Form::text('documentID', null, ['class' => 'form-control']) !!}
</div>

<!-- Docrefnumber Field -->
<div class="form-group col-sm-6">
    {!! Form::label('docRefNumber', 'Docrefnumber:') !!}
    {!! Form::text('docRefNumber', null, ['class' => 'form-control']) !!}
</div>

<!-- Isattachmentyn Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isAttachmentYN', 'Isattachmentyn:') !!}
    {!! Form::number('isAttachmentYN', null, ['class' => 'form-control']) !!}
</div>

<!-- Sendemailyn Field -->
<div class="form-group col-sm-6">
    {!! Form::label('sendEmailYN', 'Sendemailyn:') !!}
    {!! Form::number('sendEmailYN', null, ['class' => 'form-control']) !!}
</div>

<!-- Codegeneratorformat Field -->
<div class="form-group col-sm-6">
    {!! Form::label('codeGeneratorFormat', 'Codegeneratorformat:') !!}
    {!! Form::text('codeGeneratorFormat', null, ['class' => 'form-control']) !!}
</div>

<!-- Isamountapproval Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isAmountApproval', 'Isamountapproval:') !!}
    {!! Form::number('isAmountApproval', null, ['class' => 'form-control']) !!}
</div>

<!-- Isservicelineapproval Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isServiceLineApproval', 'Isservicelineapproval:') !!}
    {!! Form::number('isServiceLineApproval', null, ['class' => 'form-control']) !!}
</div>

<!-- Blockyn Field -->
<div class="form-group col-sm-6">
    {!! Form::label('blockYN', 'Blockyn:') !!}
    {!! Form::number('blockYN', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timeStamp', 'Timestamp:') !!}
    {!! Form::date('timeStamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('companyDocumentAttachments.index') !!}" class="btn btn-default">Cancel</a>
</div>
