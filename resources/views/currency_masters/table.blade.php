<table class="table table-responsive" id="currencyMasters-table">
    <thead>
        <tr>
            <th>Currencyname</th>
        <th>Currencycode</th>
        <th>Decimalplaces</th>
        <th>Exchangerate</th>
        <th>Islocal</th>
        <th>Datemodified</th>
        <th>Modifiedby</th>
        <th>Createdusergroup</th>
        <th>Createdpcid</th>
        <th>Createduserid</th>
        <th>Modifiedpc</th>
        <th>Modifieduser</th>
        <th>Createddatetime</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($currencyMasters as $currencyMaster)
        <tr>
            <td>{!! $currencyMaster->CurrencyName !!}</td>
            <td>{!! $currencyMaster->CurrencyCode !!}</td>
            <td>{!! $currencyMaster->DecimalPlaces !!}</td>
            <td>{!! $currencyMaster->ExchangeRate !!}</td>
            <td>{!! $currencyMaster->isLocal !!}</td>
            <td>{!! $currencyMaster->DateModified !!}</td>
            <td>{!! $currencyMaster->ModifiedBy !!}</td>
            <td>{!! $currencyMaster->createdUserGroup !!}</td>
            <td>{!! $currencyMaster->createdPcID !!}</td>
            <td>{!! $currencyMaster->createdUserID !!}</td>
            <td>{!! $currencyMaster->modifiedPc !!}</td>
            <td>{!! $currencyMaster->modifiedUser !!}</td>
            <td>{!! $currencyMaster->createdDateTime !!}</td>
            <td>{!! $currencyMaster->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['currencyMasters.destroy', $currencyMaster->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('currencyMasters.show', [$currencyMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('currencyMasters.edit', [$currencyMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>