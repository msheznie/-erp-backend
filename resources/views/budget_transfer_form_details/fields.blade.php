<!-- Budgettransferformautoid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('budgetTransferFormAutoID', 'Budgettransferformautoid:') !!}
    {!! Form::number('budgetTransferFormAutoID', null, ['class' => 'form-control']) !!}
</div>

<!-- Year Field -->
<div class="form-group col-sm-6">
    {!! Form::label('year', 'Year:') !!}
    {!! Form::number('year', null, ['class' => 'form-control']) !!}
</div>

<!-- Fromtemplatedetailid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('fromTemplateDetailID', 'Fromtemplatedetailid:') !!}
    {!! Form::number('fromTemplateDetailID', null, ['class' => 'form-control']) !!}
</div>

<!-- Fromservicelinesystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('fromServiceLineSystemID', 'Fromservicelinesystemid:') !!}
    {!! Form::number('fromServiceLineSystemID', null, ['class' => 'form-control']) !!}
</div>

<!-- Fromservicelinecode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('fromServiceLineCode', 'Fromservicelinecode:') !!}
    {!! Form::text('fromServiceLineCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Fromchartofaccountsystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('fromChartOfAccountSystemID', 'Fromchartofaccountsystemid:') !!}
    {!! Form::number('fromChartOfAccountSystemID', null, ['class' => 'form-control']) !!}
</div>

<!-- Fromglcode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('FromGLCode', 'Fromglcode:') !!}
    {!! Form::text('FromGLCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Fromglcodedescription Field -->
<div class="form-group col-sm-6">
    {!! Form::label('FromGLCodeDescription', 'Fromglcodedescription:') !!}
    {!! Form::text('FromGLCodeDescription', null, ['class' => 'form-control']) !!}
</div>

<!-- Totemplatedetailid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('toTemplateDetailID', 'Totemplatedetailid:') !!}
    {!! Form::number('toTemplateDetailID', null, ['class' => 'form-control']) !!}
</div>

<!-- Toservicelinesystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('toServiceLineSystemID', 'Toservicelinesystemid:') !!}
    {!! Form::number('toServiceLineSystemID', null, ['class' => 'form-control']) !!}
</div>

<!-- Toservicelinecode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('toServiceLineCode', 'Toservicelinecode:') !!}
    {!! Form::text('toServiceLineCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Tochartofaccountsystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('toChartOfAccountSystemID', 'Tochartofaccountsystemid:') !!}
    {!! Form::number('toChartOfAccountSystemID', null, ['class' => 'form-control']) !!}
</div>

<!-- Toglcode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('toGLCode', 'Toglcode:') !!}
    {!! Form::text('toGLCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Toglcodedescription Field -->
<div class="form-group col-sm-6">
    {!! Form::label('toGLCodeDescription', 'Toglcodedescription:') !!}
    {!! Form::text('toGLCodeDescription', null, ['class' => 'form-control']) !!}
</div>

<!-- Adjustmentamountlocal Field -->
<div class="form-group col-sm-6">
    {!! Form::label('adjustmentAmountLocal', 'Adjustmentamountlocal:') !!}
    {!! Form::number('adjustmentAmountLocal', null, ['class' => 'form-control']) !!}
</div>

<!-- Adjustmentamountrpt Field -->
<div class="form-group col-sm-6">
    {!! Form::label('adjustmentAmountRpt', 'Adjustmentamountrpt:') !!}
    {!! Form::number('adjustmentAmountRpt', null, ['class' => 'form-control']) !!}
</div>

<!-- Remarks Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('remarks', 'Remarks:') !!}
    {!! Form::textarea('remarks', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timestamp', 'Timestamp:') !!}
    {!! Form::date('timestamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('budgetTransferFormDetails.index') !!}" class="btn btn-default">Cancel</a>
</div>
