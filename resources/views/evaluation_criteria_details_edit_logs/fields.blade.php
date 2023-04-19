<!-- Answer Type Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('answer_type_id', 'Answer Type Id:') !!}
    {!! Form::number('answer_type_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Critera Type Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('critera_type_id', 'Critera Type Id:') !!}
    {!! Form::number('critera_type_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Description Field -->
<div class="form-group col-sm-6">
    {!! Form::label('description', 'Description:') !!}
    {!! Form::text('description', null, ['class' => 'form-control']) !!}
</div>

<!-- Is Final Level Field -->
<div class="form-group col-sm-6">
    {!! Form::label('is_final_level', 'Is Final Level:') !!}
    {!! Form::number('is_final_level', null, ['class' => 'form-control']) !!}
</div>

<!-- Level Field -->
<div class="form-group col-sm-6">
    {!! Form::label('level', 'Level:') !!}
    {!! Form::number('level', null, ['class' => 'form-control']) !!}
</div>

<!-- Master Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('master_id', 'Master Id:') !!}
    {!! Form::number('master_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Max Value Field -->
<div class="form-group col-sm-6">
    {!! Form::label('max_value', 'Max Value:') !!}
    {!! Form::number('max_value', null, ['class' => 'form-control']) !!}
</div>

<!-- Min Value Field -->
<div class="form-group col-sm-6">
    {!! Form::label('min_value', 'Min Value:') !!}
    {!! Form::number('min_value', null, ['class' => 'form-control']) !!}
</div>

<!-- Modify Type Field -->
<div class="form-group col-sm-6">
    {!! Form::label('modify_type', 'Modify Type:') !!}
    {!! Form::number('modify_type', null, ['class' => 'form-control']) !!}
</div>

<!-- Parent Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('parent_id', 'Parent Id:') !!}
    {!! Form::number('parent_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Passing Weightage Field -->
<div class="form-group col-sm-6">
    {!! Form::label('passing_weightage', 'Passing Weightage:') !!}
    {!! Form::number('passing_weightage', null, ['class' => 'form-control']) !!}
</div>

<!-- Ref Log Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('ref_log_id', 'Ref Log Id:') !!}
    {!! Form::number('ref_log_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Sort Order Field -->
<div class="form-group col-sm-6">
    {!! Form::label('sort_order', 'Sort Order:') !!}
    {!! Form::number('sort_order', null, ['class' => 'form-control']) !!}
</div>

<!-- Tender Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('tender_id', 'Tender Id:') !!}
    {!! Form::number('tender_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Weightage Field -->
<div class="form-group col-sm-6">
    {!! Form::label('weightage', 'Weightage:') !!}
    {!! Form::number('weightage', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{{ route('evaluationCriteriaDetailsEditLogs.index') }}" class="btn btn-default">Cancel</a>
</div>
