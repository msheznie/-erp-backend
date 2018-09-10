<!-- Performamasterid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('performaMasterID', 'Performamasterid:') !!}
    {!! Form::number('performaMasterID', null, ['class' => 'form-control']) !!}
</div>

<!-- Mystdtitle Field -->
<div class="form-group col-sm-6">
    {!! Form::label('myStdTitle', 'Mystdtitle:') !!}
    {!! Form::text('myStdTitle', null, ['class' => 'form-control']) !!}
</div>

<!-- Companyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companyID', 'Companyid:') !!}
    {!! Form::text('companyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Contractid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('contractid', 'Contractid:') !!}
    {!! Form::text('contractid', null, ['class' => 'form-control']) !!}
</div>

<!-- Performainvoiceno Field -->
<div class="form-group col-sm-6">
    {!! Form::label('performaInvoiceNo', 'Performainvoiceno:') !!}
    {!! Form::number('performaInvoiceNo', null, ['class' => 'form-control']) !!}
</div>

<!-- Sumofsumofstandbyamount Field -->
<div class="form-group col-sm-6">
    {!! Form::label('sumofsumofStandbyAmount', 'Sumofsumofstandbyamount:') !!}
    {!! Form::number('sumofsumofStandbyAmount', null, ['class' => 'form-control']) !!}
</div>

<!-- Ticketno Field -->
<div class="form-group col-sm-6">
    {!! Form::label('TicketNo', 'Ticketno:') !!}
    {!! Form::number('TicketNo', null, ['class' => 'form-control']) !!}
</div>

<!-- Myticketno Field -->
<div class="form-group col-sm-6">
    {!! Form::label('myTicketNo', 'Myticketno:') !!}
    {!! Form::text('myTicketNo', null, ['class' => 'form-control']) !!}
</div>

<!-- Clientid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('clientID', 'Clientid:') !!}
    {!! Form::text('clientID', null, ['class' => 'form-control']) !!}
</div>

<!-- Performadate Field -->
<div class="form-group col-sm-6">
    {!! Form::label('performaDate', 'Performadate:') !!}
    {!! Form::date('performaDate', null, ['class' => 'form-control']) !!}
</div>

<!-- Performafinanceconfirmed Field -->
<div class="form-group col-sm-6">
    {!! Form::label('performaFinanceConfirmed', 'Performafinanceconfirmed:') !!}
    {!! Form::number('performaFinanceConfirmed', null, ['class' => 'form-control']) !!}
</div>

<!-- Performaopconfirmed Field -->
<div class="form-group col-sm-6">
    {!! Form::label('PerformaOpConfirmed', 'Performaopconfirmed:') !!}
    {!! Form::number('PerformaOpConfirmed', null, ['class' => 'form-control']) !!}
</div>

<!-- Performafinanceconfirmedby Field -->
<div class="form-group col-sm-6">
    {!! Form::label('performaFinanceConfirmedBy', 'Performafinanceconfirmedby:') !!}
    {!! Form::text('performaFinanceConfirmedBy', null, ['class' => 'form-control']) !!}
</div>

<!-- Performaopconfirmeddate Field -->
<div class="form-group col-sm-6">
    {!! Form::label('performaOpConfirmedDate', 'Performaopconfirmeddate:') !!}
    {!! Form::date('performaOpConfirmedDate', null, ['class' => 'form-control']) !!}
</div>

<!-- Performafinanceconfirmeddate Field -->
<div class="form-group col-sm-6">
    {!! Form::label('performaFinanceConfirmedDate', 'Performafinanceconfirmeddate:') !!}
    {!! Form::date('performaFinanceConfirmedDate', null, ['class' => 'form-control']) !!}
</div>

<!-- Stdglcode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('stdGLcode', 'Stdglcode:') !!}
    {!! Form::text('stdGLcode', null, ['class' => 'form-control']) !!}
</div>

<!-- Sortorder Field -->
<div class="form-group col-sm-6">
    {!! Form::label('sortOrder', 'Sortorder:') !!}
    {!! Form::number('sortOrder', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timestamp', 'Timestamp:') !!}
    {!! Form::date('timestamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Proformacomment Field -->
<div class="form-group col-sm-6">
    {!! Form::label('proformaComment', 'Proformacomment:') !!}
    {!! Form::text('proformaComment', null, ['class' => 'form-control']) !!}
</div>

<!-- Isdiscount Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isDiscount', 'Isdiscount:') !!}
    {!! Form::number('isDiscount', null, ['class' => 'form-control']) !!}
</div>

<!-- Discountdescription Field -->
<div class="form-group col-sm-6">
    {!! Form::label('discountDescription', 'Discountdescription:') !!}
    {!! Form::text('discountDescription', null, ['class' => 'form-control']) !!}
</div>

<!-- Discountpercentage Field -->
<div class="form-group col-sm-6">
    {!! Form::label('DiscountPercentage', 'Discountpercentage:') !!}
    {!! Form::number('DiscountPercentage', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('performaTemps.index') !!}" class="btn btn-default">Cancel</a>
</div>
