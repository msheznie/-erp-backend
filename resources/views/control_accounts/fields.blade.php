<!-- Controlaccountcode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('controlAccountCode', 'Controlaccountcode:') !!}
    {!! Form::text('controlAccountCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Description Field -->
<div class="form-group col-sm-6">
    {!! Form::label('description', 'Description:') !!}
    {!! Form::text('description', null, ['class' => 'form-control']) !!}
</div>

<!-- Itemledgershymbol Field -->
<div class="form-group col-sm-6">
    {!! Form::label('itemLedgerShymbol', 'Itemledgershymbol:') !!}
    {!! Form::text('itemLedgerShymbol', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timeStamp', 'Timestamp:') !!}
    {!! Form::date('timeStamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('controlAccounts.index') !!}" class="btn btn-default">Cancel</a>
</div>
