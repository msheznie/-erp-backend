<!-- Company Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('company_id', 'Company Id:') !!}
    {!! Form::number('company_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Description Field -->
<div class="form-group col-sm-6">
    {!! Form::label('description', 'Description:') !!}
    {!! Form::text('description', null, ['class' => 'form-control']) !!}
</div>

<!-- Item Name Field -->
<div class="form-group col-sm-6">
    {!! Form::label('item_name', 'Item Name:') !!}
    {!! Form::text('item_name', null, ['class' => 'form-control']) !!}
</div>

<!-- Main Work Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('main_work_id', 'Main Work Id:') !!}
    {!! Form::number('main_work_id', null, ['class' => 'form-control']) !!}
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

<!-- Qty Field -->
<div class="form-group col-sm-6">
    {!! Form::label('qty', 'Qty:') !!}
    {!! Form::number('qty', null, ['class' => 'form-control']) !!}
</div>

<!-- Tender Edit Version Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('tender_edit_version_id', 'Tender Edit Version Id:') !!}
    {!! Form::number('tender_edit_version_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Tender Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('tender_id', 'Tender Id:') !!}
    {!! Form::number('tender_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Tender Ranking Line Item Field -->
<div class="form-group col-sm-6">
    {!! Form::label('tender_ranking_line_item', 'Tender Ranking Line Item:') !!}
    {!! Form::number('tender_ranking_line_item', null, ['class' => 'form-control']) !!}
</div>

<!-- Uom Field -->
<div class="form-group col-sm-6">
    {!! Form::label('uom', 'Uom:') !!}
    {!! Form::number('uom', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{{ route('tenderBoqItemsEditLogs.index') }}" class="btn btn-default">Cancel</a>
</div>
