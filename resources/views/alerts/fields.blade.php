<!-- Companyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companyID', 'Companyid:') !!}
    {!! Form::text('companyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Empid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('empID', 'Empid:') !!}
    {!! Form::text('empID', null, ['class' => 'form-control']) !!}
</div>

<!-- Docid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('docID', 'Docid:') !!}
    {!! Form::text('docID', null, ['class' => 'form-control']) !!}
</div>

<!-- Docapprovedyn Field -->
<div class="form-group col-sm-6">
    {!! Form::label('docApprovedYN', 'Docapprovedyn:') !!}
    {!! Form::number('docApprovedYN', null, ['class' => 'form-control']) !!}
</div>

<!-- Docsystemcode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('docSystemCode', 'Docsystemcode:') !!}
    {!! Form::number('docSystemCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Doccode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('docCode', 'Doccode:') !!}
    {!! Form::text('docCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Alertmessage Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('alertMessage', 'Alertmessage:') !!}
    {!! Form::textarea('alertMessage', null, ['class' => 'form-control']) !!}
</div>

<!-- Alertdatetime Field -->
<div class="form-group col-sm-6">
    {!! Form::label('alertDateTime', 'Alertdatetime:') !!}
    {!! Form::date('alertDateTime', null, ['class' => 'form-control']) !!}
</div>

<!-- Alertviewedyn Field -->
<div class="form-group col-sm-6">
    {!! Form::label('alertViewedYN', 'Alertviewedyn:') !!}
    {!! Form::number('alertViewedYN', null, ['class' => 'form-control']) !!}
</div>

<!-- Alertvieweddatetime Field -->
<div class="form-group col-sm-6">
    {!! Form::label('alertViewedDateTime', 'Alertvieweddatetime:') !!}
    {!! Form::date('alertViewedDateTime', null, ['class' => 'form-control']) !!}
</div>

<!-- Empname Field -->
<div class="form-group col-sm-6">
    {!! Form::label('empName', 'Empname:') !!}
    {!! Form::text('empName', null, ['class' => 'form-control']) !!}
</div>

<!-- Empemail Field -->
<div class="form-group col-sm-6">
    {!! Form::label('empEmail', 'Empemail:') !!}
    {!! Form::text('empEmail', null, ['class' => 'form-control']) !!}
</div>

<!-- Ccemailid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('ccEmailID', 'Ccemailid:') !!}
    {!! Form::text('ccEmailID', null, ['class' => 'form-control']) !!}
</div>

<!-- Emailalertmessage Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('emailAlertMessage', 'Emailalertmessage:') !!}
    {!! Form::textarea('emailAlertMessage', null, ['class' => 'form-control']) !!}
</div>

<!-- Isemailsend Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isEmailSend', 'Isemailsend:') !!}
    {!! Form::number('isEmailSend', null, ['class' => 'form-control']) !!}
</div>

<!-- Attachmentfilename Field -->
<div class="form-group col-sm-6">
    {!! Form::label('attachmentFileName', 'Attachmentfilename:') !!}
    {!! Form::text('attachmentFileName', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timeStamp', 'Timestamp:') !!}
    {!! Form::date('timeStamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('alerts.index') !!}" class="btn btn-default">Cancel</a>
</div>
