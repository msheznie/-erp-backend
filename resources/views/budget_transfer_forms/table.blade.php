<table class="table table-responsive" id="budgetTransferForms-table">
    <thead>
        <tr>
            <th>Documentsystemid</th>
        <th>Documentid</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Serialno</th>
        <th>Year</th>
        <th>Transfervoucherno</th>
        <th>Createddate</th>
        <th>Comments</th>
        <th>Confirmedyn</th>
        <th>Confirmeddate</th>
        <th>Confirmedbyempsystemid</th>
        <th>Confirmedbyempid</th>
        <th>Confirmedbyempname</th>
        <th>Approvedyn</th>
        <th>Approveddate</th>
        <th>Approvedbyusersystemid</th>
        <th>Approvedempid</th>
        <th>Approvedempname</th>
        <th>Rolllevforapp Curr</th>
        <th>Createddatetime</th>
        <th>Createdusersystemid</th>
        <th>Createduserid</th>
        <th>Createdpcid</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($budgetTransferForms as $budgetTransferForm)
        <tr>
            <td>{!! $budgetTransferForm->documentSystemID !!}</td>
            <td>{!! $budgetTransferForm->documentID !!}</td>
            <td>{!! $budgetTransferForm->companySystemID !!}</td>
            <td>{!! $budgetTransferForm->companyID !!}</td>
            <td>{!! $budgetTransferForm->serialNo !!}</td>
            <td>{!! $budgetTransferForm->year !!}</td>
            <td>{!! $budgetTransferForm->transferVoucherNo !!}</td>
            <td>{!! $budgetTransferForm->createdDate !!}</td>
            <td>{!! $budgetTransferForm->comments !!}</td>
            <td>{!! $budgetTransferForm->confirmedYN !!}</td>
            <td>{!! $budgetTransferForm->confirmedDate !!}</td>
            <td>{!! $budgetTransferForm->confirmedByEmpSystemID !!}</td>
            <td>{!! $budgetTransferForm->confirmedByEmpID !!}</td>
            <td>{!! $budgetTransferForm->confirmedByEmpName !!}</td>
            <td>{!! $budgetTransferForm->approvedYN !!}</td>
            <td>{!! $budgetTransferForm->approvedDate !!}</td>
            <td>{!! $budgetTransferForm->approvedByUserSystemID !!}</td>
            <td>{!! $budgetTransferForm->approvedEmpID !!}</td>
            <td>{!! $budgetTransferForm->approvedEmpName !!}</td>
            <td>{!! $budgetTransferForm->RollLevForApp_curr !!}</td>
            <td>{!! $budgetTransferForm->createdDateTime !!}</td>
            <td>{!! $budgetTransferForm->createdUserSystemID !!}</td>
            <td>{!! $budgetTransferForm->createdUserID !!}</td>
            <td>{!! $budgetTransferForm->createdPcID !!}</td>
            <td>{!! $budgetTransferForm->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['budgetTransferForms.destroy', $budgetTransferForm->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('budgetTransferForms.show', [$budgetTransferForm->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('budgetTransferForms.edit', [$budgetTransferForm->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>