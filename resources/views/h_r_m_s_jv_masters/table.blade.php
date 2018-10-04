<table class="table table-responsive" id="hRMSJvMasters-table">
    <thead>
        <tr>
            <th>Salaryprocessmasterid</th>
        <th>Accruvalnarration</th>
        <th>Accrualdateasof</th>
        <th>Documentid</th>
        <th>Jvcode</th>
        <th>Serialno</th>
        <th>Companyid</th>
        <th>Accmonth</th>
        <th>Accyear</th>
        <th>Accconfirmedyn</th>
        <th>Accconfirmedby</th>
        <th>Accconfirmeddate</th>
        <th>Jvmasterautoid</th>
        <th>Accjvselectedyn</th>
        <th>Accjvpostedyn</th>
        <th>Jvpostedby</th>
        <th>Jvposteddate</th>
        <th>Createdby</th>
        <th>Createddatetime</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($hRMSJvMasters as $hRMSJvMaster)
        <tr>
            <td>{!! $hRMSJvMaster->salaryProcessMasterID !!}</td>
            <td>{!! $hRMSJvMaster->accruvalNarration !!}</td>
            <td>{!! $hRMSJvMaster->accrualDateAsOF !!}</td>
            <td>{!! $hRMSJvMaster->documentID !!}</td>
            <td>{!! $hRMSJvMaster->JVCode !!}</td>
            <td>{!! $hRMSJvMaster->serialNo !!}</td>
            <td>{!! $hRMSJvMaster->companyID !!}</td>
            <td>{!! $hRMSJvMaster->accmonth !!}</td>
            <td>{!! $hRMSJvMaster->accYear !!}</td>
            <td>{!! $hRMSJvMaster->accConfirmedYN !!}</td>
            <td>{!! $hRMSJvMaster->accConfirmedBy !!}</td>
            <td>{!! $hRMSJvMaster->accConfirmedDate !!}</td>
            <td>{!! $hRMSJvMaster->jvMasterAutoID !!}</td>
            <td>{!! $hRMSJvMaster->accJVSelectedYN !!}</td>
            <td>{!! $hRMSJvMaster->accJVpostedYN !!}</td>
            <td>{!! $hRMSJvMaster->jvPostedBy !!}</td>
            <td>{!! $hRMSJvMaster->jvPostedDate !!}</td>
            <td>{!! $hRMSJvMaster->createdby !!}</td>
            <td>{!! $hRMSJvMaster->createdDateTime !!}</td>
            <td>{!! $hRMSJvMaster->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['hRMSJvMasters.destroy', $hRMSJvMaster->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('hRMSJvMasters.show', [$hRMSJvMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('hRMSJvMasters.edit', [$hRMSJvMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>