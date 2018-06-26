<!-- Itemissueautoid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('itemIssueAutoID', 'Itemissueautoid:') !!}
    {!! Form::number('itemIssueAutoID', null, ['class' => 'form-control']) !!}
</div>

<!-- Itemissuecode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('itemIssueCode', 'Itemissuecode:') !!}
    {!! Form::text('itemIssueCode', null, ['class' => 'form-control']) !!}
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

<!-- Itemunitofmeasure Field -->
<div class="form-group col-sm-6">
    {!! Form::label('itemUnitOfMeasure', 'Itemunitofmeasure:') !!}
    {!! Form::number('itemUnitOfMeasure', null, ['class' => 'form-control']) !!}
</div>

<!-- Unitofmeasureissued Field -->
<div class="form-group col-sm-6">
    {!! Form::label('unitOfMeasureIssued', 'Unitofmeasureissued:') !!}
    {!! Form::number('unitOfMeasureIssued', null, ['class' => 'form-control']) !!}
</div>

<!-- Clientreferencenumber Field -->
<div class="form-group col-sm-6">
    {!! Form::label('clientReferenceNumber', 'Clientreferencenumber:') !!}
    {!! Form::text('clientReferenceNumber', null, ['class' => 'form-control']) !!}
</div>

<!-- Qtyrequested Field -->
<div class="form-group col-sm-6">
    {!! Form::label('qtyRequested', 'Qtyrequested:') !!}
    {!! Form::number('qtyRequested', null, ['class' => 'form-control']) !!}
</div>

<!-- Qtyissued Field -->
<div class="form-group col-sm-6">
    {!! Form::label('qtyIssued', 'Qtyissued:') !!}
    {!! Form::number('qtyIssued', null, ['class' => 'form-control']) !!}
</div>

<!-- Comments Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('comments', 'Comments:') !!}
    {!! Form::textarea('comments', null, ['class' => 'form-control']) !!}
</div>

<!-- Convertionmeasureval Field -->
<div class="form-group col-sm-6">
    {!! Form::label('convertionMeasureVal', 'Convertionmeasureval:') !!}
    {!! Form::number('convertionMeasureVal', null, ['class' => 'form-control']) !!}
</div>

<!-- Qtyissueddefaultmeasure Field -->
<div class="form-group col-sm-6">
    {!! Form::label('qtyIssuedDefaultMeasure', 'Qtyissueddefaultmeasure:') !!}
    {!! Form::number('qtyIssuedDefaultMeasure', null, ['class' => 'form-control']) !!}
</div>

<!-- Localcurrencyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('localCurrencyID', 'Localcurrencyid:') !!}
    {!! Form::number('localCurrencyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Issuecostlocal Field -->
<div class="form-group col-sm-6">
    {!! Form::label('issueCostLocal', 'Issuecostlocal:') !!}
    {!! Form::number('issueCostLocal', null, ['class' => 'form-control']) !!}
</div>

<!-- Issuecostlocaltotal Field -->
<div class="form-group col-sm-6">
    {!! Form::label('issueCostLocalTotal', 'Issuecostlocaltotal:') !!}
    {!! Form::number('issueCostLocalTotal', null, ['class' => 'form-control']) !!}
</div>

<!-- Reportingcurrencyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('reportingCurrencyID', 'Reportingcurrencyid:') !!}
    {!! Form::number('reportingCurrencyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Issuecostrpt Field -->
<div class="form-group col-sm-6">
    {!! Form::label('issueCostRpt', 'Issuecostrpt:') !!}
    {!! Form::number('issueCostRpt', null, ['class' => 'form-control']) !!}
</div>

<!-- Issuecostrpttotal Field -->
<div class="form-group col-sm-6">
    {!! Form::label('issueCostRptTotal', 'Issuecostrpttotal:') !!}
    {!! Form::number('issueCostRptTotal', null, ['class' => 'form-control']) !!}
</div>

<!-- Currentstockqty Field -->
<div class="form-group col-sm-6">
    {!! Form::label('currentStockQty', 'Currentstockqty:') !!}
    {!! Form::number('currentStockQty', null, ['class' => 'form-control']) !!}
</div>

<!-- Currentwarehousestockqty Field -->
<div class="form-group col-sm-6">
    {!! Form::label('currentWareHouseStockQty', 'Currentwarehousestockqty:') !!}
    {!! Form::number('currentWareHouseStockQty', null, ['class' => 'form-control']) !!}
</div>

<!-- Currentstockqtyindamagereturn Field -->
<div class="form-group col-sm-6">
    {!! Form::label('currentStockQtyInDamageReturn', 'Currentstockqtyindamagereturn:') !!}
    {!! Form::number('currentStockQtyInDamageReturn', null, ['class' => 'form-control']) !!}
</div>

<!-- Maxqty Field -->
<div class="form-group col-sm-6">
    {!! Form::label('maxQty', 'Maxqty:') !!}
    {!! Form::number('maxQty', null, ['class' => 'form-control']) !!}
</div>

<!-- Minqty Field -->
<div class="form-group col-sm-6">
    {!! Form::label('minQty', 'Minqty:') !!}
    {!! Form::number('minQty', null, ['class' => 'form-control']) !!}
</div>

<!-- Selectedforbillingop Field -->
<div class="form-group col-sm-6">
    {!! Form::label('selectedForBillingOP', 'Selectedforbillingop:') !!}
    {!! Form::number('selectedForBillingOP', null, ['class' => 'form-control']) !!}
</div>

<!-- Selectedforbillingoptemp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('selectedForBillingOPtemp', 'Selectedforbillingoptemp:') !!}
    {!! Form::number('selectedForBillingOPtemp', null, ['class' => 'form-control']) !!}
</div>

<!-- Opticketno Field -->
<div class="form-group col-sm-6">
    {!! Form::label('opTicketNo', 'Opticketno:') !!}
    {!! Form::number('opTicketNo', null, ['class' => 'form-control']) !!}
</div>

<!-- Del Field -->
<div class="form-group col-sm-6">
    {!! Form::label('del', 'Del:') !!}
    {!! Form::number('del', null, ['class' => 'form-control']) !!}
</div>

<!-- Backload Field -->
<div class="form-group col-sm-6">
    {!! Form::label('backLoad', 'Backload:') !!}
    {!! Form::number('backLoad', null, ['class' => 'form-control']) !!}
</div>

<!-- Used Field -->
<div class="form-group col-sm-6">
    {!! Form::label('used', 'Used:') !!}
    {!! Form::number('used', null, ['class' => 'form-control']) !!}
</div>

<!-- Grvdocumentno Field -->
<div class="form-group col-sm-6">
    {!! Form::label('grvDocumentNO', 'Grvdocumentno:') !!}
    {!! Form::text('grvDocumentNO', null, ['class' => 'form-control']) !!}
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

<!-- Financeglcodebbssystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('financeGLcodebBSSystemID', 'Financeglcodebbssystemid:') !!}
    {!! Form::number('financeGLcodebBSSystemID', null, ['class' => 'form-control']) !!}
</div>

<!-- Financeglcodebbs Field -->
<div class="form-group col-sm-6">
    {!! Form::label('financeGLcodebBS', 'Financeglcodebbs:') !!}
    {!! Form::text('financeGLcodebBS', null, ['class' => 'form-control']) !!}
</div>

<!-- Financeglcodeplsystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('financeGLcodePLSystemID', 'Financeglcodeplsystemid:') !!}
    {!! Form::number('financeGLcodePLSystemID', null, ['class' => 'form-control']) !!}
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

