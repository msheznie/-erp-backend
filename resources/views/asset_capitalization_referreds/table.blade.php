<table class="table table-responsive" id="assetCapitalizationReferreds-table">
    <thead>
        <tr>
            <th>Capitalizationid</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Documentsystemid</th>
        <th>Documentid</th>
        <th>Capitalizationcode</th>
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
        <th>Contraaccountsystemid</th>
        <th>Contraaccountglcode</th>
        <th>Assetnbvlocal</th>
        <th>Assetnbvrpt</th>
        <th>Timesreferred</th>
        <th>Refferedbackyn</th>
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
        <th>Createddatetime</th>
        <th>Modifiedusersystemid</th>
        <th>Modifieduser</th>
        <th>Modifiedpc</th>
        <th>Cancelyn</th>
        <th>Cancelcomment</th>
        <th>Canceldate</th>
        <th>Cancelledbyempsystemid</th>
        <th>Canceledbyempid</th>
        <th>Canceledbyempname</th>
        <th>Rolllevforapp Curr</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($assetCapitalizationReferreds as $assetCapitalizationReferred)
        <tr>
            <td>{!! $assetCapitalizationReferred->capitalizationID !!}</td>
            <td>{!! $assetCapitalizationReferred->companySystemID !!}</td>
            <td>{!! $assetCapitalizationReferred->companyID !!}</td>
            <td>{!! $assetCapitalizationReferred->documentSystemID !!}</td>
            <td>{!! $assetCapitalizationReferred->documentID !!}</td>
            <td>{!! $assetCapitalizationReferred->capitalizationCode !!}</td>
            <td>{!! $assetCapitalizationReferred->documentDate !!}</td>
            <td>{!! $assetCapitalizationReferred->companyFinanceYearID !!}</td>
            <td>{!! $assetCapitalizationReferred->serialNo !!}</td>
            <td>{!! $assetCapitalizationReferred->FYBiggin !!}</td>
            <td>{!! $assetCapitalizationReferred->FYEnd !!}</td>
            <td>{!! $assetCapitalizationReferred->companyFinancePeriodID !!}</td>
            <td>{!! $assetCapitalizationReferred->FYPeriodDateFrom !!}</td>
            <td>{!! $assetCapitalizationReferred->FYPeriodDateTo !!}</td>
            <td>{!! $assetCapitalizationReferred->narration !!}</td>
            <td>{!! $assetCapitalizationReferred->allocationTypeID !!}</td>
            <td>{!! $assetCapitalizationReferred->faCatID !!}</td>
            <td>{!! $assetCapitalizationReferred->faID !!}</td>
            <td>{!! $assetCapitalizationReferred->contraAccountSystemID !!}</td>
            <td>{!! $assetCapitalizationReferred->contraAccountGLCode !!}</td>
            <td>{!! $assetCapitalizationReferred->assetNBVLocal !!}</td>
            <td>{!! $assetCapitalizationReferred->assetNBVRpt !!}</td>
            <td>{!! $assetCapitalizationReferred->timesReferred !!}</td>
            <td>{!! $assetCapitalizationReferred->refferedBackYN !!}</td>
            <td>{!! $assetCapitalizationReferred->confirmedYN !!}</td>
            <td>{!! $assetCapitalizationReferred->confirmedByEmpSystemID !!}</td>
            <td>{!! $assetCapitalizationReferred->confirmedByEmpID !!}</td>
            <td>{!! $assetCapitalizationReferred->confirmedByName !!}</td>
            <td>{!! $assetCapitalizationReferred->confirmedDate !!}</td>
            <td>{!! $assetCapitalizationReferred->approved !!}</td>
            <td>{!! $assetCapitalizationReferred->approvedDate !!}</td>
            <td>{!! $assetCapitalizationReferred->approvedByUserID !!}</td>
            <td>{!! $assetCapitalizationReferred->approvedByUserSystemID !!}</td>
            <td>{!! $assetCapitalizationReferred->createdUserGroup !!}</td>
            <td>{!! $assetCapitalizationReferred->createdUserSystemID !!}</td>
            <td>{!! $assetCapitalizationReferred->createdUserID !!}</td>
            <td>{!! $assetCapitalizationReferred->createdPcID !!}</td>
            <td>{!! $assetCapitalizationReferred->createdDateTime !!}</td>
            <td>{!! $assetCapitalizationReferred->modifiedUserSystemID !!}</td>
            <td>{!! $assetCapitalizationReferred->modifiedUser !!}</td>
            <td>{!! $assetCapitalizationReferred->modifiedPc !!}</td>
            <td>{!! $assetCapitalizationReferred->cancelYN !!}</td>
            <td>{!! $assetCapitalizationReferred->cancelComment !!}</td>
            <td>{!! $assetCapitalizationReferred->cancelDate !!}</td>
            <td>{!! $assetCapitalizationReferred->cancelledByEmpSystemID !!}</td>
            <td>{!! $assetCapitalizationReferred->canceledByEmpID !!}</td>
            <td>{!! $assetCapitalizationReferred->canceledByEmpName !!}</td>
            <td>{!! $assetCapitalizationReferred->RollLevForApp_curr !!}</td>
            <td>{!! $assetCapitalizationReferred->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['assetCapitalizationReferreds.destroy', $assetCapitalizationReferred->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('assetCapitalizationReferreds.show', [$assetCapitalizationReferred->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('assetCapitalizationReferreds.edit', [$assetCapitalizationReferred->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>