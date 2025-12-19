<!-- Id Field -->
<div class="form-group">
    {!! Form::label('id', 'Id:') !!}
    <p>{{ $chequeTemplateBank->id }}</p>
</div>

<!-- Cheque Template Master Id Field -->
<div class="form-group">
    {!! Form::label('cheque_template_master_id', 'Cheque Template Master Id:') !!}
    <p>{{ $chequeTemplateBank->cheque_template_master_id }}</p>
</div>

<!-- Bank Id Field -->
<div class="form-group">
    {!! Form::label('bank_id', 'Bank Id:') !!}
    <p>{{ $chequeTemplateBank->bank_id }}</p>
</div>

<!-- Created At Field -->
<div class="form-group">
    {!! Form::label('created_at', 'Created At:') !!}
    <p>{{ $chequeTemplateBank->created_at }}</p>
</div>

<!-- Updated At Field -->
<div class="form-group">
    {!! Form::label('updated_at', 'Updated At:') !!}
    <p>{{ $chequeTemplateBank->updated_at }}</p>
</div>

