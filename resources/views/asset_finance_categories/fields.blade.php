<!-- Financecatdescription Field -->
<div class="form-group col-sm-6">
    {!! Form::label('financeCatDescription', 'Financecatdescription:') !!}
    {!! Form::text('financeCatDescription', null, ['class' => 'form-control']) !!}
</div>

<!-- Costglcode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('COSTGLCODE', 'Costglcode:') !!}
    {!! Form::text('COSTGLCODE', null, ['class' => 'form-control']) !!}
</div>

<!-- Accdepglcode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('ACCDEPGLCODE', 'Accdepglcode:') !!}
    {!! Form::text('ACCDEPGLCODE', null, ['class' => 'form-control']) !!}
</div>

<!-- Depglcode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('DEPGLCODE', 'Depglcode:') !!}
    {!! Form::text('DEPGLCODE', null, ['class' => 'form-control']) !!}
</div>

<!-- Dispoglcode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('DISPOGLCODE', 'Dispoglcode:') !!}
    {!! Form::text('DISPOGLCODE', null, ['class' => 'form-control']) !!}
</div>

<!-- Isactive Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isActive', 'Isactive:') !!}
    {!! Form::number('isActive', null, ['class' => 'form-control']) !!}
</div>

<!-- Sortorder Field -->
<div class="form-group col-sm-6">
    {!! Form::label('sortOrder', 'Sortorder:') !!}
    {!! Form::number('sortOrder', null, ['class' => 'form-control']) !!}
</div>

<!-- Createdpcid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdPcID', 'Createdpcid:') !!}
    {!! Form::text('createdPcID', null, ['class' => 'form-control']) !!}
</div>

<!-- Createdusergroup Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdUserGroup', 'Createdusergroup:') !!}
    {!! Form::text('createdUserGroup', null, ['class' => 'form-control']) !!}
</div>

<!-- Createduserid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdUserID', 'Createduserid:') !!}
    {!! Form::text('createdUserID', null, ['class' => 'form-control']) !!}
</div>

<!-- Createddatetime Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdDateTime', 'Createddatetime:') !!}
    {!! Form::date('createdDateTime', null, ['class' => 'form-control']) !!}
</div>

<!-- Modifiedpc Field -->
<div class="form-group col-sm-6">
    {!! Form::label('modifiedPc', 'Modifiedpc:') !!}
    {!! Form::text('modifiedPc', null, ['class' => 'form-control']) !!}
</div>

<!-- Modifieduser Field -->
<div class="form-group col-sm-6">
    {!! Form::label('modifiedUser', 'Modifieduser:') !!}
    {!! Form::text('modifiedUser', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timestamp', 'Timestamp:') !!}
    {!! Form::date('timestamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('assetFinanceCategories.index') !!}" class="btn btn-default">Cancel</a>
</div>
