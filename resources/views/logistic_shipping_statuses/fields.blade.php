<!-- Logisticmasterid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('logisticMasterID', 'Logisticmasterid:') !!}
    {!! Form::number('logisticMasterID', null, ['class' => 'form-control']) !!}
</div>

<!-- Shippingstatusid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('shippingStatusID', 'Shippingstatusid:') !!}
    {!! Form::number('shippingStatusID', null, ['class' => 'form-control']) !!}
</div>

<!-- Statusdate Field -->
<div class="form-group col-sm-6">
    {!! Form::label('statusDate', 'Statusdate:') !!}
    {!! Form::date('statusDate', null, ['class' => 'form-control']) !!}
</div>

<!-- Statuscomment Field -->
<div class="form-group col-sm-6">
    {!! Form::label('statusComment', 'Statuscomment:') !!}
    {!! Form::text('statusComment', null, ['class' => 'form-control']) !!}
</div>

<!-- Createduserid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdUserID', 'Createduserid:') !!}
    {!! Form::text('createdUserID', null, ['class' => 'form-control']) !!}
</div>

<!-- Createdpcid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdPCID', 'Createdpcid:') !!}
    {!! Form::text('createdPCID', null, ['class' => 'form-control']) !!}
</div>

<!-- Createddatetime Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdDateTime', 'Createddatetime:') !!}
    {!! Form::date('createdDateTime', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timestamp', 'Timestamp:') !!}
    {!! Form::date('timestamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('logisticShippingStatuses.index') !!}" class="btn btn-default">Cancel</a>
</div>
