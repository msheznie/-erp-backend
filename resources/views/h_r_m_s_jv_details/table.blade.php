<table class="table table-responsive" id="hRMSJvDetails-table">
    <thead>
        <tr>
            <th>Accmasterid</th>
        <th>Salaryprocessmasterid</th>
        <th>Accrualnarration</th>
        <th>Accrualdateasof</th>
        <th>Companyid</th>
        <th>Serviceline</th>
        <th>Departuredate</th>
        <th>Callofdate</th>
        <th>Glcode</th>
        <th>Accrualamount</th>
        <th>Accrualcurrency</th>
        <th>Localamount</th>
        <th>Localcurrency</th>
        <th>Rptamount</th>
        <th>Rptcurrency</th>
        <th>Jvmasterautoid</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($hRMSJvDetails as $hRMSJvDetails)
        <tr>
            <td>{!! $hRMSJvDetails->accMasterID !!}</td>
            <td>{!! $hRMSJvDetails->salaryProcessMasterID !!}</td>
            <td>{!! $hRMSJvDetails->accrualNarration !!}</td>
            <td>{!! $hRMSJvDetails->accrualDateAsOF !!}</td>
            <td>{!! $hRMSJvDetails->companyID !!}</td>
            <td>{!! $hRMSJvDetails->serviceLine !!}</td>
            <td>{!! $hRMSJvDetails->departureDate !!}</td>
            <td>{!! $hRMSJvDetails->callOfDate !!}</td>
            <td>{!! $hRMSJvDetails->GlCode !!}</td>
            <td>{!! $hRMSJvDetails->accrualAmount !!}</td>
            <td>{!! $hRMSJvDetails->accrualCurrency !!}</td>
            <td>{!! $hRMSJvDetails->localAmount !!}</td>
            <td>{!! $hRMSJvDetails->localCurrency !!}</td>
            <td>{!! $hRMSJvDetails->rptAmount !!}</td>
            <td>{!! $hRMSJvDetails->rptCurrency !!}</td>
            <td>{!! $hRMSJvDetails->jvMasterAutoID !!}</td>
            <td>{!! $hRMSJvDetails->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['hRMSJvDetails.destroy', $hRMSJvDetails->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('hRMSJvDetails.show', [$hRMSJvDetails->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('hRMSJvDetails.edit', [$hRMSJvDetails->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>