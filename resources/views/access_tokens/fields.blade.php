<!-- User Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('user_id', 'User Id:') !!}
    {!! Form::number('user_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Client Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('client_id', 'Client Id:') !!}
    {!! Form::number('client_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Name Field -->
<div class="form-group col-sm-6">
    {!! Form::label('name', 'Name:') !!}
    {!! Form::text('name', null, ['class' => 'form-control']) !!}
</div>

<!-- Scopes Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('scopes', 'Scopes:') !!}
    {!! Form::textarea('scopes', null, ['class' => 'form-control']) !!}
</div>

<!-- Revoked Field -->
<div class="form-group col-sm-6">
    {!! Form::label('revoked', 'Revoked:') !!}
    <label class="checkbox-inline">
        {!! Form::hidden('revoked', false) !!}
        {!! Form::checkbox('revoked', '1', null) !!} 1
    </label>
</div>

<!-- Expires At Field -->
<div class="form-group col-sm-6">
    {!! Form::label('expires_at', 'Expires At:') !!}
    {!! Form::date('expires_at', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('accessTokens.index') !!}" class="btn btn-default">Cancel</a>
</div>
