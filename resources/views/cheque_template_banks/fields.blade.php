<!-- Cheque Template Master Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('cheque_template_master_id', 'Cheque Template Master Id:') !!}
    {!! Form::number('cheque_template_master_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Bank Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('bank_id', 'Bank Id:') !!}
    {!! Form::number('bank_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{{ route('chequeTemplateBanks.index') }}" class="btn btn-default">Cancel</a>
</div>
