<!-- Companypolicycategorydescription Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companyPolicyCategoryDescription', 'Companypolicycategorydescription:') !!}
    {!! Form::text('companyPolicyCategoryDescription', null, ['class' => 'form-control']) !!}
</div>

<!-- Applicabledocumentid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('applicableDocumentID', 'Applicabledocumentid:') !!}
    {!! Form::text('applicableDocumentID', null, ['class' => 'form-control']) !!}
</div>

<!-- Documentid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('documentID', 'Documentid:') !!}
    {!! Form::text('documentID', null, ['class' => 'form-control']) !!}
</div>

<!-- Impletemed Field -->
<div class="form-group col-sm-6">
    {!! Form::label('impletemed', 'Impletemed:') !!}
    {!! Form::text('impletemed', null, ['class' => 'form-control']) !!}
</div>

<!-- Isactive Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isActive', 'Isactive:') !!}
    {!! Form::number('isActive', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timestamp', 'Timestamp:') !!}
    {!! Form::text('timestamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('companyPolicyCategories.index') !!}" class="btn btn-default">Cancel</a>
</div>
