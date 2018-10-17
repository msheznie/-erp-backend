<!-- Templatedescription Field -->
<div class="form-group col-sm-6">
    {!! Form::label('templateDescription', 'Templatedescription:') !!}
    {!! Form::text('templateDescription', null, ['class' => 'form-control']) !!}
</div>

<!-- Templatetype Field -->
<div class="form-group col-sm-6">
    {!! Form::label('templateType', 'Templatetype:') !!}
    {!! Form::text('templateType', null, ['class' => 'form-control']) !!}
</div>

<!-- Templatereportname Field -->
<div class="form-group col-sm-6">
    {!! Form::label('templateReportName', 'Templatereportname:') !!}
    {!! Form::text('templateReportName', null, ['class' => 'form-control']) !!}
</div>

<!-- Isactive Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isActive', 'Isactive:') !!}
    {!! Form::number('isActive', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timestamp', 'Timestamp:') !!}
    {!! Form::date('timestamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('templatesMasters.index') !!}" class="btn btn-default">Cancel</a>
</div>
