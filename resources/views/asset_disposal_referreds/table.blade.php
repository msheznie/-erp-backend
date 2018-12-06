<table class="table table-responsive" id="assetDisposalReferreds-table">
    <thead>
        <tr>
            <th>Assetdisposalmasterautoid</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Tocompanysystemid</th>
        <th>Tocompanyid</th>
        <th>Customerid</th>
        <th>Serialno</th>
        <th>Companyfinanceyearid</th>
        <th>Companyfinanceperiodid</th>
        <th>Fybiggin</th>
        <th>Fyend</th>
        <th>Fyperioddatefrom</th>
        <th>Fyperioddateto</th>
        <th>Documentsystemid</th>
        <th>Documentid</th>
        <th>Disposaldocumentcode</th>
        <th>Disposaldocumentdate</th>
        <th>Narration</th>
        <th>Revenuepercentage</th>
        <th>Confirmedyn</th>
        <th>Confimedbyempsystemid</th>
        <th>Confimedbyempid</th>
        <th>Confirmedbyempname</th>
        <th>Confirmeddate</th>
        <th>Approvedyn</th>
        <th>Approvedbyuserid</th>
        <th>Approvedbyusersystemid</th>
        <th>Approveddate</th>
        <th>Disposaltype</th>
        <th>Timesreferred</th>
        <th>Refferedbackyn</th>
        <th>Rolllevforapp Curr</th>
        <th>Createdusersystemid</th>
        <th>Createduserid</th>
        <th>Createddatetime</th>
        <th>Modifiedusersystemid</th>
        <th>Modifieduser</th>
        <th>Modifiedpc</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($assetDisposalReferreds as $assetDisposalReferred)
        <tr>
            <td>{!! $assetDisposalReferred->assetdisposalMasterAutoID !!}</td>
            <td>{!! $assetDisposalReferred->companySystemID !!}</td>
            <td>{!! $assetDisposalReferred->companyID !!}</td>
            <td>{!! $assetDisposalReferred->toCompanySystemID !!}</td>
            <td>{!! $assetDisposalReferred->toCompanyID !!}</td>
            <td>{!! $assetDisposalReferred->customerID !!}</td>
            <td>{!! $assetDisposalReferred->serialNo !!}</td>
            <td>{!! $assetDisposalReferred->companyFinanceYearID !!}</td>
            <td>{!! $assetDisposalReferred->companyFinancePeriodID !!}</td>
            <td>{!! $assetDisposalReferred->FYBiggin !!}</td>
            <td>{!! $assetDisposalReferred->FYEnd !!}</td>
            <td>{!! $assetDisposalReferred->FYPeriodDateFrom !!}</td>
            <td>{!! $assetDisposalReferred->FYPeriodDateTo !!}</td>
            <td>{!! $assetDisposalReferred->documentSystemID !!}</td>
            <td>{!! $assetDisposalReferred->documentID !!}</td>
            <td>{!! $assetDisposalReferred->disposalDocumentCode !!}</td>
            <td>{!! $assetDisposalReferred->disposalDocumentDate !!}</td>
            <td>{!! $assetDisposalReferred->narration !!}</td>
            <td>{!! $assetDisposalReferred->revenuePercentage !!}</td>
            <td>{!! $assetDisposalReferred->confirmedYN !!}</td>
            <td>{!! $assetDisposalReferred->confimedByEmpSystemID !!}</td>
            <td>{!! $assetDisposalReferred->confimedByEmpID !!}</td>
            <td>{!! $assetDisposalReferred->confirmedByEmpName !!}</td>
            <td>{!! $assetDisposalReferred->confirmedDate !!}</td>
            <td>{!! $assetDisposalReferred->approvedYN !!}</td>
            <td>{!! $assetDisposalReferred->approvedByUserID !!}</td>
            <td>{!! $assetDisposalReferred->approvedByUserSystemID !!}</td>
            <td>{!! $assetDisposalReferred->approvedDate !!}</td>
            <td>{!! $assetDisposalReferred->disposalType !!}</td>
            <td>{!! $assetDisposalReferred->timesReferred !!}</td>
            <td>{!! $assetDisposalReferred->refferedBackYN !!}</td>
            <td>{!! $assetDisposalReferred->RollLevForApp_curr !!}</td>
            <td>{!! $assetDisposalReferred->createdUserSystemID !!}</td>
            <td>{!! $assetDisposalReferred->createdUserID !!}</td>
            <td>{!! $assetDisposalReferred->createdDateTime !!}</td>
            <td>{!! $assetDisposalReferred->modifiedUserSystemID !!}</td>
            <td>{!! $assetDisposalReferred->modifiedUser !!}</td>
            <td>{!! $assetDisposalReferred->modifiedPc !!}</td>
            <td>{!! $assetDisposalReferred->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['assetDisposalReferreds.destroy', $assetDisposalReferred->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('assetDisposalReferreds.show', [$assetDisposalReferred->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('assetDisposalReferreds.edit', [$assetDisposalReferred->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>