<table class="table table-responsive" id="assetCapitalizationDetails-table">
    <thead>
        <tr>
            <th>Capitalizationid</th>
        <th>Faid</th>
        <th>Facode</th>
        <th>Assetdescription</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Servicelinesystemid</th>
        <th>Servicelinecode</th>
        <th>Dateaq</th>
        <th>Assetnbvlocal</th>
        <th>Assetnbvrpt</th>
        <th>Allocatedamountlocal</th>
        <th>Allocatedamountrpt</th>
        <th>Createdusergroup</th>
        <th>Createdusersystemid</th>
        <th>Createduserid</th>
        <th>Createdpcid</th>
        <th>Modifiedusersystemid</th>
        <th>Modifieduser</th>
        <th>Modifiedpc</th>
        <th>Createddatetime</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($assetCapitalizationDetails as $assetCapitalizationDetail)
        <tr>
            <td>{!! $assetCapitalizationDetail->capitalizationID !!}</td>
            <td>{!! $assetCapitalizationDetail->faID !!}</td>
            <td>{!! $assetCapitalizationDetail->faCode !!}</td>
            <td>{!! $assetCapitalizationDetail->assetDescription !!}</td>
            <td>{!! $assetCapitalizationDetail->companySystemID !!}</td>
            <td>{!! $assetCapitalizationDetail->companyID !!}</td>
            <td>{!! $assetCapitalizationDetail->serviceLineSystemID !!}</td>
            <td>{!! $assetCapitalizationDetail->serviceLineCode !!}</td>
            <td>{!! $assetCapitalizationDetail->dateAQ !!}</td>
            <td>{!! $assetCapitalizationDetail->assetNBVLocal !!}</td>
            <td>{!! $assetCapitalizationDetail->assetNBVRpt !!}</td>
            <td>{!! $assetCapitalizationDetail->allocatedAmountLocal !!}</td>
            <td>{!! $assetCapitalizationDetail->allocatedAmountRpt !!}</td>
            <td>{!! $assetCapitalizationDetail->createdUserGroup !!}</td>
            <td>{!! $assetCapitalizationDetail->createdUserSystemID !!}</td>
            <td>{!! $assetCapitalizationDetail->createdUserID !!}</td>
            <td>{!! $assetCapitalizationDetail->createdPcID !!}</td>
            <td>{!! $assetCapitalizationDetail->modifiedUserSystemID !!}</td>
            <td>{!! $assetCapitalizationDetail->modifiedUser !!}</td>
            <td>{!! $assetCapitalizationDetail->modifiedPc !!}</td>
            <td>{!! $assetCapitalizationDetail->createdDateTime !!}</td>
            <td>{!! $assetCapitalizationDetail->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['assetCapitalizationDetails.destroy', $assetCapitalizationDetail->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('assetCapitalizationDetails.show', [$assetCapitalizationDetail->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('assetCapitalizationDetails.edit', [$assetCapitalizationDetail->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>