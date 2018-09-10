<table class="table table-responsive" id="expenseClaims-table">
    <thead>
        <tr>
            <th>Companyid</th>
        <th>Departmentid</th>
        <th>Documentid</th>
        <th>Serialno</th>
        <th>Expenseclaimcode</th>
        <th>Expenseclaimdate</th>
        <th>Clamiedbyname</th>
        <th>Comments</th>
        <th>Confirmedyn</th>
        <th>Confirmedbyempid</th>
        <th>Confirmedbyname</th>
        <th>Confirmeddate</th>
        <th>Approved</th>
        <th>Approveddate</th>
        <th>Glcodeassignedyn</th>
        <th>Addedforpayment</th>
        <th>Rejectedyn</th>
        <th>Rejectedcomment</th>
        <th>Seniormanager</th>
        <th>Pettycashyn</th>
        <th>Addedtosalary</th>
        <th>Createduserid</th>
        <th>Createdpcid</th>
        <th>Modifieduser</th>
        <th>Modifiedpc</th>
        <th>Createddatetime</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($expenseClaims as $expenseClaim)
        <tr>
            <td>{!! $expenseClaim->companyID !!}</td>
            <td>{!! $expenseClaim->departmentID !!}</td>
            <td>{!! $expenseClaim->documentID !!}</td>
            <td>{!! $expenseClaim->serialNo !!}</td>
            <td>{!! $expenseClaim->expenseClaimCode !!}</td>
            <td>{!! $expenseClaim->expenseClaimDate !!}</td>
            <td>{!! $expenseClaim->clamiedByName !!}</td>
            <td>{!! $expenseClaim->comments !!}</td>
            <td>{!! $expenseClaim->confirmedYN !!}</td>
            <td>{!! $expenseClaim->confirmedByEmpID !!}</td>
            <td>{!! $expenseClaim->confirmedByName !!}</td>
            <td>{!! $expenseClaim->confirmedDate !!}</td>
            <td>{!! $expenseClaim->approved !!}</td>
            <td>{!! $expenseClaim->approvedDate !!}</td>
            <td>{!! $expenseClaim->glCodeAssignedYN !!}</td>
            <td>{!! $expenseClaim->addedForPayment !!}</td>
            <td>{!! $expenseClaim->rejectedYN !!}</td>
            <td>{!! $expenseClaim->rejectedComment !!}</td>
            <td>{!! $expenseClaim->seniorManager !!}</td>
            <td>{!! $expenseClaim->pettyCashYN !!}</td>
            <td>{!! $expenseClaim->addedToSalary !!}</td>
            <td>{!! $expenseClaim->createdUserID !!}</td>
            <td>{!! $expenseClaim->createdPcID !!}</td>
            <td>{!! $expenseClaim->modifiedUser !!}</td>
            <td>{!! $expenseClaim->modifiedPc !!}</td>
            <td>{!! $expenseClaim->createdDateTime !!}</td>
            <td>{!! $expenseClaim->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['expenseClaims.destroy', $expenseClaim->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('expenseClaims.show', [$expenseClaim->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('expenseClaims.edit', [$expenseClaim->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>