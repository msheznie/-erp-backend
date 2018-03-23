<!-- Departmentid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('DepartmentID', 'Departmentid:') !!}
    {!! Form::text('DepartmentID', null, ['class' => 'form-control']) !!}
</div>

<!-- Departmentdescription Field -->
<div class="form-group col-sm-6">
    {!! Form::label('DepartmentDescription', 'Departmentdescription:') !!}
    {!! Form::text('DepartmentDescription', null, ['class' => 'form-control']) !!}
</div>

<!-- Isactive Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isActive', 'Isactive:') !!}
    {!! Form::number('isActive', null, ['class' => 'form-control']) !!}
</div>

<!-- Depimage Field -->
<div class="form-group col-sm-6">
    {!! Form::label('depImage', 'Depimage:') !!}
    {!! Form::text('depImage', null, ['class' => 'form-control']) !!}
</div>

<!-- Masterlevel Field -->
<div class="form-group col-sm-6">
    {!! Form::label('masterLevel', 'Masterlevel:') !!}
    {!! Form::number('masterLevel', null, ['class' => 'form-control']) !!}
</div>

<!-- Companylevel Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companyLevel', 'Companylevel:') !!}
    {!! Form::number('companyLevel', null, ['class' => 'form-control']) !!}
</div>

<!-- Listorder Field -->
<div class="form-group col-sm-6">
    {!! Form::label('listOrder', 'Listorder:') !!}
    {!! Form::number('listOrder', null, ['class' => 'form-control']) !!}
</div>

<!-- Isreport Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isReport', 'Isreport:') !!}
    {!! Form::number('isReport', null, ['class' => 'form-control']) !!}
</div>

<!-- Reportmenu Field -->
<div class="form-group col-sm-6">
    {!! Form::label('ReportMenu', 'Reportmenu:') !!}
    {!! Form::text('ReportMenu', null, ['class' => 'form-control']) !!}
</div>

<!-- Menuinitialimage Field -->
<div class="form-group col-sm-6">
    {!! Form::label('menuInitialImage', 'Menuinitialimage:') !!}
    {!! Form::text('menuInitialImage', null, ['class' => 'form-control']) !!}
</div>

<!-- Menuinitialselectedimage Field -->
<div class="form-group col-sm-6">
    {!! Form::label('menuInitialSelectedImage', 'Menuinitialselectedimage:') !!}
    {!! Form::text('menuInitialSelectedImage', null, ['class' => 'form-control']) !!}
</div>

<!-- Showincombo Field -->
<div class="form-group col-sm-6">
    {!! Form::label('showInCombo', 'Showincombo:') !!}
    {!! Form::number('showInCombo', null, ['class' => 'form-control']) !!}
</div>

<!-- Hrleaveapprovallevels Field -->
<div class="form-group col-sm-6">
    {!! Form::label('hrLeaveApprovalLevels', 'Hrleaveapprovallevels:') !!}
    {!! Form::number('hrLeaveApprovalLevels', null, ['class' => 'form-control']) !!}
</div>

<!-- Managerfield Field -->
<div class="form-group col-sm-6">
    {!! Form::label('managerfield', 'Managerfield:') !!}
    {!! Form::text('managerfield', null, ['class' => 'form-control']) !!}
</div>

<!-- Isfunctionaldepartment Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isFunctionalDepartment', 'Isfunctionaldepartment:') !!}
    {!! Form::number('isFunctionalDepartment', null, ['class' => 'form-control']) !!}
</div>

<!-- Isreportgroupyn Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isReportGroupYN', 'Isreportgroupyn:') !!}
    {!! Form::number('isReportGroupYN', null, ['class' => 'form-control']) !!}
</div>

<!-- Hrobjectivesetting Field -->
<div class="form-group col-sm-6">
    {!! Form::label('hrObjectiveSetting', 'Hrobjectivesetting:') !!}
    {!! Form::number('hrObjectiveSetting', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timeStamp', 'Timestamp:') !!}
    {!! Form::date('timeStamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('departmentMasters.index') !!}" class="btn btn-default">Cancel</a>
</div>
