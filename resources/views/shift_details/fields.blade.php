<!-- Warehouseid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('wareHouseID', 'Warehouseid:') !!}
    {!! Form::number('wareHouseID', null, ['class' => 'form-control']) !!}
</div>

<!-- Empid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('empID', 'Empid:') !!}
    {!! Form::number('empID', null, ['class' => 'form-control']) !!}
</div>

<!-- Counterid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('counterID', 'Counterid:') !!}
    {!! Form::number('counterID', null, ['class' => 'form-control']) !!}
</div>

<!-- Starttime Field -->
<div class="form-group col-sm-6">
    {!! Form::label('startTime', 'Starttime:') !!}
    {!! Form::date('startTime', null, ['class' => 'form-control']) !!}
</div>

<!-- Endtime Field -->
<div class="form-group col-sm-6">
    {!! Form::label('endTime', 'Endtime:') !!}
    {!! Form::date('endTime', null, ['class' => 'form-control']) !!}
</div>

<!-- Isclosed Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isClosed', 'Isclosed:') !!}
    <label class="checkbox-inline">
        {!! Form::hidden('isClosed', false) !!}
        {!! Form::checkbox('isClosed', '1', null) !!} 1
    </label>
</div>

<!-- Cashsales Field -->
<div class="form-group col-sm-6">
    {!! Form::label('cashSales', 'Cashsales:') !!}
    {!! Form::number('cashSales', null, ['class' => 'form-control']) !!}
</div>

<!-- Giftcardtopup Field -->
<div class="form-group col-sm-6">
    {!! Form::label('giftCardTopUp', 'Giftcardtopup:') !!}
    {!! Form::number('giftCardTopUp', null, ['class' => 'form-control']) !!}
</div>

<!-- Startingbalance Transaction Field -->
<div class="form-group col-sm-6">
    {!! Form::label('startingBalance_transaction', 'Startingbalance Transaction:') !!}
    {!! Form::number('startingBalance_transaction', null, ['class' => 'form-control']) !!}
</div>

<!-- Endingbalance Transaction Field -->
<div class="form-group col-sm-6">
    {!! Form::label('endingBalance_transaction', 'Endingbalance Transaction:') !!}
    {!! Form::number('endingBalance_transaction', null, ['class' => 'form-control']) !!}
</div>

<!-- Different Transaction Field -->
<div class="form-group col-sm-6">
    {!! Form::label('different_transaction', 'Different Transaction:') !!}
    {!! Form::number('different_transaction', null, ['class' => 'form-control']) !!}
</div>

<!-- Cashsales Local Field -->
<div class="form-group col-sm-6">
    {!! Form::label('cashSales_local', 'Cashsales Local:') !!}
    {!! Form::number('cashSales_local', null, ['class' => 'form-control']) !!}
</div>

<!-- Giftcardtopup Local Field -->
<div class="form-group col-sm-6">
    {!! Form::label('giftCardTopUp_local', 'Giftcardtopup Local:') !!}
    {!! Form::number('giftCardTopUp_local', null, ['class' => 'form-control']) !!}
</div>

<!-- Startingbalance Local Field -->
<div class="form-group col-sm-6">
    {!! Form::label('startingBalance_local', 'Startingbalance Local:') !!}
    {!! Form::number('startingBalance_local', null, ['class' => 'form-control']) !!}
</div>

<!-- Endingbalance Local Field -->
<div class="form-group col-sm-6">
    {!! Form::label('endingBalance_local', 'Endingbalance Local:') !!}
    {!! Form::number('endingBalance_local', null, ['class' => 'form-control']) !!}
</div>

<!-- Different Local Field -->
<div class="form-group col-sm-6">
    {!! Form::label('different_local', 'Different Local:') !!}
    {!! Form::number('different_local', null, ['class' => 'form-control']) !!}
</div>

<!-- Cashsales Reporting Field -->
<div class="form-group col-sm-6">
    {!! Form::label('cashSales_reporting', 'Cashsales Reporting:') !!}
    {!! Form::number('cashSales_reporting', null, ['class' => 'form-control']) !!}
