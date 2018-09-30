<table class="table table-responsive" id="assetCapitalizations-table">
    <thead>
        <tr>
            <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Documentsystemid</th>
        <th>Documentid</th>
        <th>Documentdate</th>
        <th>Companyfinanceyearid</th>
        <th>Serialno</th>
        <th>Fybiggin</th>
        <th>Fyend</th>
        <th>Companyfinanceperiodid</th>
        <th>Fyperioddatefrom</th>
        <th>Fyperioddateto</th>
        <th>Narration</th>
        <th>Allocationtypeid</th>
        <th>Facatid</th>
        <th>Faid</th>
        <th>Assetnbvlocal</th>
        <th>Assetnbvrpt</th>
        <th>Confirmedyn</th>
        <th>Confirmedbyempsystemid</th>
        <th>Confirmedbyempid</th>
        <th>Confirmedbyname</th>
        <th>Confirmeddate</th>
        <th>Approved</th>
        <th>Approveddate</th>
        <th>Approvedbyuserid</th>
        <th>Approvedbyusersystemid</th>
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
    @foreach($assetCapitalizations as $assetCapitalization)
        <tr>
            <td>{!! $assetCapitalization->companySystemID !!}</td>
            <td>{!! $assetCapitalization->companyID !!}</td>
            <td>{!! $assetCapitalization->documentSystemID !!}</td>
            <td>{!! $assetCapitalization->documentID !!}</td>
            <td>{!! $assetCapitalization->documentDate !!}</td>
            <td>{!! $assetCapitalization->companyFinanceYearID !!}</td>
            <td>{!! $assetCapitalization->serialNo !!}</td>
            <td>{!! $assetCapitalization->FYBiggin !!}</td>
            <td>{!! $assetCapitalization->FYEnd !!}</td>
            <td>{!! $assetCapitalization->companyFinancePeriodID !!}</td>
            <td>{!! $assetCapitalization->FYPeriodDateFrom !!}</td>
            <td>{!! $assetCapitalization->FYPeriodDateTo !!}</td>
            <td>{!! $assetCapitalization->narration !!}</td>
            <td>{!! $assetCapitalization->allocationTypeID !!}</td>
            <td>{!! $assetCapitalization->faCatID !!}</td>
            <td>{!! $assetCapitalization->faID !!}</td>
            <td>{!! $assetCapitalization->assetNBVLocal !!}</td>
            <td>{!! $assetCapitalization->assetNBVRpt !!}</td>
            <td>{!! $assetCapitalization->confirmedYN !!}</td>
            <td>{!! $assetCapitalization->confirmedByEmpSystemID !!}</td>
            <td>{!! $assetCapitalization->confirmedByEmpID !!}</td>
            <td>{!! $assetCapitalization->confirmedByName !!}</td>
            <td>{!! $assetCapitalization->confirmedDate !!}</td>
            <td>{!! $assetCapitalization->approved !!}</td>
            <td>{!! $assetCapitalization->approvedDate !!}</td>
            <td>{!! $assetCapitalization->approvedByUserID !!}</td>
            <td>{!! $assetCapitalization->approvedByUserSystemID !!}</td>
            <td>{!! $assetCapitalization->createdUserGroup !!}</td>
            <td>{!! $assetCapitalization->createdUserSystemID !!}</td>
            <td>{!! $assetCapitalization->createdUserID !!}</td>
            <td>{!! $assetCapitalization->createdPcID !!}</td>
            <td>{!! $assetCapitalization->modifiedUserSystemID !!}</td>
            <td>{!! $assetCapitalization->modifiedUser !!}</td>
            <td>{!! $assetCapitalization->modifiedPc !!}</td>
            <td>{!! $assetCapitalization->createdDateTime !!}</td>
            <td>{!! $assetCapitalization->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['assetCapitalizations.destroy', $assetCapitalization->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('assetCapitalizations.show', [$assetCapitalization->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('assetCapitalizations.edit', [$assetCapitalization->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>