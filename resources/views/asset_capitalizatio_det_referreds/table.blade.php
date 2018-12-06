<table class="table table-responsive" id="assetCapitalizatioDetReferreds-table">
    <thead>
        <tr>
            <th>Capitalizationdetailid</th>
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
        <th>Timesreferred</th>
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
    @foreach($assetCapitalizatioDetReferreds as $assetCapitalizatioDetReferred)
        <tr>
            <td>{!! $assetCapitalizatioDetReferred->capitalizationDetailID !!}</td>
            <td>{!! $assetCapitalizatioDetReferred->capitalizationID !!}</td>
            <td>{!! $assetCapitalizatioDetReferred->faID !!}</td>
            <td>{!! $assetCapitalizatioDetReferred->faCode !!}</td>
            <td>{!! $assetCapitalizatioDetReferred->assetDescription !!}</td>
            <td>{!! $assetCapitalizatioDetReferred->companySystemID !!}</td>
            <td>{!! $assetCapitalizatioDetReferred->companyID !!}</td>
            <td>{!! $assetCapitalizatioDetReferred->serviceLineSystemID !!}</td>
            <td>{!! $assetCapitalizatioDetReferred->serviceLineCode !!}</td>
            <td>{!! $assetCapitalizatioDetReferred->dateAQ !!}</td>
            <td>{!! $assetCapitalizatioDetReferred->assetNBVLocal !!}</td>
            <td>{!! $assetCapitalizatioDetReferred->assetNBVRpt !!}</td>
            <td>{!! $assetCapitalizatioDetReferred->allocatedAmountLocal !!}</td>
            <td>{!! $assetCapitalizatioDetReferred->allocatedAmountRpt !!}</td>
            <td>{!! $assetCapitalizatioDetReferred->timesReferred !!}</td>
            <td>{!! $assetCapitalizatioDetReferred->createdUserGroup !!}</td>
            <td>{!! $assetCapitalizatioDetReferred->createdUserSystemID !!}</td>
            <td>{!! $assetCapitalizatioDetReferred->createdUserID !!}</td>
            <td>{!! $assetCapitalizatioDetReferred->createdPcID !!}</td>
            <td>{!! $assetCapitalizatioDetReferred->modifiedUserSystemID !!}</td>
            <td>{!! $assetCapitalizatioDetReferred->modifiedUser !!}</td>
            <td>{!! $assetCapitalizatioDetReferred->modifiedPc !!}</td>
            <td>{!! $assetCapitalizatioDetReferred->createdDateTime !!}</td>
            <td>{!! $assetCapitalizatioDetReferred->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['assetCapitalizatioDetReferreds.destroy', $assetCapitalizatioDetReferred->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('assetCapitalizatioDetReferreds.show', [$assetCapitalizatioDetReferred->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('assetCapitalizatioDetReferreds.edit', [$assetCapitalizatioDetReferred->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>