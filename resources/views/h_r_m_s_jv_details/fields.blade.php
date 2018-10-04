<!-- Accmasterid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('accMasterID', 'Accmasterid:') !!}
    {!! Form::number('accMasterID', null, ['class' => 'form-control']) !!}
</div>

<!-- Salaryprocessmasterid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('salaryProcessMasterID', 'Salaryprocessmasterid:') !!}
    {!! Form::number('salaryProcessMasterID', null, ['class' => 'form-control']) !!}
</div>

<!-- Accrualnarration Field -->
<div class="form-group col-sm-6">
    {!! Form::label('accrualNarration', 'Accrualnarration:') !!}
    {!! Form::text('accrualNarration', null, ['class' => 'form-control']) !!}
</div>

<!-- Accrualdateasof Field -->
<div class="form-group col-sm-6">
    {!! Form::label('accrualDateAsOF', 'Accrualdateasof:') !!}
    {!! Form::date('accrualDateAsOF', null, ['class' => 'form-control']) !!}
</div>

<!-- Companyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companyID', 'Companyid:') !!}
    {!! Form::text('companyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Serviceline Field -->
<div class="form-group col-sm-6">
    {!! Form::label('serviceLine', 'Serviceline:') !!}
    {!! Form::text('serviceLine', null, ['class' => 'form-control']) !!}
</div>

<!-- Departuredate Field -->
<div class="form-group col-sm-6">
    {!! Form::label('departureDate', 'Departuredate:') !!}
    {!! Form::date('departureDate', null, ['class' => 'form-control']) !!}
</div>

<!-- Callofdate Field -->
<div class="form-group col-sm-6">
    {!! Form::label('callOfDate', 'Callofdate:') !!}
    {!! Form::date('callOfDate', null, ['class' => 'form-control']) !!}
</div>

<!-- Glcode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('GlCode', 'Glcode:') !!}
    {!! Form::text('GlCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Accrualamount Field -->
<div class="form-group col-sm-6">
    {!! Form::label('accrualAmount', 'Accrualamount:') !!}
    {!! Form::number('accrualAmount', null, ['class' => 'form-control']) !!}
</div>

<!-- Accrualcurrency Field -->
<div class="form-group col-sm-6">
    {!! Form::label('accrualCurrency', 'Accrualcurrency:') !!}
    {!! Form::number('accrualCurrency', null, ['class' => 'form-control']) !!}
</div>

<!-- Localamount Field -->
<div class="form-group col-sm-6">
    {!! Form::label('localAmount', 'Localamount:') !!}
    {!! Form::number('localAmount', null, ['class' => 'form-control']) !!}
</div>

<!-- Localcurrency Field -->
<div class="form-group col-sm-6">
    {!! Form::label('localCurrency', 'Localcurrency:') !!}
    {!! Form::number('localCurrency', null, ['class' => 'form-control']) !!}
</div>

<!-- Rptamount Field -->
<div class="form-group col-sm-6">
    {!! Form::label('rptAmount', 'Rptamount:') !!}
    {!! Form::number('rptAmount', null, ['class' => 'form-control']) !!}
</div>

<!-- Rptcurrency Field -->
<div class="form-group col-sm-6">
    {!! Form::label('rptCurrency', 'Rptcurrency:') !!}
    {!! Form::number('rptCurrency', null, ['class' => 'form-control']) !!}
</div>

<!-- Jvmasterautoid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('jvMasterAutoID', 'Jvmasterautoid:') !!}
    {!! Form::number('jvMasterAutoID', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timeStamp', 'Timestamp:') !!}
    {!! Form::date('timeStamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('hRMSJvDetails.index') !!}" class="btn btn-default">Cancel</a>
</div>
