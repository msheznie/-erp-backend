<!-- Ordertype Field -->
<div class="form-group col-sm-6">
    {!! Form::label('orderType', 'Ordertype:') !!}
    <label class="checkbox-inline">
        {!! Form::hidden('orderType', 0) !!}
        {!! Form::checkbox('orderType', '1', null) !!}
    </label>
</div>


<!-- Deliveryordercode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('deliveryOrderCode', 'Deliveryordercode:') !!}
    {!! Form::text('deliveryOrderCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Companysystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companySystemId', 'Companysystemid:') !!}
    {!! Form::number('companySystemId', null, ['class' => 'form-control']) !!}
</div>

<!-- Documentsystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('documentSystemId', 'Documentsystemid:') !!}
    {!! Form::number('documentSystemId', null, ['class' => 'form-control']) !!}
</div>

<!-- Companyfinanceyearid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companyFinanceYearID', 'Companyfinanceyearid:') !!}
    {!! Form::number('companyFinanceYearID', null, ['class' => 'form-control']) !!}
</div>

<!-- Fybiggin Field -->
<div class="form-group col-sm-6">
    {!! Form::label('FYBiggin', 'Fybiggin:') !!}
    {!! Form::date('FYBiggin', null, ['class' => 'form-control','id'=>'FYBiggin']) !!}
</div>

@section('scripts')
    <script type="text/javascript">
        $('#FYBiggin').datetimepicker({
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

<!-- Companyfinanceperiodid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companyFinancePeriodID', 'Companyfinanceperiodid:') !!}
    {!! Form::number('companyFinancePeriodID', null, ['class' => 'form-control']) !!}
</div>

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

<!-- Deliveryorderdate Field -->
<div class="form-group col-sm-6">
    {!! Form::label('deliveryOrderDate', 'Deliveryorderdate:') !!}
    {!! Form::date('deliveryOrderDate', null, ['class' => 'form-control','id'=>'deliveryOrderDate']) !!}
</div>

@section('scripts')
    <script type="text/javascript">
        $('#deliveryOrderDate').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            useCurrent: false
        })
    </script>
@endsection

<!-- Warehousesystemcode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('wareHouseSystemCode', 'Warehousesystemcode:') !!}
    {!! Form::number('wareHouseSystemCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Servicelinesystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('serviceLineSystemID', 'Servicelinesystemid:') !!}
    {!! Form::number('serviceLineSystemID', null, ['class' => 'form-control']) !!}
</div>

<!-- Referenceno Field -->
<div class="form-group col-sm-6">
    {!! Form::label('referenceNo', 'Referenceno:') !!}
    {!! Form::text('referenceNo', null, ['class' => 'form-control']) !!}
</div>

<!-- Customerid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('customerID', 'Customerid:') !!}
    {!! Form::number('customerID', null, ['class' => 'form-control']) !!}
</div>

<!-- Salespersonid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('salesPersonID', 'Salespersonid:') !!}
    {!! Form::number('salesPersonID', null, ['class' => 'form-control']) !!}
</div>

<!-- Narration Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('narration', 'Narration:') !!}
    {!! Form::textarea('narration', null, ['class' => 'form-control']) !!}
</div>

<!-- Notes Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('notes', 'Notes:') !!}
    {!! Form::textarea('notes', null, ['class' => 'form-control']) !!}
</div>

<!-- Contactpersonnumber Field -->
<div class="form-group col-sm-6">
    {!! Form::label('contactPersonNumber', 'Contactpersonnumber:') !!}
    {!! Form::text('contactPersonNumber', null, ['class' => 'form-control']) !!}
</div>

<!-- Contactpersonname Field -->
<div class="form-group col-sm-6">
    {!! Form::label('contactPersonName', 'Contactpersonname:') !!}
    {!! Form::text('contactPersonName', null, ['class' => 'form-control']) !!}
</div>

