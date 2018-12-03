<!-- Supcategoryicvmasterid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('supCategoryICVMasterID', 'Supcategoryicvmasterid:') !!}
    {!! Form::number('supCategoryICVMasterID', null, ['class' => 'form-control']) !!}
</div>

<!-- Subcategorycode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('subCategoryCode', 'Subcategorycode:') !!}
    {!! Form::text('subCategoryCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Categorydescription Field -->
<div class="form-group col-sm-6">
    {!! Form::label('categoryDescription', 'Categorydescription:') !!}
    {!! Form::text('categoryDescription', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timeStamp', 'Timestamp:') !!}
    {!! Form::date('timeStamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Createddatetime Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdDateTime', 'Createddatetime:') !!}
    {!! Form::date('createdDateTime', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('supplierCategoryICVSubs.index') !!}" class="btn btn-default">Cancel</a>
</div>
