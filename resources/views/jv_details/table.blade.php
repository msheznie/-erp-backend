<table class="table table-responsive" id="jvDetails-table">
    <thead>
        <tr>
            <th>Jvmasterautoid</th>
        <th>Documentsystemid</th>
        <th>Documentid</th>
        <th>Recurringjvmasterautoid</th>
        <th>Recurringjvdetailautoid</th>
        <th>Recurringmonth</th>
        <th>Servicelinesystemid</th>
        <th>Servicelinecode</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Chartofaccountsystemid</th>
        <th>Glaccount</th>
        <th>Glaccountdescription</th>
        <th>Referenceglcode</th>
        <th>Referencegldescription</th>
        <th>Comments</th>
        <th>Clientcontractid</th>
        <th>Currencyid</th>
        <th>Currencyer</th>
        <th>Debitamount</th>
        <th>Creditamount</th>
        <th>Timesreferred</th>
        <th>Companyidforconsole</th>
        <th>Selectedforconsole</th>
        <th>Createddatetime</th>
        <th>Createdusersystemid</th>
        <th>Createduserid</th>
        <th>Createdpcid</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($jvDetails as $jvDetail)
        <tr>
            <td>{!! $jvDetail->jvMasterAutoId !!}</td>
            <td>{!! $jvDetail->documentSystemID !!}</td>
            <td>{!! $jvDetail->documentID !!}</td>
            <td>{!! $jvDetail->recurringjvMasterAutoId !!}</td>
            <td>{!! $jvDetail->recurringjvDetailAutoID !!}</td>
            <td>{!! $jvDetail->recurringMonth !!}</td>
            <td>{!! $jvDetail->serviceLineSystemID !!}</td>
            <td>{!! $jvDetail->serviceLineCode !!}</td>
            <td>{!! $jvDetail->companySystemID !!}</td>
            <td>{!! $jvDetail->companyID !!}</td>
            <td>{!! $jvDetail->chartOfAccountSystemID !!}</td>
            <td>{!! $jvDetail->glAccount !!}</td>
            <td>{!! $jvDetail->glAccountDescription !!}</td>
            <td>{!! $jvDetail->referenceGLCode !!}</td>
            <td>{!! $jvDetail->referenceGLDescription !!}</td>
            <td>{!! $jvDetail->comments !!}</td>
            <td>{!! $jvDetail->clientContractID !!}</td>
            <td>{!! $jvDetail->currencyID !!}</td>
            <td>{!! $jvDetail->currencyER !!}</td>
            <td>{!! $jvDetail->debitAmount !!}</td>
            <td>{!! $jvDetail->creditAmount !!}</td>
            <td>{!! $jvDetail->timesReferred !!}</td>
            <td>{!! $jvDetail->companyIDForConsole !!}</td>
            <td>{!! $jvDetail->selectedForConsole !!}</td>
            <td>{!! $jvDetail->createdDateTime !!}</td>
            <td>{!! $jvDetail->createdUserSystemID !!}</td>
            <td>{!! $jvDetail->createdUserID !!}</td>
            <td>{!! $jvDetail->createdPcID !!}</td>
            <td>{!! $jvDetail->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['jvDetails.destroy', $jvDetail->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('jvDetails.show', [$jvDetail->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('jvDetails.edit', [$jvDetail->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>