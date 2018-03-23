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

<!-- Customercodesystem Field -->
<div class="form-group col-sm-6">
    {!! Form::label('customerCodeSystem', 'Customercodesystem:') !!}
    {!! Form::number('customerCodeSystem', null, ['class' => 'form-control']) !!}
</div>

<!-- Cutomercode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('CutomerCode', 'Cutomercode:') !!}
    {!! Form::text('CutomerCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Customershortcode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('customerShortCode', 'Customershortcode:') !!}
    {!! Form::text('customerShortCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Custglaccountsystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('custGLAccountSystemID', 'Custglaccountsystemid:') !!}
    {!! Form::number('custGLAccountSystemID', null, ['class' => 'form-control']) !!}
</div>

<!-- Custglaccount Field -->
<div class="form-group col-sm-6">
    {!! Form::label('custGLaccount', 'Custglaccount:') !!}
    {!! Form::text('custGLaccount', null, ['class' => 'form-control']) !!}
</div>

<!-- Customername Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('CustomerName', 'Customername:') !!}
    {!! Form::textarea('CustomerName', null, ['class' => 'form-control']) !!}
</div>

<!-- Reporttitle Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('ReportTitle', 'Reporttitle:') !!}
    {!! Form::textarea('ReportTitle', null, ['class' => 'form-control']) !!}
</div>

<!-- Customeraddress1 Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('customerAddress1', 'Customeraddress1:') !!}
    {!! Form::textarea('customerAddress1', null, ['class' => 'form-control']) !!}
</div>

<!-- Customeraddress2 Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('customerAddress2', 'Customeraddress2:') !!}
    {!! Form::textarea('customerAddress2', null, ['class' => 'form-control']) !!}
</div>

<!-- Customercity Field -->
<div class="form-group col-sm-6">
    {!! Form::label('customerCity', 'Customercity:') !!}
    {!! Form::text('customerCity', null, ['class' => 'form-control']) !!}
</div>

<!-- Customercountry Field -->
<div class="form-group col-sm-6">
    {!! Form::label('customerCountry', 'Customercountry:') !!}
    {!! Form::text('customerCountry', null, ['class' => 'form-control']) !!}
</div>

<!-- Custwebsite Field -->
<div class="form-group col-sm-6">
    {!! Form::label('CustWebsite', 'Custwebsite:') !!}
    {!! Form::text('CustWebsite', null, ['class' => 'form-control']) !!}
</div>

<!-- Creditlimit Field -->
<div class="form-group col-sm-6">
    {!! Form::label('creditLimit', 'Creditlimit:') !!}
    {!! Form::number('creditLimit', null, ['class' => 'form-control']) !!}
</div>

<!-- Creditdays Field -->
<div class="form-group col-sm-6">
    {!! Form::label('creditDays', 'Creditdays:') !!}
    {!! Form::number('creditDays', null, ['class' => 'form-control']) !!}
</div>

<!-- Isrelatedpartyyn Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isRelatedPartyYN', 'Isrelatedpartyyn:') !!}
    {!! Form::number('isRelatedPartyYN', null, ['class' => 'form-control']) !!}
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

<!-- Vateligible Field -->
<div class="form-group col-sm-6">
    {!! Form::label('vatEligible', 'Vateligible:') !!}
    {!! Form::number('vatEligible', null, ['class' => 'form-control']) !!}
</div>

<!-- Vatnumber Field -->
<div class="form-group col-sm-6">
    {!! Form::label('vatNumber', 'Vatnumber:') !!}
    {!! Form::text('vatNumber', null, ['class' => 'form-control']) !!}
</div>

<!-- Vatpercentage Field -->
<div class="form-group col-sm-6">
    {!! Form::label('vatPercentage', 'Vatpercentage:') !!}
    {!! Form::number('vatPercentage', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timeStamp', 'Timestamp:') !!}
    {!! Form::date('timeStamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('customerAssigneds.index') !!}" class="btn btn-default">Cancel</a>
</div>
