<!-- Bankmasterautoid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('bankmasterAutoID', 'Bankmasterautoid:') !!}
    {!! Form::number('bankmasterAutoID', null, ['class' => 'form-control']) !!}
</div>

<!-- Companyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companyID', 'Companyid:') !!}
    {!! Form::text('companyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Bankshortcode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('bankShortCode', 'Bankshortcode:') !!}
    {!! Form::text('bankShortCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Bankname Field -->
<div class="form-group col-sm-6">
    {!! Form::label('bankName', 'Bankname:') !!}
    {!! Form::text('bankName', null, ['class' => 'form-control']) !!}
</div>

<!-- Isassigned Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isAssigned', 'Isassigned:') !!}
    {!! Form::number('isAssigned', null, ['class' => 'form-control']) !!}
</div>

<!-- Isdefault Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isDefault', 'Isdefault:') !!}
    {!! Form::number('isDefault', null, ['class' => 'form-control']) !!}
</div>

<!-- Isactive Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isActive', 'Isactive:') !!}
    {!! Form::number('isActive', null, ['class' => 'form-control']) !!}
</div>

<!-- Createddatetime Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdDateTime', 'Createddatetime:') !!}
    {!! Form::date('createdDateTime', null, ['class' => 'form-control']) !!}
</div>

<!-- Createdbyempid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdByEmpID', 'Createdbyempid:') !!}
    {!! Form::text('createdByEmpID', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('TimeStamp', 'Timestamp:') !!}
    {!! Form::date('TimeStamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('bankAssigns.index') !!}" class="btn btn-default">Cancel</a>
</div>
