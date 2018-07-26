<!-- Designation Field -->
<div class="form-group col-sm-6">
    {!! Form::label('designation', 'Designation:') !!}
    {!! Form::text('designation', null, ['class' => 'form-control']) !!}
</div>

<!-- Designation O Field -->
<div class="form-group col-sm-6">
    {!! Form::label('designation_O', 'Designation O:') !!}
    {!! Form::text('designation_O', null, ['class' => 'form-control']) !!}
</div>

<!-- Localname Field -->
<div class="form-group col-sm-6">
    {!! Form::label('localName', 'Localname:') !!}
    {!! Form::text('localName', null, ['class' => 'form-control']) !!}
</div>

<!-- Jobcode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('jobCode', 'Jobcode:') !!}
    {!! Form::text('jobCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Jobdecipline Field -->
<div class="form-group col-sm-6">
    {!! Form::label('jobDecipline', 'Jobdecipline:') !!}
    {!! Form::number('jobDecipline', null, ['class' => 'form-control']) !!}
</div>

<!-- Businessfunction Field -->
<div class="form-group col-sm-6">
    {!! Form::label('businessFunction', 'Businessfunction:') !!}
    {!! Form::number('businessFunction', null, ['class' => 'form-control']) !!}
</div>

<!-- Appraisaltemplateid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('appraisalTemplateID', 'Appraisaltemplateid:') !!}
    {!! Form::number('appraisalTemplateID', null, ['class' => 'form-control']) !!}
</div>

<!-- Createdpcid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdPCid', 'Createdpcid:') !!}
    {!! Form::text('createdPCid', null, ['class' => 'form-control']) !!}
</div>

<!-- Createduserid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdUserID', 'Createduserid:') !!}
    {!! Form::text('createdUserID', null, ['class' => 'form-control']) !!}
</div>

<!-- Modifieduser Field -->
<div class="form-group col-sm-6">
    {!! Form::label('modifiedUser', 'Modifieduser:') !!}
    {!! Form::text('modifiedUser', null, ['class' => 'form-control']) !!}
</div>

<!-- Modifiedpc Field -->
<div class="form-group col-sm-6">
    {!! Form::label('modifiedPc', 'Modifiedpc:') !!}
    {!! Form::text('modifiedPc', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timestamp', 'Timestamp:') !!}
    {!! Form::date('timestamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('designations.index') !!}" class="btn btn-default">Cancel</a>
</div>
