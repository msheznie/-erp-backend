<table class="table table-responsive" id="monthlyAdditionsMasters-table">
    <thead>
        <tr>
            <th>Monthlyadditionscode</th>
        <th>Serialno</th>
        <th>Documentsystemid</th>
        <th>Documentid</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Description</th>
        <th>Currency</th>
        <th>Processperiod</th>
        <th>Datema</th>
        <th>Confirmedyn</th>
        <th>Confirmedbyempsystemid</th>
        <th>Confirmedby</th>
        <th>Confirmeddate</th>
        <th>Approvedyn</th>
        <th>Approvedbyusersystemid</th>
        <th>Approvedby</th>
        <th>Approveddate</th>
        <th>Rolllevforapp Curr</th>
        <th>Localcurrencyid</th>
        <th>Localcurrencyexchangerate</th>
        <th>Rptcurrencyid</th>
        <th>Rptcurrencyexchangerate</th>
        <th>Expenseclaimadditionyn</th>
        <th>Modifiedusersystemid</th>
        <th>Modifieduser</th>
        <th>Modifiedpc</th>
        <th>Createdusersystemid</th>
        <th>Createdusergroup</th>
        <th>Createdpc</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($monthlyAdditionsMasters as $monthlyAdditionsMaster)
        <tr>
            <td>{!! $monthlyAdditionsMaster->monthlyAdditionsCode !!}</td>
            <td>{!! $monthlyAdditionsMaster->serialNo !!}</td>
            <td>{!! $monthlyAdditionsMaster->documentSystemID !!}</td>
            <td>{!! $monthlyAdditionsMaster->documentID !!}</td>
            <td>{!! $monthlyAdditionsMaster->companySystemID !!}</td>
            <td>{!! $monthlyAdditionsMaster->CompanyID !!}</td>
            <td>{!! $monthlyAdditionsMaster->description !!}</td>
            <td>{!! $monthlyAdditionsMaster->currency !!}</td>
            <td>{!! $monthlyAdditionsMaster->processPeriod !!}</td>
            <td>{!! $monthlyAdditionsMaster->dateMA !!}</td>
            <td>{!! $monthlyAdditionsMaster->confirmedYN !!}</td>
            <td>{!! $monthlyAdditionsMaster->confirmedByEmpSystemID !!}</td>
            <td>{!! $monthlyAdditionsMaster->confirmedby !!}</td>
            <td>{!! $monthlyAdditionsMaster->confirmedDate !!}</td>
            <td>{!! $monthlyAdditionsMaster->approvedYN !!}</td>
            <td>{!! $monthlyAdditionsMaster->approvedByUserSystemID !!}</td>
            <td>{!! $monthlyAdditionsMaster->approvedby !!}</td>
            <td>{!! $monthlyAdditionsMaster->approvedDate !!}</td>
            <td>{!! $monthlyAdditionsMaster->RollLevForApp_curr !!}</td>
            <td>{!! $monthlyAdditionsMaster->localCurrencyID !!}</td>
            <td>{!! $monthlyAdditionsMaster->localCurrencyExchangeRate !!}</td>
            <td>{!! $monthlyAdditionsMaster->rptCurrencyID !!}</td>
            <td>{!! $monthlyAdditionsMaster->rptCurrencyExchangeRate !!}</td>
            <td>{!! $monthlyAdditionsMaster->expenseClaimAdditionYN !!}</td>
            <td>{!! $monthlyAdditionsMaster->modifiedUserSystemID !!}</td>
            <td>{!! $monthlyAdditionsMaster->modifieduser !!}</td>
            <td>{!! $monthlyAdditionsMaster->modifiedpc !!}</td>
            <td>{!! $monthlyAdditionsMaster->createdUserSystemID !!}</td>
            <td>{!! $monthlyAdditionsMaster->createduserGroup !!}</td>
            <td>{!! $monthlyAdditionsMaster->createdpc !!}</td>
            <td>{!! $monthlyAdditionsMaster->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['monthlyAdditionsMasters.destroy', $monthlyAdditionsMaster->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('monthlyAdditionsMasters.show', [$monthlyAdditionsMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('monthlyAdditionsMasters.edit', [$monthlyAdditionsMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>