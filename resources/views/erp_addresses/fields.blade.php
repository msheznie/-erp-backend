<!-- Companyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companyID', 'Companyid:') !!}
    {!! Form::text('companyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Locationid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('locationID', 'Locationid:') !!}
    {!! Form::number('locationID', null, ['class' => 'form-control']) !!}
</div>

<!-- Departmentid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('departmentID', 'Departmentid:') !!}
    {!! Form::text('departmentID', null, ['class' => 'form-control']) !!}
</div>

<!-- Addresstypeid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('addressTypeID', 'Addresstypeid:') !!}
    {!! Form::number('addressTypeID', null, ['class' => 'form-control']) !!}
</div>

<!-- Addressdescrption Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('addressDescrption', 'Addressdescrption:') !!}
    {!! Form::textarea('addressDescrption', null, ['class' => 'form-control']) !!}
</div>

<!-- Contactpersonid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('contactPersonID', 'Contactpersonid:') !!}
    {!! Form::text('contactPersonID', null, ['class' => 'form-control']) !!}
</div>

<!-- Contactpersontelephone Field -->
<div class="form-group col-sm-6">
    {!! Form::label('contactPersonTelephone', 'Contactpersontelephone:') !!}
    {!! Form::text('contactPersonTelephone', null, ['class' => 'form-control']) !!}
</div>

<!-- Contactpersonfaxno Field -->
<div class="form-group col-sm-6">
    {!! Form::label('contactPersonFaxNo', 'Contactpersonfaxno:') !!}
    {!! Form::text('contactPersonFaxNo', null, ['class' => 'form-control']) !!}
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
    {!! Form::label('timeStamp', 'Timestamp:') !!}
    {!! Form::date('timeStamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('erpAddresses.index') !!}" class="btn btn-default">Cancel</a>
</div>
