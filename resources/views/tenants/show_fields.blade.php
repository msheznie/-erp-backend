<!-- Id Field -->
<div class="form-group">
    {!! Form::label('id', 'Id:') !!}
    <p>{{ $tenant->id }}</p>
</div>

<!-- Name Field -->
<div class="form-group">
    {!! Form::label('name', 'Name:') !!}
    <p>{{ $tenant->name }}</p>
</div>

<!-- Sub Domain Field -->
<div class="form-group">
    {!! Form::label('sub_domain', 'Sub Domain:') !!}
    <p>{{ $tenant->sub_domain }}</p>
</div>

<!-- Database Field -->
<div class="form-group">
    {!! Form::label('database', 'Database:') !!}
    <p>{{ $tenant->database }}</p>
</div>

<!-- Created At Field -->
<div class="form-group">
    {!! Form::label('created_at', 'Created At:') !!}
    <p>{{ $tenant->created_at }}</p>
</div>

<!-- Updated At Field -->
<div class="form-group">
    {!! Form::label('updated_at', 'Updated At:') !!}
    <p>{{ $tenant->updated_at }}</p>
</div>

