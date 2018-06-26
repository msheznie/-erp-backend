<!-- Id Field -->
<div class="form-group">
    {!! Form::label('id', 'Id:') !!}
    <p>{!! $accessTokens->id !!}</p>
</div>

<!-- User Id Field -->
<div class="form-group">
    {!! Form::label('user_id', 'User Id:') !!}
    <p>{!! $accessTokens->user_id !!}</p>
</div>

<!-- Client Id Field -->
<div class="form-group">
    {!! Form::label('client_id', 'Client Id:') !!}
    <p>{!! $accessTokens->client_id !!}</p>
</div>

<!-- Name Field -->
<div class="form-group">
    {!! Form::label('name', 'Name:') !!}
    <p>{!! $accessTokens->name !!}</p>
</div>

<!-- Scopes Field -->
<div class="form-group">
    {!! Form::label('scopes', 'Scopes:') !!}
    <p>{!! $accessTokens->scopes !!}</p>
</div>

<!-- Revoked Field -->
<div class="form-group">
    {!! Form::label('revoked', 'Revoked:') !!}
    <p>{!! $accessTokens->revoked !!}</p>
</div>

<!-- Created At Field -->
<div class="form-group">
    {!! Form::label('created_at', 'Created At:') !!}
    <p>{!! $accessTokens->created_at !!}</p>
</div>

<!-- Updated At Field -->
<div class="form-group">
    {!! Form::label('updated_at', 'Updated At:') !!}
    <p>{!! $accessTokens->updated_at !!}</p>
</div>

<!-- Expires At Field -->
<div class="form-group">
    {!! Form::label('expires_at', 'Expires At:') !!}
    <p>{!! $accessTokens->expires_at !!}</p>
</div>