</div>

<!-- Giftcardtopup Reporting Field -->
<div class="form-group col-sm-6">
    {!! Form::label('giftCardTopUp_reporting', 'Giftcardtopup Reporting:') !!}
    {!! Form::number('giftCardTopUp_reporting', null, ['class' => 'form-control']) !!}
</div>

<!-- Closingcashbalance Transaction Field -->
<div class="form-group col-sm-6">
    {!! Form::label('closingCashBalance_transaction', 'Closingcashbalance Transaction:') !!}
    {!! Form::number('closingCashBalance_transaction', null, ['class' => 'form-control']) !!}
</div>

<!-- Closingcashbalance Local Field -->
<div class="form-group col-sm-6">
    {!! Form::label('closingCashBalance_local', 'Closingcashbalance Local:') !!}
    {!! Form::number('closingCashBalance_local', null, ['class' => 'form-control']) !!}
</div>

<!-- Startingbalance Reporting Field -->
<div class="form-group col-sm-6">
    {!! Form::label('startingBalance_reporting', 'Startingbalance Reporting:') !!}
    {!! Form::number('startingBalance_reporting', null, ['class' => 'form-control']) !!}
</div>

<!-- Endingbalance Reporting Field -->
<div class="form-group col-sm-6">
    {!! Form::label('endingBalance_reporting', 'Endingbalance Reporting:') !!}
    {!! Form::number('endingBalance_reporting', null, ['class' => 'form-control']) !!}
</div>

<!-- Different Local Reporting Field -->
<div class="form-group col-sm-6">
    {!! Form::label('different_local_reporting', 'Different Local Reporting:') !!}
    {!! Form::number('different_local_reporting', null, ['class' => 'form-control']) !!}
</div>

<!-- Closingcashbalance Reporting Field -->
<div class="form-group col-sm-6">
    {!! Form::label('closingCashBalance_reporting', 'Closingcashbalance Reporting:') !!}
    {!! Form::number('closingCashBalance_reporting', null, ['class' => 'form-control']) !!}
</div>

