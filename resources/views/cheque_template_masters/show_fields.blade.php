<!-- Id Field -->
<div class="form-group">
    {!! Form::label('id', 'Id:') !!}
    <p>{{ $chequeTemplateMaster->id }}</p>
</div>

<!-- Description Field -->
<div class="form-group">
    {!! Form::label('description', 'Description:') !!}
    <p>{{ $chequeTemplateMaster->description }}</p>
</div>

<!-- View Name Field -->
<div class="form-group">
    {!! Form::label('view_name', 'View Name:') !!}
    <p>{{ $chequeTemplateMaster->view_name }}</p>
</div>

<!-- Is Active Field -->
<div class="form-group">
    {!! Form::label('is_active', 'Is Active:') !!}
    <p>{{ $chequeTemplateMaster->is_active }}</p>
</div>

<!-- Created At Field -->
<div class="form-group">
    {!! Form::label('created_at', 'Created At:') !!}
    <p>{{ $chequeTemplateMaster->created_at }}</p>
</div>

<!-- Updated At Field -->
<div class="form-group">
    {!! Form::label('updated_at', 'Updated At:') !!}
    <p>{{ $chequeTemplateMaster->updated_at }}</p>
</div>

