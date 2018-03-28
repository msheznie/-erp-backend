<!-- Chartofaccountsystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('chartOfAccountSystemID', 'Chartofaccountsystemid:') !!}
    {!! Form::number('chartOfAccountSystemID', null, ['class' => 'form-control']) !!}
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

<!-- Catogaryblorplid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('catogaryBLorPLID', 'Catogaryblorplid:') !!}
    {!! Form::number('catogaryBLorPLID', null, ['class' => 'form-control']) !!}
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

<!-- Controlaccountssystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('controlAccountsSystemID', 'Controlaccountssystemid:') !!}
    {!! Form::number('controlAccountsSystemID', null, ['class' => 'form-control']) !!}
</div>

<!-- Controlaccounts Field -->
<div class="form-group col-sm-6">
    {!! Form::label('controlAccounts', 'Controlaccounts:') !!}
    {!! Form::text('controlAccounts', null, ['class' => 'form-control']) !!}
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

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timeStamp', 'Timestamp:') !!}
    {!! Form::date('timeStamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('chartOfAccountsAssigneds.index') !!}" class="btn btn-default">Cancel</a>
</div>
