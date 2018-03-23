<!-- Supplierid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('supplierID', 'Supplierid:') !!}
    {!! Form::number('supplierID', null, ['class' => 'form-control']) !!}
</div>

<!-- Supsubcategoryid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('supSubCategoryID', 'Supsubcategoryid:') !!}
    {!! Form::number('supSubCategoryID', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timestamp', 'Timestamp:') !!}
    {!! Form::date('timestamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('supplierSubCategoryAssigns.index') !!}" class="btn btn-default">Cancel</a>
</div>
