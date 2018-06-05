<!-- Purchaseorderid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('purchaseOrderID', 'Purchaseorderid:') !!}
    {!! Form::number('purchaseOrderID', null, ['class' => 'form-control']) !!}
</div>

<!-- Purchaseordercode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('purchaseOrderCode', 'Purchaseordercode:') !!}
    {!! Form::text('purchaseOrderCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Pocategoryid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('POCategoryID', 'Pocategoryid:') !!}
    {!! Form::number('POCategoryID', null, ['class' => 'form-control']) !!}
</div>

<!-- Comments Field -->
<div class="form-group col-sm-6">
    {!! Form::label('comments', 'Comments:') !!}
    {!! Form::text('comments', null, ['class' => 'form-control']) !!}
</div>

<!-- Updatedbyempsystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('updatedByEmpSystemID', 'Updatedbyempsystemid:') !!}
    {!! Form::number('updatedByEmpSystemID', null, ['class' => 'form-control']) !!}
</div>

<!-- Updatedbyempid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('updatedByEmpID', 'Updatedbyempid:') !!}
    {!! Form::number('updatedByEmpID', null, ['class' => 'form-control']) !!}
</div>

<!-- Updatedbyempname Field -->
<div class="form-group col-sm-6">
    {!! Form::label('updatedByEmpName', 'Updatedbyempname:') !!}
    {!! Form::text('updatedByEmpName', null, ['class' => 'form-control']) !!}
</div>

<!-- Updateddate Field -->
<div class="form-group col-sm-6">
    {!! Form::label('updatedDate', 'Updateddate:') !!}
    {!! Form::date('updatedDate', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timestamp', 'Timestamp:') !!}
    {!! Form::date('timestamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('purchaseOrderStatuses.index') !!}" class="btn btn-default">Cancel</a>
</div>
