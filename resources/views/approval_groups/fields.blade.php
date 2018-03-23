<!-- Rightsgroupdes Field -->
<div class="form-group col-sm-6">
    {!! Form::label('rightsGroupDes', 'Rightsgroupdes:') !!}
    {!! Form::text('rightsGroupDes', null, ['class' => 'form-control']) !!}
</div>

<!-- Isformsassigned Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isFormsAssigned', 'Isformsassigned:') !!}
    {!! Form::number('isFormsAssigned', null, ['class' => 'form-control']) !!}
</div>

<!-- Documentid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('documentID', 'Documentid:') !!}
    {!! Form::text('documentID', null, ['class' => 'form-control']) !!}
</div>

<!-- Departmentid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('departmentID', 'Departmentid:') !!}
    {!! Form::text('departmentID', null, ['class' => 'form-control']) !!}
</div>

<!-- Condition Field -->
<div class="form-group col-sm-6">
    {!! Form::label('condition', 'Condition:') !!}
    {!! Form::text('condition', null, ['class' => 'form-control']) !!}
</div>

<!-- Sortorder Field -->
<div class="form-group col-sm-6">
    {!! Form::label('sortOrder', 'Sortorder:') !!}
    {!! Form::number('sortOrder', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timestamp', 'Timestamp:') !!}
    {!! Form::date('timestamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('approvalGroups.index') !!}" class="btn btn-default">Cancel</a>
</div>
