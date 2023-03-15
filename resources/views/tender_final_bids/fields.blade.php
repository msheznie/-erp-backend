<!-- Award Field -->
<div class="form-group col-sm-6">
    {!! Form::label('award', 'Award:') !!}
    <label class="checkbox-inline">
        {!! Form::hidden('award', 0) !!}
        {!! Form::checkbox('award', '1', null) !!}
    </label>
</div>


<!-- Bid Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('bid_id', 'Bid Id:') !!}
    {!! Form::number('bid_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Com Weightage Field -->
<div class="form-group col-sm-6">
    {!! Form::label('com_weightage', 'Com Weightage:') !!}
    {!! Form::number('com_weightage', null, ['class' => 'form-control']) !!}
</div>

<!-- Status Field -->
<div class="form-group col-sm-6">
    {!! Form::label('status', 'Status:') !!}
    <label class="checkbox-inline">
        {!! Form::hidden('status', 0) !!}
        {!! Form::checkbox('status', '1', null) !!}
    </label>
</div>


<!-- Supplier Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('supplier_id', 'Supplier Id:') !!}
    {!! Form::number('supplier_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Tech Weightage Field -->
<div class="form-group col-sm-6">
    {!! Form::label('tech_weightage', 'Tech Weightage:') !!}
    {!! Form::number('tech_weightage', null, ['class' => 'form-control']) !!}
</div>

<!-- Tender Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('tender_id', 'Tender Id:') !!}
    {!! Form::number('tender_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Total Weightage Field -->
<div class="form-group col-sm-6">
    {!! Form::label('total_weightage', 'Total Weightage:') !!}
    {!! Form::number('total_weightage', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{{ route('tenderFinalBids.index') }}" class="btn btn-default">Cancel</a>
</div>
