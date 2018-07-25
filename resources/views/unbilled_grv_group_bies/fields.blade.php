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

<!-- Purchaseorderid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('purchaseOrderID', 'Purchaseorderid:') !!}
    {!! Form::number('purchaseOrderID', null, ['class' => 'form-control']) !!}
</div>

<!-- Grvautoid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('grvAutoID', 'Grvautoid:') !!}
    {!! Form::number('grvAutoID', null, ['class' => 'form-control']) !!}
</div>

<!-- Grvdate Field -->
<div class="form-group col-sm-6">
    {!! Form::label('grvDate', 'Grvdate:') !!}
    {!! Form::date('grvDate', null, ['class' => 'form-control']) !!}
</div>

<!-- Suppliertransactioncurrencyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('supplierTransactionCurrencyID', 'Suppliertransactioncurrencyid:') !!}
    {!! Form::number('supplierTransactionCurrencyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Suppliertransactioncurrencyer Field -->
<div class="form-group col-sm-6">
    {!! Form::label('supplierTransactionCurrencyER', 'Suppliertransactioncurrencyer:') !!}
    {!! Form::number('supplierTransactionCurrencyER', null, ['class' => 'form-control']) !!}
</div>

<!-- Companyreportingcurrencyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companyReportingCurrencyID', 'Companyreportingcurrencyid:') !!}
    {!! Form::number('companyReportingCurrencyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Companyreportinger Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companyReportingER', 'Companyreportinger:') !!}
    {!! Form::number('companyReportingER', null, ['class' => 'form-control']) !!}
</div>

<!-- Localcurrencyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('localCurrencyID', 'Localcurrencyid:') !!}
    {!! Form::number('localCurrencyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Localcurrencyer Field -->
<div class="form-group col-sm-6">
    {!! Form::label('localCurrencyER', 'Localcurrencyer:') !!}
    {!! Form::number('localCurrencyER', null, ['class' => 'form-control']) !!}
</div>

<!-- Tottransactionamount Field -->
<div class="form-group col-sm-6">
    {!! Form::label('totTransactionAmount', 'Tottransactionamount:') !!}
    {!! Form::number('totTransactionAmount', null, ['class' => 'form-control']) !!}
</div>

<!-- Totlocalamount Field -->
<div class="form-group col-sm-6">
    {!! Form::label('totLocalAmount', 'Totlocalamount:') !!}
    {!! Form::number('totLocalAmount', null, ['class' => 'form-control']) !!}
</div>

<!-- Totrptamount Field -->
<div class="form-group col-sm-6">
    {!! Form::label('totRptAmount', 'Totrptamount:') !!}
    {!! Form::number('totRptAmount', null, ['class' => 'form-control']) !!}
</div>

<!-- Isaddon Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isAddon', 'Isaddon:') !!}
    {!! Form::number('isAddon', null, ['class' => 'form-control']) !!}
</div>

<!-- Selectedforbooking Field -->
<div class="form-group col-sm-6">
    {!! Form::label('selectedForBooking', 'Selectedforbooking:') !!}
    {!! Form::number('selectedForBooking', null, ['class' => 'form-control']) !!}
</div>

<!-- Fullybooked Field -->
<div class="form-group col-sm-6">
    {!! Form::label('fullyBooked', 'Fullybooked:') !!}
    {!! Form::number('fullyBooked', null, ['class' => 'form-control']) !!}
</div>

<!-- Grvtype Field -->
<div class="form-group col-sm-6">
    {!! Form::label('grvType', 'Grvtype:') !!}
    {!! Form::text('grvType', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timeStamp', 'Timestamp:') !!}
    {!! Form::date('timeStamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('unbilledGrvGroupBies.index') !!}" class="btn btn-default">Cancel</a>
</div>
