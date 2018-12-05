<table class="table table-responsive" id="jvDetailsReferredbacks-table">
    <thead>
        <tr>
            <th>Jvdetailautoid</th>
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
    @foreach($jvDetailsReferredbacks as $jvDetailsReferredback)
        <tr>
            <td>{!! $jvDetailsReferredback->jvDetailAutoID !!}</td>
            <td>{!! $jvDetailsReferredback->jvMasterAutoId !!}</td>
            <td>{!! $jvDetailsReferredback->documentSystemID !!}</td>
            <td>{!! $jvDetailsReferredback->documentID !!}</td>
            <td>{!! $jvDetailsReferredback->recurringjvMasterAutoId !!}</td>
            <td>{!! $jvDetailsReferredback->recurringjvDetailAutoID !!}</td>
            <td>{!! $jvDetailsReferredback->recurringMonth !!}</td>
            <td>{!! $jvDetailsReferredback->serviceLineSystemID !!}</td>
            <td>{!! $jvDetailsReferredback->serviceLineCode !!}</td>
            <td>{!! $jvDetailsReferredback->companySystemID !!}</td>
            <td>{!! $jvDetailsReferredback->companyID !!}</td>
            <td>{!! $jvDetailsReferredback->chartOfAccountSystemID !!}</td>
            <td>{!! $jvDetailsReferredback->glAccount !!}</td>
            <td>{!! $jvDetailsReferredback->glAccountDescription !!}</td>
            <td>{!! $jvDetailsReferredback->referenceGLCode !!}</td>
            <td>{!! $jvDetailsReferredback->referenceGLDescription !!}</td>
            <td>{!! $jvDetailsReferredback->comments !!}</td>
            <td>{!! $jvDetailsReferredback->clientContractID !!}</td>
            <td>{!! $jvDetailsReferredback->currencyID !!}</td>
            <td>{!! $jvDetailsReferredback->currencyER !!}</td>
            <td>{!! $jvDetailsReferredback->debitAmount !!}</td>
            <td>{!! $jvDetailsReferredback->creditAmount !!}</td>
            <td>{!! $jvDetailsReferredback->timesReferred !!}</td>
            <td>{!! $jvDetailsReferredback->companyIDForConsole !!}</td>
            <td>{!! $jvDetailsReferredback->selectedForConsole !!}</td>
            <td>{!! $jvDetailsReferredback->createdDateTime !!}</td>
            <td>{!! $jvDetailsReferredback->createdUserSystemID !!}</td>
            <td>{!! $jvDetailsReferredback->createdUserID !!}</td>
            <td>{!! $jvDetailsReferredback->createdPcID !!}</td>
            <td>{!! $jvDetailsReferredback->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['jvDetailsReferredbacks.destroy', $jvDetailsReferredback->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('jvDetailsReferredbacks.show', [$jvDetailsReferredback->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('jvDetailsReferredbacks.edit', [$jvDetailsReferredback->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>