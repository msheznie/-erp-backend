<table class="table table-responsive" id="accruavalFromOPMasters-table">
    <thead>
        <tr>
            <th>Accruvalnarration</th>
        <th>Accrualdateasof</th>
        <th>Serialno</th>
        <th>Companyid</th>
        <th>Accmonth</th>
        <th>Accyear</th>
        <th>Accconfirmedyn</th>
        <th>Accconfirmedby</th>
        <th>Accconfirmeddate</th>
        <th>Jvmasterautoid</th>
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
    @foreach($accruavalFromOPMasters as $accruavalFromOPMaster)
        <tr>
            <td>{!! $accruavalFromOPMaster->accruvalNarration !!}</td>
            <td>{!! $accruavalFromOPMaster->accrualDateAsOF !!}</td>
            <td>{!! $accruavalFromOPMaster->serialNo !!}</td>
            <td>{!! $accruavalFromOPMaster->companyID !!}</td>
            <td>{!! $accruavalFromOPMaster->accmonth !!}</td>
            <td>{!! $accruavalFromOPMaster->accYear !!}</td>
            <td>{!! $accruavalFromOPMaster->accConfirmedYN !!}</td>
            <td>{!! $accruavalFromOPMaster->accConfirmedBy !!}</td>
            <td>{!! $accruavalFromOPMaster->accConfirmedDate !!}</td>
            <td>{!! $accruavalFromOPMaster->jvMasterAutoID !!}</td>
            <td>{!! $accruavalFromOPMaster->accJVpostedYN !!}</td>
            <td>{!! $accruavalFromOPMaster->jvPostedBy !!}</td>
            <td>{!! $accruavalFromOPMaster->jvPostedDate !!}</td>
            <td>{!! $accruavalFromOPMaster->createdby !!}</td>
            <td>{!! $accruavalFromOPMaster->createdDateTime !!}</td>
            <td>{!! $accruavalFromOPMaster->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['accruavalFromOPMasters.destroy', $accruavalFromOPMaster->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('accruavalFromOPMasters.show', [$accruavalFromOPMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('accruavalFromOPMasters.edit', [$accruavalFromOPMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>