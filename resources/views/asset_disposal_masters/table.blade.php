<table class="table table-responsive" id="assetDisposalMasters-table">
    <thead>
        <tr>
            <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Tocompanysystemid</th>
        <th>Tocompanyid</th>
        <th>Customerid</th>
        <th>Serialno</th>
        <th>Companyfinanceyearid</th>
        <th>Fybiggin</th>
        <th>Fyend</th>
        <th>Fyperioddatefrom</th>
        <th>Fyperioddateto</th>
        <th>Documentsystemid</th>
        <th>Documentid</th>
        <th>Disposaldocumentcode</th>
        <th>Disposaldocumentdate</th>
        <th>Narration</th>
        <th>Confirmedyn</th>
        <th>Confimedbyempsystemid</th>
        <th>Confimedbyempid</th>
        <th>Confirmedbyempname</th>
        <th>Confirmeddate</th>
        <th>Approvedyn</th>
        <th>Approveddate</th>
        <th>Disposaltype</th>
        <th>Createduserid</th>
        <th>Createddatetime</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($assetDisposalMasters as $assetDisposalMaster)
        <tr>
            <td>{!! $assetDisposalMaster->companySystemID !!}</td>
            <td>{!! $assetDisposalMaster->companyID !!}</td>
            <td>{!! $assetDisposalMaster->toCompanySystemID !!}</td>
            <td>{!! $assetDisposalMaster->toCompanyID !!}</td>
            <td>{!! $assetDisposalMaster->customerID !!}</td>
            <td>{!! $assetDisposalMaster->serialNo !!}</td>
            <td>{!! $assetDisposalMaster->companyFinanceYearID !!}</td>
            <td>{!! $assetDisposalMaster->FYBiggin !!}</td>
            <td>{!! $assetDisposalMaster->FYEnd !!}</td>
            <td>{!! $assetDisposalMaster->FYPeriodDateFrom !!}</td>
            <td>{!! $assetDisposalMaster->FYPeriodDateTo !!}</td>
            <td>{!! $assetDisposalMaster->documentSystemID !!}</td>
            <td>{!! $assetDisposalMaster->documentID !!}</td>
            <td>{!! $assetDisposalMaster->disposalDocumentCode !!}</td>
            <td>{!! $assetDisposalMaster->disposalDocumentDate !!}</td>
            <td>{!! $assetDisposalMaster->narration !!}</td>
            <td>{!! $assetDisposalMaster->confirmedYN !!}</td>
            <td>{!! $assetDisposalMaster->confimedByEmpSystemID !!}</td>
            <td>{!! $assetDisposalMaster->confimedByEmpID !!}</td>
            <td>{!! $assetDisposalMaster->confirmedByEmpName !!}</td>
            <td>{!! $assetDisposalMaster->confirmedDate !!}</td>
            <td>{!! $assetDisposalMaster->approvedYN !!}</td>
            <td>{!! $assetDisposalMaster->approvedDate !!}</td>
            <td>{!! $assetDisposalMaster->disposalType !!}</td>
            <td>{!! $assetDisposalMaster->createdUserID !!}</td>
            <td>{!! $assetDisposalMaster->createdDateTime !!}</td>
            <td>{!! $assetDisposalMaster->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['assetDisposalMasters.destroy', $assetDisposalMaster->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('assetDisposalMasters.show', [$assetDisposalMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('assetDisposalMasters.edit', [$assetDisposalMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>