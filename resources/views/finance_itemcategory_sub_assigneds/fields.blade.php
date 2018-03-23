<!-- Mainitemcategoryid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('mainItemCategoryID', 'Mainitemcategoryid:') !!}
    {!! Form::number('mainItemCategoryID', null, ['class' => 'form-control']) !!}
</div>

<!-- Itemcategorysubid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('itemCategorySubID', 'Itemcategorysubid:') !!}
    {!! Form::number('itemCategorySubID', null, ['class' => 'form-control']) !!}
</div>

<!-- Categorydescription Field -->
<div class="form-group col-sm-6">
    {!! Form::label('categoryDescription', 'Categorydescription:') !!}
    {!! Form::text('categoryDescription', null, ['class' => 'form-control']) !!}
</div>

<!-- Financeglcodebbssystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('financeGLcodebBSSystemID', 'Financeglcodebbssystemid:') !!}
    {!! Form::number('financeGLcodebBSSystemID', null, ['class' => 'form-control']) !!}
</div>

<!-- Financeglcodebbs Field -->
<div class="form-group col-sm-6">
    {!! Form::label('financeGLcodebBS', 'Financeglcodebbs:') !!}
    {!! Form::text('financeGLcodebBS', null, ['class' => 'form-control']) !!}
</div>

<!-- Financeglcodeplsystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('financeGLcodePLSystemID', 'Financeglcodeplsystemid:') !!}
    {!! Form::number('financeGLcodePLSystemID', null, ['class' => 'form-control']) !!}
</div>

<!-- Financeglcodepl Field -->
<div class="form-group col-sm-6">
    {!! Form::label('financeGLcodePL', 'Financeglcodepl:') !!}
    {!! Form::text('financeGLcodePL', null, ['class' => 'form-control']) !!}
</div>

<!-- Includeplforgrvyn Field -->
<div class="form-group col-sm-6">
    {!! Form::label('includePLForGRVYN', 'Includeplforgrvyn:') !!}
    {!! Form::number('includePLForGRVYN', null, ['class' => 'form-control']) !!}
</div>

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

<!-- Isactive Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isActive', 'Isactive:') !!}
    {!! Form::number('isActive', null, ['class' => 'form-control']) !!}
</div>

<!-- Isassigned Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isAssigned', 'Isassigned:') !!}
    {!! Form::number('isAssigned', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timeStamp', 'Timestamp:') !!}
    {!! Form::date('timeStamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('financeItemcategorySubAssigneds.index') !!}" class="btn btn-default">Cancel</a>
</div>
