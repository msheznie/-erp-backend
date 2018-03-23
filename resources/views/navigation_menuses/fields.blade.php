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

<!-- Languageid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('languageID', 'Languageid:') !!}
    {!! Form::number('languageID', null, ['class' => 'form-control']) !!}
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

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timestamp', 'Timestamp:') !!}
    {!! Form::date('timestamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Isaddon Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isAddon', 'Isaddon:') !!}
    {!! Form::number('isAddon', null, ['class' => 'form-control']) !!}
</div>

<!-- Addondescription Field -->
<div class="form-group col-sm-6">
    {!! Form::label('addonDescription', 'Addondescription:') !!}
    {!! Form::text('addonDescription', null, ['class' => 'form-control']) !!}
</div>

<!-- Addondetails Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('addonDetails', 'Addondetails:') !!}
    {!! Form::textarea('addonDetails', null, ['class' => 'form-control']) !!}
</div>

<!-- Iscoremodule Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isCoreModule', 'Iscoremodule:') !!}
    {!! Form::number('isCoreModule', null, ['class' => 'form-control']) !!}
</div>

<!-- Isgroup Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isGroup', 'Isgroup:') !!}
    {!! Form::number('isGroup', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('navigationMenuses.index') !!}" class="btn btn-default">Cancel</a>
</div>
