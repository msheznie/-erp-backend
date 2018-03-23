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

<!-- Accountcode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('AccountCode', 'Accountcode:') !!}
    {!! Form::text('AccountCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Accountdescription Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('AccountDescription', 'Accountdescription:') !!}
    {!! Form::textarea('AccountDescription', null, ['class' => 'form-control']) !!}
</div>

<!-- Masteraccount Field -->
<div class="form-group col-sm-6">
    {!! Form::label('masterAccount', 'Masteraccount:') !!}
    {!! Form::text('masterAccount', null, ['class' => 'form-control']) !!}
</div>

<!-- Catogaryblorpl Field -->
<div class="form-group col-sm-6">
    {!! Form::label('catogaryBLorPL', 'Catogaryblorpl:') !!}
    {!! Form::text('catogaryBLorPL', null, ['class' => 'form-control']) !!}
</div>

<!-- Controllaccountyn Field -->
<div class="form-group col-sm-6">
    {!! Form::label('controllAccountYN', 'Controllaccountyn:') !!}
    {!! Form::number('controllAccountYN', null, ['class' => 'form-control']) !!}
</div>

<!-- Controlaccounts Field -->
<div class="form-group col-sm-6">
    {!! Form::label('controlAccounts', 'Controlaccounts:') !!}
    {!! Form::text('controlAccounts', null, ['class' => 'form-control']) !!}
</div>

<!-- Isapproved Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isApproved', 'Isapproved:') !!}
    {!! Form::number('isApproved', null, ['class' => 'form-control']) !!}
</div>

<!-- Approvedby Field -->
<div class="form-group col-sm-6">
    {!! Form::label('approvedBy', 'Approvedby:') !!}
    {!! Form::text('approvedBy', null, ['class' => 'form-control']) !!}
</div>

<!-- Approveddate Field -->
<div class="form-group col-sm-6">
    {!! Form::label('approvedDate', 'Approveddate:') !!}
    {!! Form::date('approvedDate', null, ['class' => 'form-control']) !!}
</div>

<!-- Approvedcomment Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('approvedComment', 'Approvedcomment:') !!}
    {!! Form::textarea('approvedComment', null, ['class' => 'form-control']) !!}
</div>

<!-- Isactive Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isActive', 'Isactive:') !!}
    {!! Form::number('isActive', null, ['class' => 'form-control']) !!}
</div>

<!-- Isbank Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isBank', 'Isbank:') !!}
    {!! Form::number('isBank', null, ['class' => 'form-control']) !!}
</div>

<!-- Allocationid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('AllocationID', 'Allocationid:') !!}
    {!! Form::number('AllocationID', null, ['class' => 'form-control']) !!}
</div>

<!-- Relatedpartyyn Field -->
<div class="form-group col-sm-6">
    {!! Form::label('relatedPartyYN', 'Relatedpartyyn:') !!}
    {!! Form::number('relatedPartyYN', null, ['class' => 'form-control']) !!}
</div>

<!-- Intercompanyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('interCompanyID', 'Intercompanyid:') !!}
    {!! Form::text('interCompanyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Createdpcid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdPcID', 'Createdpcid:') !!}
    {!! Form::text('createdPcID', null, ['class' => 'form-control']) !!}
</div>

<!-- Createdusergroup Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdUserGroup', 'Createdusergroup:') !!}
    {!! Form::text('createdUserGroup', null, ['class' => 'form-control']) !!}
</div>

<!-- Createduserid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdUserID', 'Createduserid:') !!}
    {!! Form::text('createdUserID', null, ['class' => 'form-control']) !!}
</div>

<!-- Createddatetime Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdDateTime', 'Createddatetime:') !!}
    {!! Form::date('createdDateTime', null, ['class' => 'form-control']) !!}
</div>

<!-- Modifiedpc Field -->
<div class="form-group col-sm-6">
    {!! Form::label('modifiedPc', 'Modifiedpc:') !!}
    {!! Form::text('modifiedPc', null, ['class' => 'form-control']) !!}
</div>

<!-- Modifieduser Field -->
<div class="form-group col-sm-6">
    {!! Form::label('modifiedUser', 'Modifieduser:') !!}
    {!! Form::text('modifiedUser', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timestamp', 'Timestamp:') !!}
    {!! Form::date('timestamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('chartOfAccounts.index') !!}" class="btn btn-default">Cancel</a>
</div>
