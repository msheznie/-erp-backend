<!-- Tender Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('tender_id', 'Tender Id:') !!}
    {!! Form::number('tender_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Companysystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companySystemID', 'Companysystemid:') !!}
    {!! Form::number('companySystemID', null, ['class' => 'form-control']) !!}
</div>

<!-- Companyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companyID', 'Companyid:') !!}
    {!! Form::number('companyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Documentsystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('documentSystemID', 'Documentsystemid:') !!}
    {!! Form::number('documentSystemID', null, ['class' => 'form-control']) !!}
</div>

<!-- Documentid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('documentID', 'Documentid:') !!}
    {!! Form::number('documentID', null, ['class' => 'form-control']) !!}
</div>

<!-- Documentsystemcode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('documentSystemCode', 'Documentsystemcode:') !!}
    {!! Form::number('documentSystemCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Attachmentdescription Field -->
<div class="form-group col-sm-6">
    {!! Form::label('attachmentDescription', 'Attachmentdescription:') !!}
    {!! Form::text('attachmentDescription', null, ['class' => 'form-control']) !!}
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

<!-- Path Field -->
<div class="form-group col-sm-6">
    {!! Form::label('path', 'Path:') !!}
    {!! Form::text('path', null, ['class' => 'form-control']) !!}
</div>

<!-- Sizeinkbs Field -->
<div class="form-group col-sm-6">
    {!! Form::label('sizeInKbs', 'Sizeinkbs:') !!}
    {!! Form::number('sizeInKbs', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{{ route('srmBidDocumentattachments.index') }}" class="btn btn-default">Cancel</a>
</div>
