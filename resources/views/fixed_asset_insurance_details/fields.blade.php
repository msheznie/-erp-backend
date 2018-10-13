<!-- Companyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companyID', 'Companyid:') !!}
    {!! Form::text('companyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Faid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('faID', 'Faid:') !!}
    {!! Form::number('faID', null, ['class' => 'form-control']) !!}
</div>

<!-- Insuredyn Field -->
<div class="form-group col-sm-6">
    {!! Form::label('insuredYN', 'Insuredyn:') !!}
    {!! Form::number('insuredYN', null, ['class' => 'form-control']) !!}
</div>

<!-- Policy Field -->
<div class="form-group col-sm-6">
    {!! Form::label('policy', 'Policy:') !!}
    {!! Form::number('policy', null, ['class' => 'form-control']) !!}
</div>

<!-- Policynumber Field -->
<div class="form-group col-sm-6">
    {!! Form::label('policyNumber', 'Policynumber:') !!}
    {!! Form::text('policyNumber', null, ['class' => 'form-control']) !!}
</div>

<!-- Dateofinsurance Field -->
<div class="form-group col-sm-6">
    {!! Form::label('dateOfInsurance', 'Dateofinsurance:') !!}
    {!! Form::date('dateOfInsurance', null, ['class' => 'form-control']) !!}
</div>

<!-- Dateofexpiry Field -->
<div class="form-group col-sm-6">
    {!! Form::label('dateOfExpiry', 'Dateofexpiry:') !!}
    {!! Form::date('dateOfExpiry', null, ['class' => 'form-control']) !!}
</div>

<!-- Insuredvalue Field -->
<div class="form-group col-sm-6">
    {!! Form::label('insuredValue', 'Insuredvalue:') !!}
    {!! Form::number('insuredValue', null, ['class' => 'form-control']) !!}
</div>

<!-- Insurername Field -->
<div class="form-group col-sm-6">
    {!! Form::label('insurerName', 'Insurername:') !!}
    {!! Form::text('insurerName', null, ['class' => 'form-control']) !!}
</div>

<!-- Locationid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('locationID', 'Locationid:') !!}
    {!! Form::number('locationID', null, ['class' => 'form-control']) !!}
</div>

<!-- Buildingnumber Field -->
<div class="form-group col-sm-6">
    {!! Form::label('buildingNumber', 'Buildingnumber:') !!}
    {!! Form::text('buildingNumber', null, ['class' => 'form-control']) !!}
</div>

<!-- Openclosedarea Field -->
<div class="form-group col-sm-6">
    {!! Form::label('openClosedArea', 'Openclosedarea:') !!}
    {!! Form::number('openClosedArea', null, ['class' => 'form-control']) !!}
</div>

<!-- Containernumber Field -->
<div class="form-group col-sm-6">
    {!! Form::label('containerNumber', 'Containernumber:') !!}
    {!! Form::text('containerNumber', null, ['class' => 'form-control']) !!}
</div>

<!-- Movingitem Field -->
<div class="form-group col-sm-6">
    {!! Form::label('movingItem', 'Movingitem:') !!}
    {!! Form::number('movingItem', null, ['class' => 'form-control']) !!}
</div>

<!-- Createdbyuserid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdByUserID', 'Createdbyuserid:') !!}
    {!! Form::text('createdByUserID', null, ['class' => 'form-control']) !!}
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
    <a href="{!! route('fixedAssetInsuranceDetails.index') !!}" class="btn btn-default">Cancel</a>
</div>
