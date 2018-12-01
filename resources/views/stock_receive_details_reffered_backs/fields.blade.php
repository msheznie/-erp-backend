<!-- Stockreceivedetailsid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('stockReceiveDetailsID', 'Stockreceivedetailsid:') !!}
    {!! Form::number('stockReceiveDetailsID', null, ['class' => 'form-control']) !!}
</div>

<!-- Stockreceiveautoid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('stockReceiveAutoID', 'Stockreceiveautoid:') !!}
    {!! Form::number('stockReceiveAutoID', null, ['class' => 'form-control']) !!}
</div>

<!-- Stockreceivecode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('stockReceiveCode', 'Stockreceivecode:') !!}
    {!! Form::text('stockReceiveCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Stocktransferautoid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('stockTransferAutoID', 'Stocktransferautoid:') !!}
    {!! Form::number('stockTransferAutoID', null, ['class' => 'form-control']) !!}
</div>

<!-- Stocktransfercode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('stockTransferCode', 'Stocktransfercode:') !!}
    {!! Form::text('stockTransferCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Stocktransferdate Field -->
<div class="form-group col-sm-6">
    {!! Form::label('stockTransferDate', 'Stocktransferdate:') !!}
    {!! Form::date('stockTransferDate', null, ['class' => 'form-control']) !!}
</div>

<!-- Itemcodesystem Field -->
<div class="form-group col-sm-6">
    {!! Form::label('itemCodeSystem', 'Itemcodesystem:') !!}
    {!! Form::number('itemCodeSystem', null, ['class' => 'form-control']) !!}
</div>

<!-- Itemprimarycode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('itemPrimaryCode', 'Itemprimarycode:') !!}
    {!! Form::text('itemPrimaryCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Itemdescription Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('itemDescription', 'Itemdescription:') !!}
    {!! Form::textarea('itemDescription', null, ['class' => 'form-control']) !!}
</div>

<!-- Unitofmeasure Field -->
<div class="form-group col-sm-6">
    {!! Form::label('unitOfMeasure', 'Unitofmeasure:') !!}
    {!! Form::number('unitOfMeasure', null, ['class' => 'form-control']) !!}
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

<!-- Financeglcodebbssystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('financeGLcodebBSSystemID', 'Financeglcodebbssystemid:') !!}
    {!! Form::number('financeGLcodebBSSystemID', null, ['class' => 'form-control']) !!}
</div>

<!-- Localcurrencyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('localCurrencyID', 'Localcurrencyid:') !!}
    {!! Form::number('localCurrencyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Unitcostlocal Field -->
<div class="form-group col-sm-6">
    {!! Form::label('unitCostLocal', 'Unitcostlocal:') !!}
    {!! Form::number('unitCostLocal', null, ['class' => 'form-control']) !!}
</div>

<!-- Reportingcurrencyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('reportingCurrencyID', 'Reportingcurrencyid:') !!}
    {!! Form::number('reportingCurrencyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Unitcostrpt Field -->
<div class="form-group col-sm-6">
    {!! Form::label('unitCostRpt', 'Unitcostrpt:') !!}
    {!! Form::number('unitCostRpt', null, ['class' => 'form-control']) !!}
</div>

<!-- Qty Field -->
<div class="form-group col-sm-6">
    {!! Form::label('qty', 'Qty:') !!}
    {!! Form::number('qty', null, ['class' => 'form-control']) !!}
</div>

<!-- Comments Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('comments', 'Comments:') !!}
    {!! Form::textarea('comments', null, ['class' => 'form-control']) !!}
</div>

<!-- Timesreferred Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timesReferred', 'Timesreferred:') !!}
    {!! Form::number('timesReferred', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timeStamp', 'Timestamp:') !!}
    {!! Form::date('timeStamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('stockReceiveDetailsRefferedBacks.index') !!}" class="btn btn-default">Cancel</a>
</div>
