<!-- Claimcategoriesdescription Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('claimcategoriesDescription', 'Claimcategoriesdescription:') !!}
    {!! Form::textarea('claimcategoriesDescription', null, ['class' => 'form-control']) !!}
</div>

<!-- Glcode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('glCode', 'Glcode:') !!}
    {!! Form::text('glCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Glcodedescription Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('glCodeDescription', 'Glcodedescription:') !!}
    {!! Form::textarea('glCodeDescription', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timestamp', 'Timestamp:') !!}
    {!! Form::date('timestamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('expenseClaimCategories.index') !!}" class="btn btn-default">Cancel</a>
</div>
