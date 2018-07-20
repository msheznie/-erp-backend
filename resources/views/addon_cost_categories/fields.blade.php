<!-- Costcatdes Field -->
<div class="form-group col-sm-6">
    {!! Form::label('costCatDes', 'Costcatdes:') !!}
    {!! Form::text('costCatDes', null, ['class' => 'form-control']) !!}
</div>

<!-- Glcode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('glCode', 'Glcode:') !!}
    {!! Form::text('glCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Timesstamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timesStamp', 'Timesstamp:') !!}
    {!! Form::date('timesStamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('addonCostCategories.index') !!}" class="btn btn-default">Cancel</a>
</div>
