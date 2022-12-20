<!-- Bid Format Detail Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('bid_format_detail_id', 'Bid Format Detail Id:') !!}
    {!! Form::number('bid_format_detail_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Bid Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('bid_id', 'Bid Id:') !!}
    {!! Form::number('bid_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Status Field -->
<div class="form-group col-sm-6">
    {!! Form::label('status', 'Status:') !!}
    <label class="checkbox-inline">
        {!! Form::hidden('status', 0) !!}
        {!! Form::checkbox('status', '1', null) !!}
    </label>
</div>


<!-- Tender Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('tender_id', 'Tender Id:') !!}
    {!! Form::number('tender_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Value Field -->
<div class="form-group col-sm-6">
    {!! Form::label('value', 'Value:') !!}
    {!! Form::number('value', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{{ route('commercialBidRankingItems.index') }}" class="btn btn-default">Cancel</a>
</div>
