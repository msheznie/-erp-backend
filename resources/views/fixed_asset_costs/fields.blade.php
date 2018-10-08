<!-- Origindocumentsystemcode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('originDocumentSystemCode', 'Origindocumentsystemcode:') !!}
    {!! Form::number('originDocumentSystemCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Origindocumentid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('originDocumentID', 'Origindocumentid:') !!}
    {!! Form::text('originDocumentID', null, ['class' => 'form-control']) !!}
</div>

<!-- Itemcode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('itemCode', 'Itemcode:') !!}
    {!! Form::number('itemCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Faid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('faID', 'Faid:') !!}
    {!! Form::number('faID', null, ['class' => 'form-control']) !!}
</div>

<!-- Assetid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('assetID', 'Assetid:') !!}
    {!! Form::text('assetID', null, ['class' => 'form-control']) !!}
</div>

<!-- Assetdescription Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('assetDescription', 'Assetdescription:') !!}
    {!! Form::textarea('assetDescription', null, ['class' => 'form-control']) !!}
</div>

<!-- Costdate Field -->
<div class="form-group col-sm-6">
    {!! Form::label('costDate', 'Costdate:') !!}
    {!! Form::date('costDate', null, ['class' => 'form-control']) !!}
</div>

<!-- Localcurrencyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('localCurrencyID', 'Localcurrencyid:') !!}
    {!! Form::number('localCurrencyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Localamount Field -->
<div class="form-group col-sm-6">
    {!! Form::label('localAmount', 'Localamount:') !!}
    {!! Form::number('localAmount', null, ['class' => 'form-control']) !!}
</div>

<!-- Rptcurrencyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('rptCurrencyID', 'Rptcurrencyid:') !!}
    {!! Form::number('rptCurrencyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Rptamount Field -->
<div class="form-group col-sm-6">
    {!! Form::label('rptAmount', 'Rptamount:') !!}
    {!! Form::number('rptAmount', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timeStamp', 'Timestamp:') !!}
    {!! Form::date('timeStamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('fixedAssetCosts.index') !!}" class="btn btn-default">Cancel</a>
</div>
