<!-- Invoiceid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('invoiceID', 'Invoiceid:') !!}
    {!! Form::number('invoiceID', null, ['class' => 'form-control']) !!}
</div>

<!-- Paymentconfigmasterid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('paymentConfigMasterID', 'Paymentconfigmasterid:') !!}
    {!! Form::number('paymentConfigMasterID', null, ['class' => 'form-control']) !!}
</div>

<!-- Paymentconfigdetailid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('paymentConfigDetailID', 'Paymentconfigdetailid:') !!}
    {!! Form::number('paymentConfigDetailID', null, ['class' => 'form-control']) !!}
</div>

<!-- Glaccounttype Field -->
<div class="form-group col-sm-6">
    {!! Form::label('glAccountType', 'Glaccounttype:') !!}
    {!! Form::number('glAccountType', null, ['class' => 'form-control']) !!}
</div>

<!-- Glcode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('GLCode', 'Glcode:') !!}
    {!! Form::number('GLCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Amount Field -->
<div class="form-group col-sm-6">
    {!! Form::label('amount', 'Amount:') !!}
    {!! Form::number('amount', null, ['class' => 'form-control']) !!}
</div>

<!-- Reference Field -->
<div class="form-group col-sm-6">
    {!! Form::label('reference', 'Reference:') !!}
    {!! Form::text('reference', null, ['class' => 'form-control']) !!}
</div>

<!-- Customerautoid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('customerAutoID', 'Customerautoid:') !!}
    {!! Form::number('customerAutoID', null, ['class' => 'form-control']) !!}
</div>

<!-- Isadvancepayment Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isAdvancePayment', 'Isadvancepayment:') !!}
    {!! Form::number('isAdvancePayment', null, ['class' => 'form-control']) !!}
</div>

<!-- Createdusergroup Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdUserGroup', 'Createdusergroup:') !!}
    {!! Form::number('createdUserGroup', null, ['class' => 'form-control']) !!}
</div>

<!-- Createdpcid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdPCID', 'Createdpcid:') !!}
    {!! Form::text('createdPCID', null, ['class' => 'form-control']) !!}
</div>

<!-- Createduserid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdUserID', 'Createduserid:') !!}
    {!! Form::text('createdUserID', null, ['class' => 'form-control']) !!}
</div>

<!-- Createdusername Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdUserName', 'Createdusername:') !!}
    {!! Form::text('createdUserName', null, ['class' => 'form-control']) !!}
</div>

<!-- Createddatetime Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdDateTime', 'Createddatetime:') !!}
    {!! Form::date('createdDateTime', null, ['class' => 'form-control']) !!}
</div>

<!-- Modifiedpcid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('modifiedPCID', 'Modifiedpcid:') !!}
    {!! Form::text('modifiedPCID', null, ['class' => 'form-control']) !!}
</div>

<!-- Modifieduserid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('modifiedUserID', 'Modifieduserid:') !!}
    {!! Form::text('modifiedUserID', null, ['class' => 'form-control']) !!}
</div>

<!-- Modifiedusername Field -->
<div class="form-group col-sm-6">
    {!! Form::label('modifiedUserName', 'Modifiedusername:') !!}
    {!! Form::text('modifiedUserName', null, ['class' => 'form-control']) !!}
</div>

<!-- Modifieddatetime Field -->
<div class="form-group col-sm-6">
    {!! Form::label('modifiedDateTime', 'Modifieddatetime:') !!}
    {!! Form::date('modifiedDateTime', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timestamp', 'Timestamp:') !!}
    {!! Form::date('timestamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('gposInvoicePayments.index') !!}" class="btn btn-default">Cancel</a>
</div>
