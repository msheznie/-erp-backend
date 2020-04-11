<!-- Catalogid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('catalogID', 'Catalogid:') !!}
    {!! Form::text('catalogID', null, ['class' => 'form-control']) !!}
</div>

<!-- Catalogname Field -->
<div class="form-group col-sm-6">
    {!! Form::label('catalogName', 'Catalogname:') !!}
    {!! Form::text('catalogName', null, ['class' => 'form-control']) !!}
</div>

<!-- Fromdate Field -->
<div class="form-group col-sm-6">
    {!! Form::label('fromDate', 'Fromdate:') !!}
    {!! Form::date('fromDate', null, ['class' => 'form-control','id'=>'fromDate']) !!}
</div>

@section('scripts')
    <script type="text/javascript">
        $('#fromDate').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            useCurrent: false
        })
    </script>
@endsection

<!-- Todate Field -->
<div class="form-group col-sm-6">
    {!! Form::label('toDate', 'Todate:') !!}
    {!! Form::date('toDate', null, ['class' => 'form-control','id'=>'toDate']) !!}
</div>

@section('scripts')
    <script type="text/javascript">
        $('#toDate').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            useCurrent: false
        })
    </script>
@endsection

<!-- Customerid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('customerID', 'Customerid:') !!}
    {!! Form::number('customerID', null, ['class' => 'form-control']) !!}
</div>

<!-- Companysystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companySystemID', 'Companysystemid:') !!}
    {!! Form::number('companySystemID', null, ['class' => 'form-control']) !!}
</div>

<!-- Documentsystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('documentSystemID', 'Documentsystemid:') !!}
    {!! Form::number('documentSystemID', null, ['class' => 'form-control']) !!}
</div>

<!-- Createdby Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdBy', 'Createdby:') !!}
    {!! Form::number('createdBy', null, ['class' => 'form-control']) !!}
</div>

<!-- Createddate Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdDate', 'Createddate:') !!}
    {!! Form::date('createdDate', null, ['class' => 'form-control','id'=>'createdDate']) !!}
</div>

@section('scripts')
    <script type="text/javascript">
        $('#createdDate').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            useCurrent: false
        })
    </script>
@endsection

<!-- Modifiedby Field -->
<div class="form-group col-sm-6">
    {!! Form::label('modifiedBy', 'Modifiedby:') !!}
    {!! Form::text('modifiedBy', null, ['class' => 'form-control']) !!}
</div>

<!-- Modifieddate Field -->
<div class="form-group col-sm-6">
    {!! Form::label('modifiedDate', 'Modifieddate:') !!}
    {!! Form::date('modifiedDate', null, ['class' => 'form-control','id'=>'modifiedDate']) !!}
</div>

@section('scripts')
    <script type="text/javascript">
        $('#modifiedDate').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            useCurrent: false
        })
    </script>
@endsection

<!-- Isdelete Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isDelete', 'Isdelete:') !!}
    {!! Form::number('isDelete', null, ['class' => 'form-control']) !!}
</div>

<!-- Isactive Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isActive', 'Isactive:') !!}
    {!! Form::number('isActive', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('customerCatalogMasters.index') !!}" class="btn btn-default">Cancel</a>
</div>