<!-- Transactioncurrencyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('transactionCurrencyID', 'Transactioncurrencyid:') !!}
    {!! Form::number('transactionCurrencyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Transactioncurrencyer Field -->
<div class="form-group col-sm-6">
    {!! Form::label('transactionCurrencyER', 'Transactioncurrencyer:') !!}
    {!! Form::number('transactionCurrencyER', null, ['class' => 'form-control']) !!}
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

<!-- Companylocalcurrencyer Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companyLocalCurrencyER', 'Companylocalcurrencyer:') !!}
    {!! Form::number('companyLocalCurrencyER', null, ['class' => 'form-control']) !!}
</div>

<!-- Companylocalamount Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companyLocalAmount', 'Companylocalamount:') !!}
    {!! Form::number('companyLocalAmount', null, ['class' => 'form-control']) !!}
</div>

<!-- Companyreportingcurrencyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companyReportingCurrencyID', 'Companyreportingcurrencyid:') !!}
    {!! Form::number('companyReportingCurrencyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Companyreportingcurrencyer Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companyReportingCurrencyER', 'Companyreportingcurrencyer:') !!}
    {!! Form::number('companyReportingCurrencyER', null, ['class' => 'form-control']) !!}
</div>

<!-- Companyreportingamount Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companyReportingAmount', 'Companyreportingamount:') !!}
    {!! Form::number('companyReportingAmount', null, ['class' => 'form-control']) !!}
</div>

<!-- Confirmedyn Field -->
<div class="form-group col-sm-6">
    {!! Form::label('confirmedYN', 'Confirmedyn:') !!}
    {!! Form::number('confirmedYN', null, ['class' => 'form-control']) !!}
</div>

<!-- Confirmedbyempsystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('confirmedByEmpSystemID', 'Confirmedbyempsystemid:') !!}
    {!! Form::number('confirmedByEmpSystemID', null, ['class' => 'form-control']) !!}
</div>

<!-- Confirmedbyempid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('confirmedByEmpID', 'Confirmedbyempid:') !!}
    {!! Form::text('confirmedByEmpID', null, ['class' => 'form-control']) !!}
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

<!-- Approvedempsystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('approvedEmpSystemID', 'Approvedempsystemid:') !!}
    {!! Form::number('approvedEmpSystemID', null, ['class' => 'form-control']) !!}
</div>

<!-- Approvedbyempid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('approvedbyEmpID', 'Approvedbyempid:') !!}
    {!! Form::text('approvedbyEmpID', null, ['class' => 'form-control']) !!}
</div>

<!-- Approvedbyempname Field -->
<div class="form-group col-sm-6">
    {!! Form::label('approvedbyEmpName', 'Approvedbyempname:') !!}
    {!! Form::text('approvedbyEmpName', null, ['class' => 'form-control']) !!}
</div>

<!-- Refferedbackyn Field -->
<div class="form-group col-sm-6">
    {!! Form::label('refferedBackYN', 'Refferedbackyn:') !!}
    {!! Form::number('refferedBackYN', null, ['class' => 'form-control']) !!}
</div>

<!-- Timesreferred Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timesReferred', 'Timesreferred:') !!}
    {!! Form::number('timesReferred', null, ['class' => 'form-control']) !!}
</div>

<!-- Rolllevforapp Curr Field -->
<div class="form-group col-sm-6">
    {!! Form::label('RollLevForApp_curr', 'Rolllevforapp Curr:') !!}
    {!! Form::number('RollLevForApp_curr', null, ['class' => 'form-control']) !!}
</div>

<!-- Closedyn Field -->
<div class="form-group col-sm-6">
    {!! Form::label('closedYN', 'Closedyn:') !!}
    {!! Form::number('closedYN', null, ['class' => 'form-control']) !!}
</div>

<!-- Closeddate Field -->
<div class="form-group col-sm-6">
    {!! Form::label('closedDate', 'Closeddate:') !!}
    {!! Form::date('closedDate', null, ['class' => 'form-control','id'=>'closedDate']) !!}
</div>

@section('scripts')
    <script type="text/javascript">
        $('#closedDate').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            useCurrent: false
        })
    </script>
@endsection

<!-- Closedreason Field -->
<div class="form-group col-sm-6">
    {!! Form::label('closedReason', 'Closedreason:') !!}
    {!! Form::text('closedReason', null, ['class' => 'form-control']) !!}
</div>

<!-- Createdusersystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdUserSystemID', 'Createdusersystemid:') !!}
    {!! Form::number('createdUserSystemID', null, ['class' => 'form-control']) !!}
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

<!-- Modifiedusersystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('modifiedUserSystemID', 'Modifiedusersystemid:') !!}
    {!! Form::number('modifiedUserSystemID', null, ['class' => 'form-control']) !!}
</div>

<!-- Modifiedpcid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('modifiedPCID', 'Modifiedpcid:') !!}
    {!! Form::text('modifiedPCID', null, ['class' => 'form-control']) !!}
</div>

<!-- Modifieduserid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('modifiedUserID', 'Modifieduserid:') !!}
    {!! Form::number('modifiedUserID', null, ['class' => 'form-control']) !!}
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
    <a href="{{ route('deliveryOrders.index') }}" class="btn btn-default">Cancel</a>
</div>
