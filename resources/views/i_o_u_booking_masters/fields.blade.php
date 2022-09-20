<!-- Documentid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('documentID', 'Documentid:') !!}
    {!! Form::text('documentID', null, ['class' => 'form-control']) !!}
</div>

<!-- Serialno Field -->
<div class="form-group col-sm-6">
    {!! Form::label('serialNo', 'Serialno:') !!}
    {!! Form::number('serialNo', null, ['class' => 'form-control']) !!}
</div>

<!-- Iouvoucherautoid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('iouVoucherAutoID', 'Iouvoucherautoid:') !!}
    {!! Form::number('iouVoucherAutoID', null, ['class' => 'form-control']) !!}
</div>

<!-- Bookingcode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('bookingCode', 'Bookingcode:') !!}
    {!! Form::text('bookingCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Bookingdate Field -->
<div class="form-group col-sm-6">
    {!! Form::label('bookingDate', 'Bookingdate:') !!}
    {!! Form::date('bookingDate', null, ['class' => 'form-control','id'=>'bookingDate']) !!}
</div>

@section('scripts')
    <script type="text/javascript">
        $('#bookingDate').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            useCurrent: false
        })
    </script>
@endsection

<!-- Pullfromfuelyn Field -->
<div class="form-group col-sm-6">
    {!! Form::label('pullFromFuelYN', 'Pullfromfuelyn:') !!}
    {!! Form::number('pullFromFuelYN', null, ['class' => 'form-control']) !!}
</div>

<!-- Empid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('empID', 'Empid:') !!}
    {!! Form::number('empID', null, ['class' => 'form-control']) !!}
</div>

<!-- Empname Field -->
<div class="form-group col-sm-6">
    {!! Form::label('empName', 'Empname:') !!}
    {!! Form::text('empName', null, ['class' => 'form-control']) !!}
</div>

<!-- Usertype Field -->
<div class="form-group col-sm-6">
    {!! Form::label('userType', 'Usertype:') !!}
    {!! Form::number('userType', null, ['class' => 'form-control']) !!}
</div>

<!-- Comments Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('comments', 'Comments:') !!}
    {!! Form::textarea('comments', null, ['class' => 'form-control']) !!}
</div>

<!-- Submittedyn Field -->
<div class="form-group col-sm-6">
    {!! Form::label('submittedYN', 'Submittedyn:') !!}
    {!! Form::number('submittedYN', null, ['class' => 'form-control']) !!}
</div>

<!-- Submitteddate Field -->
<div class="form-group col-sm-6">
    {!! Form::label('submittedDate', 'Submitteddate:') !!}
    {!! Form::date('submittedDate', null, ['class' => 'form-control','id'=>'submittedDate']) !!}
</div>

@section('scripts')
    <script type="text/javascript">
        $('#submittedDate').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            useCurrent: false
        })
    </script>
@endsection

<!-- Submittedempid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('submittedEmpID', 'Submittedempid:') !!}
    {!! Form::number('submittedEmpID', null, ['class' => 'form-control']) !!}
</div>

<!-- Confirmedyn Field -->
<div class="form-group col-sm-6">
    {!! Form::label('confirmedYN', 'Confirmedyn:') !!}
    {!! Form::number('confirmedYN', null, ['class' => 'form-control']) !!}
</div>

<!-- Confirmedbyempid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('confirmedByEmpID', 'Confirmedbyempid:') !!}
    {!! Form::number('confirmedByEmpID', null, ['class' => 'form-control']) !!}
</div>

<!-- Confirmedbyname Field -->
<div class="form-group col-sm-6">
    {!! Form::label('confirmedByName', 'Confirmedbyname:') !!}
    {!! Form::text('confirmedByName', null, ['class' => 'form-control']) !!}
</div>

<!-- Confirmeddate Field -->
<div class="form-group col-sm-6">
    {!! Form::label('confirmedDate', 'Confirmeddate:') !!}
    {!! Form::date('confirmedDate', null, ['class' => 'form-control','id'=>'confirmedDate']) !!}
</div>

@section('scripts')
    <script type="text/javascript">
        $('#confirmedDate').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            useCurrent: false
        })
    </script>
@endsection

