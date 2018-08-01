<!-- Purchaserequestid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('purchaseRequestID', 'Purchaserequestid:') !!}
    {!! Form::number('purchaseRequestID', null, ['class' => 'form-control']) !!}
</div>

<!-- Itemcode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('itemCode', 'Itemcode:') !!}
    {!! Form::text('itemCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Itemprimarycode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('itemPrimaryCode', 'Itemprimarycode:') !!}
    {!! Form::text('itemPrimaryCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Itemdescription Field -->
<div class="form-group col-sm-6">
    {!! Form::label('itemDescription', 'Itemdescription:') !!}
    {!! Form::text('itemDescription', null, ['class' => 'form-control']) !!}
</div>

<!-- Itemfinancecategoryid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('itemFinanceCategoryID', 'Itemfinancecategoryid:') !!}
    {!! Form::number('itemFinanceCategoryID', null, ['class' => 'form-control']) !!}
</div>

<!-- Itemfinancecategorysubid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('itemFinanceCategorySubID', 'Itemfinancecategorysubid:') !!}
    {!! Form::number('itemFinanceCategorySubID', null, ['class' => 'form-control']) !!}
</div>

<!-- Financeglcodebbs Field -->
<div class="form-group col-sm-6">
    {!! Form::label('financeGLcodebBS', 'Financeglcodebbs:') !!}
    {!! Form::text('financeGLcodebBS', null, ['class' => 'form-control']) !!}
</div>

<!-- Financeglcodepl Field -->
<div class="form-group col-sm-6">
    {!! Form::label('financeGLcodePL', 'Financeglcodepl:') !!}
    {!! Form::text('financeGLcodePL', null, ['class' => 'form-control']) !!}
</div>

<!-- Includeplforgrvyn Field -->
<div class="form-group col-sm-6">
    {!! Form::label('includePLForGRVYN', 'Includeplforgrvyn:') !!}
    {!! Form::number('includePLForGRVYN', null, ['class' => 'form-control']) !!}
</div>

<!-- Quantityrequested Field -->
<div class="form-group col-sm-6">
    {!! Form::label('quantityRequested', 'Quantityrequested:') !!}
    {!! Form::number('quantityRequested', null, ['class' => 'form-control']) !!}
</div>

<!-- Estimatedcost Field -->
<div class="form-group col-sm-6">
    {!! Form::label('estimatedCost', 'Estimatedcost:') !!}
    {!! Form::number('estimatedCost', null, ['class' => 'form-control']) !!}
</div>

<!-- Quantityonorder Field -->
<div class="form-group col-sm-6">
    {!! Form::label('quantityOnOrder', 'Quantityonorder:') !!}
    {!! Form::number('quantityOnOrder', null, ['class' => 'form-control']) !!}
</div>

<!-- Comments Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('comments', 'Comments:') !!}
    {!! Form::textarea('comments', null, ['class' => 'form-control']) !!}
</div>

<!-- Unitofmeasure Field -->
<div class="form-group col-sm-6">
    {!! Form::label('unitOfMeasure', 'Unitofmeasure:') !!}
    {!! Form::text('unitOfMeasure', null, ['class' => 'form-control']) !!}
</div>

<!-- Quantityinhand Field -->
<div class="form-group col-sm-6">
    {!! Form::label('quantityInHand', 'Quantityinhand:') !!}
    {!! Form::number('quantityInHand', null, ['class' => 'form-control']) !!}
</div>

<!-- Timesreffered Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timesReffered', 'Timesreffered:') !!}
    {!! Form::number('timesReffered', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timeStamp', 'Timestamp:') !!}
    {!! Form::date('timeStamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Partnumber Field -->
<div class="form-group col-sm-6">
    {!! Form::label('partNumber', 'Partnumber:') !!}
    {!! Form::text('partNumber', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('prDetailsReferedHistories.index') !!}" class="btn btn-default">Cancel</a>
</div>
