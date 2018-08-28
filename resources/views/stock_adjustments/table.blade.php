<table class="table table-responsive" id="stockAdjustments-table">
    <thead>
        <tr>
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
    @foreach($stockAdjustments as $stockAdjustment)
        <tr>
            <td>{!! $stockAdjustment->companySystemID !!}</td>
            <td>{!! $stockAdjustment->companyID !!}</td>
            <td>{!! $stockAdjustment->serviceLineSystemID !!}</td>
            <td>{!! $stockAdjustment->serviceLineCode !!}</td>
            <td>{!! $stockAdjustment->documentSystemID !!}</td>
            <td>{!! $stockAdjustment->documentID !!}</td>
            <td>{!! $stockAdjustment->companyFinanceYearID !!}</td>
            <td>{!! $stockAdjustment->companyFinancePeriodID !!}</td>
            <td>{!! $stockAdjustment->FYBiggin !!}</td>
            <td>{!! $stockAdjustment->FYEnd !!}</td>
            <td>{!! $stockAdjustment->serialNo !!}</td>
            <td>{!! $stockAdjustment->stockAdjustmentCode !!}</td>
            <td>{!! $stockAdjustment->refNo !!}</td>
            <td>{!! $stockAdjustment->stockAdjustmentDate !!}</td>
            <td>{!! $stockAdjustment->location !!}</td>
            <td>{!! $stockAdjustment->comment !!}</td>
            <td>{!! $stockAdjustment->confirmedYN !!}</td>
            <td>{!! $stockAdjustment->confirmedByEmpSystemID !!}</td>
            <td>{!! $stockAdjustment->confirmedByEmpID !!}</td>
            <td>{!! $stockAdjustment->confirmedByName !!}</td>
            <td>{!! $stockAdjustment->confirmedDate !!}</td>
            <td>{!! $stockAdjustment->approved !!}</td>
            <td>{!! $stockAdjustment->createdDateTime !!}</td>
            <td>{!! $stockAdjustment->createdUserGroup !!}</td>
            <td>{!! $stockAdjustment->createdPCid !!}</td>
            <td>{!! $stockAdjustment->createdUserSystemID !!}</td>
            <td>{!! $stockAdjustment->createdUserID !!}</td>
            <td>{!! $stockAdjustment->modifiedUserSystemID !!}</td>
            <td>{!! $stockAdjustment->modifiedUser !!}</td>
            <td>{!! $stockAdjustment->modifiedPc !!}</td>
            <td>{!! $stockAdjustment->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['stockAdjustments.destroy', $stockAdjustment->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('stockAdjustments.show', [$stockAdjustment->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('stockAdjustments.edit', [$stockAdjustment->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>