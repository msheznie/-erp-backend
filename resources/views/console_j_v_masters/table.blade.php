<table class="table table-responsive" id="consoleJVMasters-table">
    <thead>
        <tr>
            <th>Serialno</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Documentsystemid</th>
        <th>Documentid</th>
        <th>Consolejvcode</th>
        <th>Consolejvdate</th>
        <th>Consolejvnarration</th>
        <th>Currencyid</th>
        <th>Currencyer</th>
        <th>Confirmedyn</th>
        <th>Confirmedbyempsystemid</th>
        <th>Confirmedbyempid</th>
        <th>Confirmedbyname</th>
        <th>Confirmeddate</th>
        <th>Localcurrencyid</th>
        <th>Localcurrencyer</th>
        <th>Rptcurrencyid</th>
        <th>Rptcurrencyer</th>
        <th>Createdusergroup</th>
        <th>Createdusersystemid</th>
        <th>Createduserid</th>
        <th>Createdpcid</th>
        <th>Modifiedusersystemid</th>
        <th>Modifieduser</th>
        <th>Modifiedpc</th>
        <th>Createddatetime</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($consoleJVMasters as $consoleJVMaster)
        <tr>
            <td>{!! $consoleJVMaster->serialNo !!}</td>
            <td>{!! $consoleJVMaster->companySystemID !!}</td>
            <td>{!! $consoleJVMaster->companyID !!}</td>
            <td>{!! $consoleJVMaster->documentSystemID !!}</td>
            <td>{!! $consoleJVMaster->documentID !!}</td>
            <td>{!! $consoleJVMaster->consoleJVcode !!}</td>
            <td>{!! $consoleJVMaster->consoleJVdate !!}</td>
            <td>{!! $consoleJVMaster->consoleJVNarration !!}</td>
            <td>{!! $consoleJVMaster->currencyID !!}</td>
            <td>{!! $consoleJVMaster->currencyER !!}</td>
            <td>{!! $consoleJVMaster->confirmedYN !!}</td>
            <td>{!! $consoleJVMaster->confirmedByEmpSystemID !!}</td>
            <td>{!! $consoleJVMaster->confirmedByEmpID !!}</td>
            <td>{!! $consoleJVMaster->confirmedByName !!}</td>
            <td>{!! $consoleJVMaster->confirmedDate !!}</td>
            <td>{!! $consoleJVMaster->localCurrencyID !!}</td>
            <td>{!! $consoleJVMaster->localCurrencyER !!}</td>
            <td>{!! $consoleJVMaster->rptCurrencyID !!}</td>
            <td>{!! $consoleJVMaster->rptCurrencyER !!}</td>
            <td>{!! $consoleJVMaster->createdUserGroup !!}</td>
            <td>{!! $consoleJVMaster->createdUserSystemID !!}</td>
            <td>{!! $consoleJVMaster->createdUserID !!}</td>
            <td>{!! $consoleJVMaster->createdPcID !!}</td>
            <td>{!! $consoleJVMaster->modifiedUserSystemID !!}</td>
            <td>{!! $consoleJVMaster->modifiedUser !!}</td>
            <td>{!! $consoleJVMaster->modifiedPc !!}</td>
            <td>{!! $consoleJVMaster->createdDateTime !!}</td>
            <td>{!! $consoleJVMaster->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['consoleJVMasters.destroy', $consoleJVMaster->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('consoleJVMasters.show', [$consoleJVMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('consoleJVMasters.edit', [$consoleJVMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>