<!-- Transactioncurrencyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('transactionCurrencyID', 'Transactioncurrencyid:') !!}
    {!! Form::number('transactionCurrencyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Transactioncurrency Field -->
<div class="form-group col-sm-6">
    {!! Form::label('transactionCurrency', 'Transactioncurrency:') !!}
    {!! Form::text('transactionCurrency', null, ['class' => 'form-control']) !!}
</div>

<!-- Transactionexchangerate Field -->
<div class="form-group col-sm-6">
    {!! Form::label('transactionExchangeRate', 'Transactionexchangerate:') !!}
    {!! Form::number('transactionExchangeRate', null, ['class' => 'form-control']) !!}
</div>

<!-- Transactioncurrencydecimalplaces Field -->
<div class="form-group col-sm-6">
    {!! Form::label('transactionCurrencyDecimalPlaces', 'Transactioncurrencydecimalplaces:') !!}
    {!! Form::number('transactionCurrencyDecimalPlaces', null, ['class' => 'form-control']) !!}
</div>

<!-- Companylocalcurrencyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companyLocalCurrencyID', 'Companylocalcurrencyid:') !!}
    {!! Form::number('companyLocalCurrencyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Companylocalcurrency Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companyLocalCurrency', 'Companylocalcurrency:') !!}
    {!! Form::text('companyLocalCurrency', null, ['class' => 'form-control']) !!}
</div>

<!-- Companylocalexchangerate Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companyLocalExchangeRate', 'Companylocalexchangerate:') !!}
    {!! Form::number('companyLocalExchangeRate', null, ['class' => 'form-control']) !!}
</div>

<!-- Companylocalcurrencydecimalplaces Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companyLocalCurrencyDecimalPlaces', 'Companylocalcurrencydecimalplaces:') !!}
    {!! Form::number('companyLocalCurrencyDecimalPlaces', null, ['class' => 'form-control']) !!}
</div>

<!-- Companyreportingcurrencyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companyReportingCurrencyID', 'Companyreportingcurrencyid:') !!}
    {!! Form::number('companyReportingCurrencyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Companyreportingcurrency Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companyReportingCurrency', 'Companyreportingcurrency:') !!}
    {!! Form::text('companyReportingCurrency', null, ['class' => 'form-control']) !!}
</div>

<!-- Companyreportingexchangerate Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companyReportingExchangeRate', 'Companyreportingexchangerate:') !!}
    {!! Form::number('companyReportingExchangeRate', null, ['class' => 'form-control']) !!}
</div>

<!-- Companyreportingcurrencydecimalplaces Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companyReportingCurrencyDecimalPlaces', 'Companyreportingcurrencydecimalplaces:') !!}
    {!! Form::number('companyReportingCurrencyDecimalPlaces', null, ['class' => 'form-control']) !!}
</div>

<!-- Companyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companyID', 'Companyid:') !!}
    {!! Form::number('companyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Companycode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companyCode', 'Companycode:') !!}
    {!! Form::text('companyCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Segmentid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('segmentID', 'Segmentid:') !!}
    {!! Form::number('segmentID', null, ['class' => 'form-control']) !!}
</div>

<!-- Segmentcode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('segmentCode', 'Segmentcode:') !!}
    {!! Form::text('segmentCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Createdusergroup Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdUserGroup', 'Createdusergroup:') !!}
    {!! Form::number('createdUserGroup', null, ['class' => 'form-control']) !!}
</div>

<!-- Createdpcid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdPCID', 'Createdpcid:') !!}
    {!! Form::text('createdPCID', null, ['class' => 'form-control']) !!}
</div>

<!-- Createdusersystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdUserSystemID', 'Createdusersystemid:') !!}
    {!! Form::number('createdUserSystemID', null, ['class' => 'form-control']) !!}
</div>

<!-- Createduserid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdUserID', 'Createduserid:') !!}
    {!! Form::text('createdUserID', null, ['class' => 'form-control']) !!}
</div>

<!-- Createddatetime Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdDateTime', 'Createddatetime:') !!}
    {!! Form::date('createdDateTime', null, ['class' => 'form-control']) !!}
</div>

<!-- Createdusername Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdUserName', 'Createdusername:') !!}
    {!! Form::text('createdUserName', null, ['class' => 'form-control']) !!}
</div>

<!-- Modifiedpcid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('modifiedPCID', 'Modifiedpcid:') !!}
    {!! Form::text('modifiedPCID', null, ['class' => 'form-control']) !!}
</div>

<!-- Modifiedusersystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('modifiedUserSystemID', 'Modifiedusersystemid:') !!}
    {!! Form::number('modifiedUserSystemID', null, ['class' => 'form-control']) !!}
</div>

<!-- Modifieduserid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('modifiedUserID', 'Modifieduserid:') !!}
    {!! Form::text('modifiedUserID', null, ['class' => 'form-control']) !!}
</div>

<!-- Modifieddatetime Field -->
<div class="form-group col-sm-6">
    {!! Form::label('modifiedDateTime', 'Modifieddatetime:') !!}
    {!! Form::date('modifiedDateTime', null, ['class' => 'form-control']) !!}
</div>

<!-- Modifiedusername Field -->
<div class="form-group col-sm-6">
    {!! Form::label('modifiedUserName', 'Modifiedusername:') !!}
    {!! Form::text('modifiedUserName', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timestamp', 'Timestamp:') !!}
    {!! Form::date('timestamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Id Store Field -->
<div class="form-group col-sm-6">
    {!! Form::label('id_store', 'Id Store:') !!}
    {!! Form::number('id_store', null, ['class' => 'form-control']) !!}
</div>

<!-- Is Sync Field -->
<div class="form-group col-sm-6">
    {!! Form::label('is_sync', 'Is Sync:') !!}
    {!! Form::number('is_sync', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('shiftDetails.index') !!}" class="btn btn-default">Cancel</a>
</div>
