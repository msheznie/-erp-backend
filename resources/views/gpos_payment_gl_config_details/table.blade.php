<table class="table table-responsive" id="gposPaymentGlConfigDetails-table">
    <thead>
        <tr>
            <th>Paymentconfigmasterid</th>
        <th>Glcode</th>
        <th>Companyid</th>
        <th>Companycode</th>
        <th>Warehouseid</th>
        <th>Isauthrequired</th>
        <th>Createdusergroup</th>
        <th>Createdpcid</th>
        <th>Createduserid</th>
        <th>Createdusername</th>
        <th>Createddatetime</th>
        <th>Modifiedpcid</th>
        <th>Modifieduserid</th>
        <th>Modifiedusername</th>
        <th>Modifieddatetime</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($gposPaymentGlConfigDetails as $gposPaymentGlConfigDetail)
        <tr>
            <td>{!! $gposPaymentGlConfigDetail->paymentConfigMasterID !!}</td>
            <td>{!! $gposPaymentGlConfigDetail->GLCode !!}</td>
            <td>{!! $gposPaymentGlConfigDetail->companyID !!}</td>
            <td>{!! $gposPaymentGlConfigDetail->companyCode !!}</td>
            <td>{!! $gposPaymentGlConfigDetail->warehouseID !!}</td>
            <td>{!! $gposPaymentGlConfigDetail->isAuthRequired !!}</td>
            <td>{!! $gposPaymentGlConfigDetail->createdUserGroup !!}</td>
            <td>{!! $gposPaymentGlConfigDetail->createdPCID !!}</td>
            <td>{!! $gposPaymentGlConfigDetail->createdUserID !!}</td>
            <td>{!! $gposPaymentGlConfigDetail->createdUserName !!}</td>
            <td>{!! $gposPaymentGlConfigDetail->createdDateTime !!}</td>
            <td>{!! $gposPaymentGlConfigDetail->modifiedPCID !!}</td>
            <td>{!! $gposPaymentGlConfigDetail->modifiedUserID !!}</td>
            <td>{!! $gposPaymentGlConfigDetail->modifiedUserName !!}</td>
            <td>{!! $gposPaymentGlConfigDetail->modifiedDateTime !!}</td>
            <td>{!! $gposPaymentGlConfigDetail->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['gposPaymentGlConfigDetails.destroy', $gposPaymentGlConfigDetail->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('gposPaymentGlConfigDetails.show', [$gposPaymentGlConfigDetail->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('gposPaymentGlConfigDetails.edit', [$gposPaymentGlConfigDetail->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>