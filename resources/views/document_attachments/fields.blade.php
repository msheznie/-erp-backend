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

<!-- Documentsystemcode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('documentSystemCode', 'Documentsystemcode:') !!}
    {!! Form::number('documentSystemCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Attachmentdescription Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('attachmentDescription', 'Attachmentdescription:') !!}
    {!! Form::textarea('attachmentDescription', null, ['class' => 'form-control']) !!}
</div>

<!-- Originalfilename Field -->
<div class="form-group col-sm-6">
    {!! Form::label('originalFileName', 'Originalfilename:') !!}
    {!! Form::text('originalFileName', null, ['class' => 'form-control']) !!}
</div>

<!-- Myfilename Field -->
<div class="form-group col-sm-6">
    {!! Form::label('myFileName', 'Myfilename:') !!}
    {!! Form::text('myFileName', null, ['class' => 'form-control']) !!}
</div>

<!-- Docexpirtydate Field -->
<div class="form-group col-sm-6">
    {!! Form::label('docExpirtyDate', 'Docexpirtydate:') !!}
    {!! Form::date('docExpirtyDate', null, ['class' => 'form-control']) !!}
</div>

<!-- Attachmenttype Field -->
<div class="form-group col-sm-6">
    {!! Form::label('attachmentType', 'Attachmenttype:') !!}
    {!! Form::number('attachmentType', null, ['class' => 'form-control']) !!}
</div>

<!-- Sizeinkbs Field -->
<div class="form-group col-sm-6">
    {!! Form::label('sizeInKbs', 'Sizeinkbs:') !!}
    {!! Form::number('sizeInKbs', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timeStamp', 'Timestamp:') !!}
    {!! Form::date('timeStamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('documentAttachments.index') !!}" class="btn btn-default">Cancel</a>
</div>
