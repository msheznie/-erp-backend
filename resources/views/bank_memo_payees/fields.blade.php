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

<!-- Documentsystemcode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('documentSystemCode', 'Documentsystemcode:') !!}
    {!! Form::number('documentSystemCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Bankmemotypeid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('bankMemoTypeID', 'Bankmemotypeid:') !!}
    {!! Form::number('bankMemoTypeID', null, ['class' => 'form-control']) !!}
</div>

<!-- Memoheader Field -->
<div class="form-group col-sm-6">
    {!! Form::label('memoHeader', 'Memoheader:') !!}
    {!! Form::text('memoHeader', null, ['class' => 'form-control']) !!}
</div>

<!-- Memodetail Field -->
<div class="form-group col-sm-6">
    {!! Form::label('memoDetail', 'Memodetail:') !!}
    {!! Form::text('memoDetail', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timestamp', 'Timestamp:') !!}
    {!! Form::date('timestamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('bankMemoPayees.index') !!}" class="btn btn-default">Cancel</a>
</div>
