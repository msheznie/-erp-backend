<!-- Id Field -->
<div class="form-group">
    {!! Form::label('id', 'Id:') !!}
    <p>{{ $poCategory->id }}</p>
</div>

<!-- Description Field -->
<div class="form-group">
    {!! Form::label('description', 'Description:') !!}
    <p>{{ $poCategory->description }}</p>
</div>

<!-- Isactive Field -->
<div class="form-group">
    {!! Form::label('isActive', 'Isactive:') !!}
    <p>{{ $poCategory->isActive }}</p>
</div>

<!-- Isdefault Field -->
<div class="form-group">
    {!! Form::label('isDefault', 'Isdefault:') !!}
    <p>{{ $poCategory->isDefault }}</p>
</div>

<!-- Createddatetime Field -->
<div class="form-group">
    {!! Form::label('createdDateTime', 'Createddatetime:') !!}
    <p>{{ $poCategory->createdDateTime }}</p>
</div>

