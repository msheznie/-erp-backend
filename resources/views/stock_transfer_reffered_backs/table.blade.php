<table class="table table-responsive" id="stockTransferRefferedBacks-table">
    <thead>
        <tr>
            <th>Stocktransferautoid</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Servicelinesystemid</th>
        <th>Servicelinecode</th>
        <th>Companyfinanceyearid</th>
        <th>Companyfinanceperiodid</th>
        <th>Fybiggin</th>
        <th>Fyend</th>
        <th>Documentsystemid</th>
        <th>Documentid</th>
        <th>Serialno</th>
        <th>Stocktransfercode</th>
        <th>Refno</th>
        <th>Tranferdate</th>
        <th>Comment</th>
        <th>Companyfromsystemid</th>
        <th>Companyfrom</th>
        <th>Companytosystemid</th>
        <th>Companyto</th>
        <th>Locationto</th>
        <th>Locationfrom</th>
        <th>Confirmedyn</th>
        <th>Confirmedbyempsystemid</th>
        <th>Confirmedbyempid</th>
        <th>Confirmedbyname</th>
        <th>Confirmeddate</th>
        <th>Approved</th>
        <th>Approveddate</th>
        <th>Approvedbyuserid</th>
        <th>Approvedbyusersystemid</th>
        <th>Posteddate</th>
        <th>Fullyreceived</th>
        <th>Timesreferred</th>
        <th>Intercompanytransferyn</th>
        <th>Rolllevforapp Curr</th>
        <th>Refferedbackyn</th>
        <th>Createddatetime</th>
        <th>Createdusergroup</th>
        <th>Createdpcid</th>
        <th>Createdusersystemid</th>
        <th>Createduserid</th>
        <th>Modifieduser</th>
        <th>Modifiedusersystemid</th>
        <th>Modifiedpc</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($stockTransferRefferedBacks as $stockTransferRefferedBack)
        <tr>
            <td>{!! $stockTransferRefferedBack->stockTransferAutoID !!}</td>
            <td>{!! $stockTransferRefferedBack->companySystemID !!}</td>
            <td>{!! $stockTransferRefferedBack->companyID !!}</td>
            <td>{!! $stockTransferRefferedBack->serviceLineSystemID !!}</td>
            <td>{!! $stockTransferRefferedBack->serviceLineCode !!}</td>
            <td>{!! $stockTransferRefferedBack->companyFinanceYearID !!}</td>
            <td>{!! $stockTransferRefferedBack->companyFinancePeriodID !!}</td>
            <td>{!! $stockTransferRefferedBack->FYBiggin !!}</td>
            <td>{!! $stockTransferRefferedBack->FYEnd !!}</td>
            <td>{!! $stockTransferRefferedBack->documentSystemID !!}</td>
            <td>{!! $stockTransferRefferedBack->documentID !!}</td>
            <td>{!! $stockTransferRefferedBack->serialNo !!}</td>
            <td>{!! $stockTransferRefferedBack->stockTransferCode !!}</td>
            <td>{!! $stockTransferRefferedBack->refNo !!}</td>
            <td>{!! $stockTransferRefferedBack->tranferDate !!}</td>
            <td>{!! $stockTransferRefferedBack->comment !!}</td>
            <td>{!! $stockTransferRefferedBack->companyFromSystemID !!}</td>
            <td>{!! $stockTransferRefferedBack->companyFrom !!}</td>
            <td>{!! $stockTransferRefferedBack->companyToSystemID !!}</td>
            <td>{!! $stockTransferRefferedBack->companyTo !!}</td>
            <td>{!! $stockTransferRefferedBack->locationTo !!}</td>
            <td>{!! $stockTransferRefferedBack->locationFrom !!}</td>
            <td>{!! $stockTransferRefferedBack->confirmedYN !!}</td>
            <td>{!! $stockTransferRefferedBack->confirmedByEmpSystemID !!}</td>
            <td>{!! $stockTransferRefferedBack->confirmedByEmpID !!}</td>
            <td>{!! $stockTransferRefferedBack->confirmedByName !!}</td>
            <td>{!! $stockTransferRefferedBack->confirmedDate !!}</td>
            <td>{!! $stockTransferRefferedBack->approved !!}</td>
            <td>{!! $stockTransferRefferedBack->approvedDate !!}</td>
            <td>{!! $stockTransferRefferedBack->approvedByUserID !!}</td>
            <td>{!! $stockTransferRefferedBack->approvedByUserSystemID !!}</td>
            <td>{!! $stockTransferRefferedBack->postedDate !!}</td>
            <td>{!! $stockTransferRefferedBack->fullyReceived !!}</td>
            <td>{!! $stockTransferRefferedBack->timesReferred !!}</td>
            <td>{!! $stockTransferRefferedBack->interCompanyTransferYN !!}</td>
            <td>{!! $stockTransferRefferedBack->RollLevForApp_curr !!}</td>
            <td>{!! $stockTransferRefferedBack->refferedBackYN !!}</td>
            <td>{!! $stockTransferRefferedBack->createdDateTime !!}</td>
            <td>{!! $stockTransferRefferedBack->createdUserGroup !!}</td>
            <td>{!! $stockTransferRefferedBack->createdPCID !!}</td>
            <td>{!! $stockTransferRefferedBack->createdUserSystemID !!}</td>
            <td>{!! $stockTransferRefferedBack->createdUserID !!}</td>
            <td>{!! $stockTransferRefferedBack->modifiedUser !!}</td>
            <td>{!! $stockTransferRefferedBack->modifiedUserSystemID !!}</td>
            <td>{!! $stockTransferRefferedBack->modifiedPc !!}</td>
            <td>{!! $stockTransferRefferedBack->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['stockTransferRefferedBacks.destroy', $stockTransferRefferedBack->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('stockTransferRefferedBacks.show', [$stockTransferRefferedBack->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('stockTransferRefferedBacks.edit', [$stockTransferRefferedBack->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>