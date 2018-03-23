<!-- Usergroupid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('userGroupID', 'Usergroupid:') !!}
    {!! Form::number('userGroupID', null, ['class' => 'form-control']) !!}
</div>

<!-- Companyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companyID', 'Companyid:') !!}
    {!! Form::text('companyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Navigationmenuid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('navigationMenuID', 'Navigationmenuid:') !!}
    {!! Form::number('navigationMenuID', null, ['class' => 'form-control']) !!}
</div>

<!-- Description Field -->
<div class="form-group col-sm-6">
    {!! Form::label('description', 'Description:') !!}
    {!! Form::text('description', null, ['class' => 'form-control']) !!}
</div>

<!-- Masterid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('masterID', 'Masterid:') !!}
    {!! Form::number('masterID', null, ['class' => 'form-control']) !!}
</div>

<!-- Url Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('url', 'Url:') !!}
    {!! Form::textarea('url', null, ['class' => 'form-control']) !!}
</div>

<!-- Pageid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('pageID', 'Pageid:') !!}
    {!! Form::text('pageID', null, ['class' => 'form-control']) !!}
</div>

<!-- Pagetitle Field -->
<div class="form-group col-sm-6">
    {!! Form::label('pageTitle', 'Pagetitle:') !!}
    {!! Form::text('pageTitle', null, ['class' => 'form-control']) !!}
</div>

<!-- Pageicon Field -->
<div class="form-group col-sm-6">
    {!! Form::label('pageIcon', 'Pageicon:') !!}
    {!! Form::text('pageIcon', null, ['class' => 'form-control']) !!}
</div>

<!-- Levelno Field -->
<div class="form-group col-sm-6">
    {!! Form::label('levelNo', 'Levelno:') !!}
    {!! Form::number('levelNo', null, ['class' => 'form-control']) !!}
</div>

<!-- Sortorder Field -->
<div class="form-group col-sm-6">
    {!! Form::label('sortOrder', 'Sortorder:') !!}
    {!! Form::number('sortOrder', null, ['class' => 'form-control']) !!}
</div>

<!-- Issubexist Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isSubExist', 'Issubexist:') !!}
    {!! Form::number('isSubExist', null, ['class' => 'form-control']) !!}
</div>

<!-- Readonly Field -->
<div class="form-group col-sm-6">
    {!! Form::label('readonly', 'Readonly:') !!}
    <label class="checkbox-inline">
        {!! Form::hidden('readonly', false) !!}
        {!! Form::checkbox('readonly', '1', null) !!} 1
    </label>
</div>

<!-- Create Field -->
<div class="form-group col-sm-6">
    {!! Form::label('create', 'Create:') !!}
    <label class="checkbox-inline">
        {!! Form::hidden('create', false) !!}
        {!! Form::checkbox('create', '1', null) !!} 1
    </label>
</div>

<!-- Update Field -->
<div class="form-group col-sm-6">
    {!! Form::label('update', 'Update:') !!}
    <label class="checkbox-inline">
        {!! Form::hidden('update', false) !!}
        {!! Form::checkbox('update', '1', null) !!} 1
    </label>
</div>

<!-- Delete Field -->
<div class="form-group col-sm-6">
    {!! Form::label('delete', 'Delete:') !!}
    <label class="checkbox-inline">
        {!! Form::hidden('delete', false) !!}
        {!! Form::checkbox('delete', '1', null) !!} 1
    </label>
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timestamp', 'Timestamp:') !!}
    {!! Form::date('timestamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('userGroupAssigns.index') !!}" class="btn btn-default">Cancel</a>
</div>