<!-- Approvedyn Field -->
<div class="form-group col-sm-6">
    {!! Form::label('approvedYN', 'Approvedyn:') !!}
    {!! Form::number('approvedYN', null, ['class' => 'form-control']) !!}
</div>

<!-- Approvedbyempid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('approvedByEmpID', 'Approvedbyempid:') !!}
    {!! Form::number('approvedByEmpID', null, ['class' => 'form-control']) !!}
</div>

<!-- Approvedbyempname Field -->
<div class="form-group col-sm-6">
    {!! Form::label('approvedByEmpName', 'Approvedbyempname:') !!}
    {!! Form::text('approvedByEmpName', null, ['class' => 'form-control']) !!}
</div>

<!-- Approveddate Field -->
<div class="form-group col-sm-6">
    {!! Form::label('approvedDate', 'Approveddate:') !!}
    {!! Form::date('approvedDate', null, ['class' => 'form-control','id'=>'approvedDate']) !!}
</div>

@section('scripts')
    <script type="text/javascript">
        $('#approvedDate').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            useCurrent: false
        })
    </script>
@endsection

<!-- Approvalcomments Field -->
<div class="form-group col-sm-6">
    {!! Form::label('approvalComments', 'Approvalcomments:') !!}
    {!! Form::text('approvalComments', null, ['class' => 'form-control']) !!}
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

<!-- Transactionamount Field -->
<div class="form-group col-sm-6">
    {!! Form::label('transactionAmount', 'Transactionamount:') !!}
    {!! Form::number('transactionAmount', null, ['class' => 'form-control']) !!}
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

<!-- Companylocalamount Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companyLocalAmount', 'Companylocalamount:') !!}
    {!! Form::number('companyLocalAmount', null, ['class' => 'form-control']) !!}
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

<!-- Companyreportingamount Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companyReportingAmount', 'Companyreportingamount:') !!}
    {!! Form::number('companyReportingAmount', null, ['class' => 'form-control']) !!}
</div>

<!-- Companyreportingcurrencydecimalplaces Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companyReportingCurrencyDecimalPlaces', 'Companyreportingcurrencydecimalplaces:') !!}
    {!! Form::number('companyReportingCurrencyDecimalPlaces', null, ['class' => 'form-control']) !!}
</div>

<!-- Empcurrencyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('empCurrencyID', 'Empcurrencyid:') !!}
    {!! Form::number('empCurrencyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Empcurrency Field -->
<div class="form-group col-sm-6">
    {!! Form::label('empCurrency', 'Empcurrency:') !!}
    {!! Form::text('empCurrency', null, ['class' => 'form-control']) !!}
</div>

<!-- Empcurrencyexchangerate Field -->
<div class="form-group col-sm-6">
    {!! Form::label('empCurrencyExchangeRate', 'Empcurrencyexchangerate:') !!}
    {!! Form::number('empCurrencyExchangeRate', null, ['class' => 'form-control']) !!}
</div>

<!-- Empcurrencyamount Field -->
<div class="form-group col-sm-6">
    {!! Form::label('empCurrencyAmount', 'Empcurrencyamount:') !!}
    {!! Form::number('empCurrencyAmount', null, ['class' => 'form-control']) !!}
</div>

<!-- Empcurrencydecimalplaces Field -->
<div class="form-group col-sm-6">
    {!! Form::label('empCurrencyDecimalPlaces', 'Empcurrencydecimalplaces:') !!}
    {!! Form::number('empCurrencyDecimalPlaces', null, ['class' => 'form-control']) !!}
</div>

<!-- Isdeleted Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isDeleted', 'Isdeleted:') !!}
    {!! Form::number('isDeleted', null, ['class' => 'form-control']) !!}
</div>

<!-- Deletedempid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('deletedEmpID', 'Deletedempid:') !!}
    {!! Form::number('deletedEmpID', null, ['class' => 'form-control']) !!}
</div>

<!-- Deleteddate Field -->
<div class="form-group col-sm-6">
    {!! Form::label('deletedDate', 'Deleteddate:') !!}
    {!! Form::date('deletedDate', null, ['class' => 'form-control','id'=>'deletedDate']) !!}
</div>

@section('scripts')
    <script type="text/javascript">
        $('#deletedDate').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            useCurrent: false
        })
    </script>
@endsection

