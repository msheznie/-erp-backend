<!-- Supplierid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('supplierID', 'Supplierid:') !!}
    {!! Form::number('supplierID', null, ['class' => 'form-control']) !!}
</div>

<!-- Contacttypeid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('contactTypeID', 'Contacttypeid:') !!}
    {!! Form::number('contactTypeID', null, ['class' => 'form-control']) !!}
</div>

<!-- Contactpersonname Field -->
<div class="form-group col-sm-6">
    {!! Form::label('contactPersonName', 'Contactpersonname:') !!}
    {!! Form::text('contactPersonName', null, ['class' => 'form-control']) !!}
</div>

<!-- Contactpersontelephone Field -->
<div class="form-group col-sm-6">
    {!! Form::label('contactPersonTelephone', 'Contactpersontelephone:') !!}
    {!! Form::text('contactPersonTelephone', null, ['class' => 'form-control']) !!}
</div>

<!-- Contactpersonfax Field -->
<div class="form-group col-sm-6">
    {!! Form::label('contactPersonFax', 'Contactpersonfax:') !!}
    {!! Form::text('contactPersonFax', null, ['class' => 'form-control']) !!}
</div>

<!-- Contactpersonemail Field -->
<div class="form-group col-sm-6">
    {!! Form::label('contactPersonEmail', 'Contactpersonemail:') !!}
    {!! Form::text('contactPersonEmail', null, ['class' => 'form-control']) !!}
</div>

<!-- Isdefault Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isDefault', 'Isdefault:') !!}
    {!! Form::number('isDefault', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timestamp', 'Timestamp:') !!}
    {!! Form::date('timestamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('supplierContactDetails.index') !!}" class="btn btn-default">Cancel</a>
</div>
