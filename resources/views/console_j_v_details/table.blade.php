<table class="table table-responsive" id="consoleJVDetails-table">
    <thead>
        <tr>
            <th>Consolejvmasterautoid</th>
        <th>Jvdetailautoid</th>
        <th>Jvmasterautoid</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Servicelinesystemid</th>
        <th>Servicelinecode</th>
        <th>Documentsystemid</th>
        <th>Documentid</th>
        <th>Documentcode</th>
        <th>Gldate</th>
        <th>Glaccountsystemid</th>
        <th>Glaccount</th>
        <th>Glaccountdescription</th>
        <th>Comments</th>
        <th>Currencyid</th>
        <th>Currencyer</th>
        <th>Debitamount</th>
        <th>Creditamount</th>
        <th>Localdebitamount</th>
        <th>Rptdebitamount</th>
        <th>Localcreditamount</th>
        <th>Rptcreditamount</th>
        <th>Consoletype</th>
        <th>Createddatetime</th>
        <th>Createdusersystemid</th>
        <th>Createduserid</th>
        <th>Createdpcid</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($consoleJVDetails as $consoleJVDetail)
        <tr>
            <td>{!! $consoleJVDetail->consoleJvMasterAutoId !!}</td>
            <td>{!! $consoleJVDetail->jvDetailAutoID !!}</td>
            <td>{!! $consoleJVDetail->jvMasterAutoId !!}</td>
            <td>{!! $consoleJVDetail->companySystemID !!}</td>
            <td>{!! $consoleJVDetail->companyID !!}</td>
            <td>{!! $consoleJVDetail->serviceLineSystemID !!}</td>
            <td>{!! $consoleJVDetail->serviceLineCode !!}</td>
            <td>{!! $consoleJVDetail->documentSystemID !!}</td>
            <td>{!! $consoleJVDetail->documentID !!}</td>
            <td>{!! $consoleJVDetail->documentCode !!}</td>
            <td>{!! $consoleJVDetail->glDate !!}</td>
            <td>{!! $consoleJVDetail->glAccountSystemID !!}</td>
            <td>{!! $consoleJVDetail->glAccount !!}</td>
            <td>{!! $consoleJVDetail->glAccountDescription !!}</td>
            <td>{!! $consoleJVDetail->comments !!}</td>
            <td>{!! $consoleJVDetail->currencyID !!}</td>
            <td>{!! $consoleJVDetail->currencyER !!}</td>
            <td>{!! $consoleJVDetail->debitAmount !!}</td>
            <td>{!! $consoleJVDetail->creditAmount !!}</td>
            <td>{!! $consoleJVDetail->localDebitAmount !!}</td>
            <td>{!! $consoleJVDetail->rptDebitAmount !!}</td>
            <td>{!! $consoleJVDetail->localCreditAmount !!}</td>
            <td>{!! $consoleJVDetail->rptCreditAmount !!}</td>
            <td>{!! $consoleJVDetail->consoleType !!}</td>
            <td>{!! $consoleJVDetail->createdDateTime !!}</td>
            <td>{!! $consoleJVDetail->createdUserSystemID !!}</td>
            <td>{!! $consoleJVDetail->createdUserID !!}</td>
            <td>{!! $consoleJVDetail->createdPcID !!}</td>
            <td>{!! $consoleJVDetail->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['consoleJVDetails.destroy', $consoleJVDetail->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('consoleJVDetails.show', [$consoleJVDetail->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('consoleJVDetails.edit', [$consoleJVDetail->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>