<!-- Currentlevelno Field -->
<div class="form-group col-sm-6">
    {!! Form::label('currentLevelNo', 'Currentlevelno:') !!}
    {!! Form::number('currentLevelNo', null, ['class' => 'form-control']) !!}
</div>

<!-- Companyfinanceyearid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companyFinanceYearID', 'Companyfinanceyearid:') !!}
    {!! Form::number('companyFinanceYearID', null, ['class' => 'form-control']) !!}
</div>

<!-- Companyfinanceyear Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companyFinanceYear', 'Companyfinanceyear:') !!}
    {!! Form::text('companyFinanceYear', null, ['class' => 'form-control']) !!}
</div>

<!-- Fybegin Field -->
<div class="form-group col-sm-6">
    {!! Form::label('FYBegin', 'Fybegin:') !!}
    {!! Form::date('FYBegin', null, ['class' => 'form-control','id'=>'FYBegin']) !!}
</div>

@section('scripts')
    <script type="text/javascript">
        $('#FYBegin').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            useCurrent: false
        })
    </script>
@endsection

<!-- Fyend Field -->
<div class="form-group col-sm-6">
    {!! Form::label('FYEnd', 'Fyend:') !!}
    {!! Form::date('FYEnd', null, ['class' => 'form-control','id'=>'FYEnd']) !!}
</div>

@section('scripts')
    <script type="text/javascript">
        $('#FYEnd').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            useCurrent: false
        })
    </script>
@endsection

<!-- Fyperioddatefrom Field -->
<div class="form-group col-sm-6">
    {!! Form::label('FYPeriodDateFrom', 'Fyperioddatefrom:') !!}
    {!! Form::date('FYPeriodDateFrom', null, ['class' => 'form-control','id'=>'FYPeriodDateFrom']) !!}
</div>

@section('scripts')
    <script type="text/javascript">
        $('#FYPeriodDateFrom').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            useCurrent: false
        })
    </script>
@endsection

<!-- Fyperioddateto Field -->
<div class="form-group col-sm-6">
    {!! Form::label('FYPeriodDateTo', 'Fyperioddateto:') !!}
    {!! Form::date('FYPeriodDateTo', null, ['class' => 'form-control','id'=>'FYPeriodDateTo']) !!}
</div>

@section('scripts')
    <script type="text/javascript">
        $('#FYPeriodDateTo').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            useCurrent: false
        })
    </script>
@endsection

<!-- Companyfinanceperiodid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companyFinancePeriodID', 'Companyfinanceperiodid:') !!}
    {!! Form::number('companyFinancePeriodID', null, ['class' => 'form-control']) !!}
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

<!-- Createduserid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdUserID', 'Createduserid:') !!}
    {!! Form::text('createdUserID', null, ['class' => 'form-control']) !!}
</div>

<!-- Createddatetime Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdDateTime', 'Createddatetime:') !!}
    {!! Form::date('createdDateTime', null, ['class' => 'form-control','id'=>'createdDateTime']) !!}
</div>

@section('scripts')
    <script type="text/javascript">
        $('#createdDateTime').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            useCurrent: false
        })
    </script>
@endsection

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

<!-- Modifieduserid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('modifiedUserID', 'Modifieduserid:') !!}
    {!! Form::text('modifiedUserID', null, ['class' => 'form-control']) !!}
</div>

<!-- Modifieddatetime Field -->
<div class="form-group col-sm-6">
    {!! Form::label('modifiedDateTime', 'Modifieddatetime:') !!}
    {!! Form::date('modifiedDateTime', null, ['class' => 'form-control','id'=>'modifiedDateTime']) !!}
</div>

@section('scripts')
    <script type="text/javascript">
        $('#modifiedDateTime').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            useCurrent: false
        })
    </script>
@endsection

<!-- Modifiedusername Field -->
<div class="form-group col-sm-6">
    {!! Form::label('modifiedUserName', 'Modifiedusername:') !!}
    {!! Form::text('modifiedUserName', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timestamp', 'Timestamp:') !!}
    {!! Form::date('timestamp', null, ['class' => 'form-control','id'=>'timestamp']) !!}
</div>

@section('scripts')
    <script type="text/javascript">
        $('#timestamp').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            useCurrent: false
        })
    </script>
@endsection

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{{ route('iOUBookingMasters.index') }}" class="btn btn-default">Cancel</a>
</div>
