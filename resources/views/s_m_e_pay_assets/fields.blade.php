<!-- Empid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('empID', 'Empid:') !!}
    {!! Form::number('empID', null, ['class' => 'form-control']) !!}
</div>

<!-- Assettypeid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('assetTypeID', 'Assettypeid:') !!}
    {!! Form::number('assetTypeID', null, ['class' => 'form-control']) !!}
</div>

<!-- Description Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('description', 'Description:') !!}
    {!! Form::textarea('description', null, ['class' => 'form-control']) !!}
</div>

<!-- Asset Serial No Field -->
<div class="form-group col-sm-6">
    {!! Form::label('asset_serial_no', 'Asset Serial No:') !!}
    {!! Form::text('asset_serial_no', null, ['class' => 'form-control']) !!}
</div>

<!-- Assetconditionid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('assetConditionID', 'Assetconditionid:') !!}
    {!! Form::number('assetConditionID', null, ['class' => 'form-control']) !!}
</div>

<!-- Handoverdate Field -->
<div class="form-group col-sm-6">
    {!! Form::label('handOverDate', 'Handoverdate:') !!}
    {!! Form::date('handOverDate', null, ['class' => 'form-control','id'=>'handOverDate']) !!}
</div>

@section('scripts')
    <script type="text/javascript">
        $('#handOverDate').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            useCurrent: false
        })
    </script>
@endsection

<!-- Returnstatus Field -->
<div class="form-group col-sm-6">
    {!! Form::label('returnStatus', 'Returnstatus:') !!}
    {!! Form::number('returnStatus', null, ['class' => 'form-control']) !!}
</div>

<!-- Returndate Field -->
<div class="form-group col-sm-6">
    {!! Form::label('returnDate', 'Returndate:') !!}
    {!! Form::date('returnDate', null, ['class' => 'form-control','id'=>'returnDate']) !!}
</div>

@section('scripts')
    <script type="text/javascript">
        $('#returnDate').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            useCurrent: false
        })
    </script>
@endsection

<!-- Returncomment Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('returnComment', 'Returncomment:') !!}
    {!! Form::textarea('returnComment', null, ['class' => 'form-control']) !!}
</div>

<!-- Companyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companyID', 'Companyid:') !!}
    {!! Form::number('companyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Createdusergroup Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdUserGroup', 'Createdusergroup:') !!}
    {!! Form::number('createdUserGroup', null, ['class' => 'form-control']) !!}
</div>

<!-- Createdpcid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdPCID', 'Createdpcid:') !!}
    {!! Form::text('createdPCID', null, ['class' => 'form-control']) !!}
</div>

<!-- Createduserid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdUserID', 'Createduserid:') !!}
    {!! Form::number('createdUserID', null, ['class' => 'form-control']) !!}
</div>

<!-- Createddatetime Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdDateTime', 'Createddatetime:') !!}
    {!! Form::date('createdDateTime', null, ['class' => 'form-control','id'=>'createdDateTime']) !!}
</div>

@section('scripts')
    <script type="text/javascript">
        $('#createdDateTime').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            useCurrent: false
        })
    </script>
@endsection

<!-- Modifiedpcid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('modifiedPCID', 'Modifiedpcid:') !!}
    {!! Form::text('modifiedPCID', null, ['class' => 'form-control']) !!}
</div>

<!-- Modifieduserid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('modifiedUserID', 'Modifieduserid:') !!}
    {!! Form::number('modifiedUserID', null, ['class' => 'form-control']) !!}
</div>

<!-- Modifieddatetime Field -->
<div class="form-group col-sm-6">
    {!! Form::label('modifiedDateTime', 'Modifieddatetime:') !!}
    {!! Form::date('modifiedDateTime', null, ['class' => 'form-control','id'=>'modifiedDateTime']) !!}
</div>

@section('scripts')
    <script type="text/javascript">
        $('#modifiedDateTime').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            useCurrent: false
        })
    </script>
@endsection

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timestamp', 'Timestamp:') !!}
    {!! Form::date('timestamp', null, ['class' => 'form-control','id'=>'timestamp']) !!}
</div>

@section('scripts')
    <script type="text/javascript">
        $('#timestamp').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            useCurrent: false
        })
    </script>
@endsection

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{{ route('sMEPayAssets.index') }}" class="btn btn-default">Cancel</a>
</div>
