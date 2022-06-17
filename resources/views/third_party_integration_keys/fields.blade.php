<!-- Company Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('company_id', 'Company Id:') !!}
    {!! Form::number('company_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Third Party System Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('third_party_system_id', 'Third Party System Id:') !!}
    {!! Form::number('third_party_system_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Api Key Field -->
<div class="form-group col-sm-6">
    {!! Form::label('api_key', 'Api Key:') !!}
    {!! Form::text('api_key', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{{ route('thirdPartyIntegrationKeys.index') }}" class="btn btn-default">Cancel</a>
</div>
