<!-- Barcode Font Field -->
<div class="form-group col-sm-6">
    {!! Form::label('barcode_font', 'Barcode Font:') !!}
    {!! Form::text('barcode_font', null, ['class' => 'form-control']) !!}
</div>

<!-- Height Field -->
<div class="form-group col-sm-6">
    {!! Form::label('height', 'Height:') !!}
    {!! Form::text('height', null, ['class' => 'form-control']) !!}
</div>

<!-- No Of Coulmns Field -->
<div class="form-group col-sm-6">
    {!! Form::label('no_of_coulmns', 'No Of Coulmns:') !!}
    {!! Form::text('no_of_coulmns', null, ['class' => 'form-control']) !!}
</div>

<!-- No Of Rows Field -->
<div class="form-group col-sm-6">
    {!! Form::label('no_of_rows', 'No Of Rows:') !!}
    {!! Form::text('no_of_rows', null, ['class' => 'form-control']) !!}
</div>

<!-- Page Size Field -->
<div class="form-group col-sm-6">
    {!! Form::label('page_size', 'Page Size:') !!}
    {!! Form::text('page_size', null, ['class' => 'form-control']) !!}
</div>

<!-- Width Field -->
<div class="form-group col-sm-6">
    {!! Form::label('width', 'Width:') !!}
    {!! Form::text('width', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{{ route('barcodeConfigurations.index') }}" class="btn btn-default">Cancel</a>
</div>
