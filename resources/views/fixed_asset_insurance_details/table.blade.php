<table class="table table-responsive" id="fixedAssetInsuranceDetails-table">
    <thead>
        <tr>
            <th>Companyid</th>
        <th>Faid</th>
        <th>Insuredyn</th>
        <th>Policy</th>
        <th>Policynumber</th>
        <th>Dateofinsurance</th>
        <th>Dateofexpiry</th>
        <th>Insuredvalue</th>
        <th>Insurername</th>
        <th>Locationid</th>
        <th>Buildingnumber</th>
        <th>Openclosedarea</th>
        <th>Containernumber</th>
        <th>Movingitem</th>
        <th>Createdbyuserid</th>
        <th>Createddatetime</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($fixedAssetInsuranceDetails as $fixedAssetInsuranceDetail)
        <tr>
            <td>{!! $fixedAssetInsuranceDetail->companyID !!}</td>
            <td>{!! $fixedAssetInsuranceDetail->faID !!}</td>
            <td>{!! $fixedAssetInsuranceDetail->insuredYN !!}</td>
            <td>{!! $fixedAssetInsuranceDetail->policy !!}</td>
            <td>{!! $fixedAssetInsuranceDetail->policyNumber !!}</td>
            <td>{!! $fixedAssetInsuranceDetail->dateOfInsurance !!}</td>
            <td>{!! $fixedAssetInsuranceDetail->dateOfExpiry !!}</td>
            <td>{!! $fixedAssetInsuranceDetail->insuredValue !!}</td>
            <td>{!! $fixedAssetInsuranceDetail->insurerName !!}</td>
            <td>{!! $fixedAssetInsuranceDetail->locationID !!}</td>
            <td>{!! $fixedAssetInsuranceDetail->buildingNumber !!}</td>
            <td>{!! $fixedAssetInsuranceDetail->openClosedArea !!}</td>
            <td>{!! $fixedAssetInsuranceDetail->containerNumber !!}</td>
            <td>{!! $fixedAssetInsuranceDetail->movingItem !!}</td>
            <td>{!! $fixedAssetInsuranceDetail->createdByUserID !!}</td>
            <td>{!! $fixedAssetInsuranceDetail->createdDateTime !!}</td>
            <td>{!! $fixedAssetInsuranceDetail->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['fixedAssetInsuranceDetails.destroy', $fixedAssetInsuranceDetail->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('fixedAssetInsuranceDetails.show', [$fixedAssetInsuranceDetail->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('fixedAssetInsuranceDetails.edit', [$fixedAssetInsuranceDetail->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>