<table class="table table-responsive" id="bankReconciliations-table">
    <thead>
        <tr>
            <th>Documentsystemid</th>
        <th>Documentid</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Bankglautoid</th>
        <th>Month</th>
        <th>Bankrecprimarycode</th>
        <th>Year</th>
        <th>Bankrecasof</th>
        <th>Openingbalance</th>
        <th>Closingbalance</th>
        <th>Description</th>
        <th>Confirmedyn</th>
        <th>Confirmedbyempsystemid</th>
        <th>Confirmedbyempid</th>
        <th>Confirmedbyname</th>
        <th>Confirmeddate</th>
        <th>Approvedyn</th>
        <th>Approveddate</th>
        <th>Approvedbyuserid</th>
        <th>Approvedbyusersystemid</th>
        <th>Rolllevforapp Curr</th>
        <th>Createdpcid</th>
        <th>Createdusersystemid</th>
        <th>Createduserid</th>
        <th>Modifiedpc</th>
        <th>Modifiedusersystemid</th>
        <th>Modifieduser</th>
        <th>Createddatetime</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($bankReconciliations as $bankReconciliation)
        <tr>
            <td>{!! $bankReconciliation->documentSystemID !!}</td>
            <td>{!! $bankReconciliation->documentID !!}</td>
            <td>{!! $bankReconciliation->companySystemID !!}</td>
            <td>{!! $bankReconciliation->companyID !!}</td>
            <td>{!! $bankReconciliation->bankGLAutoID !!}</td>
            <td>{!! $bankReconciliation->month !!}</td>
            <td>{!! $bankReconciliation->bankRecPrimaryCode !!}</td>
            <td>{!! $bankReconciliation->year !!}</td>
            <td>{!! $bankReconciliation->bankRecAsOf !!}</td>
            <td>{!! $bankReconciliation->openingBalance !!}</td>
            <td>{!! $bankReconciliation->closingBalance !!}</td>
            <td>{!! $bankReconciliation->description !!}</td>
            <td>{!! $bankReconciliation->confirmedYN !!}</td>
            <td>{!! $bankReconciliation->confirmedByEmpSystemID !!}</td>
            <td>{!! $bankReconciliation->confirmedByEmpID !!}</td>
            <td>{!! $bankReconciliation->confirmedByName !!}</td>
            <td>{!! $bankReconciliation->confirmedDate !!}</td>
            <td>{!! $bankReconciliation->approvedYN !!}</td>
            <td>{!! $bankReconciliation->approvedDate !!}</td>
            <td>{!! $bankReconciliation->approvedByUserID !!}</td>
            <td>{!! $bankReconciliation->approvedByUserSystemID !!}</td>
            <td>{!! $bankReconciliation->RollLevForApp_curr !!}</td>
            <td>{!! $bankReconciliation->createdPcID !!}</td>
            <td>{!! $bankReconciliation->createdUserSystemID !!}</td>
            <td>{!! $bankReconciliation->createdUserID !!}</td>
            <td>{!! $bankReconciliation->modifiedPc !!}</td>
            <td>{!! $bankReconciliation->modifiedUserSystemID !!}</td>
            <td>{!! $bankReconciliation->modifiedUser !!}</td>
            <td>{!! $bankReconciliation->createdDateTime !!}</td>
            <td>{!! $bankReconciliation->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['bankReconciliations.destroy', $bankReconciliation->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('bankReconciliations.show', [$bankReconciliation->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('bankReconciliations.edit', [$bankReconciliation->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>