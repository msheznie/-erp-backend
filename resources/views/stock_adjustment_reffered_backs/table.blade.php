<table class="table table-responsive" id="stockAdjustmentRefferedBacks-table">
    <thead>
        <tr>
            <th>Stockadjustmentautoid</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Servicelinesystemid</th>
        <th>Servicelinecode</th>
        <th>Documentsystemid</th>
        <th>Documentid</th>
        <th>Companyfinanceyearid</th>
        <th>Companyfinanceperiodid</th>
        <th>Fybiggin</th>
        <th>Fyend</th>
        <th>Serialno</th>
        <th>Stockadjustmentcode</th>
        <th>Refno</th>
        <th>Stockadjustmentdate</th>
        <th>Location</th>
        <th>Comment</th>
        <th>Confirmedyn</th>
        <th>Confirmedbyempsystemid</th>
        <th>Confirmedbyempid</th>
        <th>Confirmedbyname</th>
        <th>Confirmeddate</th>
        <th>Approved</th>
        <th>Refferedbackyn</th>
        <th>Timesreferred</th>
        <th>Createddatetime</th>
        <th>Createdusergroup</th>
        <th>Createdpcid</th>
        <th>Createdusersystemid</th>
        <th>Createduserid</th>
        <th>Modifiedusersystemid</th>
        <th>Modifieduser</th>
        <th>Modifiedpc</th>
        <th>Timestamp</th>
        <th>Rolllevforapp Curr</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($stockAdjustmentRefferedBacks as $stockAdjustmentRefferedBack)
        <tr>
            <td>{!! $stockAdjustmentRefferedBack->stockAdjustmentAutoID !!}</td>
            <td>{!! $stockAdjustmentRefferedBack->companySystemID !!}</td>
            <td>{!! $stockAdjustmentRefferedBack->companyID !!}</td>
            <td>{!! $stockAdjustmentRefferedBack->serviceLineSystemID !!}</td>
            <td>{!! $stockAdjustmentRefferedBack->serviceLineCode !!}</td>
            <td>{!! $stockAdjustmentRefferedBack->documentSystemID !!}</td>
            <td>{!! $stockAdjustmentRefferedBack->documentID !!}</td>
            <td>{!! $stockAdjustmentRefferedBack->companyFinanceYearID !!}</td>
            <td>{!! $stockAdjustmentRefferedBack->companyFinancePeriodID !!}</td>
            <td>{!! $stockAdjustmentRefferedBack->FYBiggin !!}</td>
            <td>{!! $stockAdjustmentRefferedBack->FYEnd !!}</td>
            <td>{!! $stockAdjustmentRefferedBack->serialNo !!}</td>
            <td>{!! $stockAdjustmentRefferedBack->stockAdjustmentCode !!}</td>
            <td>{!! $stockAdjustmentRefferedBack->refNo !!}</td>
            <td>{!! $stockAdjustmentRefferedBack->stockAdjustmentDate !!}</td>
            <td>{!! $stockAdjustmentRefferedBack->location !!}</td>
            <td>{!! $stockAdjustmentRefferedBack->comment !!}</td>
            <td>{!! $stockAdjustmentRefferedBack->confirmedYN !!}</td>
            <td>{!! $stockAdjustmentRefferedBack->confirmedByEmpSystemID !!}</td>
            <td>{!! $stockAdjustmentRefferedBack->confirmedByEmpID !!}</td>
            <td>{!! $stockAdjustmentRefferedBack->confirmedByName !!}</td>
            <td>{!! $stockAdjustmentRefferedBack->confirmedDate !!}</td>
            <td>{!! $stockAdjustmentRefferedBack->approved !!}</td>
            <td>{!! $stockAdjustmentRefferedBack->refferedBackYN !!}</td>
            <td>{!! $stockAdjustmentRefferedBack->timesReferred !!}</td>
            <td>{!! $stockAdjustmentRefferedBack->createdDateTime !!}</td>
            <td>{!! $stockAdjustmentRefferedBack->createdUserGroup !!}</td>
            <td>{!! $stockAdjustmentRefferedBack->createdPCid !!}</td>
            <td>{!! $stockAdjustmentRefferedBack->createdUserSystemID !!}</td>
            <td>{!! $stockAdjustmentRefferedBack->createdUserID !!}</td>
            <td>{!! $stockAdjustmentRefferedBack->modifiedUserSystemID !!}</td>
            <td>{!! $stockAdjustmentRefferedBack->modifiedUser !!}</td>
            <td>{!! $stockAdjustmentRefferedBack->modifiedPc !!}</td>
            <td>{!! $stockAdjustmentRefferedBack->timestamp !!}</td>
            <td>{!! $stockAdjustmentRefferedBack->RollLevForApp_curr !!}</td>
            <td>
                {!! Form::open(['route' => ['stockAdjustmentRefferedBacks.destroy', $stockAdjustmentRefferedBack->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('stockAdjustmentRefferedBacks.show', [$stockAdjustmentRefferedBack->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('stockAdjustmentRefferedBacks.edit', [$stockAdjustmentRefferedBack->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>