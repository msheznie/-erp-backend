<!-- Description Field -->
<div class="form-group col-sm-6">
    {!! Form::label('description', 'Description:') !!}
    {!! Form::text('description', null, ['class' => 'form-control']) !!}
</div>

<!-- Glaccounttype Field -->
<div class="form-group col-sm-6">
    {!! Form::label('glAccountType', 'Glaccounttype:') !!}
    {!! Form::number('glAccountType', null, ['class' => 'form-control']) !!}
</div>

<!-- Querystring Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('queryString', 'Querystring:') !!}
    {!! Form::textarea('queryString', null, ['class' => 'form-control']) !!}
</div>

<!-- Image Field -->
<div class="form-group col-sm-6">
    {!! Form::label('image', 'Image:') !!}
    {!! Form::text('image', null, ['class' => 'form-control']) !!}
</div>

<!-- Isactive Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isActive', 'Isactive:') !!}
    {!! Form::number('isActive', null, ['class' => 'form-control']) !!}
</div>

<!-- Sortorder Field -->
<div class="form-group col-sm-6">
    {!! Form::label('sortOrder', 'Sortorder:') !!}
    {!! Form::number('sortOrder', null, ['class' => 'form-control']) !!}
</div>

<!-- Selectboxname Field -->
<div class="form-group col-sm-6">
    {!! Form::label('selectBoxName', 'Selectboxname:') !!}
    {!! Form::text('selectBoxName', null, ['class' => 'form-control']) !!}
</div>

<!-- Timesstamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timesstamp', 'Timesstamp:') !!}
    {!! Form::date('timesstamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('gposPaymentGlConfigMasters.index') !!}" class="btn btn-default">Cancel</a>
</div>
