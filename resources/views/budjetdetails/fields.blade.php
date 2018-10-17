<!-- Budgetmasterid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('budgetmasterID', 'Budgetmasterid:') !!}
    {!! Form::number('budgetmasterID', null, ['class' => 'form-control']) !!}
</div>

<!-- Companysystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companySystemID', 'Companysystemid:') !!}
    {!! Form::number('companySystemID', null, ['class' => 'form-control']) !!}
</div>

<!-- Companyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companyId', 'Companyid:') !!}
    {!! Form::text('companyId', null, ['class' => 'form-control']) !!}
</div>

<!-- Companyfinanceyearid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companyFinanceYearID', 'Companyfinanceyearid:') !!}
    {!! Form::number('companyFinanceYearID', null, ['class' => 'form-control']) !!}
</div>

<!-- Servicelinesystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('serviceLineSystemID', 'Servicelinesystemid:') !!}
    {!! Form::number('serviceLineSystemID', null, ['class' => 'form-control']) !!}
</div>

<!-- Serviceline Field -->
<div class="form-group col-sm-6">
    {!! Form::label('serviceLine', 'Serviceline:') !!}
    {!! Form::text('serviceLine', null, ['class' => 'form-control']) !!}
</div>

<!-- Templatedetailid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('templateDetailID', 'Templatedetailid:') !!}
    {!! Form::number('templateDetailID', null, ['class' => 'form-control']) !!}
</div>

<!-- Chartofaccountid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('chartOfAccountID', 'Chartofaccountid:') !!}
    {!! Form::number('chartOfAccountID', null, ['class' => 'form-control']) !!}
</div>

<!-- Glcode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('glCode', 'Glcode:') !!}
    {!! Form::text('glCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Glcodetype Field -->
<div class="form-group col-sm-6">
    {!! Form::label('glCodeType', 'Glcodetype:') !!}
    {!! Form::text('glCodeType', null, ['class' => 'form-control']) !!}
</div>

<!-- Year Field -->
<div class="form-group col-sm-6">
    {!! Form::label('Year', 'Year:') !!}
    {!! Form::number('Year', null, ['class' => 'form-control']) !!}
</div>

<!-- Month Field -->
<div class="form-group col-sm-6">
    {!! Form::label('month', 'Month:') !!}
    {!! Form::number('month', null, ['class' => 'form-control']) !!}
</div>

<!-- Budjetamtlocal Field -->
<div class="form-group col-sm-6">
    {!! Form::label('budjetAmtLocal', 'Budjetamtlocal:') !!}
    {!! Form::number('budjetAmtLocal', null, ['class' => 'form-control']) !!}
</div>

<!-- Budjetamtrpt Field -->
<div class="form-group col-sm-6">
    {!! Form::label('budjetAmtRpt', 'Budjetamtrpt:') !!}
    {!! Form::number('budjetAmtRpt', null, ['class' => 'form-control']) !!}
</div>

<!-- Createdbyusersystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdByUserSystemID', 'Createdbyusersystemid:') !!}
    {!! Form::number('createdByUserSystemID', null, ['class' => 'form-control']) !!}
</div>

<!-- Createdbyuserid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdByUserID', 'Createdbyuserid:') !!}
    {!! Form::text('createdByUserID', null, ['class' => 'form-control']) !!}
</div>

<!-- Modifiedbyusersystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('modifiedByUserSystemID', 'Modifiedbyusersystemid:') !!}
    {!! Form::number('modifiedByUserSystemID', null, ['class' => 'form-control']) !!}
</div>

<!-- Modifiedbyuserid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('modifiedByUserID', 'Modifiedbyuserid:') !!}
    {!! Form::text('modifiedByUserID', null, ['class' => 'form-control']) !!}
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
    <a href="{!! route('budjetdetails.index') !!}" class="btn btn-default">Cancel</a>
</div>
