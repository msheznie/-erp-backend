<!-- Accruvalnarration Field -->
<div class="form-group col-sm-6">
    {!! Form::label('accruvalNarration', 'Accruvalnarration:') !!}
    {!! Form::text('accruvalNarration', null, ['class' => 'form-control']) !!}
</div>

<!-- Accrualdateasof Field -->
<div class="form-group col-sm-6">
    {!! Form::label('accrualDateAsOF', 'Accrualdateasof:') !!}
    {!! Form::date('accrualDateAsOF', null, ['class' => 'form-control']) !!}
</div>

<!-- Serialno Field -->
<div class="form-group col-sm-6">
    {!! Form::label('serialNo', 'Serialno:') !!}
    {!! Form::number('serialNo', null, ['class' => 'form-control']) !!}
</div>

<!-- Companyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companyID', 'Companyid:') !!}
    {!! Form::text('companyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Accmonth Field -->
<div class="form-group col-sm-6">
    {!! Form::label('accmonth', 'Accmonth:') !!}
    {!! Form::number('accmonth', null, ['class' => 'form-control']) !!}
</div>

<!-- Accyear Field -->
<div class="form-group col-sm-6">
    {!! Form::label('accYear', 'Accyear:') !!}
    {!! Form::number('accYear', null, ['class' => 'form-control']) !!}
</div>

<!-- Accconfirmedyn Field -->
<div class="form-group col-sm-6">
    {!! Form::label('accConfirmedYN', 'Accconfirmedyn:') !!}
    {!! Form::number('accConfirmedYN', null, ['class' => 'form-control']) !!}
</div>

<!-- Accconfirmedby Field -->
<div class="form-group col-sm-6">
    {!! Form::label('accConfirmedBy', 'Accconfirmedby:') !!}
    {!! Form::text('accConfirmedBy', null, ['class' => 'form-control']) !!}
</div>

<!-- Accconfirmeddate Field -->
<div class="form-group col-sm-6">
    {!! Form::label('accConfirmedDate', 'Accconfirmeddate:') !!}
    {!! Form::date('accConfirmedDate', null, ['class' => 'form-control']) !!}
</div>

<!-- Jvmasterautoid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('jvMasterAutoID', 'Jvmasterautoid:') !!}
    {!! Form::number('jvMasterAutoID', null, ['class' => 'form-control']) !!}
</div>

<!-- Accjvpostedyn Field -->
<div class="form-group col-sm-6">
    {!! Form::label('accJVpostedYN', 'Accjvpostedyn:') !!}
    {!! Form::number('accJVpostedYN', null, ['class' => 'form-control']) !!}
</div>

<!-- Jvpostedby Field -->
<div class="form-group col-sm-6">
    {!! Form::label('jvPostedBy', 'Jvpostedby:') !!}
    {!! Form::text('jvPostedBy', null, ['class' => 'form-control']) !!}
</div>

<!-- Jvposteddate Field -->
<div class="form-group col-sm-6">
    {!! Form::label('jvPostedDate', 'Jvposteddate:') !!}
    {!! Form::date('jvPostedDate', null, ['class' => 'form-control']) !!}
</div>

<!-- Createdby Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdby', 'Createdby:') !!}
    {!! Form::text('createdby', null, ['class' => 'form-control']) !!}
</div>

<!-- Createddatetime Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdDateTime', 'Createddatetime:') !!}
    {!! Form::date('createdDateTime', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timestamp', 'Timestamp:') !!}
    {!! Form::date('timestamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('accruavalFromOPMasters.index') !!}" class="btn btn-default">Cancel</a>
</div>