<!-- P1 Field -->
<div class="form-group col-sm-6">
    {!! Form::label('p1', 'P1:') !!}
    {!! Form::number('p1', null, ['class' => 'form-control']) !!}
</div>

<!-- P2 Field -->
<div class="form-group col-sm-6">
    {!! Form::label('p2', 'P2:') !!}
    {!! Form::number('p2', null, ['class' => 'form-control']) !!}
</div>

<!-- P3 Field -->
<div class="form-group col-sm-6">
    {!! Form::label('p3', 'P3:') !!}
    {!! Form::number('p3', null, ['class' => 'form-control']) !!}
</div>

<!-- P4 Field -->
<div class="form-group col-sm-6">
    {!! Form::label('p4', 'P4:') !!}
    {!! Form::number('p4', null, ['class' => 'form-control']) !!}
</div>

<!-- P5 Field -->
<div class="form-group col-sm-6">
    {!! Form::label('p5', 'P5:') !!}
    {!! Form::number('p5', null, ['class' => 'form-control']) !!}
</div>

<!-- P6 Field -->
<div class="form-group col-sm-6">
    {!! Form::label('p6', 'P6:') !!}
    {!! Form::number('p6', null, ['class' => 'form-control']) !!}
</div>

<!-- P7 Field -->
<div class="form-group col-sm-6">
    {!! Form::label('p7', 'P7:') !!}
    {!! Form::number('p7', null, ['class' => 'form-control']) !!}
</div>

<!-- P8 Field -->
<div class="form-group col-sm-6">
    {!! Form::label('p8', 'P8:') !!}
    {!! Form::number('p8', null, ['class' => 'form-control']) !!}
</div>

<!-- P9 Field -->
<div class="form-group col-sm-6">
    {!! Form::label('p9', 'P9:') !!}
    {!! Form::number('p9', null, ['class' => 'form-control']) !!}
</div>

<!-- P10 Field -->
<div class="form-group col-sm-6">
    {!! Form::label('p10', 'P10:') !!}
    {!! Form::number('p10', null, ['class' => 'form-control']) !!}
</div>

<!-- P11 Field -->
<div class="form-group col-sm-6">
    {!! Form::label('p11', 'P11:') !!}
    {!! Form::number('p11', null, ['class' => 'form-control']) !!}
</div>

<!-- P12 Field -->
<div class="form-group col-sm-6">
    {!! Form::label('p12', 'P12:') !!}
    {!! Form::number('p12', null, ['class' => 'form-control']) !!}
</div>

<!-- P13 Field -->
<div class="form-group col-sm-6">
    {!! Form::label('p13', 'P13:') !!}
    {!! Form::number('p13', null, ['class' => 'form-control']) !!}
</div>

<!-- Pl10 Field -->
<div class="form-group col-sm-6">
    {!! Form::label('pl10', 'Pl10:') !!}
    {!! Form::number('pl10', null, ['class' => 'form-control']) !!}
</div>

<!-- Pl3 Field -->
<div class="form-group col-sm-6">
    {!! Form::label('pl3', 'Pl3:') !!}
    {!! Form::number('pl3', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timestamp', 'Timestamp:') !!}
    {!! Form::date('timestamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('itemIssueDetails.index') !!}" class="btn btn-default">Cancel</a>
</div>
