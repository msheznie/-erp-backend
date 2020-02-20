<div class="table-responsive">
    <table class="table" id="serviceLines-table">
        <thead>
            <tr>
                <th>Servicelinecode</th>
        <th>Servicelinemastercode</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Servicelinedes</th>
        <th>Locationid</th>
        <th>Isactive</th>
        <th>Ispublic</th>
        <th>Isserviceline</th>
        <th>Isdepartment</th>
        <th>Ismaster</th>
        <th>Consolecode</th>
        <th>Consoledescription</th>
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
        @foreach($serviceLines as $serviceLine)
            <tr>
                <td>{!! $serviceLine->ServiceLineCode !!}</td>
            <td>{!! $serviceLine->serviceLineMasterCode !!}</td>
            <td>{!! $serviceLine->companySystemID !!}</td>
            <td>{!! $serviceLine->companyID !!}</td>
            <td>{!! $serviceLine->ServiceLineDes !!}</td>
            <td>{!! $serviceLine->locationID !!}</td>
            <td>{!! $serviceLine->isActive !!}</td>
            <td>{!! $serviceLine->isPublic !!}</td>
            <td>{!! $serviceLine->isServiceLine !!}</td>
            <td>{!! $serviceLine->isDepartment !!}</td>
            <td>{!! $serviceLine->isMaster !!}</td>
            <td>{!! $serviceLine->consoleCode !!}</td>
            <td>{!! $serviceLine->consoleDescription !!}</td>
            <td>{!! $serviceLine->createdUserGroup !!}</td>
            <td>{!! $serviceLine->createdPcID !!}</td>
            <td>{!! $serviceLine->createdUserID !!}</td>
            <td>{!! $serviceLine->modifiedPc !!}</td>
            <td>{!! $serviceLine->modifiedUser !!}</td>
            <td>{!! $serviceLine->createdDateTime !!}</td>
            <td>{!! $serviceLine->timeStamp !!}</td>
                <td>
                    {!! Form::open(['route' => ['serviceLines.destroy', $serviceLine->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{!! route('serviceLines.show', [$serviceLine->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                        <a href="{!! route('serviceLines.edit', [$serviceLine->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                        {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
