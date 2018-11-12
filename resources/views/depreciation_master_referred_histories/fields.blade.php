<!-- Depmasterautoid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('depMasterAutoID', 'Depmasterautoid:') !!}
    {!! Form::number('depMasterAutoID', null, ['class' => 'form-control']) !!}
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

<!-- Serialno Field -->
<div class="form-group col-sm-6">
    {!! Form::label('serialNo', 'Serialno:') !!}
    {!! Form::number('serialNo', null, ['class' => 'form-control']) !!}
</div>

<!-- Companyfinanceyearid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companyFinanceYearID', 'Companyfinanceyearid:') !!}
    {!! Form::number('companyFinanceYearID', null, ['class' => 'form-control']) !!}
</div>

<!-- Fybiggin Field -->
<div class="form-group col-sm-6">
    {!! Form::label('FYBiggin', 'Fybiggin:') !!}
    {!! Form::date('FYBiggin', null, ['class' => 'form-control']) !!}
</div>

<!-- Fyend Field -->
<div class="form-group col-sm-6">
    {!! Form::label('FYEnd', 'Fyend:') !!}
    {!! Form::date('FYEnd', null, ['class' => 'form-control']) !!}
</div>

<!-- Companyfinanceperiodid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companyFinancePeriodID', 'Companyfinanceperiodid:') !!}
    {!! Form::number('companyFinancePeriodID', null, ['class' => 'form-control']) !!}
</div>

<!-- Fyperioddatefrom Field -->
<div class="form-group col-sm-6">
    {!! Form::label('FYPeriodDateFrom', 'Fyperioddatefrom:') !!}
    {!! Form::date('FYPeriodDateFrom', null, ['class' => 'form-control']) !!}
</div>

<!-- Fyperioddateto Field -->
<div class="form-group col-sm-6">
    {!! Form::label('FYPeriodDateTo', 'Fyperioddateto:') !!}
    {!! Form::date('FYPeriodDateTo', null, ['class' => 'form-control']) !!}
</div>

<!-- Depcode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('depCode', 'Depcode:') !!}
    {!! Form::text('depCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Depdate Field -->
<div class="form-group col-sm-6">
    {!! Form::label('depDate', 'Depdate:') !!}
    {!! Form::date('depDate', null, ['class' => 'form-control']) !!}
</div>

<!-- Depmonthyear Field -->
<div class="form-group col-sm-6">
    {!! Form::label('depMonthYear', 'Depmonthyear:') !!}
    {!! Form::text('depMonthYear', null, ['class' => 'form-control']) !!}
</div>

<!-- Deplocalcur Field -->
<div class="form-group col-sm-6">
    {!! Form::label('depLocalCur', 'Deplocalcur:') !!}
    {!! Form::number('depLocalCur', null, ['class' => 'form-control']) !!}
</div>

<!-- Depamountlocal Field -->
<div class="form-group col-sm-6">
    {!! Form::label('depAmountLocal', 'Depamountlocal:') !!}
    {!! Form::number('depAmountLocal', null, ['class' => 'form-control']) !!}
</div>

<!-- Deprptcur Field -->
<div class="form-group col-sm-6">
    {!! Form::label('depRptCur', 'Deprptcur:') !!}
    {!! Form::number('depRptCur', null, ['class' => 'form-control']) !!}
</div>

<!-- Depamountrpt Field -->
<div class="form-group col-sm-6">
    {!! Form::label('depAmountRpt', 'Depamountrpt:') !!}
    {!! Form::number('depAmountRpt', null, ['class' => 'form-control']) !!}
</div>

<!-- Timesreferred Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timesReferred', 'Timesreferred:') !!}
    {!! Form::number('timesReferred', null, ['class' => 'form-control']) !!}
</div>

<!-- Refferedbackyn Field -->
<div class="form-group col-sm-6">
    {!! Form::label('refferedBackYN', 'Refferedbackyn:') !!}
    <label class="checkbox-inline">
        {!! Form::hidden('refferedBackYN', false) !!}
        {!! Form::checkbox('refferedBackYN', '1', null) !!} 1
    </label>
</div>

<!-- Rolllevforapp Curr Field -->
<div class="form-group col-sm-6">
    {!! Form::label('RollLevForApp_curr', 'Rolllevforapp Curr:') !!}
    <label class="checkbox-inline">
        {!! Form::hidden('RollLevForApp_curr', false) !!}
        {!! Form::checkbox('RollLevForApp_curr', '1', null) !!} 1
    </label>
</div>

<!-- Isdepprocessingyn Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isDepProcessingYN', 'Isdepprocessingyn:') !!}
    <label class="checkbox-inline">
        {!! Form::hidden('isDepProcessingYN', false) !!}
        {!! Form::checkbox('isDepProcessingYN', '1', null) !!} 1
    </label>
</div>

<!-- Confirmedyn Field -->
<div class="form-group col-sm-6">
    {!! Form::label('confirmedYN', 'Confirmedyn:') !!}
    {!! Form::number('confirmedYN', null, ['class' => 'form-control']) !!}
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

<!-- Confirmeddate Field -->
<div class="form-group col-sm-6">
    {!! Form::label('confirmedDate', 'Confirmeddate:') !!}
    {!! Form::date('confirmedDate', null, ['class' => 'form-control']) !!}
</div>

<!-- Approved Field -->
<div class="form-group col-sm-6">
    {!! Form::label('approved', 'Approved:') !!}
    {!! Form::number('approved', null, ['class' => 'form-control']) !!}
</div>

<!-- Approveddate Field -->
<div class="form-group col-sm-6">
    {!! Form::label('approvedDate', 'Approveddate:') !!}
    {!! Form::date('approvedDate', null, ['class' => 'form-control']) !!}
</div>

<!-- Approvedbyuserid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('approvedByUserID', 'Approvedbyuserid:') !!}
    {!! Form::text('approvedByUserID', null, ['class' => 'form-control']) !!}
</div>

<!-- Approvedbyusersystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('approvedByUserSystemID', 'Approvedbyusersystemid:') !!}
    {!! Form::number('approvedByUserSystemID', null, ['class' => 'form-control']) !!}
</div>

<!-- Createduserid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdUserID', 'Createduserid:') !!}
    {!! Form::text('createdUserID', null, ['class' => 'form-control']) !!}
</div>

<!-- Createdusersystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdUserSystemID', 'Createdusersystemid:') !!}
    {!! Form::number('createdUserSystemID', null, ['class' => 'form-control']) !!}
</div>

<!-- Createdpcid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdPCID', 'Createdpcid:') !!}
    {!! Form::text('createdPCID', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timeStamp', 'Timestamp:') !!}
    {!! Form::date('timeStamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('depreciationMasterReferredHistories.index') !!}" class="btn btn-default">Cancel</a>
</div>
