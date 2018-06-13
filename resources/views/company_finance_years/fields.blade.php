<!-- Companysystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companySystemID', 'Companysystemid:') !!}
    {!! Form::number('companySystemID', null, ['class' => 'form-control']) !!}
</div>

<!-- Companyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companyID', 'Companyid:') !!}
    {!! Form::text('companyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Bigginingdate Field -->
<div class="form-group col-sm-6">
    {!! Form::label('bigginingDate', 'Bigginingdate:') !!}
    {!! Form::date('bigginingDate', null, ['class' => 'form-control']) !!}
</div>

<!-- Endingdate Field -->
<div class="form-group col-sm-6">
    {!! Form::label('endingDate', 'Endingdate:') !!}
    {!! Form::date('endingDate', null, ['class' => 'form-control']) !!}
</div>

<!-- Isactive Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isActive', 'Isactive:') !!}
    {!! Form::number('isActive', null, ['class' => 'form-control']) !!}
</div>

<!-- Iscurrent Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isCurrent', 'Iscurrent:') !!}
    {!! Form::number('isCurrent', null, ['class' => 'form-control']) !!}
</div>

<!-- Isclosed Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isClosed', 'Isclosed:') !!}
    {!! Form::number('isClosed', null, ['class' => 'form-control']) !!}
</div>

<!-- Closedbyempsystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('closedByEmpSystemID', 'Closedbyempsystemid:') !!}
    {!! Form::number('closedByEmpSystemID', null, ['class' => 'form-control']) !!}
</div>

<!-- Closedbyempid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('closedByEmpID', 'Closedbyempid:') !!}
    {!! Form::text('closedByEmpID', null, ['class' => 'form-control']) !!}
</div>

<!-- Closedbyempname Field -->
<div class="form-group col-sm-6">
    {!! Form::label('closedByEmpName', 'Closedbyempname:') !!}
    {!! Form::text('closedByEmpName', null, ['class' => 'form-control']) !!}
</div>

<!-- Closeddate Field -->
<div class="form-group col-sm-6">
    {!! Form::label('closedDate', 'Closeddate:') !!}
    {!! Form::date('closedDate', null, ['class' => 'form-control']) !!}
</div>

<!-- Comments Field -->
<div class="form-group col-sm-6">
    {!! Form::label('comments', 'Comments:') !!}
    {!! Form::text('comments', null, ['class' => 'form-control']) !!}
</div>

<!-- Createdusergroup Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdUserGroup', 'Createdusergroup:') !!}
    {!! Form::text('createdUserGroup', null, ['class' => 'form-control']) !!}
</div>

<!-- Createduserid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdUserID', 'Createduserid:') !!}
    {!! Form::text('createdUserID', null, ['class' => 'form-control']) !!}
</div>

<!-- Createdpcid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdPcID', 'Createdpcid:') !!}
    {!! Form::text('createdPcID', null, ['class' => 'form-control']) !!}
</div>

<!-- Createddatetime Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdDateTime', 'Createddatetime:') !!}
    {!! Form::date('createdDateTime', null, ['class' => 'form-control']) !!}
</div>

<!-- Modifieduser Field -->
<div class="form-group col-sm-6">
    {!! Form::label('modifiedUser', 'Modifieduser:') !!}
    {!! Form::text('modifiedUser', null, ['class' => 'form-control']) !!}
</div>

<!-- Modifiedpc Field -->
<div class="form-group col-sm-6">
    {!! Form::label('modifiedPc', 'Modifiedpc:') !!}
    {!! Form::text('modifiedPc', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timeStamp', 'Timestamp:') !!}
    {!! Form::date('timeStamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('companyFinanceYears.index') !!}" class="btn btn-default">Cancel</a>
</div>
