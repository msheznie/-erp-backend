<!-- Bankmemoheader Field -->
<div class="form-group col-sm-6">
    {!! Form::label('bankMemoHeader', 'Bankmemoheader:') !!}
    {!! Form::text('bankMemoHeader', null, ['class' => 'form-control']) !!}
</div>

<!-- Sortorder Field -->
<div class="form-group col-sm-6">
    {!! Form::label('sortOrder', 'Sortorder:') !!}
    {!! Form::number('sortOrder', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('bankMemoTypes.index') !!}" class="btn btn-default">Cancel</a>
</div>
