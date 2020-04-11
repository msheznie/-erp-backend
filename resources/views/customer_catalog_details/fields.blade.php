<!-- Customercatalogmasterid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('customerCatalogMasterID', 'Customercatalogmasterid:') !!}
    {!! Form::number('customerCatalogMasterID', null, ['class' => 'form-control']) !!}
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

<!-- Partno Field -->
<div class="form-group col-sm-6">
    {!! Form::label('partNo', 'Partno:') !!}
    {!! Form::text('partNo', null, ['class' => 'form-control']) !!}
</div>

<!-- Localcurrencyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('localCurrencyID', 'Localcurrencyid:') !!}
    {!! Form::number('localCurrencyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Localprice Field -->
<div class="form-group col-sm-6">
    {!! Form::label('localPrice', 'Localprice:') !!}
    {!! Form::number('localPrice', null, ['class' => 'form-control']) !!}
</div>

<!-- Reportingcurrencyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('reportingCurrencyID', 'Reportingcurrencyid:') !!}
    {!! Form::number('reportingCurrencyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Reportingprice Field -->
<div class="form-group col-sm-6">
    {!! Form::label('reportingPrice', 'Reportingprice:') !!}
    {!! Form::number('reportingPrice', null, ['class' => 'form-control']) !!}
</div>

<!-- Leadtime Field -->
<div class="form-group col-sm-6">
    {!! Form::label('leadTime', 'Leadtime:') !!}
    {!! Form::number('leadTime', null, ['class' => 'form-control']) !!}
</div>

<!-- Isdelete Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isDelete', 'Isdelete:') !!}
    {!! Form::number('isDelete', null, ['class' => 'form-control']) !!}
</div>

<!-- Timstamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timstamp', 'Timstamp:') !!}
    {!! Form::date('timstamp', null, ['class' => 'form-control','id'=>'timstamp']) !!}
</div>

@section('scripts')
    <script type="text/javascript">
        $('#timstamp').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            useCurrent: false
        })
    </script>
@endsection

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('customerCatalogDetails.index') !!}" class="btn btn-default">Cancel</a>
</div>
