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

<!-- Documentdate Field -->
<div class="form-group col-sm-6">
    {!! Form::label('documentDate', 'Documentdate:') !!}
    {!! Form::date('documentDate', null, ['class' => 'form-control']) !!}
</div>

<!-- Companyfinanceyearid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companyFinanceYearID', 'Companyfinanceyearid:') !!}
    {!! Form::number('companyFinanceYearID', null, ['class' => 'form-control']) !!}
</div>

<!-- Serialno Field -->
<div class="form-group col-sm-6">
    {!! Form::label('serialNo', 'Serialno:') !!}
    {!! Form::number('serialNo', null, ['class' => 'form-control']) !!}
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

<!-- Narration Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('narration', 'Narration:') !!}
    {!! Form::textarea('narration', null, ['class' => 'form-control']) !!}
</div>

<!-- Allocationtypeid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('allocationTypeID', 'Allocationtypeid:') !!}
    <label class="checkbox-inline">
        {!! Form::hidden('allocationTypeID', false) !!}
        {!! Form::checkbox('allocationTypeID', '1', null) !!} 1
    </label>
</div>

<!-- Facatid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('faCatID', 'Facatid:') !!}
    {!! Form::number('faCatID', null, ['class' => 'form-control']) !!}
</div>

<!-- Faid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('faID', 'Faid:') !!}
    {!! Form::number('faID', null, ['class' => 'form-control']) !!}
</div>

<!-- Assetnbvlocal Field -->
<div class="form-group col-sm-6">
    {!! Form::label('assetNBVLocal', 'Assetnbvlocal:') !!}
    {!! Form::number('assetNBVLocal', null, ['class' => 'form-control']) !!}
</div>

<!-- Assetnbvrpt Field -->
<div class="form-group col-sm-6">
    {!! Form::label('assetNBVRpt', 'Assetnbvrpt:') !!}
    {!! Form::number('assetNBVRpt', null, ['class' => 'form-control']) !!}
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

<!-- Confirmedbyname Field -->
<div class="form-group col-sm-6">
    {!! Form::label('confirmedByName', 'Confirmedbyname:') !!}
    {!! Form::text('confirmedByName', null, ['class' => 'form-control']) !!}
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

<!-- Createdusergroup Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdUserGroup', 'Createdusergroup:') !!}
    {!! Form::text('createdUserGroup', null, ['class' => 'form-control']) !!}
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

<!-- Modifiedusersystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('modifiedUserSystemID', 'Modifiedusersystemid:') !!}
    {!! Form::number('modifiedUserSystemID', null, ['class' => 'form-control']) !!}
</div>

<!-- Modifieduser Field -->
<div class="form-group col-sm-6">
    {!! Form::label('modifiedUser', 'Modifieduser:') !!}
    {!! Form::text('modifiedUser', null, ['class' => 'form-control']) !!}
</div>

<!-- Modifiedpc Field -->
<div class="form-group col-sm-6">
    {!! Form::label('modifiedPc', 'Modifiedpc:') !!}
    {!! Form::text('modifiedPc', null, ['class' => 'form-control']) !!}
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
    <a href="{!! route('assetCapitalizations.index') !!}" class="btn btn-default">Cancel</a>
</div>
