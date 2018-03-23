<table class="table table-responsive" id="segmentMasters-table">
    <thead>
        <tr>
            <th>Servicelinecode</th>
        <th>Servicelinemastercode</th>
        <th>Companyid</th>
        <th>Servicelinedes</th>
        <th>Locationid</th>
        <th>Isactive</th>
        <th>Ispublic</th>
        <th>Isserviceline</th>
        <th>Isdepartment</th>
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
    @foreach($segmentMasters as $segmentMaster)
        <tr>
            <td>{!! $segmentMaster->ServiceLineCode !!}</td>
            <td>{!! $segmentMaster->serviceLineMasterCode !!}</td>
            <td>{!! $segmentMaster->companyID !!}</td>
            <td>{!! $segmentMaster->ServiceLineDes !!}</td>
            <td>{!! $segmentMaster->locationID !!}</td>
            <td>{!! $segmentMaster->isActive !!}</td>
            <td>{!! $segmentMaster->isPublic !!}</td>
            <td>{!! $segmentMaster->isServiceLine !!}</td>
            <td>{!! $segmentMaster->isDepartment !!}</td>
            <td>{!! $segmentMaster->createdUserGroup !!}</td>
            <td>{!! $segmentMaster->createdPcID !!}</td>
            <td>{!! $segmentMaster->createdUserID !!}</td>
            <td>{!! $segmentMaster->modifiedPc !!}</td>
            <td>{!! $segmentMaster->modifiedUser !!}</td>
            <td>{!! $segmentMaster->createdDateTime !!}</td>
            <td>{!! $segmentMaster->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['segmentMasters.destroy', $segmentMaster->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('segmentMasters.show', [$segmentMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('segmentMasters.edit', [$segmentMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>