<!-- Documentsystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('documentSystemID', 'Documentsystemid:') !!}
    {!! Form::number('documentSystemID', null, ['class' => 'form-control']) !!}
</div>

<!-- Documentid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('documentID', 'Documentid:') !!}
    {!! Form::text('documentID', null, ['class' => 'form-control']) !!}
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

<!-- Serialno Field -->
<div class="form-group col-sm-6">
    {!! Form::label('serialNo', 'Serialno:') !!}
    {!! Form::number('serialNo', null, ['class' => 'form-control']) !!}
</div>

<!-- Year Field -->
<div class="form-group col-sm-6">
    {!! Form::label('year', 'Year:') !!}
    {!! Form::number('year', null, ['class' => 'form-control']) !!}
</div>

<!-- Transfervoucherno Field -->
<div class="form-group col-sm-6">
    {!! Form::label('transferVoucherNo', 'Transfervoucherno:') !!}
    {!! Form::text('transferVoucherNo', null, ['class' => 'form-control']) !!}
</div>

<!-- Createddate Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdDate', 'Createddate:') !!}
    {!! Form::date('createdDate', null, ['class' => 'form-control']) !!}
</div>

<!-- Comments Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('comments', 'Comments:') !!}
    {!! Form::textarea('comments', null, ['class' => 'form-control']) !!}
</div>

<!-- Confirmedyn Field -->
<div class="form-group col-sm-6">
    {!! Form::label('confirmedYN', 'Confirmedyn:') !!}
    {!! Form::number('confirmedYN', null, ['class' => 'form-control']) !!}
</div>

<!-- Confirmeddate Field -->
<div class="form-group col-sm-6">
    {!! Form::label('confirmedDate', 'Confirmeddate:') !!}
    {!! Form::date('confirmedDate', null, ['class' => 'form-control']) !!}
</div>

<!-- Confirmedbyempsystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('confirmedByEmpSystemID', 'Confirmedbyempsystemid:') !!}
    {!! Form::number('confirmedByEmpSystemID', null, ['class' => 'form-control']) !!}
</div>

<!-- Confirmedbyempid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('confirmedByEmpID', 'Confirmedbyempid:') !!}
    {!! Form::text('confirmedByEmpID', null, ['class' => 'form-control']) !!}
</div>

<!-- Confirmedbyempname Field -->
<div class="form-group col-sm-6">
    {!! Form::label('confirmedByEmpName', 'Confirmedbyempname:') !!}
    {!! Form::text('confirmedByEmpName', null, ['class' => 'form-control']) !!}
</div>

<!-- Approvedyn Field -->
<div class="form-group col-sm-6">
    {!! Form::label('approvedYN', 'Approvedyn:') !!}
    {!! Form::number('approvedYN', null, ['class' => 'form-control']) !!}
</div>

<!-- Approveddate Field -->
<div class="form-group col-sm-6">
    {!! Form::label('approvedDate', 'Approveddate:') !!}
    {!! Form::date('approvedDate', null, ['class' => 'form-control']) !!}
</div>

<!-- Approvedbyusersystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('approvedByUserSystemID', 'Approvedbyusersystemid:') !!}
    {!! Form::number('approvedByUserSystemID', null, ['class' => 'form-control']) !!}
</div>

<!-- Approvedempid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('approvedEmpID', 'Approvedempid:') !!}
    {!! Form::text('approvedEmpID', null, ['class' => 'form-control']) !!}
</div>

<!-- Approvedempname Field -->
<div class="form-group col-sm-6">
    {!! Form::label('approvedEmpName', 'Approvedempname:') !!}
    {!! Form::text('approvedEmpName', null, ['class' => 'form-control']) !!}
</div>

<!-- Rolllevforapp Curr Field -->
<div class="form-group col-sm-6">
    {!! Form::label('RollLevForApp_curr', 'Rolllevforapp Curr:') !!}
    {!! Form::number('RollLevForApp_curr', null, ['class' => 'form-control']) !!}
</div>

<!-- Createddatetime Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdDateTime', 'Createddatetime:') !!}
    {!! Form::date('createdDateTime', null, ['class' => 'form-control']) !!}
</div>

<!-- Createdusersystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdUserSystemID', 'Createdusersystemid:') !!}
    {!! Form::number('createdUserSystemID', null, ['class' => 'form-control']) !!}
</div>

<!-- Createduserid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdUserID', 'Createduserid:') !!}
    {!! Form::text('createdUserID', null, ['class' => 'form-control']) !!}
</div>

<!-- Createdpcid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdPcID', 'Createdpcid:') !!}
    {!! Form::text('createdPcID', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timestamp', 'Timestamp:') !!}
    {!! Form::date('timestamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('budgetTransferForms.index') !!}" class="btn btn-default">Cancel</a>
</div>
