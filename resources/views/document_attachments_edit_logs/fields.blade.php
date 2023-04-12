<!-- Approvallevelorder Field -->
<div class="form-group col-sm-6">
    {!! Form::label('approvalLevelOrder', 'Approvallevelorder:') !!}
    {!! Form::number('approvalLevelOrder', null, ['class' => 'form-control']) !!}
</div>

<!-- Attachmentdescription Field -->
<div class="form-group col-sm-6">
    {!! Form::label('attachmentDescription', 'Attachmentdescription:') !!}
    {!! Form::text('attachmentDescription', null, ['class' => 'form-control']) !!}
</div>

<!-- Attachmenttype Field -->
<div class="form-group col-sm-6">
    {!! Form::label('attachmentType', 'Attachmenttype:') !!}
    {!! Form::number('attachmentType', null, ['class' => 'form-control']) !!}
</div>

<!-- Companysystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companySystemID', 'Companysystemid:') !!}
    {!! Form::number('companySystemID', null, ['class' => 'form-control']) !!}
</div>

<!-- Docexpirtydate Field -->
<div class="form-group col-sm-6">
    {!! Form::label('docExpirtyDate', 'Docexpirtydate:') !!}
    {!! Form::date('docExpirtyDate', null, ['class' => 'form-control','id'=>'docExpirtyDate']) !!}
</div>

@section('scripts')
    <script type="text/javascript">
        $('#docExpirtyDate').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            useCurrent: false
        })
    </script>
@endsection

<!-- Documentid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('documentID', 'Documentid:') !!}
    {!! Form::text('documentID', null, ['class' => 'form-control']) !!}
</div>

<!-- Documentsystemcode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('documentSystemCode', 'Documentsystemcode:') !!}
    {!! Form::number('documentSystemCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Documentsystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('documentSystemID', 'Documentsystemid:') !!}
    {!! Form::number('documentSystemID', null, ['class' => 'form-control']) !!}
</div>

<!-- Enveloptype Field -->
<div class="form-group col-sm-6">
    {!! Form::label('envelopType', 'Enveloptype:') !!}
    {!! Form::number('envelopType', null, ['class' => 'form-control']) !!}
</div>

<!-- Isuploaded Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isUploaded', 'Isuploaded:') !!}
    {!! Form::number('isUploaded', null, ['class' => 'form-control']) !!}
</div>

<!-- Master Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('master_id', 'Master Id:') !!}
    {!! Form::number('master_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Modify Type Field -->
<div class="form-group col-sm-6">
    {!! Form::label('modify_type', 'Modify Type:') !!}
    {!! Form::number('modify_type', null, ['class' => 'form-control']) !!}
</div>

<!-- Myfilename Field -->
<div class="form-group col-sm-6">
    {!! Form::label('myFileName', 'Myfilename:') !!}
    {!! Form::text('myFileName', null, ['class' => 'form-control']) !!}
</div>

<!-- Originalfilename Field -->
<div class="form-group col-sm-6">
    {!! Form::label('originalFileName', 'Originalfilename:') !!}
    {!! Form::text('originalFileName', null, ['class' => 'form-control']) !!}
</div>

<!-- Parent Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('parent_id', 'Parent Id:') !!}
    {!! Form::number('parent_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Path Field -->
<div class="form-group col-sm-6">
    {!! Form::label('path', 'Path:') !!}
    {!! Form::text('path', null, ['class' => 'form-control']) !!}
</div>

<!-- Pullfromanotherdocument Field -->
<div class="form-group col-sm-6">
    {!! Form::label('pullFromAnotherDocument', 'Pullfromanotherdocument:') !!}
    {!! Form::number('pullFromAnotherDocument', null, ['class' => 'form-control']) !!}
</div>

<!-- Ref Log Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('ref_log_id', 'Ref Log Id:') !!}
    {!! Form::number('ref_log_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Sizeinkbs Field -->
<div class="form-group col-sm-6">
    {!! Form::label('sizeInKbs', 'Sizeinkbs:') !!}
    {!! Form::number('sizeInKbs', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{{ route('documentAttachmentsEditLogs.index') }}" class="btn btn-default">Cancel</a>
</div>
