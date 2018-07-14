<table class="table table-responsive" id="stockTransfers-table">
    <thead>
        <tr>
            <th>Companyid</th>
        <th>Servicelinecode</th>
        <th>Companyfinanceyearid</th>
        <th>Fybiggin</th>
        <th>Fyend</th>
        <th>Documentid</th>
        <th>Serialno</th>
        <th>Stocktransfercode</th>
        <th>Refno</th>
        <th>Tranferdate</th>
        <th>Comment</th>
        <th>Companyfrom</th>
        <th>Companyto</th>
        <th>Locationto</th>
        <th>Locationfrom</th>
        <th>Confirmedyn</th>
        <th>Confirmedbyempid</th>
        <th>Confirmedbyname</th>
        <th>Confirmeddate</th>
        <th>Approved</th>
        <th>Posteddate</th>
        <th>Fullyreceived</th>
        <th>Timesreferred</th>
        <th>Intercompanytransferyn</th>
        <th>Rolllevforapp Curr</th>
        <th>Createddatetime</th>
        <th>Createdusergroup</th>
        <th>Createdpcid</th>
        <th>Createduserid</th>
        <th>Modifieduser</th>
        <th>Modifiedpc</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($stockTransfers as $stockTransfer)
        <tr>
            <td>{!! $stockTransfer->companyID !!}</td>
            <td>{!! $stockTransfer->serviceLineCode !!}</td>
            <td>{!! $stockTransfer->companyFinanceYearID !!}</td>
            <td>{!! $stockTransfer->FYBiggin !!}</td>
            <td>{!! $stockTransfer->FYEnd !!}</td>
            <td>{!! $stockTransfer->documentID !!}</td>
            <td>{!! $stockTransfer->serialNo !!}</td>
            <td>{!! $stockTransfer->stockTransferCode !!}</td>
            <td>{!! $stockTransfer->refNo !!}</td>
            <td>{!! $stockTransfer->tranferDate !!}</td>
            <td>{!! $stockTransfer->comment !!}</td>
            <td>{!! $stockTransfer->companyFrom !!}</td>
            <td>{!! $stockTransfer->companyTo !!}</td>
            <td>{!! $stockTransfer->locationTo !!}</td>
            <td>{!! $stockTransfer->locationFrom !!}</td>
            <td>{!! $stockTransfer->confirmedYN !!}</td>
            <td>{!! $stockTransfer->confirmedByEmpID !!}</td>
            <td>{!! $stockTransfer->confirmedByName !!}</td>
            <td>{!! $stockTransfer->confirmedDate !!}</td>
            <td>{!! $stockTransfer->approved !!}</td>
            <td>{!! $stockTransfer->postedDate !!}</td>
            <td>{!! $stockTransfer->fullyReceived !!}</td>
            <td>{!! $stockTransfer->timesReferred !!}</td>
            <td>{!! $stockTransfer->interCompanyTransferYN !!}</td>
            <td>{!! $stockTransfer->RollLevForApp_curr !!}</td>
            <td>{!! $stockTransfer->createdDateTime !!}</td>
            <td>{!! $stockTransfer->createdUserGroup !!}</td>
            <td>{!! $stockTransfer->createdPCID !!}</td>
            <td>{!! $stockTransfer->createdUserID !!}</td>
            <td>{!! $stockTransfer->modifiedUser !!}</td>
            <td>{!! $stockTransfer->modifiedPc !!}</td>
            <td>{!! $stockTransfer->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['stockTransfers.destroy', $stockTransfer->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('stockTransfers.show', [$stockTransfer->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('stockTransfers.edit', [$stockTransfer->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>