<!-- Id Field -->
<div class="form-group">
    {!! Form::label('id', 'Id:') !!}
    <p>{{ $thirdPartyIntegrationKeys->id }}</p>
</div>

<!-- Company Id Field -->
<div class="form-group">
    {!! Form::label('company_id', 'Company Id:') !!}
    <p>{{ $thirdPartyIntegrationKeys->company_id }}</p>
</div>

<!-- Third Party System Id Field -->
<div class="form-group">
    {!! Form::label('third_party_system_id', 'Third Party System Id:') !!}
    <p>{{ $thirdPartyIntegrationKeys->third_party_system_id }}</p>
</div>

<!-- Api Key Field -->
<div class="form-group">
    {!! Form::label('api_key', 'Api Key:') !!}
    <p>{{ $thirdPartyIntegrationKeys->api_key }}</p>
</div>

<!-- Created At Field -->
<div class="form-group">
    {!! Form::label('created_at', 'Created At:') !!}
    <p>{{ $thirdPartyIntegrationKeys->created_at }}</p>
</div>

<!-- Updated At Field -->
<div class="form-group">
    {!! Form::label('updated_at', 'Updated At:') !!}
    <p>{{ $thirdPartyIntegrationKeys->updated_at }}</p>
</div>

