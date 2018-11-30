<table class="table table-responsive" id="stockReceiveRefferedBacks-table">
    <thead>
        <tr>
            <th>Stockreceiveautoid</th>
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
        <th>Stockreceivecode</th>
        <th>Refno</th>
        <th>Receiveddate</th>
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
        <th>Timesreferred</th>
        <th>Intercompanytransferyn</th>
        <th>Rolllevforapp Curr</th>
        <th>Createddatetime</th>
        <th>Createdusergroup</th>
        <th>Createdpcid</th>
        <th>Createdusersystemid</th>
        <th>Createduserid</th>
        <th>Modifiedusersystemid</th>
        <th>Modifieduser</th>
        <th>Modifiedpc</th>
        <th>Timestamp</th>
        <th>Refferedbackyn</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($stockReceiveRefferedBacks as $stockReceiveRefferedBack)
        <tr>
            <td>{!! $stockReceiveRefferedBack->stockReceiveAutoID !!}</td>
            <td>{!! $stockReceiveRefferedBack->companySystemID !!}</td>
            <td>{!! $stockReceiveRefferedBack->companyID !!}</td>
            <td>{!! $stockReceiveRefferedBack->serviceLineSystemID !!}</td>
            <td>{!! $stockReceiveRefferedBack->serviceLineCode !!}</td>
            <td>{!! $stockReceiveRefferedBack->companyFinanceYearID !!}</td>
            <td>{!! $stockReceiveRefferedBack->companyFinancePeriodID !!}</td>
            <td>{!! $stockReceiveRefferedBack->FYBiggin !!}</td>
            <td>{!! $stockReceiveRefferedBack->FYEnd !!}</td>
            <td>{!! $stockReceiveRefferedBack->documentSystemID !!}</td>
            <td>{!! $stockReceiveRefferedBack->documentID !!}</td>
            <td>{!! $stockReceiveRefferedBack->serialNo !!}</td>
            <td>{!! $stockReceiveRefferedBack->stockReceiveCode !!}</td>
            <td>{!! $stockReceiveRefferedBack->refNo !!}</td>
            <td>{!! $stockReceiveRefferedBack->receivedDate !!}</td>
            <td>{!! $stockReceiveRefferedBack->comment !!}</td>
            <td>{!! $stockReceiveRefferedBack->companyFromSystemID !!}</td>
            <td>{!! $stockReceiveRefferedBack->companyFrom !!}</td>
            <td>{!! $stockReceiveRefferedBack->companyToSystemID !!}</td>
            <td>{!! $stockReceiveRefferedBack->companyTo !!}</td>
            <td>{!! $stockReceiveRefferedBack->locationTo !!}</td>
            <td>{!! $stockReceiveRefferedBack->locationFrom !!}</td>
            <td>{!! $stockReceiveRefferedBack->confirmedYN !!}</td>
            <td>{!! $stockReceiveRefferedBack->confirmedByEmpSystemID !!}</td>
            <td>{!! $stockReceiveRefferedBack->confirmedByEmpID !!}</td>
            <td>{!! $stockReceiveRefferedBack->confirmedByName !!}</td>
            <td>{!! $stockReceiveRefferedBack->confirmedDate !!}</td>
            <td>{!! $stockReceiveRefferedBack->approved !!}</td>
            <td>{!! $stockReceiveRefferedBack->approvedDate !!}</td>
            <td>{!! $stockReceiveRefferedBack->approvedByUserID !!}</td>
            <td>{!! $stockReceiveRefferedBack->approvedByUserSystemID !!}</td>
            <td>{!! $stockReceiveRefferedBack->postedDate !!}</td>
            <td>{!! $stockReceiveRefferedBack->timesReferred !!}</td>
            <td>{!! $stockReceiveRefferedBack->interCompanyTransferYN !!}</td>
            <td>{!! $stockReceiveRefferedBack->RollLevForApp_curr !!}</td>
            <td>{!! $stockReceiveRefferedBack->createdDateTime !!}</td>
            <td>{!! $stockReceiveRefferedBack->createdUserGroup !!}</td>
            <td>{!! $stockReceiveRefferedBack->createdPCID !!}</td>
            <td>{!! $stockReceiveRefferedBack->createdUserSystemID !!}</td>
            <td>{!! $stockReceiveRefferedBack->createdUserID !!}</td>
            <td>{!! $stockReceiveRefferedBack->modifiedUserSystemID !!}</td>
            <td>{!! $stockReceiveRefferedBack->modifiedUser !!}</td>
            <td>{!! $stockReceiveRefferedBack->modifiedPc !!}</td>
            <td>{!! $stockReceiveRefferedBack->timestamp !!}</td>
            <td>{!! $stockReceiveRefferedBack->refferedBackYN !!}</td>
            <td>
                {!! Form::open(['route' => ['stockReceiveRefferedBacks.destroy', $stockReceiveRefferedBack->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('stockReceiveRefferedBacks.show', [$stockReceiveRefferedBack->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('stockReceiveRefferedBacks.edit', [$stockReceiveRefferedBack->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>