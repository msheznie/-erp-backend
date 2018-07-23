<table class="table table-responsive" id="stockReceives-table">
    <thead>
        <tr>
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
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($stockReceives as $stockReceive)
        <tr>
            <td>{!! $stockReceive->companySystemID !!}</td>
            <td>{!! $stockReceive->companyID !!}</td>
            <td>{!! $stockReceive->serviceLineSystemID !!}</td>
            <td>{!! $stockReceive->serviceLineCode !!}</td>
            <td>{!! $stockReceive->companyFinanceYearID !!}</td>
            <td>{!! $stockReceive->companyFinancePeriodID !!}</td>
            <td>{!! $stockReceive->FYBiggin !!}</td>
            <td>{!! $stockReceive->FYEnd !!}</td>
            <td>{!! $stockReceive->documentSystemID !!}</td>
            <td>{!! $stockReceive->documentID !!}</td>
            <td>{!! $stockReceive->serialNo !!}</td>
            <td>{!! $stockReceive->stockReceiveCode !!}</td>
            <td>{!! $stockReceive->refNo !!}</td>
            <td>{!! $stockReceive->receivedDate !!}</td>
            <td>{!! $stockReceive->comment !!}</td>
            <td>{!! $stockReceive->companyFromSystemID !!}</td>
            <td>{!! $stockReceive->companyFrom !!}</td>
            <td>{!! $stockReceive->companyToSystemID !!}</td>
            <td>{!! $stockReceive->companyTo !!}</td>
            <td>{!! $stockReceive->locationTo !!}</td>
            <td>{!! $stockReceive->locationFrom !!}</td>
            <td>{!! $stockReceive->confirmedYN !!}</td>
            <td>{!! $stockReceive->confirmedByEmpSystemID !!}</td>
            <td>{!! $stockReceive->confirmedByEmpID !!}</td>
            <td>{!! $stockReceive->confirmedByName !!}</td>
            <td>{!! $stockReceive->confirmedDate !!}</td>
            <td>{!! $stockReceive->approved !!}</td>
            <td>{!! $stockReceive->approvedDate !!}</td>
            <td>{!! $stockReceive->postedDate !!}</td>
            <td>{!! $stockReceive->timesReferred !!}</td>
            <td>{!! $stockReceive->interCompanyTransferYN !!}</td>
            <td>{!! $stockReceive->RollLevForApp_curr !!}</td>
            <td>{!! $stockReceive->createdDateTime !!}</td>
            <td>{!! $stockReceive->createdUserGroup !!}</td>
            <td>{!! $stockReceive->createdPCID !!}</td>
            <td>{!! $stockReceive->createdUserSystemID !!}</td>
            <td>{!! $stockReceive->createdUserID !!}</td>
            <td>{!! $stockReceive->modifiedUserSystemID !!}</td>
            <td>{!! $stockReceive->modifiedUser !!}</td>
            <td>{!! $stockReceive->modifiedPc !!}</td>
            <td>{!! $stockReceive->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['stockReceives.destroy', $stockReceive->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('stockReceives.show', [$stockReceive->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('stockReceives.edit', [$stockReceive->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>