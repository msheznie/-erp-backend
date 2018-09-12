<!-- Logisticmasterid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('logisticMasterID', 'Logisticmasterid:') !!}
    {!! Form::number('logisticMasterID', null, ['class' => 'form-control']) !!}
</div>

<!-- Companysystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companySystemID', 'Companysystemid:') !!}
    {!! Form::number('companySystemID', null, ['class' => 'form-control']) !!}
</div>

<!-- Companyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companyID', 'Companyid:') !!}
    {!! Form::text('companyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Supplierid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('supplierID', 'Supplierid:') !!}
    {!! Form::number('supplierID', null, ['class' => 'form-control']) !!}
</div>

<!-- Poid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('POid', 'Poid:') !!}
    {!! Form::number('POid', null, ['class' => 'form-control']) !!}
</div>

<!-- Podetailid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('POdetailID', 'Podetailid:') !!}
    {!! Form::number('POdetailID', null, ['class' => 'form-control']) !!}
</div>

<!-- Itemcodesystem Field -->
<div class="form-group col-sm-6">
    {!! Form::label('itemcodeSystem', 'Itemcodesystem:') !!}
    {!! Form::number('itemcodeSystem', null, ['class' => 'form-control']) !!}
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

<!-- Partno Field -->
<div class="form-group col-sm-6">
    {!! Form::label('partNo', 'Partno:') !!}
    {!! Form::text('partNo', null, ['class' => 'form-control']) !!}
</div>

<!-- Itemuom Field -->
<div class="form-group col-sm-6">
    {!! Form::label('itemUOM', 'Itemuom:') !!}
    {!! Form::number('itemUOM', null, ['class' => 'form-control']) !!}
</div>

<!-- Itempoqtry Field -->
<div class="form-group col-sm-6">
    {!! Form::label('itemPOQtry', 'Itempoqtry:') !!}
    {!! Form::number('itemPOQtry', null, ['class' => 'form-control']) !!}
</div>

<!-- Itemshippingqty Field -->
<div class="form-group col-sm-6">
    {!! Form::label('itemShippingQty', 'Itemshippingqty:') !!}
    {!! Form::number('itemShippingQty', null, ['class' => 'form-control']) !!}
</div>

<!-- Podeliverywarehouslocation Field -->
<div class="form-group col-sm-6">
    {!! Form::label('POdeliveryWarehousLocation', 'Podeliverywarehouslocation:') !!}
    {!! Form::number('POdeliveryWarehousLocation', null, ['class' => 'form-control']) !!}
</div>

<!-- Grvstatus Field -->
<div class="form-group col-sm-6">
    {!! Form::label('GRVStatus', 'Grvstatus:') !!}
    {!! Form::number('GRVStatus', null, ['class' => 'form-control']) !!}
</div>

<!-- Grvsystemcode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('GRVsystemCode', 'Grvsystemcode:') !!}
    {!! Form::number('GRVsystemCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timestamp', 'Timestamp:') !!}
    {!! Form::date('timestamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('logisticDetails.index') !!}" class="btn btn-default">Cancel</a>
</div>
